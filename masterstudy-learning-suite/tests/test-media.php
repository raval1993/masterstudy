<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
$url = get_post_meta( 9840, 'lesson_ext_link_url', true );
$relative = substr( wp_parse_url( $url, PHP_URL_PATH ), strlen( '/wp-content/course-automation/videos/' ) );
foreach ( array( '../wp-config.php', '../../secret.mp4', '/etc/passwd', 'a%2fb.mp4', "a\0.mp4", 'a/../b.mp4', 'test.txt', 'a.mp4/extra' ) as $invalid ) { if ( mls_video_file( $invalid ) !== null ) { WP_CLI::error( 'Video path escape accepted.' ); } }
if ( ! mls_video_file( $relative ) ) { WP_CLI::error( 'Pilot video resolution failed.' ); }
wp_set_current_user( (int) $admins[0] );
if ( ! mls_video_authorized( $relative ) ) { WP_CLI::error( 'Instructor video authorization failed.' ); }
wp_set_current_user( 0 );
if ( get_post_meta( 9840, 'preview', true ) !== 'on' && mls_video_authorized( $relative ) ) { WP_CLI::error( 'Guest can access a non-preview lesson video.' ); }
$private_url = get_post_meta( 9841, 'lesson_ext_link_url', true ); $private_relative = substr( wp_parse_url( $private_url, PHP_URL_PATH ), strlen( '/wp-content/course-automation/videos/' ) );
if ( get_post_meta( 9841, 'preview', true ) === 'on' || mls_video_authorized( $private_relative ) ) { WP_CLI::error( 'Guest can access the non-preview regression lesson.' ); }
echo "PASS: safe video path resolution, original publisher URLs, assigned instructor access and guest denial for non-preview media.\n";
