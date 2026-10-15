<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
$action = $args[0] ?? 'create';
$key = '_mls_browser_fixture';
if ( $action === 'cleanup' ) {
	$users = get_users( array( 'meta_key' => $key, 'meta_value' => '1', 'fields' => 'ID' ) );
	require_once ABSPATH . 'wp-admin/includes/user.php';
	foreach ( $users as $id ) {
		$user = get_userdata( $id );
		if ( ! $user || $user->roles !== array( 'subscriber' ) || strpos( $user->user_login, 'mls-browser-test-' ) !== 0 ) { continue; }
		stm_lms_get_delete_user_courses( $id );
		foreach ( get_posts( array( 'post_type' => 'mls_evidence', 'post_status' => 'private', 'author' => $id, 'posts_per_page' => -1 ) ) as $evidence ) {
			$data = json_decode( $evidence->post_content, true );
			if ( preg_match( '/^[a-f0-9]{48}\.(pdf|png|jpe?g|txt)$/', $data['storage'] ?? '' ) ) { $path = mls_private_dir() . '/' . $data['storage']; if ( is_file( $path ) && ! is_link( $path ) ) { unlink( $path ); } }
			wp_delete_post( $evidence->ID, true );
		}
		foreach ( get_posts( array( 'post_type' => array( 'mls_attempt', 'mls_event' ), 'post_status' => 'private', 'author' => $id, 'posts_per_page' => -1, 'fields' => 'ids' ) ) as $post ) { wp_delete_post( $post, true ); }
		wp_delete_user( $id );
	}
	echo "Temporary browser test accounts and their course records removed.\n"; return;
}
if ( $action !== 'create' ) { WP_CLI::error( 'Use create or cleanup.' ); }
$login = 'mls-browser-test-' . strtolower( wp_generate_password( 10, false ) );
$password = wp_generate_password( 32, false );
$id = wp_insert_user( array( 'user_login' => $login, 'user_pass' => $password, 'user_email' => $login . '@example.invalid', 'display_name' => 'Learning Suite Test', 'role' => 'subscriber' ) );
if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
update_user_meta( $id, $key, '1' );
STM_LMS_Course::add_user_course( 9838, $id, 9840 );
echo wp_json_encode( array( 'login' => $login, 'password' => $password, 'user' => $id ) ) . "\n";
