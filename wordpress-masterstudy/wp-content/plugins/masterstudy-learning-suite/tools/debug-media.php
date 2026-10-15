<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
if ( ! function_exists( 'mls_video_authorized' ) ) { require dirname( __DIR__ ) . '/includes/media.php'; }
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) ); wp_set_current_user( (int) $admins[0] );
$url = get_post_meta( 9840, 'lesson_ext_link_url', true ); $relative = substr( wp_parse_url( $url, PHP_URL_PATH ), strlen( '/wp-content/course-automation/videos/' ) );
echo wp_json_encode( array( 'stored_url' => $url, 'content_url' => content_url( '/course-automation/videos/' . $relative ), 'context' => mls_context( 9840 ), 'lesson_access' => mls_video_lesson_access( 9840 ), 'authorized' => mls_video_authorized( $relative ) ), JSON_PRETTY_PRINT ) . "\n";
wp_set_current_user( 0 );
