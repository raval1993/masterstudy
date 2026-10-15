<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
$admin = (int) get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) )[0];
$context = mls_context( 9840 );
if ( ! $context ) { WP_CLI::error( 'Pilot release unavailable.' ); }
$existing = get_posts( array( 'post_type' => 'mls_job', 'post_status' => 'private', 'posts_per_page' => 1, 'meta_key' => '_mls_pilot_worker', 'meta_value' => 'enrichment_v1' ) );
if ( $existing ) { echo 'Existing pilot worker job: ' . $existing[0]->ID . ' (' . get_post_meta( $existing[0]->ID, '_mls_status', true ) . ")\n"; return; }
$id = wp_insert_post( array( 'post_type' => 'mls_job', 'post_status' => 'private', 'post_author' => $admin, 'post_title' => 'Pilot worked-example draft: ' . get_the_title( 9840 ), 'post_content' => wp_slash( wp_json_encode( array( 'context' => $context, 'kind' => 'enrichment', 'attempt' => 0 ) ) ) ), true );
if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
update_post_meta( $id, '_mls_status', 'queued' ); update_post_meta( $id, '_mls_course', 9838 ); update_post_meta( $id, '_mls_pilot_worker', 'enrichment_v1' );
$scheduled = wp_schedule_single_event( time() - 1, 'mls_generate_job', array( $id ), true );
if ( is_wp_error( $scheduled ) || ! $scheduled ) { update_post_meta( $id, '_mls_status', 'failed' ); WP_CLI::error( 'Could not schedule pilot draft.' ); }
echo 'Queued private pilot enrichment job: ' . $id . "\n";
