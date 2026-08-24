<?php

use MasterStudy\Lms\Repositories\CurriculumRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function stm_lms_add_user_course( $user_course ) {
	global $wpdb;
	$table_name = stm_lms_user_courses_name( $wpdb );

	$wpdb->insert(
		$table_name,
		$user_course
	);
}

function stm_lms_get_user_course( $user_id, $course_id, $fields = array(), $enterprise = '' ) {
	global $wpdb;
	$table = stm_lms_user_courses_name( $wpdb );

	$fields = ( empty( $fields ) ) ? '*' : implode( ',', $fields );

	$query = "SELECT {$fields} FROM {$table} WHERE user_id = %d AND course_id = %d";

	$params = array( $user_id, $course_id );

	if ( ! empty( $enterprise ) ) {
		$query   .= ' AND enterprise_id = %d';
		$params[] = $enterprise;
	}

	$query .= ' LIMIT 1';

	return $wpdb->get_results( $wpdb->prepare( $query, $params ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}


function masterstudy_lms_get_user_course_membership( $user_id, $course_id, $enterprise = '' ) {
	global $wpdb;

	$table = stm_lms_user_courses_name( $wpdb );

	$sql    = "SELECT subscription_id, user_id, course_id
	            FROM {$table}
	            WHERE user_id = %d AND course_id = %d";
	$params = array( (int) $user_id, (int) $course_id );

	if ( ! empty( $enterprise ) ) {
		$sql     .= ' AND enterprise_id = %d';
		$params[] = (int) $enterprise;
	}

	// If multiples exist, prefer the latest record.
	$sql .= ' ORDER BY user_course_id DESC LIMIT 1';

	$prepared = $wpdb->prepare( $sql, $params ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$row      = $wpdb->get_row( $prepared, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	if ( is_array( $row ) && ! empty( $row ) ) {
		return $row;
	}

	return null;
}

function masterstudy_lms_update_user_course_membership( $user_id, $course_id, $subscription_id ) {
	global $wpdb;

	$table = stm_lms_user_courses_name( $wpdb );

	$updated = $wpdb->update(
		$table,
		array( 'subscription_id' => (int) $subscription_id ),
		array(
			'user_id'   => (int) $user_id,
			'course_id' => (int) $course_id,
		),
		array( '%d' ),
		array( '%d', '%d' )
	);

	return ( false !== $updated && $updated > 0 );
}

function stm_lms_get_course_id_by_user_course_id( $user_course_id ) {
	global $wpdb;
	$table = stm_lms_user_courses_name( $wpdb );

	return $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT course_id FROM {$table} WHERE user_course_id = %d",
			$user_course_id
		)
	);
}

function stm_lms_update_start_time_in_user_course( $user_id, $course_id ) {
	global $wpdb;

	$table_name = stm_lms_user_courses_name( $wpdb );

	$wpdb->update(
		$table_name,
		array( 'start_time' => time() ),
		array(
			'user_id'   => $user_id,
			'course_id' => $course_id,
		),
		array( '%d' ),
		array( '%d', '%d' )
	);
}

function stm_lms_get_user_completed_courses( $user_id, $fields = array(), $limit = 1 ) {
	global $wpdb;

	$table = stm_lms_user_courses_name( $wpdb );

	$fields = ( empty( $fields ) ) ? '*' : implode( ',', $fields );

	$threshold = STM_LMS_Options::get_option( 'certificate_threshold', 70 );

	$request = $wpdb->prepare(
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		"SELECT {$fields} FROM {$table} WHERE user_ID = %d AND progress_percent >= %d",
		$user_id,
		$threshold
	);

	if ( -1 !== $limit ) {
		$request .= $wpdb->prepare( ' LIMIT %d', $limit );
	}

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	return $wpdb->get_results( $request, ARRAY_A );
}

function stm_lms_get_course_users( $course_id, $fields = array() ) {
	global $wpdb;
	$table = stm_lms_user_courses_name( $wpdb );

	$fields = ( empty( $fields ) ) ? '*' : implode( ',', $fields );

	return $wpdb->get_results(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT {$fields} FROM {$table} WHERE course_id = %d",
			$course_id
		),
		ARRAY_A
	);
}

function stm_lms_get_user_courses_by_subscription( $user_id, $subscription_id, $fields = array(), $limit = 1, $order_by = '' ) {
	global $wpdb;
	$table = stm_lms_user_courses_name( $wpdb );

	$fields = ( empty( $fields ) ) ? '*' : implode( ',', $fields );

	$subs = ( '*' !== $subscription_id )
		? $wpdb->prepare( 'subscription_id = %d', $subscription_id )
		: 'subscription_id > 0';

	$request = $wpdb->prepare(
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		"SELECT {$fields} FROM {$table} WHERE user_ID = %d AND {$subs}",
		$user_id
	);

	if ( ! empty( $order_by ) ) {
		$request .= $wpdb->prepare( ' ORDER BY %s', $order_by );
	}
	if ( ! empty( $limit ) ) {
		$request .= $wpdb->prepare( ' LIMIT %d', $limit );
	}

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	return $wpdb->get_results( $request, ARRAY_A );
}

function stm_lms_get_user_courses( $user_id, $limit = '', $offset = '', $fields = array(), $get_total = false, $courses = '', $sort = '' ) {
	global $wpdb;
	$table = stm_lms_user_courses_name( $wpdb );

	$fields = ( empty( $fields ) ) ? '*' : implode( ',', $fields );

	if ( $get_total ) {
		$fields = 'COUNT(*)';
	}

	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		$courses = $wpdb->prepare( ' AND lng_code=%s ', get_locale() ) . $courses;
	}

	if ( empty( $sort ) ) {
		$sort = 'ORDER BY user_course_id DESC';
	}

	$request = $wpdb->prepare(
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		"SELECT {$fields} FROM {$table} WHERE user_id = %d {$courses} {$sort}",
		$user_id
	);

	if ( ! empty( $limit ) ) {
		$request .= $wpdb->prepare( ' LIMIT %d', $limit );
	}
	if ( ! empty( $offset ) ) {
		$request .= $wpdb->prepare( ' OFFSET %d', $offset );
	}

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	return $wpdb->get_results( $request, ARRAY_A );
}

function stm_lms_update_user_course_progress( $user_course_id, $progress, $reset = false ) {
	global $wpdb;
	$table = stm_lms_user_courses_name( $wpdb );

	$progress_args = array( 'progress_percent' => $progress );

	if ( $reset ) {
		$progress_args['current_lesson_id'] = 0;
		$progress_args['end_time']          = 0;
	}

	$wpdb->update(
		$table,
		$progress_args,
		array( 'user_course_id' => $user_course_id ),
		$reset ? array( '%d', '%d' ) : array( '%d' ),
		array( '%d' )
	);
}

function stm_lms_update_user_course_endtime( $user_course_id, $endtime ) {
	global $wpdb;
	$table = stm_lms_user_courses_name( $wpdb );

	$wpdb->update(
		$table,
		array( 'end_time' => $endtime ),
		array( 'user_course_id' => $user_course_id ),
		array( '%d' ),
		array( '%d' )
	);
}

function stm_lms_get_delete_user_course( $user_id, $item_id ) {
	do_action( 'masterstudy_lms_before_delete_user_course', $user_id, $item_id );
	global $wpdb;
	$table = stm_lms_user_courses_name( $wpdb );

	$wpdb->delete(
		$table,
		array(
			'user_id'   => $user_id,
			'course_id' => $item_id,
		)
	);

	$course_id  = $item_id;
	$student_id = $user_id;

	$curriculum = ( new CurriculumRepository() )->get_curriculum( $course_id );

	if ( empty( $curriculum['materials'] ) ) {
		die;
	}

	$user_manage_class = new STM_LMS_User_Manager_Course_User();
	foreach ( $curriculum['materials'] as $material ) {
		switch ( $material['post_type'] ) {
			case 'stm-lessons':
				$user_manage_class::reset_lesson( $student_id, $course_id, $material['post_id'] );
				break;
			case 'stm-assignments':
				$user_manage_class::reset_assignment( $student_id, $course_id, $material['post_id'] );
				break;
			case 'stm-quizzes':
				$user_manage_class::reset_quiz( $student_id, $course_id, $material['post_id'] );
				break;
		}
	}

	stm_lms_reset_user_answers( $item_id, $user_id );
	stm_lms_reset_marker_answers( $item_id, $user_id );
	do_action( 'masterstudy_lms_after_delete_user_course', $user_id, $item_id );
}

function stm_lms_get_delete_user_courses( $user_id ) {
	global $wpdb;
	$table = stm_lms_user_courses_name( $wpdb );

	$wpdb->delete(
		$table,
		array(
			'user_id' => $user_id,
		)
	);
}

/**
 * @param array $user_ids
 *
 * @return array
 */
function stm_lms_delete_users_in_courses( array $user_ids ): array {
	global $wpdb;

	if ( empty( $user_ids ) ) {
		return array(
			'data'            => array(),
			'total'           => 0,
			'lessons_deleted' => 0,
		);
	}

	$user_ids      = array_map( 'absint', $user_ids );
	$is_admin      = current_user_can( 'administrator' );
	$author_filter = $is_admin ? '' : 'AND p.post_author = %d';

	$courses_table = stm_lms_user_courses_name( $wpdb );
	$lessons_table = stm_lms_user_lessons_name( $wpdb );
	$quizzes_table = stm_lms_user_quizzes_name( $wpdb );
	$answers_table = stm_lms_user_answers_name( $wpdb );

	$placeholders = implode( ',', array_fill( 0, count( $user_ids ), '%d' ) );
	$params       = $is_admin ? $user_ids : array_merge( $user_ids, array( (int) get_current_user_id() ) );

	$delete_quizzes_sql = "
	DELETE uq
	FROM {$quizzes_table} AS uq
	INNER JOIN {$wpdb->posts} AS p ON uq.course_id = p.ID
	WHERE uq.user_id IN ( {$placeholders} )
		{$author_filter}
	";

	$delete_answers_sql = "
	DELETE ua
	FROM {$answers_table} AS ua
	INNER JOIN {$wpdb->posts} AS p ON ua.course_id = p.ID
	WHERE ua.user_id IN ( {$placeholders} )
		{$author_filter}
	";

	$select_sql = "
		SELECT uc.user_id, uc.course_id
		FROM {$courses_table} AS uc
		INNER JOIN {$wpdb->posts} AS p ON uc.course_id = p.ID
		WHERE uc.user_id IN ( {$placeholders} )
			{$author_filter}
	";

	$delete_lessons_sql = "
		DELETE ul
		FROM {$lessons_table} AS ul
		INNER JOIN {$courses_table} AS uc
			ON ul.user_id = uc.user_id
			AND ul.course_id = uc.course_id
		INNER JOIN {$wpdb->posts} AS p ON uc.course_id = p.ID
		WHERE uc.user_id IN ( {$placeholders} )
			{$author_filter}
	";

	$delete_courses_sql = "
		DELETE uc
		FROM {$courses_table} AS uc
		INNER JOIN {$wpdb->posts} AS p ON uc.course_id = p.ID
		WHERE uc.user_id IN ( {$placeholders} )
			{$author_filter}
	";

	$data = $wpdb->get_results(
		$wpdb->prepare( $select_sql, ...$params ), // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		ARRAY_A
	);

	$lessons_deleted = (int) $wpdb->query(
		$wpdb->prepare( $delete_lessons_sql, ...$params ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	);

	$answers_deleted = (int) $wpdb->query(
		$wpdb->prepare( $delete_answers_sql, ...$params ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	);

	$quizzes_deleted = (int) $wpdb->query(
		$wpdb->prepare( $delete_quizzes_sql, ...$params ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	);

	$total = (int) $wpdb->query(
		$wpdb->prepare( $delete_courses_sql, ...$params ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	);

	return array(
		'data'            => $data,
		'total'           => $total,
		'lessons_deleted' => $lessons_deleted,
		'quizzes_deleted' => $quizzes_deleted,
		'answers_deleted' => $answers_deleted,
	);
}

function stm_lms_get_delete_courses( $course_id ) {
	global $wpdb;
	$table = stm_lms_user_courses_name( $wpdb );

	$wpdb->delete(
		$table,
		array(
			'course_id' => $course_id,
		)
	);
}

function stm_lms_update_user_current_lesson( $course_id, $item_id, $user_id ) {
	global $wpdb;

	$wpdb->update(
		stm_lms_user_courses_name( $wpdb ),
		array( 'current_lesson_id' => $item_id ),
		array(
			'user_id'   => $user_id,
			'course_id' => $course_id,
		),
		array( '%d' ),
		array( '%d' )
	);
}

/**
 * @param string $search
 * @param int $course_id
 * @param string $date_from
 * @param string $date_to
 * @param string $order
 * @param string $orderby
 *
 * @return array
 */
function stm_lms_build_enrolled_query_parts( string $search = '', int $course_id = 0, string $date_from = '', string $date_to = '', string $order = '', string $orderby = '' ): array {
	global $wpdb;

	$courses_table  = stm_lms_user_courses_name( $wpdb );
	$points_enabled = is_ms_lms_addon_enabled( 'point_system' );
	$points_table   = $points_enabled ? stm_lms_point_system_name( $wpdb ) : '';

	$params = ! current_user_can( 'administrator' ) ? array( get_current_user_id() ) : array();
	$where  = array();

	if ( $course_id > 0 ) {
		$where[]       = 'c.course_id = %d';
		$params[]      = $course_id;
		$params[]      = $course_id;
		$course_filter = 'AND c.course_id = %d';
	} else {
		$course_filter = '';
	}

	if ( '' !== $search ) {
		$like    = '%' . $wpdb->esc_like( $search ) . '%';
		$where[] =
			"(u.display_name LIKE %s
             OR u.user_email LIKE %s
             OR u.user_login LIKE %s
             OR u.user_nicename LIKE %s
             OR EXISTS (
                SELECT 1
                FROM {$wpdb->usermeta} um
                WHERE um.user_id = u.ID
                  AND um.meta_key IN('first_name','last_name')
                  AND um.meta_value LIKE %s
             )
            )";
		$params  = array_merge( $params, array_fill( 0, 5, $like ) );
	}

	if ( current_user_can( 'administrator' ) ) {
		$join_courses = "LEFT JOIN (
            SELECT c.user_id, c.course_id, COUNT(*) AS enrolled
            FROM {$courses_table} c
            INNER JOIN {$wpdb->posts} p ON p.ID = c.course_id
            WHERE 1=1 {$course_filter}
            GROUP BY c.user_id
        ) c ON c.user_id = u.ID";

		$join_courses_export = "LEFT JOIN {$courses_table} c ON c.user_id = u.ID
        LEFT JOIN {$wpdb->posts} p ON p.ID = c.course_id {$course_filter}";

		if ( is_multisite() ) {
			$join_user_meta       = " INNER JOIN {$wpdb->usermeta} AS um ON um.user_id = u.ID AND um.meta_key = %s";
			$join_courses        .= $join_user_meta;
			$join_courses_export .= $join_user_meta;
			$params[]             = $wpdb->get_blog_prefix() . 'capabilities';
		}
	} else {
		$join_courses = "INNER JOIN (
            SELECT c.user_id, c.course_id, COUNT(*) AS enrolled
            FROM {$courses_table} c
            INNER JOIN {$wpdb->posts} p ON p.ID = c.course_id AND p.post_author = %d
            WHERE 1=1 {$course_filter}
            GROUP BY c.user_id
        ) c ON c.user_id = u.ID";

		$join_courses_export = "INNER JOIN {$courses_table} c ON c.user_id = u.ID
        INNER JOIN {$wpdb->posts} p ON p.ID = c.course_id AND p.post_author = %d {$course_filter}";
	}

	if ( $date_from && $date_to ) {
		$where[]  = 'u.user_registered BETWEEN %s AND %s';
		$params[] = $date_from;
		$params[] = $date_to;
	}

	$join_points = $points_enabled
		? "LEFT JOIN (
               SELECT user_id, SUM(score) AS points
               FROM {$points_table}
               GROUP BY user_id
           ) p ON p.user_id = u.ID"
		: '';

	// Validate and sanitize orderby parameter to prevent SQL injection
	$allowed_orderby_fields = array(
		'joined'   => 'u.user_registered',
		'enrolled' => 'c.enrolled',
		'points'   => 'p.points',
		'id'       => 'u.ID',
		'name'     => 'u.display_name',
		'email'    => 'u.user_email',
		'login'    => 'u.user_login',
	);

	// Default to safe field if orderby is not in whitelist
	$safe_orderby = 'u.user_registered';
	if ( ! empty( $orderby ) && isset( $allowed_orderby_fields[ $orderby ] ) ) {
		$safe_orderby = $allowed_orderby_fields[ $orderby ];
	}

	// Validate and sanitize order parameter
	$safe_order = 'DESC';
	if ( ! empty( $order ) ) {
		$order_upper = strtoupper( $order );
		if ( in_array( $order_upper, array( 'ASC', 'DESC' ), true ) ) {
			$safe_order = $order_upper;
		}
	}

	$order_by = "ORDER BY {$safe_orderby} {$safe_order}";

	$select = array( 'u.ID', 'c.enrolled' );
	if ( $points_enabled ) {
		$select[] = 'COALESCE(p.points, 0) AS points';
	}

	$select_sql = $select ? implode( ', ', $select ) : '';
	$where_sql  = $where ? 'WHERE ' . implode( ' AND ', $where ) : '';

	return compact( 'join_courses', 'join_courses_export', 'join_points', 'where_sql', 'params', 'select_sql', 'order_by' );
}

/**
 * @param string $search
 * @param int $course_id
 * @param string $date_from
 * @param string $date_to
 * @param string $order
 * @param string $orderby
 *
 * @return int
 */
function stm_lms_get_users_enrolled_count( string $search = '', int $course_id = 0, string $date_from = '', string $date_to = '' ): int {
	global $wpdb;

	$parts = stm_lms_build_enrolled_query_parts( $search, $course_id, $date_from, $date_to );
	$sql   = "SELECT COUNT(DISTINCT u.ID) FROM {$wpdb->users} u {$parts['join_courses']} {$parts['where_sql']}";

	return (int) $wpdb->get_var( $wpdb->prepare( $sql, ...$parts['params'] ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

/**
 *
 * @param string $search
 * @param int $course_id
 * @param string $date_from
 * @param string $date_to
 * @param string $order
 * @param string $orderby
 * @param int $limit
 * @param int $offset
 *
 * @return array
 */
function stm_lms_get_users_enrolled_list( string $search = '', int $course_id = 0, string $date_from = '', string $date_to = '', string $order = '', string $orderby = '', int $limit = 10, int $offset = 0 ): array {
	global $wpdb;

	$parts = stm_lms_build_enrolled_query_parts( $search, $course_id, $date_from, $date_to, $order, $orderby );

	$sql = "SELECT {$parts['select_sql']}
            FROM {$wpdb->users} u
            {$parts['join_courses']}
            {$parts['join_points']}
            {$parts['where_sql']}
            {$parts['order_by']}";

	$params = $parts['params'];

	if ( -1 !== $limit ) {
		$sql     .= ' LIMIT %d';
		$params[] = $limit;
	}
	if ( $offset ) {
		$sql     .= ' OFFSET %d';
		$params[] = $offset;
	}

	return $wpdb->get_results( $wpdb->prepare( $sql, ...$params ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

/**
 * @param string $search
 * @param int $course_id
 * @param string $date_from
 * @param string $date_to
 * @param string $order
 * @param string $orderby
 * @param int $limit
 * @param int $offset
 *
 * @return array
 */
function stm_lms_get_users_enrolled_export( string $search = '', int $course_id = 0, string $date_from = '', string $date_to = '', string $order = '', string $orderby = '', int $limit = 10, int $offset = 0 ): array {
	global $wpdb;

	$parts = stm_lms_build_enrolled_query_parts( $search, $course_id, $date_from, $date_to, $order, $orderby );

	$sql_ids = "SELECT DISTINCT u.ID
                FROM {$wpdb->users} u
                {$parts['join_courses_export']}
                {$parts['where_sql']}
                {$parts['order_by']}";

	$params_ids = $parts['params'];
	if ( -1 !== $limit ) {
		$sql_ids     .= ' LIMIT %d';
		$params_ids[] = $limit;
	}
	if ( $offset ) {
		$sql_ids     .= ' OFFSET %d';
		$params_ids[] = $offset;
	}

	$user_ids = $wpdb->get_col( $wpdb->prepare( $sql_ids, ...$params_ids ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	if ( empty( $user_ids ) ) {
		return array();
	}

	$courses_table = stm_lms_user_courses_name( $wpdb );
	$placeholders  = implode( ',', array_fill( 0, count( $user_ids ), '%d' ) );
	$course_filter = $course_id > 0 ? 'c.course_id = %d AND' : '';

	$sql_courses = "SELECT c.user_id, c.course_id
                    FROM {$courses_table} c
                    INNER JOIN {$wpdb->posts} p ON p.ID = c.course_id AND p.post_author = %d
                    WHERE {$course_filter} c.user_id IN({$placeholders})
                    GROUP BY c.course_id, c.user_id";

	$course_params = array( get_current_user_id() );
	if ( $course_id > 0 ) {
		$course_params[] = $course_id;
	}
	$course_params = array_merge( $course_params, $user_ids );
	$rows          = $wpdb->get_results( $wpdb->prepare( $sql_courses, ...$course_params ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	$grouped = array();
	foreach ( $rows as $row ) {
		$grouped[ $row['user_id'] ][] = $row['course_id'];
	}

	return array_map(
		fn( $uid ) => array(
			'ID'      => $uid,
			'courses' => $grouped[ $uid ] ?? array(),
		),
		$user_ids
	);
}
