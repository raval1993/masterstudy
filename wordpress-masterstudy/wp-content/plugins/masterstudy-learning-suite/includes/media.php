<?php
defined( 'ABSPATH' ) || exit;

function mls_video_file( string $relative ): ?string {
	if ( ! preg_match( '~^[A-Za-z0-9_-]+(?:/[A-Za-z0-9_-]+)*\.mp4$~D', $relative ) ) { return null; }
	$root = realpath( WP_CONTENT_DIR . '/course-automation/videos' );
	$path = $root ? realpath( $root . '/' . $relative ) : false;
	return $root && $path && is_file( $path ) && str_starts_with( $path, $root . DIRECTORY_SEPARATOR ) ? $path : null;
}

function mls_video_lesson_access( int $id ): bool {
	$context = mls_context( $id );
	if ( $context ) { return mls_access( $context, true ); }
	// Older publisher courses may be enrolled but not in the verified media group.
	$course = (int) get_post_meta( $id, '_ca_course_post_id', true );
	$members = array_map( 'intval', (array) get_post_meta( $course, '_ca_lesson_post_ids', true ) );
	if ( get_post_type( $id ) !== 'stm-lessons' || get_post_status( $id ) !== 'publish' || get_post_type( $course ) !== 'stm-courses' || get_post_status( $course ) !== 'publish' || ! in_array( $id, $members, true ) || ! get_post_meta( $course, '_ca_course_id', true ) || get_post_meta( $course, '_ca_course_id', true ) !== get_post_meta( $id, '_ca_course_id', true ) ) { return false; }
	if ( mls_manage( $course ) ) { return true; }
	if ( mls_gate_reason( $course, $id ) ) { return false; }
	return get_post_meta( $id, 'preview', true ) === 'on' || ( is_user_logged_in() && STM_LMS_User::has_course_access( $course, $id, false ) );
}

function mls_video_authorized( string $relative ): bool {
	if ( ! mls_video_file( $relative ) ) { return false; }
	$url = content_url( '/course-automation/videos/' . $relative );
	$urls = array_values( array_unique( array( set_url_scheme( $url, 'https' ), set_url_scheme( $url, 'http' ) ) ) );
	$lessons = get_posts( array( 'post_type' => 'stm-lessons', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_query' => array( array( 'key' => 'lesson_ext_link_url', 'value' => $urls, 'compare' => 'IN' ) ) ) );
	foreach ( $lessons as $lesson ) { if ( mls_video_lesson_access( (int) $lesson ) ) { return true; } }
	return false;
}

add_action( 'init', function () {
	if ( ! isset( $_GET['mls_protected_video'] ) ) { return; }
	nocache_headers(); header( 'Vary: Cookie', false ); header( 'X-Content-Type-Options: nosniff' ); header( 'X-Robots-Tag: noindex' );
	if ( ! defined( 'MLS_PROTECTED_MEDIA' ) || ! MLS_PROTECTED_MEDIA ) { status_header( 503 ); exit; }
	if ( ! in_array( $_SERVER['REQUEST_METHOD'] ?? '', array( 'GET', 'HEAD' ), true ) ) { status_header( 405 ); header( 'Allow: GET, HEAD' ); exit; }
	$relative = is_string( $_GET['mls_protected_video'] ) ? wp_unslash( $_GET['mls_protected_video'] ) : '';
	$path = mls_video_file( $relative );
	if ( ! $path || ! mls_video_authorized( $relative ) ) { status_header( 403 ); exit; }
	status_header( 200 ); header( 'Content-Type: video/mp4' ); header( 'Content-Disposition: inline' );
	header( 'X-Sendfile: ' . $path ); exit;
}, 99 );
