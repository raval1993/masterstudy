<?php
defined( 'ABSPATH' ) || exit;

function mls_admin_menu(): void {
	if ( current_user_can( 'manage_options' ) || in_array( 'stm_lms_instructor', wp_get_current_user()->roles, true ) ) { add_menu_page( 'Learning Suite', 'Learning Suite', 'read', 'mls-suite', 'mls_admin_page', 'dashicons-welcome-learn-more', 58 ); }
}

function mls_admin_page(): void {
	$course = absint( $_GET['course'] ?? 9838 );
	if ( ! mls_manage( $course ) ) { echo '<div class="wrap"><h1>Learning Suite</h1><p>Select an assigned course.</p><form method="get"><input type="hidden" name="page" value="mls-suite"><label>Course ID <input name="course" type="number" min="1"></label><button class="button">Open course</button></form></div>'; return; }
	global $wpdb;
	$counts = $wpdb->get_results( "SELECT post_type,COUNT(*) AS total FROM {$wpdb->posts} WHERE post_type IN ('mls_bank','mls_attempt','mls_event','mls_group') GROUP BY post_type" );
	echo '<div class="wrap"><h1>Learning Suite</h1><p><a href="' . esc_url( home_url( '/learning-hub/' ) ) . '">Open learner portal</a> | <a href="' . esc_url( admin_url( 'admin.php?page=mls-operations&course=' . $course ) ) . '">Evidence, cohorts, exports and teaching revisions</a></p>';
	if ( current_user_can( 'manage_options' ) ) { foreach ( $counts as $count ) { echo '<span style="margin-right:24px">' . esc_html( $count->post_type ) . ': <strong>' . intval( $count->total ) . '</strong></span>'; } }
	$lessons = (array) get_post_meta( $course, '_ca_lesson_post_ids', true );
	echo '<h2>Course configuration</h2><form method="get"><input type="hidden" name="page" value="mls-suite"><label>Course ID <input name="course" type="number" min="1" value="' . $course . '"></label> <button class="button">Open course</button></form>';
	if ( get_post_type( $course ) !== 'stm-courses' ) { echo '<p>Select a valid course.</p></div>'; return; }
	echo '<h3>' . esc_html( get_the_title( $course ) ) . '</h3>';
	echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=mls-forms&course=' . $course ) ) . '">Learner forms</a> | <a href="' . esc_url( admin_url( 'admin.php?page=mls-rubrics&course=' . $course ) ) . '">Assessment rubric editor</a></p>';
	foreach ( current_user_can( 'manage_options' ) ? array( 'rules' => 'Learning rules', 'instructors' => 'Course instructors', 'live' => 'Live classroom' ) : array() as $type => $title ) {
		$value = get_post_meta( $course, '_mls_' . $type, true );
		if ( ! is_array( $value ) ) { $value = array(); }
		echo '<details style="margin:18px 0"><summary><strong>' . esc_html( $title ) . '</strong></summary><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'mls_save' );
		echo '<input type="hidden" name="action" value="mls_save"><input type="hidden" name="mode" value="' . esc_attr( $type ) . '"><input type="hidden" name="course" value="' . $course . '"><textarea name="data" rows="7" class="large-text code">' . esc_textarea( wp_json_encode( $value, JSON_PRETTY_PRINT ) ) . '</textarea><button class="button button-primary">Save</button></form></details>';
	}
	echo '<h2>Local AI authoring</h2><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'; wp_nonce_field( 'mls_generate' );
	echo '<input type="hidden" name="action" value="mls_generate"><label>Lesson <select name="lesson">';
	foreach ( $lessons as $lesson ) { echo '<option value="' . intval( $lesson ) . '">' . esc_html( get_the_title( $lesson ) ) . '</option>'; }
	echo '</select></label> <select name="kind"><option value="quiz">Draft quiz and rubric</option><option value="enrichment">Draft worked example and practice</option></select> <button class="button">Queue draft</button></form>';
	$jobs = get_posts( array( 'post_type' => 'mls_job', 'post_status' => 'private', 'posts_per_page' => 20, 'meta_key' => '_mls_course', 'meta_value' => $course ) );
	foreach ( $jobs as $job ) { echo '<p>' . esc_html( $job->post_title ) . ': <strong>' . esc_html( get_post_meta( $job->ID, '_mls_status', true ) ) . '</strong> ' . esc_html( get_post_meta( $job->ID, '_mls_error', true ) ) . '</p>'; }
	$drafts = get_posts( array( 'post_type' => 'mls_draft', 'post_status' => 'private', 'posts_per_page' => 20, 'meta_key' => '_mls_course', 'meta_value' => $course ) );
	foreach ( $drafts as $draft ) { echo '<details><summary>' . esc_html( $draft->post_title ) . ' (draft)</summary><p>' . nl2br( esc_html( $draft->post_content ) ) . '</p></details>'; }
	echo '<h2>Question banks</h2><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'; wp_nonce_field( 'mls_save' );
	echo '<input type="hidden" name="action" value="mls_save"><input type="hidden" name="mode" value="bank"><input type="hidden" name="course" value="' . $course . '"><label>Lesson <select name="lesson">';
	foreach ( $lessons as $lesson ) { echo '<option value="' . intval( $lesson ) . '">' . esc_html( get_the_title( $lesson ) ) . '</option>'; }
	echo '</select></label><p><label>Bank JSON<textarea class="large-text code" name="data" rows="12">' . esc_textarea( wp_json_encode( array( 'minutes' => 20, 'pass_percent' => 80, 'randomize' => true, 'questions' => array( array( 'type' => 'short', 'prompt' => 'Explain the key steps you would take in this lesson.', 'explanation' => '', 'rubric' => 'Accuracy; relevant steps; appropriate workplace example.' ) ) ), JSON_PRETTY_PRINT ) ) . '</textarea></label></p><p><label><input type="checkbox" name="publish" value="1"> I have reviewed the questions, answers and assessment criteria.</label></p><button class="button button-primary">Save question bank</button></form>';
	$banks = get_posts( array( 'post_type' => 'mls_bank', 'post_status' => array( 'draft', 'publish' ), 'posts_per_page' => 50, 'meta_key' => '_mls_course', 'meta_value' => $course ) );
	echo '<table class="widefat striped" style="margin-top:20px"><thead><tr><th>Bank</th><th>Status</th><th>Lesson</th><th>Review</th></tr></thead><tbody>';
	foreach ( $banks as $bank ) {
		echo '<tr><td>' . esc_html( $bank->post_title ) . '<details><summary>View questions</summary><pre style="white-space:pre-wrap">' . esc_html( json_encode( json_decode( $bank->post_content, true ), JSON_PRETTY_PRINT ) ) . '</pre></details></td><td>' . esc_html( $bank->post_status ) . '</td><td>' . intval( get_post_meta( $bank->ID, '_mls_lesson', true ) ) . '</td><td><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'mls_save' );
		echo '<input type="hidden" name="action" value="mls_save"><input type="hidden" name="mode" value="approve"><input type="hidden" name="course" value="' . $course . '"><input type="hidden" name="bank" value="' . $bank->ID . '"><button class="button">Approve reviewed bank</button></form></td></tr>';
	}
	echo '</tbody></table><h2>Gradebook and submissions</h2>';
	$attempts = get_posts( array( 'post_type' => 'mls_attempt', 'post_status' => 'private', 'posts_per_page' => 50, 'meta_key' => '_mls_course', 'meta_value' => $course ) );
	echo '<table class="widefat striped"><thead><tr><th>Learner</th><th>Assessment</th><th>Status</th><th>Review</th></tr></thead><tbody>';
	foreach ( $attempts as $attempt ) {
		$data = json_decode( $attempt->post_content, true ); $user = get_userdata( $attempt->post_author );
		$evidence_html = mls_evidence_list( $attempt->ID );
		echo '<tr><td>' . esc_html( $user ? $user->display_name : 'Deleted account' ) . '</td><td>' . esc_html( $attempt->post_title ) . '<details><summary>Answers and rubric</summary><pre style="white-space:pre-wrap">' . esc_html( wp_json_encode( $data, JSON_PRETTY_PRINT ) ) . '</pre></details>' . $evidence_html . '</td><td>' . esc_html( get_post_meta( $attempt->ID, '_mls_status', true ) ) . '</td><td><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'; wp_nonce_field( 'mls_save' );
		echo '<input type="hidden" name="action" value="mls_save"><input type="hidden" name="mode" value="grade"><input type="hidden" name="course" value="' . $course . '"><input type="hidden" name="attempt" value="' . $attempt->ID . '"><input type="hidden" name="submission_hash" value="' . esc_attr( mls_submission_hash( $attempt->ID ) ) . '">' . mls_rubric_marking_fields( $data, $attempt->ID ) . '<label>Decision<select name="status"><option value="passed">Passed</option><option value="revision_required">Revision requested</option></select></label><p><label>Feedback<textarea name="feedback" rows="3" required></textarea></label></p><button class="button">Record instructor decision</button></form><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'; wp_nonce_field( 'mls_generate' );
		echo '<input type="hidden" name="action" value="mls_generate"><input type="hidden" name="kind" value="feedback"><input type="hidden" name="lesson" value="' . intval( get_post_meta( $attempt->ID, '_mls_lesson', true ) ) . '"><input type="hidden" name="attempt" value="' . $attempt->ID . '"><button class="button">Queue AI feedback suggestion</button></form></td></tr>';
	}
	echo '</tbody></table><h2>Configuration formats</h2><p>Rules: {"prerequisites":[course_id],"linear":true,"drip":{"lesson_id":days},"trial_days":7}. Instructors: [user_id]. Live: {"url":"https://…zoom.us/j/…","starts_at":"2026-10-20T10:00:00Z"}. Form: [{"name":"goal","label":"Learning goal","required":true}].</p></div>';
}

function mls_admin_save(): void {
	check_admin_referer( 'mls_save' );
	$course = absint( $_POST['course'] ?? 0 );
	if ( ! mls_manage( $course ) || get_post_type( $course ) !== 'stm-courses' ) { wp_die( 'Not authorised.', '', array( 'response' => 403 ) ); }
	$mode = sanitize_key( $_POST['mode'] ?? '' );
	if ( ! current_user_can( 'manage_options' ) && ! in_array( $mode, array( 'bank', 'approve', 'grade' ), true ) ) { wp_die( 'Administrator required for course settings.', '', array( 'response' => 403 ) ); }
	try {
		if ( strlen( $_POST['data'] ?? '' ) > 100000 ) { throw new InvalidArgumentException( 'Configuration too large.' ); }
		$data = json_decode( wp_unslash( $_POST['data'] ?? '[]' ), true, 32, JSON_THROW_ON_ERROR );
		if ( $mode === 'bank' ) {
			$context = mls_context( absint( $_POST['lesson'] ?? 0 ) );
			if ( ! $context || $context['course_id'] !== $course ) { throw new InvalidArgumentException( 'Wrong lesson/course mapping.' ); }
			$data = mls_validate_bank( $data );
			$id = wp_insert_post( array( 'post_type' => 'mls_bank', 'post_status' => ! empty( $_POST['publish'] ) ? 'publish' : 'draft', 'post_title' => get_the_title( $context['lesson_id'] ), 'post_content' => wp_slash( wp_json_encode( $data ) ) ), true );
			if ( is_wp_error( $id ) ) { throw new RuntimeException( $id->get_error_message() ); }
			foreach ( array( '_mls_course' => $course, '_mls_lesson' => $context['lesson_id'], '_mls_release' => $context['release'] ) as $key => $value ) { update_post_meta( $id, $key, $value ); }
			if ( ! empty( $_POST['publish'] ) ) { update_post_meta( $id, '_mls_reviewed_by', get_current_user_id() ); }
		} elseif ( $mode === 'approve' ) {
			$id = absint( $_POST['bank'] ?? 0 ); $bank = get_post( $id );
			if ( ! $bank || $bank->post_type !== 'mls_bank' || (int) get_post_meta( $id, '_mls_course', true ) !== $course ) { throw new InvalidArgumentException( 'Invalid bank.' ); }
			mls_validate_bank( json_decode( $bank->post_content, true ) );
			$context = mls_context( (int) get_post_meta( $id, '_mls_lesson', true ) );
			if ( ! $context || $context['release'] !== get_post_meta( $id, '_mls_release', true ) ) { throw new InvalidArgumentException( 'This bank belongs to an older release.' ); }
			if ( get_post_meta( $id, '_mls_source', true ) === 'local-ai-draft' ) {
				$source = ''; foreach ( mce_study_sections( get_post_field( 'post_content', $context['lesson_id'] ) ) as $section ) { $source .= $section['text'] . "\n"; }
				mls_validate_ai_bank( json_decode( $bank->post_content, true ), $source );
			}
			wp_update_post( array( 'ID' => $id, 'post_status' => 'publish' ) );
			update_post_meta( $id, '_mls_reviewed_by', get_current_user_id() );
		} elseif ( $mode === 'grade' ) {
			$result = mls_record_grade( $course, absint( $_POST['attempt'] ?? 0 ), sanitize_key( $_POST['status'] ?? '' ), wp_unslash( $_POST['feedback'] ?? '' ), sanitize_text_field( $_POST['submission_hash'] ?? '' ), mls_marks_from_post( wp_unslash( $_POST['marks'] ?? array() ) ) );
			if ( is_wp_error( $result ) ) { throw new InvalidArgumentException( $result->get_error_message() ); }
		} elseif ( $mode === 'rules' ) {
			$data = mls_validate_rules( $data, $course ); update_post_meta( $course, '_mls_rules', $data );
		} elseif ( $mode === 'instructors' ) {
			foreach ( $data as $user ) { $record = get_userdata( absint( $user ) ); if ( ! $record || ! array_intersect( array( 'administrator', 'stm_lms_instructor' ), $record->roles ) ) { throw new InvalidArgumentException( 'Use existing instructor/admin IDs.' ); } }
			update_post_meta( $course, '_mls_instructors', array_values( array_unique( array_map( 'absint', $data ) ) ) );
		} elseif ( $mode === 'live' ) {
			$url = esc_url_raw( $data['url'] ?? '', array( 'https' ) ); $host = wp_parse_url( $url, PHP_URL_HOST );
			if ( $url && ! preg_match( '/(^|\.)(zoom\.us|meet\.google\.com|meet\.jit\.si)$/', $host ?? '' ) ) { throw new InvalidArgumentException( 'Use an HTTPS Zoom, Google Meet or Jitsi meeting URL.' ); }
			update_post_meta( $course, '_mls_live', array( 'url' => $url, 'starts_at' => sanitize_text_field( $data['starts_at'] ?? '' ) ) );
		} elseif ( $mode === 'form' ) {
			throw new InvalidArgumentException( 'Use the Learner forms editor to save a version-checked form.' );
		} else { throw new InvalidArgumentException( 'Unknown action.' ); }
		mls_event( $course, 0, 'configuration_' . $mode );
		wp_safe_redirect( admin_url( 'admin.php?page=mls-suite&course=' . $course ) ); exit;
	} catch ( Throwable $error ) { wp_die( esc_html( $error->getMessage() ), 'Configuration error', array( 'response' => 400 ) ); }
}
