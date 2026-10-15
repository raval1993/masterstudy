<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( 1 );
}
$context = mce_context_for_lesson( 9840 );
$users = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
if ( ! $context || ! $users ) {
	WP_CLI::error( 'Pilot or test account unavailable.' );
}
$user = (int) $users[0];
$key = '_mce_study_9840_' . substr( hash( 'sha256', $context['release'] ), 0, 12 );
if ( metadata_exists( 'user', $user, $key ) ) {
	WP_CLI::error( 'Refusing to touch an existing learner record.' );
}
wp_set_current_user( $user );
try {
	$request = new WP_REST_Request( 'POST', '/mce/v1/study/9840' );
	$request->set_param( 'lesson_id', 9840 );
	$request->set_header( 'Content-Type', 'application/json' );
	$request->set_body( wp_json_encode( array( 'notes' => 'Temporary deployment storage check', 'log' => 'Fictional training exercise', 'checks' => array( 0 ), 'acknowledged' => true ) ) );
	$result = mce_study_record( $request );
	if ( is_wp_error( $result ) || empty( $result->get_data()['acknowledged_at'] ) ) {
		throw new RuntimeException( 'Study record save failed.' );
	}
	$request = new WP_REST_Request( 'GET', '/mce/v1/study/9840' );
	$request->set_param( 'lesson_id', 9840 );
	$result = mce_study_record( $request );
	if ( is_wp_error( $result ) || 'Temporary deployment storage check' !== ( $result->get_data()['notes'] ?? '' ) ) {
		throw new RuntimeException( 'Saved record did not survive rereading.' );
	}
	echo "PASS: live save, reread and acknowledgement timestamp.\n";
} finally {
	delete_user_meta( $user, $key );
	wp_set_current_user( 0 );
}
if ( metadata_exists( 'user', $user, $key ) ) {
	WP_CLI::error( 'Temporary test record was not removed.' );
}
echo "PASS: temporary study record removed.\n";
