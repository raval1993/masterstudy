<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
define( 'MLS_TEST_RUN', true );
function ops_assert( bool $ok, string $message ): void { if ( ! $ok ) { throw new RuntimeException( $message ); } }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
$admin = (int) $admins[0]; $users = array(); $posts = array(); $error = null;
$mail_filter = fn() => true; add_filter( 'pre_wp_mail', $mail_filter );
try {
	foreach ( array( 'manager', 'learner', 'outsider' ) as $role ) { $id = wp_insert_user( array( 'user_login' => 'mls-ops-test-' . $role . '-' . strtolower( wp_generate_password( 10, false ) ), 'user_pass' => wp_generate_password( 40 ), 'user_email' => 'test-' . wp_generate_password( 12, false ) . '@example.invalid', 'role' => 'subscriber' ) ); ops_assert( ! is_wp_error( $id ), 'Fixture creation failed.' ); $users[ $role ] = $id; }
	wp_set_current_user( $admin );
	$group = mls_save_group( 0, 'Temporary regression cohort', array( 'courses' => array( 9838 ), 'managers' => array( $users['manager'] ), 'members' => array(), 'eligible_members' => array( $users['learner'], $users['outsider'] ), 'seats' => 1 ) );
	ops_assert( ! is_wp_error( $group ), 'Could not create cohort.' ); $posts[] = $group;
	wp_set_current_user( $users['outsider'] );
	ops_assert( ! mls_group_manage( $group ) && is_wp_error( mls_group_add_members( $group, array( $users['learner'] ) ) ), 'Unrelated user can manage group.' );
	ops_assert( is_wp_error( mls_save_group( $group, 'Privilege escalation', array() ) ), 'Manager configuration escalated.' );
	wp_set_current_user( $users['manager'] );
	ops_assert( is_wp_error( mls_group_add_members( $group, array( $admin ) ) ), 'Manager can enrol users outside the approved roster.' );
	ops_assert( ! mls_manage( 9838 ), 'Cohort manager can grade or read evidence.' );
	$result = mls_group_add_members( $group, array( $users['learner'] ) ); ops_assert( ! is_wp_error( $result ), 'Manager enrolment failed.' );
	ops_assert( str_contains( mls_cohort_portal(), 'Temporary regression cohort' ), 'Assigned manager cannot open the cohort portal.' );
	$first = STM_LMS_Course::get_user_course( $users['learner'], 9838 ); ops_assert( ! empty( $first ), 'Native enrolment missing.' );
	$result = mls_group_add_members( $group, array( $users['learner'] ) ); $second = STM_LMS_Course::get_user_course( $users['learner'], 9838 );
	ops_assert( ! is_wp_error( $result ) && $first['start_time'] === $second['start_time'] && $first['progress_percent'] === $second['progress_percent'], 'Duplicate enrolment reset learner progress.' );
	ops_assert( is_wp_error( mls_group_add_members( $group, array( $users['outsider'] ) ) ), 'Seat allowance bypassed.' );
	$evidence = wp_insert_post( array( 'post_type' => 'mls_evidence', 'post_status' => 'private', 'post_author' => $users['learner'], 'post_title' => 'Private test file' ) ); $posts[] = $evidence; update_post_meta( $evidence, '_mls_course', 9838 );
	ops_assert( ! mls_evidence_allowed( get_post( $evidence ) ), 'Manager can access learner evidence.' );
	wp_set_current_user( $users['learner'] ); ops_assert( mls_evidence_allowed( get_post( $evidence ) ), 'Evidence owner denied.' );
	ops_assert( ! current_user_can( 'edit_post', $evidence ) && ! current_user_can( 'delete_post', $evidence ), 'Native WordPress endpoints can bypass the private evidence workflow.' );
	parse_str( wp_parse_url( mls_evidence_url( $evidence ), PHP_URL_QUERY ), $download ); ops_assert( (int) ( $download['id'] ?? 0 ) === $evidence && isset( $download['_wpnonce'] ), 'Evidence API URL contains HTML entities instead of query parameters.' );
	wp_set_current_user( $users['outsider'] ); ops_assert( ! mls_evidence_allowed( get_post( $evidence ) ), 'Another learner can read evidence.' );
	wp_set_current_user( $admin ); ops_assert( mls_evidence_allowed( get_post( $evidence ) ), 'Administrator cannot review evidence.' );
	ops_assert( is_dir( mls_private_dir() ), 'Private storage unavailable.' );
	$context = mls_context( 9840 ); $draft = wp_insert_post( array( 'post_type' => 'mls_draft', 'post_status' => 'private', 'post_title' => 'enrichment: temporary test', 'post_content' => 'Temporary test draft; never published.' ) ); $posts[] = $draft;
	foreach ( array( '_mls_course' => 9838, '_mls_lesson' => 9840, '_mls_release' => $context['release'] ) as $key => $value ) { update_post_meta( $draft, $key, $value ); }
	$before = get_post_field( 'post_content', 9840 ); $conflict = mls_apply_review( 9838, 'apply_draft', $draft, str_repeat( '0', 64 ), str_repeat( 'Reviewed teaching example. ', 10 ) );
	ops_assert( is_wp_error( $conflict ) && $conflict->get_error_data()['status'] === 409 && get_post_field( 'post_content', 9840 ) === $before, 'Stale review overwrote a live lesson.' );
	ops_assert( is_wp_error( mls_issue_certificate( 9838, $users['learner'], 'This learner has not finished the native course.' ) ), 'Incomplete learner received certificate.' );
	ops_assert( mls_csv_cell( '=HYPERLINK("https://invalid")' )[0] === "'" && mls_csv_cell( "\t@SUM(1)" )[0] === "'", 'Spreadsheet formula injection not neutralized.' );
	wp_set_current_user( 0 ); $request = new WP_REST_Request( 'POST', '/mls/v1/evidence/9840' ); $response = rest_do_request( $request ); ops_assert( in_array( $response->get_status(), array( 401, 403 ), true ), 'Guest evidence route exposed.' );
} catch ( Throwable $caught ) { $error = $caught; }
finally {
	remove_filter( 'pre_wp_mail', $mail_filter );
	foreach ( $posts as $post ) { wp_delete_post( $post, true ); }
	$events = get_posts( array( 'post_type' => 'mls_event', 'post_status' => 'private', 'posts_per_page' => 100 ) );
	foreach ( $events as $event ) { $data = json_decode( $event->post_content, true ); if ( in_array( $data['group'] ?? 0, $posts, true ) ) { wp_delete_post( $event->ID, true ); } }
	require_once ABSPATH . 'wp-admin/includes/user.php'; foreach ( $users as $user ) { stm_lms_get_delete_user_courses( $user ); wp_delete_user( $user ); }
	wp_set_current_user( 0 );
}
if ( $error ) { WP_CLI::error( $error->getMessage() ); }
echo "PASS: cohort isolation, native idempotent enrolment, seat limits, owner/instructor evidence access, guest denial, private storage, review conflicts, certificate eligibility and CSV safety. Fixtures removed.\n";
