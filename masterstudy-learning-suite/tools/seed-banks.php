<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
$courses = get_posts( array( 'post_type' => 'stm-courses', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_ca_verified_release' ) );
$created = 0; $skipped = 0; $pilot = 0;
foreach ( $courses as $course ) {
	foreach ( (array) get_post_meta( $course, '_ca_lesson_post_ids', true ) as $lesson ) {
		$context = mls_context( (int) $lesson );
		if ( ! $context ) { continue; }
		$existing = get_posts( array( 'post_type' => 'mls_bank', 'post_status' => array( 'draft', 'publish' ), 'posts_per_page' => 1, 'fields' => 'ids', 'meta_query' => array( array( 'key' => '_mls_lesson', 'value' => $lesson ), array( 'key' => '_mls_release', 'value' => $context['release'] ) ) ) );
		if ( $existing ) { ++$skipped; continue; }
		$sections = mce_study_sections( get_post_field( 'post_content', $lesson ) );
		if ( ! $sections ) { continue; }
		$questions = array();
		foreach ( array_slice( $sections, 0, 3 ) as $section ) {
			$questions[] = array( 'type' => 'short', 'prompt' => 'Explain "' . $section['heading'] . '" in your own words. Apply it to a fictional workplace example and identify what you would need to check before acting.', 'explanation' => $section['text'], 'rubric' => 'Explains the source guidance accurately; uses a relevant fictional example; identifies a practical action and required checks.' );
		}
		$authored = $context['source_id'] === 'BSBTEC202' ? ( mce_pilot_questions()[ $context['lesson_key'] ] ?? null ) : null;
		if ( $authored ) { array_unshift( $questions, array( 'type' => 'mcq', 'prompt' => $authored[0], 'options' => $authored[1], 'correct' => $authored[2], 'explanation' => $authored[3] ) ); ++$pilot; }
		$bank = mls_validate_bank( array( 'minutes' => 0, 'pass_percent' => 80, 'randomize' => false, 'questions' => $questions ) );
		$id = wp_insert_post( array( 'post_type' => 'mls_bank', 'post_status' => 'draft', 'post_title' => get_the_title( $lesson ), 'post_content' => wp_slash( wp_json_encode( $bank ) ) ), true );
		if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
		foreach ( array( '_mls_course' => $course, '_mls_lesson' => $lesson, '_mls_release' => $context['release'], '_mls_origin' => 'source_reflection_v1' ) as $key => $value ) { update_post_meta( $id, $key, $value ); }
		++$created;
	}
}
echo wp_json_encode( array( 'draft_banks_created' => $created, 'existing_banks_preserved' => $skipped, 'pilot_banks' => $pilot ) ) . "\n";
