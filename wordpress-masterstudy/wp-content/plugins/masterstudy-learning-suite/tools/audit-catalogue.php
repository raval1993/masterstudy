<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
if ( ( $args[0] ?? '' ) === 'start' ) { mls_start_catalogue_audit(); }
$state = get_option( 'mls_catalogue_audit', array() );
if ( in_array( $state['status'] ?? '', array( 'queued', 'running' ), true ) ) { wp_clear_scheduled_hook( 'mls_catalogue_audit_step' ); mls_catalogue_audit_step(); }
echo wp_json_encode( get_option( 'mls_catalogue_audit', array() ), JSON_PRETTY_PRINT ) . "\n";
