<?php
defined( 'ABSPATH' ) || exit;

function mls_form_request( WP_REST_Request $request ) {
	$context = mls_context( absint( $request['lesson'] ) );
	$input = $request->get_json_params();
	if ( ! $context || ! mls_access( $context ) || ! is_array( $input ) || strlen( $request->get_body() ) > 20000 ) { return mls_error( 'Form unavailable.', 403 ); }
	$schema = get_post_meta( $context['course_id'], '_mls_form', true );
	if ( ! is_array( $schema ) || ! $schema ) { return mls_error( 'No learner form configured.', 404 ); }
	global $wpdb; $lock = 'mls-form-schema-' . $context['course_id'];
	if ( (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s,2)', $lock ) ) !== 1 ) { return mls_error( 'Please retry shortly.', 409 ); }
	try {
		clean_post_cache( $context['course_id'] ); $schema = mls_validate_form_schema( (array) get_post_meta( $context['course_id'], '_mls_form', true ) );
		$hash = mls_form_fingerprint( $schema );
		if ( ! $schema || ! is_string( $input['schema'] ?? null ) || ! hash_equals( $hash, $input['schema'] ) ) { return mls_error( 'The form changed. Reload the lesson before saving.', 409 ); }
		if ( ! is_array( $input['answers'] ?? null ) ) { return mls_error( 'Invalid form answers.' ); }
		$answers = mls_validate_form_answers( $schema, $input['answers'] );
		$latest = mls_form_latest( get_current_user_id(), $context, $hash );
		if ( $latest && ( json_decode( $latest->post_content, true )['answers'] ?? null ) === $answers ) { return mls_private_response( array( 'saved' => true, 'entry' => $latest->ID ) ); }
		if ( ! mls_rate( 'form', 5 ) ) { return mls_error( 'Wait a few seconds before saving again.', 429 ); }
		$id = wp_insert_post( array( 'post_type' => 'mls_form_entry', 'post_status' => 'private', 'post_author' => get_current_user_id(), 'post_title' => get_the_title( $context['course_id'] ), 'post_content' => wp_slash( wp_json_encode( array( 'schema' => $schema, 'answers' => $answers, 'updated_at' => time() ) ) ) ), true );
		if ( is_wp_error( $id ) ) { return $id; }
		foreach ( array( '_mls_course' => $context['course_id'], '_mls_release' => $context['release'], '_mls_form_hash' => $hash ) as $key => $value ) { update_post_meta( $id, $key, $value ); }
		mls_event( $context['course_id'], $context['lesson_id'], 'learner_form_saved', array( 'entry' => $id, 'schema' => $hash ) );
		return mls_private_response( array( 'saved' => true, 'entry' => $id ), 201 );
	} catch ( InvalidArgumentException $error ) { return mls_error( $error->getMessage() ); }
	finally { $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) ); }
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
	$records = array( 'exported_at' => gmdate( 'c' ), 'learner_id' => get_current_user_id(), 'assessments' => array(), 'events' => array(), 'evidence' => array(), 'completion_records' => array(), 'learner_forms' => array() );
	foreach ( array( 'mls_attempt' => 'assessments', 'mls_event' => 'events', 'mls_evidence' => 'evidence', 'mls_certificate' => 'completion_records', 'mls_form_entry' => 'learner_forms' ) as $type => $key ) {
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
