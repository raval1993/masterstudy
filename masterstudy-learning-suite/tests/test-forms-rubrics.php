<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
define( 'MLS_TEST_RUN', true );
function fr_assert( bool $ok, string $message ): void { if ( ! $ok ) { throw new RuntimeException( $message ); } }
function fr_request( string $route, array $data ): WP_REST_Request { $request = new WP_REST_Request( 'POST', '/mls/v1/' . $route . '/9840' ); $request->set_param( 'lesson', 9840 ); $request->set_header( 'Content-Type', 'application/json' ); $request->set_body( wp_json_encode( $data ) ); return $request; }
$users = array(); $posts = array(); $files = array(); $error = null;
$schema = mls_validate_form_schema( array( array( 'name' => 'goal', 'label' => 'Learning goal', 'required' => true ), array( 'name' => 'mode', 'label' => 'Study mode', 'type' => 'select', 'options' => array( 'Online', 'Blended' ) ), array( 'name' => 'confirm', 'label' => 'I confirm my goals', 'type' => 'checkbox', 'required' => true ) ) );
$filter = function ( $value, $object, $key ) use ( &$schema, &$users ) { if ( $object === 9838 && $key === '_mls_form' ) { return array( $schema ); } if ( $object === 9838 && $key === '_mls_instructors' ) { return array( array( $users['instructor'] ?? 0 ) ); } return $value; };
$mock = null; $bank_filter = function ( $posts, $query ) use ( &$mock ) { return $mock && $query->get( 'post_type' ) === 'mls_bank' ? array( $mock ) : $posts; };
$mail_filter = fn() => true; add_filter( 'pre_wp_mail', $mail_filter ); add_filter( 'get_post_metadata', $filter, 10, 3 ); add_filter( 'posts_pre_query', $bank_filter, 10, 2 );
try {
	foreach ( array( 'learner' => 'subscriber', 'instructor' => 'stm_lms_instructor', 'outsider' => 'subscriber' ) as $key => $role ) { $id = wp_insert_user( array( 'user_login' => 'mls-fr-test-' . $key . '-' . strtolower( wp_generate_password( 10, false ) ), 'user_pass' => wp_generate_password( 40 ), 'user_email' => 'test-' . wp_generate_password( 12, false ) . '@example.invalid', 'role' => $role ) ); fr_assert( ! is_wp_error( $id ), 'Could not create isolated fixtures.' ); $users[ $key ] = $id; }
	STM_LMS_Course::add_user_course( 9838, $users['learner'], 9840 ); wp_set_current_user( $users['learner'] );
	$hash = mls_form_fingerprint( $schema ); $answers = array( 'goal' => 'Practise safe workplace communication.', 'mode' => 'Online', 'confirm' => true );
	$response = mls_form_request( fr_request( 'form', array( 'schema' => $hash, 'answers' => $answers ) ) ); fr_assert( $response instanceof WP_REST_Response, 'Valid form save failed.' ); $entry = $response->get_data()['entry']; $posts[] = $entry;
	fr_assert( ! current_user_can( 'read_post', $entry ) && ! current_user_can( 'edit_post', $entry ), 'Private learner form raw endpoints exposed.' );
	$again = mls_form_request( fr_request( 'form', array( 'schema' => $hash, 'answers' => $answers ) ) ); fr_assert( $again instanceof WP_REST_Response && $again->get_data()['entry'] === $entry, 'Duplicate form created another revision.' );
	$schema[0]['label'] = 'Updated learning goal'; $stale = mls_form_request( fr_request( 'form', array( 'schema' => $hash, 'answers' => $answers ) ) ); fr_assert( is_wp_error( $stale ) && $stale->get_error_data()['status'] === 409, 'Stale form accepted.' );
	fr_assert( mls_form_latest( $users['learner'], mls_context( 9840 ), mls_form_fingerprint( $schema ) ) === null, 'Old response silently mapped onto a new schema.' );
	fr_assert( mls_form_latest( $users['outsider'], mls_context( 9840 ), $hash ) === null, 'Another learner can retrieve the saved form.' );
	$invalid = mls_form_request( fr_request( 'form', array( 'schema' => mls_form_fingerprint( $schema ), 'answers' => array_merge( $answers, array( 'role' => 'administrator' ) ) ) ) ); fr_assert( is_wp_error( $invalid ), 'Unexpected form fields accepted.' );
	$fixture_course = wp_insert_post( array( 'post_type' => 'stm-courses', 'post_status' => 'draft', 'post_author' => $users['instructor'], 'post_title' => 'Disposable form settings regression' ) ); $posts[] = $fixture_course;
	fr_assert( is_wp_error( mls_save_form_schema( $fixture_course, $schema, mls_form_fingerprint( array() ) ) ), 'A learner changed a course form.' );
	$admin = (int) get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) )[0]; wp_set_current_user( $admin );
	fr_assert( mls_save_form_schema( $fixture_course, $schema, mls_form_fingerprint( array() ) ) === true, 'Administrator form settings save failed.' );
	$schema_conflict = mls_save_form_schema( $fixture_course, array(), mls_form_fingerprint( array() ) ); fr_assert( is_wp_error( $schema_conflict ) && $schema_conflict->get_error_data()['status'] === 409, 'Stale form editor discarded an administrator change.' );
	wp_set_current_user( $users['instructor'] ); $_GET['course'] = 9838; ob_start(); mls_forms_admin(); $form_html = ob_get_clean(); fr_assert( str_contains( $form_html, 'View saved response' ) && ! str_contains( $form_html, 'id="mls-form-builder"' ), 'Instructor form response access or administrator-only builder failed.' );
	wp_set_current_user( $users['learner'] );
	$banks = get_posts( array( 'post_type' => 'mls_bank', 'post_status' => 'publish', 'posts_per_page' => 1, 'meta_key' => '_mls_lesson', 'meta_value' => 9840 ) ); $mock = clone $banks[0];
	$mock->post_content = wp_json_encode( mls_validate_bank( array( 'questions' => array( array( 'type' => 'practical', 'prompt' => 'Demonstrate a safe workplace communication process.', 'rubric' => 'PRIVATE MARKER NOTE', 'criteria' => array( array( 'label' => 'Select and justify a secure channel.', 'max_points' => 5, 'min_points' => 3, 'evidence_required' => true ), array( 'label' => 'Explain appropriate escalation steps.', 'max_points' => 5, 'min_points' => 2 ) ) ) ) ) ) );
	$started = mls_assessment_request( fr_request( 'assessment', array() ) ); fr_assert( $started instanceof WP_REST_Response, 'Weighted attempt could not start.' ); $attempt = $started->get_data()['attempt']; $posts[] = $attempt;
	$public = $started->get_data(); fr_assert( isset( $public['bank']['questions'][0]['criteria'] ) && ! isset( $public['bank']['questions'][0]['rubric'] ), 'Public criteria or marker note privacy failed.' );
	$submitted = mls_attempt_request( fr_request( 'attempt', array( 'attempt' => $attempt, 'answers' => array( 'q0' => 'My temporary practical submission has sufficient characters.' ), 'score' => 100, 'marks' => array( 'q0_c0' => array( 'points' => 5 ) ) ) ) );
	fr_assert( $submitted instanceof WP_REST_Response && $submitted->get_data()['status'] === 'pending_review' && $submitted->get_data()['score'] === null, 'Learner marks changed the grade.' );
	$marks = array( 'q0_c0' => array( 'points' => 4, 'evidence' => array() ), 'q0_c1' => array( 'points' => 4 ) );
	wp_set_current_user( $users['outsider'] ); fr_assert( is_wp_error( mls_record_grade( 9838, $attempt, 'passed', 'Unauthorised marking attempt.', mls_submission_hash( $attempt ), $marks ) ), 'Unassigned marker accepted.' );
	wp_set_current_user( $users['instructor'] ); fr_assert( is_wp_error( mls_record_grade( 9838, $attempt, 'passed', 'Evidence is still missing.', mls_submission_hash( $attempt ), $marks ) ), 'Missing required evidence passed.' );
	$path = mls_private_dir() . '/' . bin2hex( random_bytes( 24 ) ) . '.txt'; file_put_contents( $path, 'Disposable evidence fixture; never course content.' ); chmod( $path, 0600 ); $files[] = $path;
	$evidence = wp_insert_post( array( 'post_type' => 'mls_evidence', 'post_status' => 'private', 'post_author' => $users['learner'], 'post_title' => 'Temporary rubric evidence', 'post_content' => wp_slash( wp_json_encode( array( 'storage' => basename( $path ), 'sha256' => hash_file( 'sha256', $path ) ) ) ) ) ); $posts[] = $evidence;
	foreach ( array( '_mls_course' => 9838, '_mls_attempt' => $attempt, '_mls_release' => mls_context( 9840 )['release'] ) as $key => $value ) { update_post_meta( $evidence, $key, $value ); }
	$marks['q0_c0']['evidence'] = array( $evidence ); update_post_meta( $evidence, '_mls_attempt', $attempt + 1 );
	fr_assert( is_wp_error( mls_record_grade( 9838, $attempt, 'passed', 'Cross-submission file must be rejected.', mls_submission_hash( $attempt ), $marks ) ), 'Evidence from another submission accepted.' );
	update_post_meta( $evidence, '_mls_attempt', $attempt ); file_put_contents( $path, 'Tampered bytes.' );
	fr_assert( is_wp_error( mls_record_grade( 9838, $attempt, 'passed', 'Tampered file must be rejected.', mls_submission_hash( $attempt ), $marks ) ), 'Tampered evidence accepted.' );
	file_put_contents( $path, 'Disposable evidence fixture; never course content.' );
	$stale_hash = mls_submission_hash( $attempt ); update_post_meta( $attempt, '_mls_evidence_version', $evidence );
	fr_assert( is_wp_error( mls_record_grade( 9838, $attempt, 'passed', 'Stale submission must be rejected.', $stale_hash, $marks ) ), 'Changed evidence version bypassed review lock.' );
	$result = mls_record_grade( 9838, $attempt, 'passed', 'Reviewed the response and verified both criteria against its evidence.', mls_submission_hash( $attempt ), $marks );
	fr_assert( $result === true && get_post_meta( $attempt, '_mls_status', true ) === 'passed', 'Valid instructor rubric decision failed.' );
	$mock_data = json_decode( $mock->post_content, true );
	$changes = array( 'pass_percent' => 80, 'criteria' => array( 0 => $mock_data['questions'][0]['criteria'] ) );
	$conflict = mls_save_rubric_bank( 9838, 9840, $mock->ID, str_repeat( '0', 64 ), $changes, false ); fr_assert( is_wp_error( $conflict ) && $conflict->get_error_data()['status'] === 409, 'Stale rubric editor overwrote an assessment.' );
	$new_bank = mls_save_rubric_bank( 9838, 9840, $mock->ID, hash( 'sha256', $mock->post_content ), $changes, false );
	fr_assert( is_int( $new_bank ) && get_post_status( $new_bank ) === 'draft', 'Rubric draft editor did not save a separate version.' ); $posts[] = $new_bank;
	fr_assert( get_post_meta( $new_bank, '_mls_parent_bank', true ) == $mock->ID && json_decode( get_post_field( 'post_content', $attempt ), true )['bank'] === $mock_data, 'Bank revision changed the attempt snapshot.' );
	wp_set_current_user( $users['learner'] ); $view = mls_attempt_view( get_post( $attempt ) )->get_data(); fr_assert( $view['score'] === 80 && $view['rubric_review']['marks']['q0_c0']['points'] === 4, 'Criterion feedback unavailable to learner.' );
	wp_set_current_user( 0 ); $guest = rest_do_request( fr_request( 'form', array( 'schema' => $hash, 'answers' => $answers ) ) ); fr_assert( in_array( $guest->get_status(), array( 401, 403 ), true ), 'Guest form save permitted.' );
	remove_filter( 'posts_pre_query', $bank_filter, 10 ); $mock = null;
	$context = mls_context( 9840 ); $active = mls_active_course_banks( 9838, $context['release'] ); fr_assert( is_array( $active ), 'Active bank mapping failed.' );
	$lesson_ids = array_map( fn( $bank ) => (int) get_post_meta( $bank->ID, '_mls_lesson', true ), $active ); fr_assert( count( array_unique( $lesson_ids ) ) === count( $lesson_ids ), 'Historical bank versions became extra certificate requirements.' );
	$latest = mls_bank( $context ); $selected = array_values( array_filter( $active, fn( $bank ) => (int) get_post_meta( $bank->ID, '_mls_lesson', true ) === 9840 ) );
	fr_assert( count( $selected ) === 1 && $selected[0]->ID === $latest->ID, 'Certificate checks and assessment start select different bank versions.' );
} catch ( Throwable $caught ) { $error = $caught; }
finally {
	remove_filter( 'pre_wp_mail', $mail_filter ); remove_filter( 'get_post_metadata', $filter, 10 ); remove_filter( 'posts_pre_query', $bank_filter, 10 );
	foreach ( $posts as $id ) { wp_delete_post( $id, true ); } foreach ( $files as $path ) { if ( is_file( $path ) && ! is_link( $path ) ) { unlink( $path ); } }
	if ( isset( $fixture_course ) ) { foreach ( get_posts( array( 'post_type' => 'mls_event', 'post_status' => 'private', 'meta_key' => '_mls_course', 'meta_value' => $fixture_course, 'posts_per_page' => -1, 'fields' => 'ids' ) ) as $id ) { wp_delete_post( $id, true ); } }
	foreach ( $users as $user ) { foreach ( get_posts( array( 'post_type' => array( 'mls_event', 'mls_mail' ), 'post_status' => 'private', 'author' => $user, 'posts_per_page' => -1, 'fields' => 'ids' ) ) as $id ) { wp_delete_post( $id, true ); } stm_lms_get_delete_user_courses( $user ); require_once ABSPATH . 'wp-admin/includes/user.php'; wp_delete_user( $user ); }
	wp_set_current_user( 0 );
}
if ( $error ) { WP_CLI::error( $error->getMessage() ); }
echo "PASS: typed form persistence/idempotency, schema conflict and privacy, guest denial, pending rubric review, forged marks rejection, criterion thresholds, evidence ownership/integrity, stale review locks and learner criterion feedback. Fixtures removed.\n";
