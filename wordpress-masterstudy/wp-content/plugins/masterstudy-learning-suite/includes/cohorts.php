<?php
defined( 'ABSPATH' ) || exit;

function mls_group_data( int $id ): ?array {
	$post = get_post( $id );
	return $post && $post->post_type === 'mls_group' && $post->post_status === 'private' ? json_decode( $post->post_content, true ) : null;
}

function mls_group_manage( int $id ): bool {
	$data = mls_group_data( $id );
	return $data && ( current_user_can( 'manage_options' ) || in_array( get_current_user_id(), $data['managers'], true ) );
}

function mls_save_group( int $id, string $title, array $data ) {
	if ( ! current_user_can( 'manage_options' ) || ( $id && ! mls_group_data( $id ) ) ) { return mls_error( 'Administrator required.', 403 ); }
	$title = sanitize_text_field( $title );
	$clean = array( 'managers' => array_values( array_unique( array_map( 'absint', $data['managers'] ?? array() ) ) ), 'courses' => array_values( array_unique( array_map( 'absint', $data['courses'] ?? array() ) ) ), 'members' => array_values( array_unique( array_map( 'absint', $data['members'] ?? array() ) ) ), 'eligible_members' => array_values( array_unique( array_map( 'absint', $data['eligible_members'] ?? ( $data['members'] ?? array() ) ) ) ), 'seats' => absint( $data['seats'] ?? 0 ) );
	if ( ! $title || ! $clean['courses'] || $clean['seats'] < 1 || $clean['seats'] > 500 || count( $clean['members'] ) > $clean['seats'] || count( $clean['courses'] ) > 50 || ! $clean['managers'] || count( $clean['managers'] ) > 10 ) { return mls_error( 'Provide a title, 1-50 courses, 1-10 managers and 1-500 seats. Members cannot exceed seats.' ); }
	foreach ( $clean['courses'] as $course ) { if ( get_post_type( $course ) !== 'stm-courses' || get_post_status( $course ) !== 'publish' ) { return mls_error( 'Use published MasterStudy course IDs.' ); } }
	if ( count( $clean['eligible_members'] ) > 500 || array_diff( $clean['members'], $clean['eligible_members'] ) ) { return mls_error( 'Provide an approved roster of up to 500 learners, including all current members.' ); }
	foreach ( array_merge( $clean['managers'], $clean['eligible_members'] ) as $user ) { if ( ! get_userdata( $user ) ) { return mls_error( 'Use existing learner and manager IDs.' ); } }
	$record = array( 'post_type' => 'mls_group', 'post_status' => 'private', 'post_title' => $title, 'post_content' => wp_slash( wp_json_encode( $clean ) ) );
	$result = $id ? wp_update_post( array_merge( $record, array( 'ID' => $id ) ), true ) : wp_insert_post( $record, true );
	if ( ! is_wp_error( $result ) ) { mls_event( 0, 0, 'cohort_saved', array( 'group' => $result, 'seats' => $clean['seats'] ) ); }
	return $result;
}

function mls_group_add_members( int $id, array $members ) {
	if ( ! mls_group_manage( $id ) ) { return mls_error( 'You cannot manage this cohort.', 403 ); }
	global $wpdb; $lock = 'mls-group-' . $id;
	if ( (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s,2)', $lock ) ) !== 1 ) { return mls_error( 'Please retry shortly.', 409 ); }
	try {
		clean_post_cache( $id ); $data = mls_group_data( $id );
		$new = array_values( array_unique( array_map( 'absint', $members ) ) );
		if ( array_diff( $new, $data['eligible_members'] ?? $data['members'] ) ) { return mls_error( 'Only administrator-approved roster members can be enrolled by a cohort manager.', 403 ); }
		if ( count( $new ) > 100 ) { return mls_error( 'Add up to 100 existing learners per batch.' ); }
		foreach ( $new as $user ) { if ( ! get_userdata( $user ) ) { return mls_error( 'A learner ID was not found.' ); } }
		$all = array_values( array_unique( array_merge( $data['members'], $new ) ) );
		if ( count( $all ) > $data['seats'] ) { return mls_error( 'This batch exceeds the cohort seat allowance.', 409 ); }
		$data['members'] = $all;
		$saved = wp_update_post( array( 'ID' => $id, 'post_content' => wp_slash( wp_json_encode( $data ) ) ), true );
		if ( is_wp_error( $saved ) ) { return $saved; }
		// Never reset progress or start dates on existing enrolments.
		foreach ( $new as $user ) { foreach ( $data['courses'] as $course ) { if ( ! STM_LMS_Course::get_user_course( $user, $course ) ) { $lessons = (array) get_post_meta( $course, '_ca_lesson_post_ids', true ); STM_LMS_Course::add_user_course( $course, $user, (int) ( $lessons[0] ?? 0 ) ); } } }
		mls_event( 0, 0, 'cohort_enrolment', array( 'group' => $id, 'member_count' => count( $new ) ) );
		return $data;
	} finally { $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) ); }
}

function mls_cohort_portal(): string {
	if ( ! is_user_logged_in() ) { return ''; }
	$groups = get_posts( array( 'post_type' => 'mls_group', 'post_status' => 'private', 'posts_per_page' => -1 ) ); $html = '';
	foreach ( $groups as $group ) {
		if ( ! mls_group_manage( $group->ID ) ) { continue; } $data = mls_group_data( $group->ID );
		$html .= '<h2>' . esc_html( $group->post_title ) . '</h2><p>' . count( $data['members'] ) . ' / ' . $data['seats'] . ' seats</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">' . wp_nonce_field( 'mls_operations', '_wpnonce', true, false ) . '<input type="hidden" name="action" value="mls_operations"><input type="hidden" name="mode" value="cohort_enrol"><input type="hidden" name="group" value="' . $group->ID . '"><label>Approved learner IDs <input name="members" required maxlength="1000"></label><button>Add learners</button></form><div class="mls-table-wrap"><table><thead><tr><th>Learner</th><th>Course</th><th>Progress</th></tr></thead><tbody>';
		foreach ( array_slice( $data['members'], 0, 50 ) as $user ) { foreach ( $data['courses'] as $course ) { $record = get_userdata( $user ); if ( ! $record ) { continue; } $progress = STM_LMS_Course::get_user_course( $user, $course ); $html .= '<tr><td>' . esc_html( $record->display_name ) . '</td><td>' . esc_html( get_the_title( $course ) ) . '</td><td>' . intval( $progress['progress_percent'] ?? 0 ) . '%</td></tr>'; } }
		$html .= '</tbody></table></div>';
	}
	return $html;
}
