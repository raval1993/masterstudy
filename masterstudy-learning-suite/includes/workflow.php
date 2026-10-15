<?php
defined( 'ABSPATH' ) || exit;

function mls_form_request( WP_REST_Request $request ) {
	$context = mls_context( absint( $request['lesson'] ) );
	$input = $request->get_json_params();
	if ( ! $context || ! mls_access( $context ) || ! is_array( $input ) || strlen( $request->get_body() ) > 20000 ) { return mls_error( 'Form unavailable.', 403 ); }
	$schema = get_post_meta( $context['course_id'], '_mls_form', true );
	if ( ! is_array( $schema ) || ! $schema ) { return mls_error( 'No learner form configured.', 404 ); }
	$answers = array();
	foreach ( $schema as $field ) {
		$value = $input[ $field['name'] ] ?? '';
		if ( ! is_string( $value ) || strlen( $value ) > 1000 || ( $field['required'] && ! trim( $value ) ) ) { return mls_error( 'Complete the required fields (maximum 1000 characters each).' ); }
		$answers[ $field['name'] ] = sanitize_textarea_field( $value );
	}
	update_user_meta( get_current_user_id(), '_mls_form_' . $context['course_id'], array( 'answers' => $answers, 'updated_at' => time(), 'release' => $context['release'] ) );
	mls_event( $context['course_id'], $context['lesson_id'], 'learner_form_saved' );
	return mls_private_response( array( 'saved' => true ) );
}

function mls_rewards( int $user ): array {
	$attempts = get_posts( array( 'post_type' => 'mls_attempt', 'post_status' => 'private', 'author' => $user, 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_mls_status', 'meta_value' => 'passed' ) );
	$lessons = array();
	foreach ( $attempts as $id ) {
		$lesson = (int) get_post_meta( $id, '_mls_lesson', true );
		$context = mls_context( $lesson );
		if ( $context && $context['release'] === get_post_meta( $id, '_mls_release', true ) ) { $lessons[ $lesson ] = true; }
	}
	$count = count( $lessons );
	return array( 'xp' => $count * 25, 'passed_lessons' => $count, 'badges' => array_values( array_filter( array( $count >= 1 ? 'First assessment' : '', $count >= 10 ? 'Consistent learner' : '', $count >= 50 ? 'Learning milestone' : '' ) ) ) );
}

function mls_download_records(): void {
	if ( ! is_user_logged_in() ) { wp_die( 'Sign in first.', '', array( 'response' => 403 ) ); }
	check_admin_referer( 'mls_records' );
	$records = array( 'exported_at' => gmdate( 'c' ), 'learner_id' => get_current_user_id(), 'assessments' => array(), 'events' => array(), 'evidence' => array(), 'completion_records' => array() );
	foreach ( array( 'mls_attempt' => 'assessments', 'mls_event' => 'events', 'mls_evidence' => 'evidence', 'mls_certificate' => 'completion_records' ) as $type => $key ) {
		$posts = get_posts( array( 'post_type' => $type, 'post_status' => 'private', 'author' => get_current_user_id(), 'posts_per_page' => -1 ) );
		foreach ( $posts as $post ) {
			$data = json_decode( $post->post_content, true );
			if ( $type === 'mls_attempt' && is_array( $data['bank'] ?? null ) ) { $data['bank'] = mls_public_bank( $data['bank'] ); }
			if ( $type === 'mls_evidence' ) { unset( $data['storage'] ); }
			if ( $type === 'mls_certificate' ) { unset( $data['review_note'] ); }
			$records[ $key ][] = array( 'id' => $post->ID, 'title' => $post->post_title, 'recorded_at' => $post->post_date_gmt, 'course' => (int) get_post_meta( $post->ID, '_mls_course', true ), 'lesson' => (int) get_post_meta( $post->ID, '_mls_lesson', true ), 'status' => get_post_meta( $post->ID, '_mls_status', true ), 'release' => get_post_meta( $post->ID, '_mls_release', true ), 'data' => $data );
		}
	}
	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="my-learning-records.json"' );
	header( 'X-Content-Type-Options: nosniff' );
	echo wp_json_encode( $records, JSON_PRETTY_PRINT ); exit;
}
