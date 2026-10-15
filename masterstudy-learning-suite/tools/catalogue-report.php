<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
$report = array( 'scan' => get_option( 'mls_catalogue_audit', array() ), 'issue_counts' => array(), 'published_courses' => (int) wp_count_posts( 'stm-courses' )->publish, 'reviewed_question_banks' => (int) wp_count_posts( 'mls_bank' )->publish );
foreach ( get_posts( array( 'post_type' => 'stm-courses', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids' ) ) as $id ) { $audit = get_post_meta( $id, '_mls_quality_audit', true ); foreach ( $audit['issues'] ?? array() as $issue ) { $report['issue_counts'][ $issue ] = ( $report['issue_counts'][ $issue ] ?? 0 ) + 1; } }
echo wp_json_encode( $report, JSON_PRETTY_PRINT ) . "\n";
