<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function stm_lms_add_user_answer( $user_answer ) {
	global $wpdb;
	$table_name = stm_lms_user_answers_name( $wpdb );

	$result = $wpdb->insert(
		$table_name,
		$user_answer
	);
	return ! is_wp_error( $result ) ? $wpdb->insert_id : 0;
}

function stm_lms_reset_user_answers( $course_id, $student_id ) {
	global $wpdb;
	$table = stm_lms_user_answers_name( $wpdb );
	$wpdb->query(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"DELETE FROM {$table} WHERE `course_id` = %d AND `user_id` = %d ",
			$course_id,
			$student_id
		)
	);
	wp_reset_postdata();
}

function stm_lms_reset_marker_answers( $course_id, $student_id ) {
	global $wpdb;

	$table = stm_lms_lesson_marker_user_answers_name( $wpdb );
	$wpdb->query(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"DELETE FROM {$table} WHERE `course_id` = %d AND `user_id` = %d",
			$course_id,
			$student_id
		)
	);
	wp_reset_postdata();
}

function stm_lms_get_user_answers( $user_id, $quiz_id, $attempt = '1', $get_correct = false, $fields = array() ) {
	global $wpdb;
	$table = stm_lms_user_answers_name( $wpdb );

	$fields = ( empty( $fields ) ) ? '*' : implode( ',', $fields );

	$request = $wpdb->prepare(
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		"SELECT {$fields} FROM {$table} WHERE user_ID = %d AND quiz_id = %d AND attempt_number = %d",
		$user_id,
		$quiz_id,
		$attempt
	);

	if ( $get_correct ) {
		$request .= ' AND correct_answer = 1';
	}

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	return $wpdb->get_results( $request, ARRAY_N );
}

function stm_lms_get_lesson_markers( $lesson_id ) {
	global $wpdb;

	if ( ! $lesson_id ) {
		return array();
	}

	$table = stm_lms_lesson_marker_questions_name( $wpdb );

	$markers = $wpdb->get_results(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT * FROM {$table} WHERE lesson_id = %d",
			$lesson_id,
		),
		ARRAY_A
	);

	return $markers ?? array();
}

function stm_lms_get_lesson_markers_correct_answer( $lesson_id, $question_id ) {
	global $wpdb;

	if ( ! $lesson_id || ! $question_id ) {
		return array();
	}

	$table = stm_lms_lesson_marker_questions_name( $wpdb );

	$answers = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT answers FROM {$table} WHERE id = %d AND lesson_id = %d",
			$question_id,
			$lesson_id
		)
	);

	if ( ! empty( $answers ) ) {
		$answers        = unserialize( $answers );
		$correct_answer = array_column(
			array_filter(
				$answers,
				fn ( $a ) => $a['is_correct']
			),
			'answer_id'
		);

		return $correct_answer;
	}

	return array();
}

function stm_lms_get_user_marker_answers( $user_id, $lesson_id, $question_id ) {
	global $wpdb;

	if ( ! $user_id || ! $lesson_id || ! $question_id ) {
		return array();
	}

	$table = stm_lms_lesson_marker_user_answers_name( $wpdb );

	$user_answer = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT `user_answers` FROM {$table} WHERE user_id = %d AND lesson_id = %d AND question_id = %d",
			$user_id,
			$lesson_id,
			$question_id
		)
	);

	return $user_answer ? array_map( 'intval', explode( ',', $user_answer ) ) : array();
}

function stm_lms_add_user_marker_answer( $user_answer ) {
	global $wpdb;

	if ( in_array( null, $user_answer, true ) ) {
		return 0;
	}

	$table_name = stm_lms_lesson_marker_user_answers_name( $wpdb );

	$existing = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT user_answer_id FROM {$table_name} WHERE user_id = %d AND question_id = %d AND lesson_id = %d",
			$user_answer['user_id'],
			$user_answer['question_id'],
			$user_answer['lesson_id']
		)
	);

	if ( $existing ) {
		$result = $wpdb->update(
			$table_name,
			$user_answer,
			array( 'user_answer_id' => $existing )
		);

		return false !== $result ? $existing : 0;
	}

	$wpdb->insert( $table_name, $user_answer );

	return (int) $wpdb->insert_id;
}

function stm_lms_get_quiz_latest_answers( int $user_id, int $quiz_id, array $fields = array() ): array {
	global $wpdb;
	$table      = stm_lms_user_answers_name( $wpdb );
	$fields_sql = empty( $fields ) ? '*' : implode( ',', array_map( 'esc_sql', $fields ) );

	$sql = sprintf(
		"SELECT %s FROM {$table} WHERE user_id = %%d AND quiz_id = %%d
        AND attempt_number = (SELECT MAX(attempt_number) FROM {$table} WHERE user_id = %%d AND quiz_id = %%d)
		ORDER BY user_answer_id DESC",
		$fields_sql
	);

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	return $wpdb->get_results( $wpdb->prepare( $sql, $user_id, $quiz_id, $user_id, $quiz_id ), ARRAY_A );
}

function stm_lms_get_quiz_attempt_answers( $user_id, $quiz_id, $fields = array(), $attempt = 1 ) {
	global $wpdb;
	$table = stm_lms_user_answers_name( $wpdb );

	$fields = ( empty( $fields ) ) ? '*' : implode( ',', $fields );

	return $wpdb->get_results(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT {$fields} FROM {$table} WHERE user_ID = %d AND quiz_id = %d AND attempt_number = %d ORDER BY user_answer_id DESC",
			$user_id,
			$quiz_id,
			$attempt
		),
		ARRAY_A
	);
}
