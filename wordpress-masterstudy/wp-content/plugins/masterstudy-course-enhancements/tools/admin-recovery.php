<?php
// Run with WP-CLI. Browser requests cannot generate account recovery tokens.
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	if ( PHP_SAPI !== 'cli' ) {
		http_response_code( 403 );
	}
	exit( 1 );
}
$login = $args[0] ?? 'admin';
$user = get_user_by( 'login', $login );
if ( ! $user || ! user_can( $user, 'manage_options' ) ) {
	WP_CLI::error( 'Select an existing administrator account.' );
}
if ( 'https' !== wp_parse_url( home_url(), PHP_URL_SCHEME ) ) {
	WP_CLI::error( 'HTTPS must be enabled before generating an admin recovery link.' );
}
$key = get_password_reset_key( $user );
if ( is_wp_error( $key ) ) {
	WP_CLI::error( $key->get_error_message() );
}
$url = add_query_arg( array( 'action' => 'rp', 'key' => $key, 'login' => $user->user_login ), network_site_url( 'wp-login.php', 'login' ) );
echo wp_json_encode( array( 'admin_login' => $user->user_login, 'reset_url' => $url, 'single_use' => true, 'expires_in_seconds' => apply_filters( 'password_reset_expiration', DAY_IN_SECONDS ) ), JSON_UNESCAPED_SLASHES ) . "\n";
