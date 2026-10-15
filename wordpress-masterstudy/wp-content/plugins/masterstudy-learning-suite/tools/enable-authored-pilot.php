<?php
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit( 1 ); }
$admin = (int) get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) )[0];
$created = 0; $preserved = 0;
foreach ( (array) get_post_meta( 9838, '_ca_lesson_post_ids', true ) as $lesson ) {
	$context = mls_context( (int) $lesson );
	if ( ! $context || $context['source_id'] !== 'BSBTEC202' ) { WP_CLI::error( 'Unexpected pilot mapping.' ); }
	$question = mce_pilot_questions()[ $context['lesson_key'] ] ?? null;
	if ( ! $question ) { continue; }
	$existing = get_posts( array( 'post_type' => 'mls_bank', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_query' => array( array( 'key' => '_mls_lesson', 'value' => $lesson ), array( 'key' => '_mls_release', 'value' => $context['release'] ), array( 'key' => '_mls_origin', 'value' => 'authored_pilot_v1' ) ) ) );
	if ( $existing ) { ++$preserved; continue; }
	$bank = mls_validate_bank( array( 'minutes' => 10, 'pass_percent' => 100, 'questions' => array( array( 'type' => 'mcq', 'prompt' => $question[0], 'options' => $question[1], 'correct' => $question[2], 'explanation' => $question[3], 'rubric' => 'Formative practice check only; not a competency determination.' ) ) ) );
	$id = wp_insert_post( array( 'post_type' => 'mls_bank', 'post_status' => 'publish', 'post_author' => $admin, 'post_title' => 'Practice check: ' . get_the_title( $lesson ), 'post_content' => wp_slash( wp_json_encode( $bank ) ) ), true );
	if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
	foreach ( array( '_mls_course' => 9838, '_mls_lesson' => $lesson, '_mls_release' => $context['release'], '_mls_origin' => 'authored_pilot_v1', '_mls_reviewed_by' => $admin ) as $key => $value ) { update_post_meta( $id, $key, $value ); }
	++$created;
}
echo wp_json_encode( array( 'authored_pilot_checks_enabled' => $created, 'preserved' => $preserved, 'ai_banks_published' => 0 ) ) . "\n";
