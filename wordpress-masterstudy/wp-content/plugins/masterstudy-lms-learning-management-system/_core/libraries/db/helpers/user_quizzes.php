<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function stm_lms_add_user_quiz( $user_quiz ) {
	global $wpdb;
	$table_name = stm_lms_user_quizzes_name( $wpdb );

	if ( is_array( $user_quiz ) ) {
		$user_quiz['created_at'] = current_time( 'mysql' );
	}

	$wpdb->insert(
		$table_name,
		$user_quiz
	);

	do_action( 'masterstudy_lms_user_quiz_added', $user_quiz );
}

function stm_lms_get_user_quizzes( $user_id, $quiz_id, $course_id = null, $fields = array(), $get_total = false ) {
	global $wpdb;
	$table         = stm_lms_user_quizzes_name( $wpdb );
	$select_fields = $get_total ? 'COUNT(*)' : ( $fields ? implode( ',', $fields ) : '*' );
	$sql           = "SELECT {$select_fields} FROM {$table} WHERE user_id = %d AND quiz_id = %d";

	$params = array( $user_id, $quiz_id );

	if ( ! is_null( $course_id ) ) {
		$sql     .= ' AND course_id = %d';
		$params[] = $course_id;
	}

	$sql = $wpdb->prepare( $sql, ...$params ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	return $get_total ? $wpdb->get_var( $sql ) : $wpdb->get_results( $sql, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

function stm_lms_get_user_all_course_quizzes( $user_id, $course_id, $quiz_id, $fields = array(), $get_total = false ) {
	global $wpdb;
	$table = stm_lms_user_quizzes_name( $wpdb );

	$fields = empty( $fields ) ? '*' : implode( ',', $fields );

	if ( $get_total ) {
		$fields = 'COUNT(*)';
	}

	return $wpdb->get_results(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT {$fields} FROM {$table} WHERE user_id = %d AND course_id = %d AND quiz_id = %d",
			$user_id,
			$course_id,
			$quiz_id
		),
		ARRAY_A
	);
}

function stm_lms_get_user_course_quizzes( $user_id, $course_id = null, $fields = array(), $status = 'passed', $quiz_ids = array() ) {
	global $wpdb;
	$table = stm_lms_user_quizzes_name( $wpdb );

	$fields = ( empty( $fields ) ) ? '*' : implode( ',', $fields );

	$course_condition = ( $course_id )
		? $wpdb->prepare(
			' AND course_id = %d',
			$course_id
		)
		: '';

	$quiz_condition = '';
	if ( ! empty( $quiz_ids ) ) {
		$placeholders   = implode( ',', array_fill( 0, count( $quiz_ids ), '%d' ) );
		$quiz_condition = " AND quiz_id IN ($placeholders)";
	}

	$params = array( $user_id, $status );
	if ( ! empty( $quiz_ids ) ) {
		$params = array_merge( $params, $quiz_ids );
	}

	$query = "SELECT {$fields} FROM {$table} WHERE user_id = %d AND status = %s {$course_condition}{$quiz_condition}";

	return $wpdb->get_results( $wpdb->prepare( $query, $params ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

function stm_lms_get_user_last_quiz( $user_id, $quiz_id, $fields = array(), $course_id = null ) {
	global $wpdb;
	$table = stm_lms_user_quizzes_name( $wpdb );

	$select_fields = empty( $fields ) ? 'main.*' : implode(
		', ',
		array_map(
			function ( $field ) {
				$field = esc_sql( $field );
				return str_starts_with( $field, 'main.' ) ? $field : "main.{$field}";
			},
			$fields
		)
	);

	$where  = array( 'main.user_id = %d', 'main.quiz_id = %d' );
	$params = array( $user_id, $quiz_id );

	if ( ! empty( $course_id ) ) {
		$where[]  = 'main.course_id = %d';
		$params[] = $course_id;
	}

	$attempt_where = array( 'sq.user_id = main.user_id', 'sq.quiz_id = main.quiz_id', 'sq.user_quiz_id <= main.user_quiz_id' );
	if ( ! empty( $course_id ) ) {
		$attempt_where[] = 'sq.course_id = main.course_id';
	}

	$query = sprintf(
		'SELECT %s, (SELECT COUNT(*) FROM %s AS sq WHERE %s) AS attempt_number FROM %s AS main WHERE %s ORDER BY main.user_quiz_id DESC LIMIT 1',
		$select_fields,
		$table,
		implode( ' AND ', $attempt_where ),
		$table,
		implode( ' AND ', $where )
	);

	return $wpdb->get_row( $wpdb->prepare( $query, $params ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

function stm_lms_search_courses_quizzes( $user_id, $search, $limit = '', $offset = '', $get_total = false ) {
	global $wpdb;

	$quizzes = stm_lms_user_quizzes_name( $wpdb );
	$answers = stm_lms_user_answers_name( $wpdb );
	$like    = '%' . $wpdb->esc_like( $search ) . '%';

	$join = "LEFT JOIN wp_posts quiz_post ON quiz_post.ID = q.quiz_id AND quiz_post.post_status = 'publish' AND quiz_post.post_type = %s
		LEFT JOIN wp_posts course_post ON course_post.ID = q.course_id AND course_post.post_status = 'publish' AND course_post.post_type = %s";

	$where = 'WHERE q.user_id = %d AND (
		quiz_post.post_title LIKE %s OR
		course_post.post_title LIKE %s
	)';

	$params = array( MasterStudy\Lms\Plugin\PostType::QUIZ, MasterStudy\Lms\Plugin\PostType::COURSE, $user_id, $like, $like );

	if ( $get_total ) {
		$sql = "SELECT COUNT(DISTINCT q.quiz_id, q.course_id) AS total_quizzes FROM {$quizzes} q {$join} {$where}";

		return (int) $wpdb->get_var( $wpdb->prepare( $sql, ...$params ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	$fields = 'q.quiz_id, q.course_id, q.user_quiz_id, q.progress, q.status, COUNT(DISTINCT a.attempt_number) AS attempts, COALESCE(a.questions_count, 0) AS questions_count';
	$join  .= " LEFT JOIN (
		SELECT a.quiz_id, a.course_id, a.attempt_number, COUNT(a.user_answer_id) AS questions_count
		FROM {$answers} a
		WHERE a.user_id = %d
		GROUP BY a.quiz_id, a.course_id, a.attempt_number
	) a ON a.quiz_id = q.quiz_id AND a.course_id = q.course_id";
	$join  .= " INNER JOIN (SELECT MAX(user_quiz_id) AS last_quiz_id FROM {$quizzes} WHERE user_id = %d GROUP BY quiz_id, course_id) AS latest ON latest.last_quiz_id = q.user_quiz_id";
	$sql    = "SELECT {$fields} FROM {$quizzes} q {$join} {$where} GROUP BY q.quiz_id, q.course_id";
	$params = array( MasterStudy\Lms\Plugin\PostType::QUIZ, MasterStudy\Lms\Plugin\PostType::COURSE, $user_id, $user_id, $user_id, $like, $like );

	if ( ! empty( $limit ) ) {
		$sql     .= ' LIMIT %d';
		$params[] = (int) $limit;
	}
	if ( ! empty( $offset ) ) {
		$sql     .= ' OFFSET %d';
		$params[] = (int) $offset;
	}

	return $wpdb->get_results( $wpdb->prepare( $sql, ...$params ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

function stm_lms_get_course_all_quizzes( $user_id, $search = '', $limit = '', $offset = '', $get_total = false ) {
	global $wpdb;

	$quizzes_table = stm_lms_user_quizzes_name( $wpdb );
	$answers_table = stm_lms_user_answers_name( $wpdb );

	$quiz_post_type   = MasterStudy\Lms\Plugin\PostType::QUIZ;
	$course_post_type = MasterStudy\Lms\Plugin\PostType::COURSE;

	$where = '';
	$join  = '';

	if ( '' !== $search ) {
		$like = '%' . $wpdb->esc_like( $search ) . '%';

		$join = "
			LEFT JOIN {$wpdb->posts} quiz_post
				ON quiz_post.ID = q.quiz_id
				AND quiz_post.post_status = 'publish'
				AND quiz_post.post_type = '{$quiz_post_type}'
			LEFT JOIN {$wpdb->posts} course_post
				ON course_post.ID = q.course_id
				AND course_post.post_status = 'publish'
				AND course_post.post_type = '{$course_post_type}'
		";

		$where = $wpdb->prepare( 'AND ( quiz_post.post_title LIKE %s OR course_post.post_title LIKE %s )', $like, $like );
	}

	if ( $get_total ) {
		$sql_count = "SELECT COUNT(DISTINCT q.course_id) FROM {$quizzes_table} q
			INNER JOIN {$wpdb->posts} p ON p.ID = q.quiz_id
			INNER JOIN {$wpdb->posts} pc ON pc.ID = q.course_id AND pc.post_type = %s
			{$join}
			WHERE q.user_id = %d AND p.post_type = %s AND p.post_status = 'publish' {$where}";

		return (int) $wpdb->get_var( $wpdb->prepare( $sql_count, $course_post_type, $user_id, $quiz_post_type ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	$sql_courses = "SELECT p.ID AS course_id, sub.max_id FROM {$quizzes_table} q
		INNER JOIN (
			SELECT quiz_id, course_id, MAX(user_quiz_id) AS max_id FROM {$quizzes_table}
			WHERE user_id = %d
			GROUP BY quiz_id, course_id
		) sub ON q.user_quiz_id = sub.max_id
		INNER JOIN {$wpdb->posts} p ON p.ID = q.course_id
		{$join}
		WHERE p.post_type = %s AND p.post_status = 'publish' {$where}
		GROUP BY p.ID
		ORDER BY sub.max_id DESC";

	$params_courses = array( $user_id, $course_post_type );
	if ( $limit ) {
		$sql_courses     .= ' LIMIT %d';
		$params_courses[] = (int) $limit;
	}
	if ( $offset ) {
		$sql_courses     .= ' OFFSET %d';
		$params_courses[] = (int) $offset;
	}

	$courses_data = $wpdb->get_results( $wpdb->prepare( $sql_courses, ...$params_courses ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	if ( empty( $courses_data ) ) {
		return array();
	}

	$course_ids    = wp_list_pluck( $courses_data, 'course_id' );
	$course_ids_in = implode( ',', array_map( 'absint', $course_ids ) );

	$sql_quizzes = "SELECT p.ID AS course_id,
			q.user_quiz_id, q.quiz_id, q.status AS quiz_status, q.progress,
			sub.attempts AS attempts_count,
			a.questions_count,
			sub.max_id
		FROM {$quizzes_table} q
		INNER JOIN (
			SELECT quiz_id, course_id, MAX(user_quiz_id) AS max_id, COUNT(*) AS attempts FROM {$quizzes_table}
			WHERE user_id = %d
			GROUP BY quiz_id, course_id
		) sub
			ON q.user_quiz_id = sub.max_id
		LEFT JOIN (
			SELECT a.quiz_id, a.course_id, COUNT(a.user_answer_id) AS questions_count FROM {$answers_table} a
			WHERE a.user_id = %d
				AND a.attempt_number = (
					SELECT MAX(a2.attempt_number) FROM {$answers_table} a2
					WHERE a2.user_id   = a.user_id AND a2.quiz_id   = a.quiz_id AND a2.course_id = a.course_id
				)
			GROUP BY a.quiz_id, a.course_id
		) a ON a.quiz_id   = q.quiz_id AND a.course_id = q.course_id
		INNER JOIN {$wpdb->posts} p ON p.ID = q.course_id
		{$join}
		WHERE p.post_type = %s AND p.post_status = 'publish' AND p.ID IN ({$course_ids_in}) {$where}
		ORDER BY sub.max_id DESC";

	$params_quizzes  = array( $user_id, $user_id, $course_post_type );
	$quizzes_data    = $wpdb->get_results( $wpdb->prepare( $sql_quizzes, ...$params_quizzes ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$grouped_quizzes = array();

	if ( ! empty( $quizzes_data ) ) {
		foreach ( $quizzes_data as $qd ) {
			$c_id = (int) $qd['course_id'];

			if ( ! isset( $grouped_quizzes[ $c_id ] ) ) {
				$grouped_quizzes[ $c_id ] = array();
			}

			$grouped_quizzes[ $c_id ][] = array(
				'user_quiz_id'    => (int) $qd['user_quiz_id'],
				'quiz_id'         => (int) $qd['quiz_id'],
				'quiz_status'     => $qd['quiz_status'],
				'progress'        => (int) $qd['progress'],
				'attempts_count'  => (int) $qd['attempts_count'],
				'questions_count' => (int) $qd['questions_count'],
			);
		}
	}

	$result = array();
	foreach ( $courses_data as $cd ) {
		$c_id = (int) $cd['course_id'];

		$quizzes_array = array();
		if ( ! empty( $grouped_quizzes[ $c_id ] ) ) {
			$quizzes_array = $grouped_quizzes[ $c_id ];
		}

		$result[] = array(
			'course_id' => $c_id,
			'quizzes'   => $quizzes_array,
		);
	}

	return $result;
}

function stm_lms_get_quiz_last_attempt( $user_id, $course_id, $quiz_id ) {
	if ( ! $user_id || ! $course_id || ! $quiz_id ) {
		return false;
	}

	global $wpdb;

	$quizzes = stm_lms_user_quizzes_name( $wpdb );
	$answers = stm_lms_user_answers_name( $wpdb );

	$attempts_subquery = "SELECT q1.*, ( SELECT COUNT(*) FROM {$quizzes} q2
		WHERE q2.user_id = q1.user_id AND q2.course_id = q1.course_id AND q2.quiz_id = q1.quiz_id AND q2.user_quiz_id <= q1.user_quiz_id ) AS attempt_number
		FROM {$quizzes} q1 WHERE q1.user_id = %d AND q1.course_id = %d AND q1.quiz_id = %d";

	$sql = "SELECT attempts.*, COALESCE(SUM(CASE WHEN a.correct_answer = 1 THEN 1 ELSE 0 END), 0) AS correct, COALESCE(SUM(CASE WHEN a.correct_answer = 0 THEN 1 ELSE 0 END), 0) AS incorrect
	FROM ( {$attempts_subquery} ) AS attempts
	LEFT JOIN {$answers} a ON a.user_id = attempts.user_id AND a.course_id = attempts.course_id AND a.quiz_id = attempts.quiz_id AND a.attempt_number = attempts.attempt_number
	GROUP BY attempts.user_quiz_id
	ORDER BY attempts.user_quiz_id DESC
	LIMIT 1";

	$params = array( $user_id, $course_id, $quiz_id );

	$result = $wpdb->get_row( $wpdb->prepare( $sql, ...$params ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	return $result ? $result : false;
}

function stm_lms_get_quiz_all_attempts( $user_id, $course_id, $quiz_id, $limit = '', $offset = '', $get_total = false ) {
	if ( ! $user_id || ! $course_id || ! $quiz_id ) {
		return array();
	}

	global $wpdb;

	$quizzes = stm_lms_user_quizzes_name( $wpdb );
	$answers = stm_lms_user_answers_name( $wpdb );

	if ( $get_total ) {
		$sql = "SELECT COUNT(*) FROM ( SELECT user_quiz_id FROM {$quizzes} WHERE user_id = %d AND course_id = %d AND quiz_id = %d ) AS total_attempts";

		return (int) $wpdb->get_var( $wpdb->prepare( $sql, $user_id, $course_id, $quiz_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	$attempts_subquery = "SELECT q1.*, ( SELECT COUNT(*) FROM {$quizzes} q2
	WHERE q2.user_id = q1.user_id AND q2.course_id = q1.course_id AND q2.quiz_id = q1.quiz_id AND q2.user_quiz_id <= q1.user_quiz_id ) AS attempt_number
	FROM {$quizzes} q1 WHERE q1.user_id = %d AND q1.course_id = %d AND q1.quiz_id = %d";

	$sql = "SELECT attempts.*, COALESCE(SUM(CASE WHEN a.correct_answer = 1 THEN 1 ELSE 0 END), 0) AS correct, COALESCE(SUM(CASE WHEN a.correct_answer = 0 THEN 1 ELSE 0 END), 0) AS incorrect
	FROM ( {$attempts_subquery} ) AS attempts
	LEFT JOIN {$answers} a ON a.user_id = attempts.user_id AND a.course_id = attempts.course_id AND a.quiz_id = attempts.quiz_id AND a.attempt_number = attempts.attempt_number
	GROUP BY attempts.user_quiz_id
	ORDER BY attempts.user_quiz_id DESC";

	$params = array( $user_id, $course_id, $quiz_id );

	if ( ! empty( $limit ) ) {
		$sql     .= ' LIMIT %d';
		$params[] = (int) $limit;
	}
	if ( ! empty( $offset ) ) {
		$sql     .= ' OFFSET %d';
		$params[] = (int) $offset;
	}

	return $wpdb->get_results( $wpdb->prepare( $sql, ...$params ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

function stm_lms_attempts_exists( $course_id, $quiz_id, $user_id ) {
	global $wpdb;

	$table = stm_lms_user_quizzes_name( $wpdb );

	$sql = "SELECT 1
		FROM {$table} q
		WHERE q.user_id = %d
		  AND q.course_id = %d
		  AND q.quiz_id = %d
		  AND EXISTS (
			SELECT 1 FROM {$wpdb->posts} p1 WHERE p1.ID = q.quiz_id
		  )
		  AND EXISTS (
			SELECT 1 FROM {$wpdb->posts} p2 WHERE p2.ID = q.course_id
		  )
		LIMIT 1";

	return (bool) $wpdb->get_var( $wpdb->prepare( $sql, $user_id, $course_id, $quiz_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

function stm_lms_get_attempt( int $attempt_id, int $user_id, int $quiz_id = 0, int $course_id = 0 ): array {
	if ( ! $attempt_id || ! $user_id || ! $quiz_id || ! $course_id ) {
		return array();
	}

	global $wpdb;

	$quizzes = esc_sql( stm_lms_user_quizzes_name( $wpdb ) );
	$answers = esc_sql( stm_lms_user_answers_name( $wpdb ) );

	$request = "SELECT q.*, t.attempt_number FROM {$quizzes} AS q
		CROSS JOIN (
			SELECT COUNT(*) AS attempt_number FROM {$quizzes}
			WHERE user_id = %d AND quiz_id = %d AND course_id = %d AND user_quiz_id <= %d
		) AS t
		WHERE q.user_quiz_id = %d
		LIMIT 1";

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$result = $wpdb->get_row( $wpdb->prepare( $request, $user_id, $quiz_id, $course_id, $attempt_id, $attempt_id ), ARRAY_A );

	if ( empty( $result ) ) {
		return array();
	}

	$answers_request = "SELECT question_id, user_answer, correct_answer, questions_order FROM {$answers}
		WHERE user_id = %d AND quiz_id = %d AND course_id = %d AND attempt_number = %d";

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$answers_data  = $wpdb->get_results( $wpdb->prepare( $answers_request, $user_id, $quiz_id, $course_id, $result['attempt_number'] ), ARRAY_A );
	$answers_array = array();

	if ( ! empty( $answers_data ) ) {
		foreach ( $answers_data as $answer_row ) {
			if ( isset( $answer_row['question_id'] ) ) {
				$answers_array[ $answer_row['question_id'] ] = array(
					'question_id'     => $answer_row['question_id'],
					'user_answer'     => $answer_row['user_answer'],
					'correct_answer'  => $answer_row['correct_answer'],
					'questions_order' => $answer_row['questions_order'],
				);
			}
		}
	}

	$result['answers'] = $answers_array;

	return $result;
}

function stm_lms_get_course_passed_quizzes( $course_id, $fields = array() ) {
	global $wpdb;
	$table = stm_lms_user_quizzes_name( $wpdb );

	$fields = ( empty( $fields ) ) ? '*' : implode( ',', $fields );

	return $wpdb->get_results(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT {$fields} FROM {$table} WHERE course_id = %d AND status = 'passed'",
			$course_id
		),
		ARRAY_A
	);
}

function stm_lms_check_quiz( $user_id, $quiz_id, $fields = array() ) {
	global $wpdb;
	$table = stm_lms_user_quizzes_name( $wpdb );

	$fields = ( empty( $fields ) ) ? '*' : implode( ',', $fields );

	return $wpdb->get_results(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT {$fields} FROM {$table} WHERE  status = 'passed' AND user_id = %d AND quiz_id = %d",
			$user_id,
			$quiz_id
		),
		ARRAY_A
	);
}

function stm_lms_delete_user_quiz( $user_id, $course_id, $quiz_id ) {
	global $wpdb;
	$table = stm_lms_user_quizzes_name( $wpdb );

	$wpdb->delete(
		$table,
		array(
			'user_id'   => $user_id,
			'course_id' => $course_id,
			'quiz_id'   => $quiz_id,
		)
	);

	do_action( 'masterstudy_lms_user_quiz_deleted', $user_id, $course_id, $quiz_id );
}
