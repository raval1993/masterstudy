<?php
defined( 'ABSPATH' ) || exit;

function mls_submission_hash( int $id ): string { return hash( 'sha256', get_post_field( 'post_content', $id ) . ':' . get_post_meta( $id, '_mls_evidence_version', true ) ); }

function mls_record_grade( int $course, int $id, string $status, string $feedback, string $expected, array $marks = array() ) {
	if ( ! mls_manage( $course ) || get_post_type( $id ) !== 'mls_attempt' || (int) get_post_meta( $id, '_mls_course', true ) !== $course || ! in_array( $status, array( 'passed', 'revision_required' ), true ) ) { return mls_error( 'Invalid assessment decision.', 403 ); }
	global $wpdb; $lock = 'mls-submit-' . $id;
	if ( (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s,2)', $lock ) ) !== 1 ) { return mls_error( 'Please retry shortly.', 409 ); }
	try {
		clean_post_cache( $id );
		if ( ! hash_equals( mls_submission_hash( $id ), $expected ) ) { return mls_error( 'Submission or evidence changed. Reload and review the latest evidence first.', 409 ); }
		$context = mls_context( (int) get_post_meta( $id, '_mls_lesson', true ) );
		if ( ! $context || $context['release'] !== get_post_meta( $id, '_mls_release', true ) || ! in_array( get_post_meta( $id, '_mls_status', true ), array( 'pending_review', 'passed', 'revision_required' ), true ) ) { return mls_error( 'This attempt is not reviewable for the current release.', 409 ); }
		$feedback = sanitize_textarea_field( $feedback ); if ( ! trim( $feedback ) || strlen( $feedback ) > 10000 ) { return mls_error( 'Provide written feedback, up to 10000 characters.' ); }
		$content = json_decode( get_post_field( 'post_content', $id ), true );
		if ( mls_has_rubric( $content['bank'] ) ) {
			try { $review = mls_rubric_result( $content['bank'], $content['answers'], $marks ); }
			catch ( InvalidArgumentException $error ) { return mls_error( $error->getMessage() ); }
			if ( ! mls_rubric_evidence_check( $id, $review['marks'] ) ) { return mls_error( 'Evidence is missing, changed or belongs to another submission.', 409 ); }
			if ( $status === 'passed' && $review['status'] !== 'passed' ) { return mls_error( 'A pass requires the weighted threshold, every criterion minimum and required evidence.' ); }
			$content['rubric_review'] = $review; $content['score'] = $review['score'];
		} elseif ( $marks ) { return mls_error( 'This submission has no weighted rubric.' ); }
		$content['feedback'] = $feedback; $content['reviewed_by'] = get_current_user_id(); $content['reviewed_at'] = time();
		$result = wp_update_post( array( 'ID' => $id, 'post_content' => wp_slash( wp_json_encode( $content ) ) ), true ); if ( is_wp_error( $result ) ) { return $result; }
		update_post_meta( $id, '_mls_status', $status ); mls_event( $course, $context['lesson_id'], 'instructor_decision', array( 'attempt' => $id, 'status' => $status, 'feedback' => $feedback ) ); return true;
	} finally { $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) ); }
}

function mls_bank( array $context ): ?WP_Post {
	$posts = get_posts( array( 'post_type' => 'mls_bank', 'post_status' => 'publish', 'posts_per_page' => 1, 'meta_query' => array(
		array( 'key' => '_mls_lesson', 'value' => $context['lesson_id'] ), array( 'key' => '_mls_release', 'value' => $context['release'] ),
	), 'orderby' => 'ID', 'order' => 'DESC' ) );
	return $posts[0] ?? null;
}

function mls_assessment_request( WP_REST_Request $request ) {
	$context = mls_context( absint( $request['lesson'] ) );
	if ( ! $context || ! mls_access( $context ) ) { return mls_error( 'Assessment unavailable.', 403 ); }
	$bank_post = mls_bank( $context );
	if ( ! $bank_post ) { return mls_error( 'No reviewed assessment is available yet.', 404 ); }
	$bank = json_decode( $bank_post->post_content, true );
	if ( $request->get_method() === 'GET' ) { return mls_private_response( array( 'title' => $bank_post->post_title, 'bank' => mls_public_bank( $bank ) ) ); }
	global $wpdb;
	$lock = 'mls-start-' . get_current_user_id() . '-' . $context['lesson_id'];
	if ( (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 2)', $lock ) ) !== 1 ) { return mls_error( 'Please retry shortly.', 409 ); }
	try {
		$open = get_posts( array( 'post_type' => 'mls_attempt', 'post_status' => 'private', 'author' => get_current_user_id(), 'posts_per_page' => 1, 'meta_query' => array( array( 'key' => '_mls_lesson', 'value' => $context['lesson_id'] ), array( 'key' => '_mls_status', 'value' => array( 'in_progress', 'pending_review' ), 'compare' => 'IN' ) ) ) );
		if ( $open ) {
			$old = json_decode( $open[0]->post_content, true );
			$expired = get_post_meta( $open[0]->ID, '_mls_status', true ) === 'in_progress' && $old['bank']['minutes'] && time() > $old['started_at'] + $old['bank']['minutes'] * 60;
			if ( ! $expired && get_post_meta( $open[0]->ID, '_mls_release', true ) === $context['release'] ) { return mls_attempt_view( $open[0] ); }
			update_post_meta( $open[0]->ID, '_mls_status', $expired ? 'expired' : 'retired_release' );
		}
		if ( ! mls_rate( 'attempt', 10 ) ) { return mls_error( 'Please wait before starting another attempt.', 429 ); }
		if ( $bank['randomize'] ) { shuffle( $bank['questions'] ); }
		$id = wp_insert_post( array( 'post_type' => 'mls_attempt', 'post_status' => 'private', 'post_author' => get_current_user_id(), 'post_title' => get_the_title( $context['lesson_id'] ), 'post_content' => wp_slash( wp_json_encode( array( 'bank' => $bank, 'started_at' => time(), 'answers' => array() ) ) ) ), true );
		if ( is_wp_error( $id ) ) { return $id; }
		foreach ( array( '_mls_course' => $context['course_id'], '_mls_lesson' => $context['lesson_id'], '_mls_release' => $context['release'], '_mls_status' => 'in_progress', '_mls_bank' => $bank_post->ID ) as $key => $value ) { update_post_meta( $id, $key, $value ); }
		mls_event( $context['course_id'], $context['lesson_id'], 'assessment_started', array( 'attempt' => $id ) );
		return mls_attempt_view( get_post( $id ) );
	} finally { $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) ); }
}

function mls_attempt_view( WP_Post $post ) {
	$data = json_decode( $post->post_content, true );
	$status = get_post_meta( $post->ID, '_mls_status', true );
	$return = array( 'attempt' => $post->ID, 'status' => $status, 'started_at' => $data['started_at'], 'expires_at' => $data['bank']['minutes'] ? $data['started_at'] + $data['bank']['minutes'] * 60 : null, 'bank' => mls_public_bank( $data['bank'] ), 'answers' => $data['answers'] ?? array(), 'score' => $data['score'] ?? null, 'feedback' => $data['feedback'] ?? '' );
	$return['evidence'] = array();
	if ( isset( $data['rubric_review'] ) ) { $return['rubric_review'] = $data['rubric_review']; }
	foreach ( get_posts( array( 'post_type' => 'mls_evidence', 'post_status' => 'private', 'posts_per_page' => 5, 'meta_key' => '_mls_attempt', 'meta_value' => $post->ID ) ) as $item ) { if ( mls_evidence_allowed( $item ) ) { $return['evidence'][] = array( 'id' => $item->ID, 'name' => $item->post_title, 'url' => mls_evidence_url( $item->ID ) ); } }
	if ( $status !== 'in_progress' ) { $return['suggested_answers'] = array_column( $data['bank']['questions'], 'explanation', 'id' ); }
	return mls_private_response( $return );
}

function mls_attempt_request( WP_REST_Request $request ) {
	$input = $request->get_method() === 'GET' ? $request->get_params() : $request->get_json_params();
	if ( ! is_array( $input ) || strlen( $request->get_body() ) > 100000 ) { return mls_error( 'Invalid assessment data.' ); }
	$post = get_post( absint( $input['attempt'] ?? 0 ) );
	$context = mls_context( absint( $request['lesson'] ) );
	if ( ! $context || ! mls_access( $context ) || ! $post || $post->post_type !== 'mls_attempt' || (int) $post->post_author !== get_current_user_id() || (int) get_post_meta( $post->ID, '_mls_lesson', true ) !== $context['lesson_id'] || get_post_meta( $post->ID, '_mls_release', true ) !== $context['release'] ) { return mls_error( 'Attempt unavailable.', 403 ); }
	if ( $request->get_method() === 'GET' ) { return mls_attempt_view( $post ); }
	global $wpdb;
	$lock = 'mls-submit-' . $post->ID;
	if ( (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 2)', $lock ) ) !== 1 ) { return mls_error( 'Please retry shortly.', 409 ); }
	try {
		clean_post_cache( $post->ID );
		if ( get_post_meta( $post->ID, '_mls_status', true ) !== 'in_progress' ) { return mls_error( 'This attempt has already been submitted.', 409 ); }
		$data = json_decode( get_post_field( 'post_content', $post->ID ), true );
		if ( $data['bank']['minutes'] && time() > $data['started_at'] + $data['bank']['minutes'] * 60 ) {
			update_post_meta( $post->ID, '_mls_status', 'expired' ); return mls_error( 'The attempt time limit has expired.', 409 );
		}
		$answers = $input['answers'] ?? array();
		if ( ! is_array( $answers ) ) { return mls_error( 'Invalid answers.' ); }
		$clean = array();
		foreach ( $data['bank']['questions'] as $question ) {
			$value = $answers[ $question['id'] ] ?? null;
			if ( $question['type'] === 'mcq' ) {
				if ( ! is_int( $value ) || $value < 0 || $value >= count( $question['options'] ) ) { return mls_error( 'Answer each question.' ); }
				$clean[ $question['id'] ] = $value;
			} else {
				if ( ! is_string( $value ) || strlen( trim( $value ) ) < 10 || strlen( $value ) > 10000 ) { return mls_error( 'Written answers must contain 10-10000 characters.' ); }
				$clean[ $question['id'] ] = sanitize_textarea_field( $value );
			}
		}
		$grade = mls_grade( $data['bank'], $clean );
		$evidence = get_posts( array( 'post_type' => 'mls_evidence', 'post_status' => 'private', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_mls_attempt', 'meta_value' => $post->ID ) );
		if ( $evidence ) { $grade['status'] = 'pending_review'; }
		$data['answers'] = $clean; $data['submitted_at'] = time(); $data['score'] = $grade['score'];
		$result = wp_update_post( array( 'ID' => $post->ID, 'post_content' => wp_slash( wp_json_encode( $data ) ) ), true );
		if ( is_wp_error( $result ) ) { return $result; }
		update_post_meta( $post->ID, '_mls_status', $grade['status'] );
		mls_event( $context['course_id'], $context['lesson_id'], 'assessment_submitted', array( 'attempt' => $post->ID, 'status' => $grade['status'] ) );
		return mls_attempt_view( get_post( $post->ID ) );
	} finally { $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) ); }
}
