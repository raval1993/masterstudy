<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
define( 'MLS_TEST_RUN', true );
function mls_assert( bool $result, string $message ): void { if ( ! $result ) { throw new RuntimeException( $message ); } }
function mls_test_request( string $route, array $data = array() ): WP_REST_Request {
	$request = new WP_REST_Request( 'POST', '/mls/v1/' . $route . '/9840' );
	$request->set_param( 'lesson', 9840 ); $request->set_header( 'Content-Type', 'application/json' ); $request->set_body( wp_json_encode( $data ) ); return $request;
}
$admin = wp_insert_user( array( 'user_login' => 'mls-instructor-test-' . strtolower( wp_generate_password( 12, false ) ), 'user_pass' => wp_generate_password( 40 ), 'role' => 'stm_lms_instructor' ) );
if ( is_wp_error( $admin ) ) { WP_CLI::error( 'Cannot create isolated instructor fixture.' ); }
$team_filter = function ( $value, $object, $key ) use ( $admin ) { return $object === 9838 && $key === '_mls_instructors' ? array( array( $admin ) ) : $value; };
add_filter( 'get_post_metadata', $team_filter, 10, 3 );
$attempts = array(); $user = 0; $error = null;
$rate_key = '_mls_rate_' . $admin . '_attempt';
$rate_exists = metadata_exists( 'user', $admin, $rate_key ); $rate = get_user_meta( $admin, $rate_key, true );
$banks = get_posts( array( 'post_type' => 'mls_bank', 'post_status' => 'draft', 'posts_per_page' => 1, 'meta_key' => '_mls_lesson', 'meta_value' => 9840 ) );
$mock = clone $banks[0];
$mock->post_content = wp_json_encode( mls_validate_bank( array( 'minutes' => 1, 'questions' => array( array( 'type' => 'mcq', 'prompt' => 'Which answer is the correct test choice?', 'options' => array( 'Incorrect "quoted" option', 'Correct option' ), 'correct' => 1, 'explanation' => 'A quoted "answer" remains valid JSON.' ) ) ) ) );
// Supply a bank only inside this CLI process; never publish test questions.
$filter = function ( $posts, $query ) use ( $mock ) { return $query->get( 'post_type' ) === 'mls_bank' ? array( $mock ) : $posts; };
add_filter( 'posts_pre_query', $filter, 10, 2 );
try {
	wp_set_current_user( $admin ); delete_user_meta( $admin, $rate_key );
	$start = mls_assessment_request( mls_test_request( 'assessment' ) );
	mls_assert( $start instanceof WP_REST_Response, 'Could not start attempt.' );
	$data = $start->get_data(); $attempts[] = $data['attempt'];
	mls_assert( ! isset( $data['bank']['questions'][0]['correct'] ), 'Answer key exposed.' );
	$again = mls_assessment_request( mls_test_request( 'assessment' ) )->get_data();
	mls_assert( $again['attempt'] === $data['attempt'], 'Duplicate active attempts created.' );
	$submitted = mls_attempt_request( mls_test_request( 'attempt', array( 'attempt' => $data['attempt'], 'answers' => array( 'q0' => 0 ), 'score' => 100 ) ) )->get_data();
	mls_assert( $submitted['score'] === 0 && $submitted['status'] === 'revision_required', 'Forged client score accepted.' );
	$duplicate = mls_attempt_request( mls_test_request( 'attempt', array( 'attempt' => $data['attempt'], 'answers' => array( 'q0' => 1 ) ) ) );
	mls_assert( is_wp_error( $duplicate ) && $duplicate->get_error_data()['status'] === 409, 'Duplicate submit accepted.' );
	mls_assert( json_decode( get_post_field( 'post_content', $data['attempt'] ), true )['bank']['questions'][0]['options'][0] === 'Incorrect "quoted" option', 'JSON quotes damaged.' );
	update_post_meta( $data['attempt'], '_mls_status', 'pending_review' );
	$pending = mls_assessment_request( mls_test_request( 'assessment' ) )->get_data();
	mls_assert( $pending['attempt'] === $data['attempt'] && $pending['status'] === 'pending_review', 'Pending review was replaced by a new attempt.' );
	$review_hash = mls_submission_hash( $data['attempt'] ); update_post_meta( $data['attempt'], '_mls_evidence_version', 'temporary-test-version' );
	$stale = mls_record_grade( 9838, $data['attempt'], 'revision_required', 'Reviewed temporary test submission.', $review_hash );
	mls_assert( is_wp_error( $stale ) && $stale->get_error_data()['status'] === 409, 'Changed evidence was marked from a stale review.' );
	$decision = mls_record_grade( 9838, $data['attempt'], 'revision_required', 'Reviewed temporary test submission.', mls_submission_hash( $data['attempt'] ) );
	mls_assert( $decision === true && get_post_meta( $data['attempt'], '_mls_status', true ) === 'revision_required', 'Current instructor review failed.' );
	delete_user_meta( $admin, $rate_key );
	$timed = mls_assessment_request( mls_test_request( 'assessment' ) )->get_data(); $attempts[] = $timed['attempt'];
	$stored = json_decode( get_post_field( 'post_content', $timed['attempt'] ), true ); $stored['started_at'] = time() - 120;
	wp_update_post( array( 'ID' => $timed['attempt'], 'post_content' => wp_slash( wp_json_encode( $stored ) ) ) );
	$expired = mls_attempt_request( mls_test_request( 'attempt', array( 'attempt' => $timed['attempt'], 'answers' => array( 'q0' => 1 ) ) ) );
	mls_assert( is_wp_error( $expired ) && get_post_meta( $timed['attempt'], '_mls_status', true ) === 'expired', 'Deadline not enforced.' );
	$user = wp_insert_user( array( 'user_login' => 'mls-test-' . wp_generate_password( 12, false ), 'user_pass' => wp_generate_password( 40 ), 'role' => 'subscriber' ) );
	if ( is_wp_error( $user ) ) { throw new RuntimeException( 'Cannot create temporary permission test user.' ); }
	wp_set_current_user( $user );
	$private = mls_attempt_request( mls_test_request( 'attempt', array( 'attempt' => $data['attempt'] ) ) );
	mls_assert( is_wp_error( $private ) && $private->get_error_data()['status'] === 403, 'Another learner can read an attempt.' );
	$rules = function ( $value, $object, $key ) { return $object === 9838 && $key === '_mls_rules' ? array( array( 'drip' => array( 9840 => 10 ) ) ) : $value; };
	add_filter( 'get_post_metadata', $rules, 10, 3 );
	try { mls_assert( mls_gate_reason( 9838, 9840 ) !== '', 'Drip gate did not lock an unenrolled learner.' ); }
	finally { remove_filter( 'get_post_metadata', $rules, 10 ); }
	wp_set_current_user( 0 );
	$response = rest_do_request( new WP_REST_Request( 'GET', '/mls/v1/assessment/9840' ) );
	mls_assert( $response->get_status() === 401 || $response->get_status() === 403, 'Guest REST permission failed.' );
} catch ( Throwable $caught ) { $error = $caught; }
finally {
	remove_filter( 'posts_pre_query', $filter, 10 );
	foreach ( $attempts as $attempt ) { wp_delete_post( $attempt, true ); }
	$events = get_posts( array( 'post_type' => 'mls_event', 'post_status' => 'private', 'author' => $admin, 'posts_per_page' => 30 ) );
	foreach ( $events as $event ) { $data = json_decode( $event->post_content, true ); if ( in_array( $data['attempt'] ?? 0, $attempts, true ) ) { wp_delete_post( $event->ID, true ); } }
	if ( is_int( $user ) && $user ) { require_once ABSPATH . 'wp-admin/includes/user.php'; wp_delete_user( $user ); }
	if ( $rate_exists ) { update_user_meta( $admin, $rate_key, $rate ); } else { delete_user_meta( $admin, $rate_key ); }
	remove_filter( 'get_post_metadata', $team_filter, 10 ); require_once ABSPATH . 'wp-admin/includes/user.php'; wp_delete_user( $admin );
	wp_set_current_user( 0 );
}
if ( $error ) { WP_CLI::error( $error->getMessage() ); }
echo "PASS: private attempts, answer-key protection, start/submit idempotency, scoring, JSON integrity, expiry, drip and guest access. Test records removed.\n";
