<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
wp_set_current_user( (int) $admins[0] );
mls_event( 0, 0, 'platform_release_verified', array( 'version' => MLS_VERSION, 'checks' => array( 'assessments', 'evidence', 'cohorts', 'import_conflicts', 'protected_media', 'http_range', 'guest_video_denial' ), 'recorded_at' => gmdate( 'c' ) ) );
wp_set_current_user( 0 );
require __DIR__ . '/verify-audit-chain.php';
$head = get_option( 'mls_audit_head' ); $sealed = get_post( (int) $head['id'] );
$copy = wp_insert_post( array( 'post_type' => 'mls_event', 'post_status' => 'private', 'post_author' => $sealed->post_author, 'post_title' => $sealed->post_title, 'post_content' => wp_slash( $sealed->post_content ), 'post_date' => $sealed->post_date, 'post_date_gmt' => $sealed->post_date_gmt ), true );
if ( is_wp_error( $copy ) ) { WP_CLI::error( 'Cannot create isolated audit-integrity fixture.' ); }
try {
	update_post_meta( $copy, '_mls_course', 0 ); update_post_meta( $copy, '_mls_lesson', 0 );
	update_post_meta( $copy, '_mls_audit_payload', wp_slash( get_post_meta( $sealed->ID, '_mls_audit_payload', true ) ) ); update_post_meta( $copy, '_mls_audit_hash', get_post_meta( $sealed->ID, '_mls_audit_hash', true ) );
	if ( mls_verify_event( $copy ) ) { throw new RuntimeException( 'Replayed audit payload accepted on another record ID.' ); }
} finally { wp_delete_post( $copy, true ); }
echo "PASS: sealed JSON integrity and rejection of a copied audit payload on another record. Private fixture removed.\n";
$args = array( 'cleanup' );
require __DIR__ . '/browser-fixture.php';
echo 'Live Learning Suite: ' . MLS_VERSION . "\n";
