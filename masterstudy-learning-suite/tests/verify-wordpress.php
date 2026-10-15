<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
if ( ! $admins ) { WP_CLI::error( 'No administrator for test context.' ); }
wp_set_current_user( (int) $admins[0] );
$context = mls_context( 9840 );
if ( ! $context || ! mls_access( $context ) ) { WP_CLI::error( 'Pilot access failed.' ); }
$page = get_page_by_path( 'learning-hub' );
if ( ! $page || strpos( $page->post_content, '[mls_learning_hub]' ) === false ) { WP_CLI::error( 'Learning portal missing.' ); }
$banks = get_posts( array( 'post_type' => 'mls_bank', 'post_status' => 'draft', 'posts_per_page' => 1, 'meta_query' => array( array( 'key' => '_mls_lesson', 'value' => 9840 ), array( 'key' => '_mls_origin', 'value' => 'source_reflection_v1' ) ) ) );
if ( ! $banks ) { WP_CLI::error( 'Pilot draft bank missing.' ); }
$bank = mls_validate_bank( json_decode( $banks[0]->post_content, true, 512, JSON_THROW_ON_ERROR ) );
if ( count( $bank['questions'] ) < 2 ) { WP_CLI::error( 'Pilot bank is incomplete.' ); }
global $post; $post = get_post( 9840 );
$html = mls_lesson_tools( $post->post_content );
if ( strpos( $html, 'mls-tutor' ) === false || strpos( $html, 'Spaced review' ) === false ) { WP_CLI::error( 'Lesson activities missing.' ); }
wp_set_current_user( 0 );
if ( mls_access( $context ) ) { WP_CLI::error( 'Guest can access private assessment data.' ); }
echo "PASS: release mapping, learner access, drafted bank, portal and activities.\n";
