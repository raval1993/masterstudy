<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( 1 );
}
$course_id = 9838;
$lesson_id = 9840;
$context = mce_context_for_lesson( $lesson_id );
if ( ! $context || $context['course_id'] !== $course_id ) {
	WP_CLI::error( 'Live pilot identity or curriculum failed validation.' );
}
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
if ( ! $admins ) {
	WP_CLI::error( 'No administrator exists for read-only verification.' );
}
wp_set_current_user( (int) $admins[0] );
$request = new WP_REST_Request( 'GET', '/mce/v1/lesson/' . $lesson_id );
$request->set_param( 'lesson_id', $lesson_id );
$transcript = mce_get_lesson_transcript( $request );
if ( is_wp_error( $transcript ) || count( $transcript->get_data()['cues'] ?? array() ) !== 4 ) {
	WP_CLI::error( 'Pilot transcript is unavailable or has an unexpected scene count.' );
}
global $post;
$post = get_post( $lesson_id );
$html = apply_filters( 'the_content', $post->post_content );
foreach ( array( 'mce-study', 'mce-quiz', 'mce-tools', 'My study notes', 'Review cards' ) as $marker ) {
	if ( false === strpos( $html, $marker ) ) {
		WP_CLI::error( 'Missing rendered enhancement: ' . $marker );
	}
}
wp_set_current_user( 0 );
$guest_record = mce_study_record( $request );
if ( ! is_wp_error( $guest_record ) ) {
	WP_CLI::error( 'Anonymous study record was not denied.' );
}
$courses = get_posts( array( 'post_type' => 'stm-courses', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_ca_verified_release' ) );
$stats = array( 'version' => MCE_VERSION, 'verified_courses' => count( $courses ), 'eligible_lessons' => 0, 'readable_transcripts' => 0, 'study_sections' => 0, 'pilot_questions' => 0, 'missing_transcripts' => 0, 'course_url' => get_permalink( $course_id ), 'preview_url' => STM_LMS_Course::item_url( $course_id, $lesson_id ) );
foreach ( $courses as $course ) {
	$lessons = get_post_meta( $course, '_ca_lesson_post_ids', true );
	foreach ( is_array( $lessons ) ? $lessons : array() as $lesson ) {
		$item = mce_context_for_lesson( (int) $lesson );
		if ( ! $item ) {
			continue;
		}
		++$stats['eligible_lessons'];
		$path = mce_transcript_path( $item['release'], $item['lesson_key'] );
		if ( $path && mce_parse_vtt( file_get_contents( $path ) ) ) {
			++$stats['readable_transcripts'];
		} else {
			++$stats['missing_transcripts'];
		}
		if ( mce_study_sections( get_post_field( 'post_content', $lesson ) ) ) {
			++$stats['study_sections'];
		}
		if ( MCE_PILOT_COURSE_ID === $item['source_id'] && isset( mce_pilot_questions()[ $item['lesson_key'] ] ) ) {
			++$stats['pilot_questions'];
		}
	}
}
echo wp_json_encode( $stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n";
