<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
$users = get_users( array( 'meta_key' => '_mls_browser_fixture', 'meta_value' => '1', 'number' => 1 ) );
if ( ! $users || $users[0]->roles !== array( 'subscriber' ) ) { WP_CLI::error( 'A disposable enrolled browser fixture is required.' ); }
$user = $users[0]; $manager = WP_Session_Tokens::get_instance( $user->ID ); $expiry = time() + 120; $token = $manager->create( $expiry );
try {
	$url = get_post_meta( 9840, 'lesson_ext_link_url', true ); $cookie = wp_generate_auth_cookie( $user->ID, $expiry, 'logged_in', $token );
	$response = wp_remote_get( $url, array( 'timeout' => 30, 'redirection' => 0, 'headers' => array( 'Range' => 'bytes=0-1023', 'Cookie' => LOGGED_IN_COOKIE . '=' . $cookie ), 'limit_response_size' => 1024 ) );
	if ( is_wp_error( $response ) ) { throw new RuntimeException( $response->get_error_message() ); }
	if ( wp_remote_retrieve_response_code( $response ) !== 206 || strlen( wp_remote_retrieve_body( $response ) ) !== 1024 || ! str_starts_with( wp_remote_retrieve_header( $response, 'content-range' ), 'bytes 0-1023/' ) || wp_remote_retrieve_header( $response, 'x-sendfile' ) ) { throw new RuntimeException( 'Authenticated byte-range media request failed: HTTP ' . wp_remote_retrieve_response_code( $response ) ); }
	$guest = wp_remote_get( $url, array( 'timeout' => 30, 'redirection' => 0, 'headers' => array( 'Range' => 'bytes=0-1023' ), 'limit_response_size' => 1024 ) );
	$expected = get_post_meta( 9840, 'preview', true ) === 'on' ? 206 : 403;
	if ( is_wp_error( $guest ) || wp_remote_retrieve_response_code( $guest ) !== $expected ) { throw new RuntimeException( 'Guest/preview media authorization did not match lesson access.' ); }
	$private_url = get_post_meta( 9841, 'lesson_ext_link_url', true );
	$private = wp_remote_get( $private_url, array( 'timeout' => 30, 'redirection' => 0, 'headers' => array( 'Range' => 'bytes=0-1023' ), 'limit_response_size' => 1024 ) );
	if ( get_post_meta( 9841, 'preview', true ) === 'on' || is_wp_error( $private ) || wp_remote_retrieve_response_code( $private ) !== 403 ) { throw new RuntimeException( 'Direct non-preview video request was not denied to guests.' ); }
	echo "PASS: trusted HTTPS, enrolled learner cookie, HTTP 206 seek/range delivery (1024 bytes), guest access rules and no physical-path header disclosure. Temporary HTTP session removed.\n";
} finally { $manager->destroy( $token ); }
