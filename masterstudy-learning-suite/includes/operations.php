<?php
defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function () {
	if ( current_user_can( 'manage_options' ) || in_array( 'stm_lms_instructor', wp_get_current_user()->roles, true ) ) { add_submenu_page( 'mls-suite', 'Learning operations', 'Operations', 'read', 'mls-operations', 'mls_operations_page' ); }
} );
add_action( 'admin_post_mls_operations', 'mls_operations_save' );
add_action( 'admin_post_mls_gradebook_csv', 'mls_gradebook_csv' );
add_action( 'mls_event_recorded', 'mls_seal_event', 10, 5 );

function mls_seal_event( int $id, int $course, int $lesson, string $type, array $details ): void {
	if ( ( defined( 'MLS_TEST_RUN' ) && MLS_TEST_RUN ) || get_user_meta( get_current_user_id(), '_mls_browser_fixture', true ) ) { return; }
	global $wpdb;
	if ( (int) $wpdb->get_var( "SELECT GET_LOCK('mls-audit-chain',5)" ) !== 1 ) { update_post_meta( $id, '_mls_audit_error', 'seal_lock_unavailable' ); return; }
	try {
		$secret = get_option( 'mls_audit_secret' );
		if ( ! $secret ) { $secret = bin2hex( random_bytes( 32 ) ); add_option( 'mls_audit_secret', $secret, '', false ); }
		$head = get_option( 'mls_audit_head', array( 'hash' => '', 'id' => 0 ) );
		$post = get_post( $id );
		$payload = wp_json_encode( array( 'id' => $id, 'actor' => (int) $post->post_author, 'recorded_at' => $post->post_date_gmt, 'course' => $course, 'lesson' => $lesson, 'type' => $type, 'content' => $post->post_content, 'previous_id' => (int) $head['id'], 'previous_hash' => $head['hash'] ) );
		$hash = hash_hmac( 'sha256', $payload, $secret );
		update_post_meta( $id, '_mls_audit_payload', wp_slash( $payload ) ); update_post_meta( $id, '_mls_audit_hash', $hash );
		update_option( 'mls_audit_head', array( 'id' => $id, 'hash' => $hash ), false );
	} finally { $wpdb->get_var( "SELECT RELEASE_LOCK('mls-audit-chain')" ); }
}

function mls_verify_event( int $id ): bool {
	$post = get_post( $id ); $payload = get_post_meta( $id, '_mls_audit_payload', true ); $hash = get_post_meta( $id, '_mls_audit_hash', true ); $secret = get_option( 'mls_audit_secret' );
	if ( ! $post || $post->post_type !== 'mls_event' || ! $payload || ! $hash || ! $secret || ! hash_equals( $hash, hash_hmac( 'sha256', $payload, $secret ) ) ) { return false; }
	$data = json_decode( $payload, true );
	return $data['id'] === $id && $data['content'] === $post->post_content && $data['actor'] === (int) $post->post_author && $data['recorded_at'] === $post->post_date_gmt && $data['type'] === $post->post_title && $data['course'] === (int) get_post_meta( $id, '_mls_course', true ) && $data['lesson'] === (int) get_post_meta( $id, '_mls_lesson', true ) && ( ! $data['previous_id'] || hash_equals( $data['previous_hash'], (string) get_post_meta( $data['previous_id'], '_mls_audit_hash', true ) ) );
}

function mls_operation_form( string $mode, int $course = 0 ): void {
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'; wp_nonce_field( 'mls_operations' );
	echo '<input type="hidden" name="action" value="mls_operations"><input type="hidden" name="mode" value="' . esc_attr( $mode ) . '"><input type="hidden" name="course" value="' . $course . '">';
}

function mls_operations_page(): void {
	$course = absint( $_GET['course'] ?? 9838 );
	if ( ! mls_manage( $course ) || get_post_type( $course ) !== 'stm-courses' ) { wp_die( 'Select an assigned course.', '', array( 'response' => 403 ) ); }
	echo '<div class="wrap"><h1>Learning operations</h1><form method="get"><input type="hidden" name="page" value="mls-operations"><label>Course ID <input type="number" name="course" min="1" value="' . $course . '"></label><button class="button">Open</button></form><h2>' . esc_html( get_the_title( $course ) ) . '</h2>';
	mls_gradebook_page( $course );
	echo '<h2>Reviewed teaching updates</h2>'; mls_review_page( $course );
	echo '<h2>Completion records</h2>'; mls_certificate_admin( $course );
	if ( current_user_can( 'manage_options' ) ) {
		echo '<h2>Cohorts</h2>'; mls_operation_form( 'cohort_save' );
		echo '<p><label>Group ID (0 creates new) <input type="number" name="group" value="0" min="0"></label></p><p><label>Title <input name="title" required maxlength="200"></label></p><p><label>Manager IDs <input name="managers" required></label></p><p><label>Course IDs <input name="courses" value="' . $course . '" required></label></p><p><label>Approved learner roster IDs <input name="roster" maxlength="5000" required></label></p><p><label>Seats <input type="number" name="seats" min="1" max="500" value="25" required></label></p><button class="button">Save cohort configuration</button></form><p>Managers enrol only administrator-approved roster members through the private Learning Hub. Existing enrolments are preserved. Saving configuration does not remove enrolments.</p>';
		$groups = get_posts( array( 'post_type' => 'mls_group', 'post_status' => 'private', 'posts_per_page' => 50 ) );
		foreach ( $groups as $group ) { echo '<p>Group ' . $group->ID . ': ' . esc_html( $group->post_title ) . '</p>'; }
		echo '<h2>Email delivery</h2><p>' . ( mls_mail_ready() ? 'Private SMTP transport configured. Run a delivery test before enabling learner notifications.' : 'Blocked: configure MLS_SMTP_HOST, MLS_SMTP_USER, MLS_SMTP_PASSWORD and MLS_SMTP_FROM privately in wp-config.php. Optional MLS_SMTP_PORT: 465 (SMTPS) or 587 (STARTTLS). No secrets are stored in the plugin or shown here.' ) . '</p>';
		foreach ( get_posts( array( 'post_type' => 'mls_mail', 'post_status' => 'private', 'posts_per_page' => 20 ) ) as $message ) { echo '<p>#' . $message->ID . ' ' . esc_html( $message->post_title ) . ': ' . esc_html( get_post_meta( $message->ID, '_mls_status', true ) ) . '</p>'; }
		echo '<h2>Catalogue quality scan</h2>'; $state = get_option( 'mls_catalogue_audit', array() );
		echo '<p>' . esc_html( wp_json_encode( $state ) ) . '</p>'; mls_operation_form( 'audit' ); echo '<button class="button">Start / restart read-only catalogue scan</button></form><p>The worker audits 25 records per checkpoint. It does not publish draft assessments or change course material.</p>';
	}
	echo '</div>';
}

function mls_operation_ids( string $value ): array { return array_values( array_unique( array_filter( array_map( 'absint', preg_split( '/[\s,;]+/', trim( $value ) ) ) ) ) ); }

function mls_operations_save(): void {
	check_admin_referer( 'mls_operations' ); $mode = sanitize_key( $_POST['mode'] ?? '' ); $course = absint( $_POST['course'] ?? 0 );
	try {
		if ( $mode === 'cohort_enrol' ) {
			$result = mls_group_add_members( absint( $_POST['group'] ?? 0 ), mls_operation_ids( wp_unslash( $_POST['members'] ?? '' ) ) );
			if ( is_wp_error( $result ) ) { throw new RuntimeException( $result->get_error_message() ); }
			wp_safe_redirect( home_url( '/learning-hub/' ) ); exit;
		}
		if ( ! mls_manage( $course ) && ! current_user_can( 'manage_options' ) ) { throw new RuntimeException( 'Not authorised.' ); }
		if ( $mode === 'cohort_save' ) {
			$id = absint( $_POST['group'] ?? 0 ); $old = mls_group_data( $id );
			$result = mls_save_group( $id, wp_unslash( $_POST['title'] ?? '' ), array( 'managers' => mls_operation_ids( wp_unslash( $_POST['managers'] ?? '' ) ), 'courses' => mls_operation_ids( wp_unslash( $_POST['courses'] ?? '' ) ), 'members' => $old['members'] ?? array(), 'eligible_members' => mls_operation_ids( wp_unslash( $_POST['roster'] ?? '' ) ), 'seats' => absint( $_POST['seats'] ?? 0 ) ) );
			if ( is_wp_error( $result ) ) { throw new RuntimeException( $result->get_error_message() ); }
		} elseif ( $mode === 'apply_draft' || $mode === 'restore_revision' ) {
			$result = mls_apply_review( $course, $mode, absint( $_POST['record'] ?? 0 ), sanitize_text_field( $_POST['hash'] ?? '' ), wp_unslash( $_POST['text'] ?? '' ) );
			if ( is_wp_error( $result ) ) { throw new RuntimeException( $result->get_error_message() ); }
		} elseif ( $mode === 'certificate' ) {
			$result = mls_issue_certificate( $course, absint( $_POST['learner'] ?? 0 ), wp_unslash( $_POST['note'] ?? '' ) );
			if ( is_wp_error( $result ) ) { throw new RuntimeException( $result->get_error_message() ); }
		} elseif ( $mode === 'revoke_certificate' ) {
			$id = absint( $_POST['record'] ?? 0 );
			if ( get_post_type( $id ) !== 'mls_certificate' || (int) get_post_meta( $id, '_mls_course', true ) !== $course ) { throw new RuntimeException( 'Invalid certificate.' ); }
			update_post_meta( $id, '_mls_revoked', gmdate( 'c' ) ); mls_event( $course, 0, 'certificate_revoked', array( 'certificate' => $id ) );
		} elseif ( $mode === 'audit' && current_user_can( 'manage_options' ) ) {
			mls_start_catalogue_audit();
		} else { throw new RuntimeException( 'Unknown action or missing permission.' ); }
		wp_safe_redirect( admin_url( 'admin.php?page=mls-operations&course=' . $course ) ); exit;
	} catch ( Throwable $error ) { wp_die( esc_html( $error->getMessage() ), 'Learning operations', array( 'response' => 400 ) ); }
}

function mls_gradebook_query( int $course, int $page, string $status = '' ): WP_Query {
	$args = array( 'post_type' => 'mls_attempt', 'post_status' => 'private', 'posts_per_page' => 25, 'paged' => max( 1, $page ), 'meta_query' => array( array( 'key' => '_mls_course', 'value' => $course ) ) );
	if ( in_array( $status, array( 'in_progress', 'pending_review', 'passed', 'revision_required', 'expired', 'retired_release' ), true ) ) { $args['meta_query'][] = array( 'key' => '_mls_status', 'value' => $status ); }
	return new WP_Query( $args );
}

function mls_gradebook_page( int $course ): void {
	$page = max( 1, absint( $_GET['pg'] ?? 1 ) ); $status = sanitize_key( $_GET['status'] ?? '' ); $query = mls_gradebook_query( $course, $page, $status );
	echo '<h2>Gradebook</h2><form method="get"><input type="hidden" name="page" value="mls-operations"><input type="hidden" name="course" value="' . $course . '"><label>Status <select name="status"><option value="">All</option>';
	foreach ( array( 'pending_review', 'passed', 'revision_required', 'in_progress', 'expired', 'retired_release' ) as $value ) { echo '<option value="' . $value . '" ' . selected( $status, $value, false ) . '>' . esc_html( str_replace( '_', ' ', $value ) ) . '</option>'; }
	echo '</select></label><button class="button">Filter</button></form><p>' . $query->found_posts . ' submissions. <a href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=mls_gradebook_csv&course=' . $course ), 'mls_gradebook_' . $course ) ) . '">Export course CSV</a></p><table class="widefat striped"><thead><tr><th>Learner</th><th>Lesson</th><th>Status</th><th>Score</th><th>Evidence</th></tr></thead><tbody>';
	foreach ( $query->posts as $post ) { $data = json_decode( $post->post_content, true ); $user = get_userdata( $post->post_author ); echo '<tr><td>' . esc_html( $user ? $user->display_name : 'Deleted account' ) . '</td><td>' . esc_html( $post->post_title ) . '</td><td>' . esc_html( get_post_meta( $post->ID, '_mls_status', true ) ) . '</td><td>' . esc_html( isset( $data['score'] ) ? $data['score'] . '%' : '-' ) . '</td><td>' . mls_evidence_list( $post->ID ) . '</td></tr>'; }
	echo '</tbody></table><p>' . wp_kses_post( paginate_links( array( 'base' => add_query_arg( array( 'page' => 'mls-operations', 'course' => $course, 'status' => $status, 'pg' => '%#%' ), admin_url( 'admin.php' ) ), 'current' => $page, 'total' => $query->max_num_pages ) ) ) . '</p><p><a href="' . esc_url( admin_url( 'admin.php?page=mls-suite&course=' . $course ) ) . '">Mark and review submissions</a></p>';
}

function mls_csv_cell( $value ): string { $value = (string) $value; return preg_match( '/^[\s]*[=+@-]/', $value ) ? "'" . $value : $value; }

function mls_gradebook_csv(): void {
	$course = absint( $_GET['course'] ?? 0 ); check_admin_referer( 'mls_gradebook_' . $course );
	if ( ! mls_manage( $course ) || get_post_type( $course ) !== 'stm-courses' ) { wp_die( 'Not authorised.', '', array( 'response' => 403 ) ); }
	nocache_headers(); header( 'Content-Type: text/csv; charset=utf-8' ); header( 'X-Content-Type-Options: nosniff' ); header( 'Content-Disposition: attachment; filename="course-' . $course . '-gradebook.csv"' );
	$handle = fopen( 'php://output', 'w' ); fputcsv( $handle, array( 'Attempt', 'Learner ID', 'Learner', 'Lesson', 'Status', 'Score', 'Submitted UTC', 'Feedback' ), ',', '"', '' );
	for ( $page = 1; ; ++$page ) { $query = mls_gradebook_query( $course, $page ); foreach ( $query->posts as $post ) { $data = json_decode( $post->post_content, true ); $user = get_userdata( $post->post_author ); fputcsv( $handle, array_map( 'mls_csv_cell', array( $post->ID, $post->post_author, $user ? $user->display_name : 'Deleted account', $post->post_title, get_post_meta( $post->ID, '_mls_status', true ), $data['score'] ?? '', isset( $data['submitted_at'] ) ? gmdate( 'c', $data['submitted_at'] ) : '', $data['feedback'] ?? '' ) ), ',', '"', '' ); } if ( $page >= $query->max_num_pages ) { break; } }
	fclose( $handle ); exit;
}

add_action( 'mls_catalogue_audit_step', 'mls_catalogue_audit_step' );
function mls_start_catalogue_audit(): void {
	wp_clear_scheduled_hook( 'mls_catalogue_audit_step' ); update_option( 'mls_catalogue_audit', array( 'status' => 'queued', 'cursor' => 0, 'scanned' => 0, 'needs_review' => 0, 'started_at' => gmdate( 'c' ) ), false );
	wp_schedule_single_event( time() + 5, 'mls_catalogue_audit_step' );
}

function mls_catalogue_audit_step(): void {
	global $wpdb;
	if ( (int) $wpdb->get_var( "SELECT GET_LOCK('mls-catalogue-audit',0)" ) !== 1 ) { return; }
	try {
		$state = get_option( 'mls_catalogue_audit', array() ); if ( ! in_array( $state['status'] ?? '', array( 'queued', 'running' ), true ) ) { return; }
		$ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type='stm-courses' AND post_status='publish' AND ID>%d ORDER BY ID LIMIT 25", (int) $state['cursor'] ) );
		foreach ( $ids as $id ) {
			$lessons = array_values( array_filter( array_map( 'intval', (array) get_post_meta( $id, '_ca_lesson_post_ids', true ) ) ) ); $issues = array();
			if ( ! get_post_meta( $id, '_ca_verified_release', true ) ) { $issues[] = 'release_not_verified'; }
			if ( ! has_post_thumbnail( $id ) ) { $issues[] = 'featured_image_missing'; }
			if ( ! $lessons ) { $issues[] = 'lesson_mapping_missing'; }
			$reviewed = 0; $missing_media = 0; $thin = 0;
			foreach ( $lessons as $lesson ) { $context = mls_context( $lesson ); if ( $context && mls_bank( $context ) ) { ++$reviewed; } if ( mb_strlen( wp_strip_all_tags( get_post_field( 'post_content', $lesson ) ) ) < 500 ) { ++$thin; } if ( ! get_post_meta( $lesson, 'lesson_ext_link_url', true ) && ! get_post_meta( $lesson, 'lesson_video_url', true ) ) { ++$missing_media; } }
			if ( $reviewed < count( $lessons ) ) { $issues[] = 'assessment_review_required'; } if ( $thin ) { $issues[] = 'short_lessons'; } if ( $missing_media ) { $issues[] = 'media_review_required'; }
			update_post_meta( $id, '_mls_quality_audit', array( 'checked_at' => gmdate( 'c' ), 'lessons' => count( $lessons ), 'reviewed_banks' => $reviewed, 'short_lessons' => $thin, 'lessons_without_linked_video' => $missing_media, 'issues' => $issues ) );
			++$state['scanned']; if ( $issues ) { ++$state['needs_review']; } $state['cursor'] = (int) $id;
		}
		$state['status'] = count( $ids ) < 25 ? 'complete' : 'running'; $state['updated_at'] = gmdate( 'c' ); update_option( 'mls_catalogue_audit', $state, false );
		if ( $state['status'] === 'running' ) { wp_schedule_single_event( time() + 10, 'mls_catalogue_audit_step' ); }
	} finally { $wpdb->get_var( "SELECT RELEASE_LOCK('mls-catalogue-audit')" ); }
}
