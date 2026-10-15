<?php
/**
 * Plugin Name: MasterStudy Course Enhancements
 * Description: Accessible video tools, study cards, practice logs and knowledge checks for MasterStudy courses.
 * Version: 1.1.0
 */
defined( 'ABSPATH' ) || exit;

const MCE_PILOT_COURSE_ID = 'BSBTEC202';
const MCE_RELEASES_DIRECTORY = '/opt/masterstudy/course-automation/data/business-releases';
const MCE_VERSION = '1.1.0';

require_once __DIR__ . '/learner-tools.php';

add_filter( 'the_content', 'mce_append_lesson_tools', 30 );
add_filter( 'the_content', 'mce_pilot_course_alt_text', 20 );
add_action( 'rest_api_init', 'mce_register_routes' );
add_action( 'wp_enqueue_scripts', 'mce_enqueue_assets' );
add_action( 'stm_lms_before_item_template_start', 'mce_enqueue_player_assets', 20, 2 );

function mce_pilot_course_alt_text( string $content ): string {
	if ( is_admin() ) {
		return $content;
	}
	$lesson = get_post();
	if ( ! $lesson instanceof WP_Post || ! mce_context_for_lesson( $lesson->ID ) ) {
		return $content;
	}
	$title = get_the_title( $lesson );
	$title = preg_replace( '/^[A-Z0-9][A-Z0-9-]*\s*[-\x{2013}\x{2014}]\s*/u', '', $title );
	$title = preg_replace( '/^\d+(?:\.\d+)*\s+/u', '', $title );
	$title = trim( sanitize_text_field( $title ) );
	if ( '' === $title ) {
		return $content;
	}
	$alt = esc_attr( 'Instructional slide covering ' . $title );
	return preg_replace_callback(
		'/(<img\b[^>]*\balt\s*=\s*)("|\')Lesson image\2/i',
		static fn( array $match ): string => $match[1] . $match[2] . $alt . $match[2],
		$content
	);
}

function mce_context_for_lesson( int $lesson_id ): ?array {
	$lesson = get_post( $lesson_id );
	if ( ! $lesson instanceof WP_Post || 'stm-lessons' !== $lesson->post_type || 'publish' !== $lesson->post_status ) {
		return null;
	}

	$course_id = absint( get_post_meta( $lesson_id, '_ca_course_post_id', true ) );
	$course    = get_post( $course_id );
	$release   = (string) get_post_meta( $lesson_id, '_ca_verified_release', true );
	$lesson_key = (string) get_post_meta( $lesson_id, '_ca_lesson_id', true );
	$video_url = (string) get_post_meta( $lesson_id, 'lesson_ext_link_url', true );
	$source_id = (string) get_post_meta( $course_id, '_ca_course_id', true );
	$members = get_post_meta( $course_id, '_ca_lesson_post_ids', true );

	if (
		! $course instanceof WP_Post || 'stm-courses' !== $course->post_type || 'publish' !== $course->post_status ||
		! preg_match( '/^[A-Z0-9][A-Z0-9-]{0,79}$/', $source_id ) ||
		$source_id !== get_post_meta( $lesson_id, '_ca_course_id', true ) ||
		! preg_match( '/^' . preg_quote( $source_id, '/' ) . '-[a-f0-9]{12}$/', $release ) ||
		! is_array( $members ) || ! in_array( $lesson_id, array_map( 'intval', $members ), true ) ||
		$release !== get_post_meta( $course_id, '_ca_verified_release', true ) ||
		$release !== get_post_meta( $lesson_id, '_ca_verified_release', true ) ||
		! preg_match( '/^M\d{2,4}-L\d{2,4}$/', $lesson_key ) ||
		! in_array( get_post_meta( $lesson_id, 'video_type', true ), array( 'ext_link', 'html' ), true ) ||
		! mce_is_publisher_video_url( $video_url )
	) {
		return null;
	}

	return array(
		'lesson_id' => $lesson_id,
		'course_id' => $course_id,
		'lesson_key' => $lesson_key,
		'release' => $release,
		'source_id' => $source_id,
	);
}

function mce_is_publisher_video_url( string $url ): bool {
	$home = wp_parse_url( home_url() );
	$video = wp_parse_url( $url );
	return is_array( $home ) && is_array( $video ) &&
		isset( $home['host'], $video['host'], $video['path'] ) &&
		strtolower( $home['host'] ) === strtolower( $video['host'] ) &&
		false !== strpos( $video['path'], '/wp-content/course-automation/videos/' ) &&
		(string) pathinfo( $video['path'], PATHINFO_EXTENSION ) === 'mp4';
}

function mce_lesson_is_accessible( array $context ): bool {
	$lesson_id = $context['lesson_id'];
	$course_id = $context['course_id'];
	if ( 'on' === get_post_meta( $lesson_id, 'preview', true ) || current_user_can( 'edit_post', $course_id ) ) {
		return true;
	}
	return is_user_logged_in() && class_exists( 'STM_LMS_User' ) && STM_LMS_User::has_course_access( $course_id, '', false );
}

function mce_transcript_path( string $release, string $lesson_key ): ?string {
	if ( ! preg_match( '/^[A-Z0-9][A-Z0-9-]{0,79}-[a-f0-9]{12}$/', $release ) || ! preg_match( '/^M\d{2,4}-L\d{2,4}$/', $lesson_key ) ) {
		return null;
	}
	$base_setting = defined( 'MCE_BUSINESS_RELEASES_DIRECTORY' ) ? MCE_BUSINESS_RELEASES_DIRECTORY : MCE_RELEASES_DIRECTORY;
	$base = realpath( $base_setting );
	if ( ! $base || ! is_dir( $base ) ) {
		return null;
	}
	$path = realpath( $base . DIRECTORY_SEPARATOR . $release . DIRECTORY_SEPARATOR . $lesson_key . DIRECTORY_SEPARATOR . 'transcript.vtt' );
	$prefix = rtrim( $base, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
	if ( ! $path || 0 !== strpos( $path, $prefix ) || ! is_file( $path ) || ! is_readable( $path ) || filesize( $path ) > 1048576 ) {
		return null;
	}
	return $path;
}

function mce_parse_vtt( string $vtt, array $scenes = array() ): array {
	$vtt = preg_replace( '/^\xEF\xBB\xBF/', '', $vtt );
	if ( ! is_string( $vtt ) || ! preg_match( '/^WEBVTT(?:[ \t][^\r\n]*)?\r?\n/', $vtt ) ) {
		return array();
	}
	$blocks = preg_split( '/\r?\n\s*\r?\n/', trim( $vtt ) );
	$cues = array();
	foreach ( $blocks as $block ) {
		$lines = preg_split( '/\r?\n/', trim( $block ) );
		$timing_index = null;
		foreach ( $lines as $index => $line ) {
			if ( false !== strpos( $line, '-->' ) ) {
				$timing_index = $index;
				break;
			}
		}
		if ( null === $timing_index || ! preg_match( '/^((?:\d{2}:)?\d{2}:\d{2}\.\d{3})\s+-->\s+((?:\d{2}:)?\d{2}:\d{2}\.\d{3})(?:\s+.*)?$/', trim( $lines[ $timing_index ] ), $times ) ) {
			continue;
		}
		$start = mce_vtt_seconds( $times[1] );
		$end = mce_vtt_seconds( $times[2] );
		$text = trim( wp_strip_all_tags( implode( "\n", array_slice( $lines, $timing_index + 1 ) ), true ) );
		$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		if ( false === $start || false === $end || $end <= $start || '' === $text || count( $cues ) >= 100 || strlen( $text ) > 40000 ) {
			return array();
		}
		$scene = $scenes[ count( $cues ) ] ?? array();
		$label = is_array( $scene ) && ! empty( $scene['heading'] ) ? sanitize_text_field( $scene['heading'] ) : sprintf( 'Scene %d', count( $cues ) + 1 );
		$cues[] = array( 'start' => $start, 'end' => $end, 'label' => $label, 'text' => $text );
	}
	return $cues;
}

function mce_lesson_scene_headings( int $lesson_id ): array {
	$post = get_post( $lesson_id );
	if ( ! $post instanceof WP_Post || ! function_exists( 'parse_blocks' ) ) {
		return array();
	}
	$labels = array();
	mce_collect_scene_headings( parse_blocks( $post->post_content ), $labels );
	return array_map( static fn( string $heading ): array => array( 'heading' => $heading ), $labels );
}

function mce_collect_scene_headings( array $blocks, array &$labels ): void {
	foreach ( $blocks as $block ) {
		if ( 'core/heading' === ( $block['blockName'] ?? '' ) && 5 === intval( $block['attrs']['level'] ?? 2 ) ) {
			$heading = sanitize_text_field( html_entity_decode( wp_strip_all_tags( $block['innerHTML'] ?? '', true ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
			if ( '' !== $heading ) {
				$labels[] = $heading;
			}
		}
		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			mce_collect_scene_headings( $block['innerBlocks'], $labels );
		}
	}
}

function mce_vtt_seconds( string $stamp ) {
	$parts = explode( ':', $stamp );
	$seconds = (float) array_pop( $parts );
	$minutes = (int) array_pop( $parts );
	$hours = empty( $parts ) ? 0 : (int) array_pop( $parts );
	if ( $seconds >= 60 || $minutes >= 60 ) {
		return false;
	}
	return ( $hours * 3600 ) + ( $minutes * 60 ) + $seconds;
}

function mce_register_routes(): void {
	register_rest_route(
		'mce/v1',
		'/lesson/(?P<lesson_id>\d+)',
		array(
			'methods' => 'GET',
			'permission_callback' => static function ( WP_REST_Request $request ): bool {
				$context = mce_context_for_lesson( absint( $request['lesson_id'] ) );
				return $context && mce_lesson_is_accessible( $context );
			},
			'callback' => 'mce_get_lesson_transcript',
		)
	);
}

function mce_get_lesson_transcript( WP_REST_Request $request ) {
	$context = mce_context_for_lesson( absint( $request['lesson_id'] ) );
	if ( ! $context || ! mce_lesson_is_accessible( $context ) ) {
		return new WP_Error( 'mce_not_available', 'Transcript unavailable.', array( 'status' => 404 ) );
	}
	$path = mce_transcript_path( $context['release'], $context['lesson_key'] );
	if ( ! $path ) {
		return new WP_Error( 'mce_not_available', 'Transcript unavailable.', array( 'status' => 404 ) );
	}
	$raw = file_get_contents( $path );
	$scene_data = json_decode( (string) get_post_meta( $context['lesson_id'], '_ca_video_scenes', true ), true );
	if ( ! is_array( $scene_data ) || empty( $scene_data ) ) {
		$scene_data = mce_lesson_scene_headings( $context['lesson_id'] );
	}
	$cues = mce_parse_vtt( (string) $raw, is_array( $scene_data ) ? $scene_data : array() );
	if ( empty( $cues ) ) {
		return new WP_Error( 'mce_not_available', 'Transcript unavailable.', array( 'status' => 404 ) );
	}
	$clean_vtt = "WEBVTT\n\n";
	foreach ( $cues as $cue ) {
		$clean_vtt .= mce_vtt_stamp( $cue['start'] ) . ' --> ' . mce_vtt_stamp( $cue['end'] ) . "\n" .
			str_replace( array( '&', '<', '>' ), array( '&amp;', '&lt;', '&gt;' ), $cue['text'] ) . "\n\n";
	}
	$response = new WP_REST_Response( array( 'vtt' => $clean_vtt, 'cues' => $cues ), 200 );
	$response->header( 'Cache-Control', 'private, no-store, max-age=0' );
	$response->header( 'X-Content-Type-Options', 'nosniff' );
	return $response;
}

function mce_vtt_stamp( float $seconds ): string {
	$ms = (int) round( $seconds * 1000 );
	return sprintf( '%02d:%02d:%02d.%03d', intdiv( $ms, 3600000 ), intdiv( $ms, 60000 ) % 60, intdiv( $ms, 1000 ) % 60, $ms % 1000 );
}

function mce_append_lesson_tools( string $content ): string {
	if ( is_admin() ) {
		return $content;
	}
	$lesson = get_post();
	if ( ! $lesson instanceof WP_Post ) {
		return $content;
	}
	$context = mce_context_for_lesson( $lesson->ID );
	if ( ! $context || ! mce_lesson_is_accessible( $context ) || ! mce_transcript_path( $context['release'], $context['lesson_key'] ) ) {
		return $content;
	}
	$endpoint = rest_url( 'mce/v1/lesson/' . $lesson->ID );
	return $content . sprintf(
		'<section class="mce-tools" data-endpoint="%1$s" data-nonce="%2$s" aria-label="Video learning tools"><h3>Video learning tools</h3><p class="mce-status" role="status" aria-live="polite">Loading transcript…</p><div class="mce-controls" hidden></div></section>',
		esc_url( $endpoint ),
		esc_attr( wp_create_nonce( 'wp_rest' ) )
	);
}

function mce_enqueue_assets(): void {
	if ( is_admin() ) {
		return;
	}
	$course_id = get_queried_object_id();
	if ( ! get_post_meta( $course_id, '_ca_verified_release', true ) && ! mce_context_for_lesson( $course_id ) ) {
		return;
	}
	mce_enqueue_tool_assets();
}

function mce_enqueue_player_assets( $course_id, $lesson_id ): void {
	$context = mce_context_for_lesson( (int) $lesson_id );
	if ( $context && (int) $course_id === $context['course_id'] && mce_lesson_is_accessible( $context ) ) {
		mce_enqueue_tool_assets();
	}
}

function mce_enqueue_tool_assets(): void {
	wp_enqueue_style( 'mce-course-tools', plugins_url( 'assets/course-tools.css', __FILE__ ), array(), MCE_VERSION );
	wp_enqueue_script( 'mce-course-tools', plugins_url( 'assets/course-tools.js', __FILE__ ), array(), MCE_VERSION, true );
	wp_enqueue_script( 'mce-study-tools', plugins_url( 'assets/study-tools.js', __FILE__ ), array(), MCE_VERSION, true );
}
