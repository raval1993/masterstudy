<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
$head = get_option( 'mls_audit_head', array( 'id' => 0 ) ); $id = (int) $head['id']; $seen = array();
while ( $id ) {
	if ( isset( $seen[ $id ] ) || ! mls_verify_event( $id ) ) { WP_CLI::error( 'Audit integrity check failed at event ' . $id ); }
	$seen[ $id ] = true; $data = json_decode( get_post_meta( $id, '_mls_audit_payload', true ), true ); $id = (int) $data['previous_id'];
}
echo 'PASS: ' . count( $seen ) . " sealed events linked to the current audit head. Legacy unsealed events are not covered. The seal is tamper-evident, not an immutable external archive.\n";
