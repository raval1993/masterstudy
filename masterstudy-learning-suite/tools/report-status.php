<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
global $wpdb;
$courses = get_posts( array( 'post_type' => 'stm-courses', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_ca_verified_release' ) );
$source_banks = get_posts( array( 'post_type' => 'mls_bank', 'post_status' => 'draft', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_mls_origin', 'meta_value' => 'source_reflection_v1' ) );
$jobs = get_posts( array( 'post_type' => 'mls_job', 'post_status' => 'private', 'posts_per_page' => 10 ) );
$report = array( 'verified_published_courses' => count( $courses ), 'source_reflection_draft_banks' => count( $source_banks ), 'approved_banks' => (int) wp_count_posts( 'mls_bank' )->publish, 'jobs' => array(), 'remaining_test_users' => count( get_users( array( 'meta_key' => '_mls_browser_fixture', 'meta_value' => '1', 'fields' => 'ID' ) ) ) );
foreach ( $jobs as $job ) {
	$output = (int) get_post_meta( $job->ID, '_mls_output', true );
	$report['jobs'][] = array( 'id' => $job->ID, 'status' => get_post_meta( $job->ID, '_mls_status', true ), 'error' => get_post_meta( $job->ID, '_mls_error', true ), 'output_id' => $output, 'output_type' => $output ? get_post_type( $output ) : null );
}
echo wp_json_encode( $report, JSON_PRETTY_PRINT ) . "\n";
