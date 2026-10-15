<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
$ids = array(); $error = null;
try {
	$id = wp_insert_post( array( 'post_type' => 'stm-courses', 'post_status' => 'draft', 'post_title' => 'MLSTEST - Temporary import fixture', 'post_content' => 'Original fixture content, never published.' ), true );
	if ( is_wp_error( $id ) ) { throw new RuntimeException( $id->get_error_message() ); } $ids[] = $id;
	$data = array( 'course_id' => 'MLSTEST', 'title' => 'Temporary import fixture', 'modules' => array() );
	update_post_meta( $id, '_ca_import_snapshot_hash', ca_publisher_snapshot_hash( $id ) );
	$before = get_post_field( 'post_content', $id );
	if ( is_wp_error( ca_publisher_preflight_edits( $id, $data, $before ) ) ) { throw new RuntimeException( 'Unchanged import was blocked.' ); }
	wp_update_post( array( 'ID' => $id, 'post_content' => 'Manual edit of temporary fixture only.' ) );
	$conflict = ca_publisher_preflight_edits( $id, $data, $before );
	if ( ! is_wp_error( $conflict ) || $conflict->get_error_code() !== 'ca_manual_edit_conflict' || get_post_field( 'post_content', $id ) !== 'Manual edit of temporary fixture only.' ) { throw new RuntimeException( 'Manual content edit was not protected.' ); }
	delete_post_meta( $id, '_ca_import_snapshot_hash' );
	if ( ! is_wp_error( ca_publisher_preflight_edits( $id, $data, $before ) ) ) { throw new RuntimeException( 'Unknown baseline replacement accepted.' ); }
	update_post_meta( $id, '_ca_import_snapshot_hash', ca_publisher_snapshot_hash( $id ) ); update_post_meta( $id, '_ca_lesson_post_ids', array( 9840 ) );
	if ( ! is_wp_error( ca_publisher_preflight_edits( $id, $data, get_post_field( 'post_content', $id ) ) ) ) { throw new RuntimeException( 'Existing lesson removal accepted.' ); }
} catch ( Throwable $caught ) { $error = $caught; }
finally { foreach ( $ids as $id ) { wp_delete_post( $id, true ); } }
if ( $error ) { WP_CLI::error( $error->getMessage() ); }
echo "PASS: importer manual edits, missing baselines, no-op imports and removed lesson protection. Only private draft fixtures used and removed.\n";
