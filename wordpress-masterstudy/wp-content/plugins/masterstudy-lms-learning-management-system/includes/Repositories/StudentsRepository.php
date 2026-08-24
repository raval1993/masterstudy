<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Repositories\CurriculumMaterialRepository;
use MasterStudy\Lms\Plugin\PostType;
use STM_LMS_Helpers;

final class StudentsRepository {

	public function get_course_students( array $params = array() ): array {
		global $wpdb;

		$course_table = stm_lms_user_courses_name( $wpdb );
		$user_table   = $wpdb->users;
		$fields       = "{$course_table}.user_id, {$course_table}.course_id, {$course_table}.start_time, {$course_table}.progress_percent, {$user_table}.display_name";
		$per_page     = $params['per_page'] ?? 10;
		$page         = $params['page'] ?? 1;
		$course_id    = $params['course_id'] ?? 0;
		$offset       = ( $page - 1 ) * $per_page;
		$filtering    = '';

		if ( ! empty( $params['order'] ) && ! empty( $params['orderby'] ) ) {
			$order = strtoupper( $params['order'] );

			if ( in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
				switch ( $params['orderby'] ) {
					case 'username':
						$filtering .= " ORDER BY {$user_table}.display_name {$order}";
						break;
					case 'email':
						$filtering .= " ORDER BY {$user_table}.user_email {$order}";
						break;
					case 'ago':
						$filtering .= " ORDER BY {$course_table}.start_time {$order}";
						break;
					case 'progress_percent':
						$filtering .= " ORDER BY {$course_table}.progress_percent {$order}";
						break;
				}
			}
		}

		$total_query = "SELECT COUNT(*) FROM {$course_table}
			INNER JOIN $user_table
			ON {$course_table}.user_id = {$user_table}.ID
			WHERE {$course_table}.course_id = %d";

		if ( ! empty( $params['s'] ) ) {
			$search_term   = '%' . strtolower( $params['s'] ) . '%';
			$search_string = $wpdb->prepare(
				' AND (LOWER(display_name) LIKE %s OR LOWER(user_email) LIKE %s)',
				$search_term,
				$search_term
			);
			$filtering    .= $search_string;
			$total_query  .= $search_string;
		}

		$base_query = "SELECT {$fields} FROM {$course_table}
			INNER JOIN $user_table
			ON {$course_table}.user_id = {$user_table}.ID
			WHERE {$course_table}.course_id = %d {$filtering}";

		$students = $wpdb->get_results(
			$wpdb->prepare(
				// phpCS:ignore WordPress.DB.PreparedSQL.NotPrepared
				$base_query,
				$course_id
			),
			ARRAY_A
		);

		foreach ( $students as &$data ) {
			$data                  = ( new \STM_LMS_User_Manager_Course() )->map_students( $data );
			$data['student']       = $this->normalize_student_avatar_data( $data['student'] );
			$student_id            = $data['user_id'];
			$data['progress_link'] = \STM_LMS_Instructor::instructor_manage_students_url() . "/?course_id=$course_id&student_id=$student_id";
		}

		$total = $wpdb->get_var(
			$wpdb->prepare(
				// phpCS:ignore WordPress.DB.PreparedSQL.NotPrepared
				$total_query,
				$course_id
			)
		);

		if ( ! empty( $params['subscribed'] ) ) {
			$subscribed = self::get_subscribed_users( $students, $total, $params );
			$students   = $subscribed['students'];
			$total      = $subscribed['total'];
		}

		if ( ! empty( $params['orderby'] ) ) {
			$order    = strtoupper( $params['order'] ?? 'ASC' );
			$students = $this->sort_students( $students, $params, $order );
		}

		$students = array_slice( $students, $offset, $per_page );
		$output   = array(
			'students'     => $students,
			'page'         => $page,
			'total'        => $total,
			'per_page'     => $per_page,
			'max_pages'    => ceil( $total / $per_page ),
			'course_title' => get_the_title( $course_id ),
		);

		return $output;
	}

	public function get_all_students( array $params = array() ): array {
		$per_page  = $params['per_page'] ?? get_option( 'posts_per_page', 10 );
		$page      = $params['page'] ?? 1;
		$search    = $params['s'] ?? '';
		$course_id = $params['course_id'] ?? 0;
		$date_from = $params['date_from'] ? wp_date( 'Y-m-d H:i:s', strtotime( $params['date_from'] ) ) : '';
		$date_to   = $params['date_to'] ? wp_date( 'Y-m-d H:i:s', strtotime( $params['date_to'] . ' 23:59:59' ) ) : '';
		$order     = $params['order'] ?? '';
		$orderby   = $params['orderby'] ?? '';
		$offset    = ( $page - 1 ) * $per_page;

		$students = stm_lms_get_users_enrolled_list( $search, $course_id, $date_from, $date_to, $order, $orderby, $per_page, $offset );
		$total    = stm_lms_get_users_enrolled_count( $search, $course_id, $date_from, $date_to );

		if ( ! empty( $students ) ) {
			foreach ( $students as &$student ) {
				$user = get_userdata( $student['ID'] );

				if ( ! $user->exists() ) {
					continue;
				}

				$name         = trim( "{$user->first_name} {$user->last_name}" );
				$display_name = '' !== $name ? $name : $user->data->display_name;

				$referer  = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
				$is_admin = strpos( $referer, '/wp-admin/' ) !== false;
				$url      = '';

				if ( STM_LMS_Helpers::is_pro_plus() ) {
					$url = $is_admin ? admin_url( "admin.php?page=manage_students&user_id=$user->ID&role=student" ) : ms_plugin_user_account_url( "enrolled-students/$user->ID" );
				}

				$student = array(
					'user_id'            => $user->ID,
					'display_name'       => $display_name,
					'email'              => $user->user_email,
					'registered'         => $user->user_registered,
					'enrolled'           => $student['enrolled'] ?? 0,
					'points'             => $student['points'] ?? 0,
					'url'                => $url,
					'date_formatted'     => STM_LMS_Helpers::format_date( $user->user_registered ),
					'can_clear_sessions' => masterstudy_lms_can_manage_user_sessions() && ! \STM_LMS_User_Sessions::is_exempt_user( $user ),
				);
			}
		}

		return array(
			'students'       => $students,
			'pages'          => (int) ceil( $total / $per_page ),
			'current_page'   => (int) $page,
			'total_students' => $total,
			'total'          => ( $total <= $offset + $per_page ),
		);
	}

	public function get_subscribed_users( $students, $total, $params ) {
		$per_page           = $params['per_page'] ?? 10;
		$page               = $params['page'] ?? 1;
		$course_id          = $params['course_id'] ?? 0;
		$coming_soon_emails = get_post_meta( $course_id, 'coming_soon_student_emails', true );

		if ( is_ms_lms_addon_enabled( 'coming_soon' ) && ! empty( $coming_soon_emails ) && empty( $params['s'] ) ) {
			$subscribed_user_emails  = array_column( $coming_soon_emails, 'email' );
			$course_enrolled_emails  = array_column( array_column( $students, 'student' ), 'email' );
			$subscribed_guest_emails = array_diff( $subscribed_user_emails, $course_enrolled_emails );

			foreach ( $subscribed_guest_emails as $guest_email ) {
				++$total;

				$user       = get_user_by( 'email', $guest_email );
				$avatar_url = get_avatar_url( 'guest@example.com' );

				if ( $user ) {
					$avatar_url = \STM_LMS_User::get_avatar_url( $user->ID );
				}

				$avatar_img = "<img src='" . esc_url( $avatar_url ) . "' class='avatar' alt='User Avatar'>";

				if ( $user ) {
					$students[] = array(
						'course_id'     => $course_id,
						'progress_link' => esc_url( \STM_LMS_Instructor::instructor_manage_students_url() . "/?course_id=$course_id&student_id=$user->ID" ),
						'student'       => array(
							'id'         => $user->ID,
							'login'      => $user->user_login,
							'email'      => $guest_email,
							'avatar'     => $avatar_img,
							'avatar_url' => $this->normalize_avatar_url( $avatar_url ),
							'url'        => esc_url( \STM_LMS_User::student_public_page_url( $user->ID ) ),
						),
					);
				} else {
					$students[] = array(
						'course_id'     => $course_id,
						'progress_link' => '#',
						'student'       => array(
							'id'         => 0,
							'login'      => esc_html__( 'Guest', 'masterstudy-lms-learning-management-system' ),
							'email'      => $guest_email,
							'avatar'     => $avatar_img,
							'avatar_url' => $this->normalize_avatar_url( $avatar_url ),
							'url'        => '',
						),
					);
				}
			}

			if ( $coming_soon_emails && is_array( $coming_soon_emails ) ) {
				$coming_soon_emails_indexed = array_column( $coming_soon_emails, null, 'email' );
				$students                   = array_map(
					function ( $item ) use ( $coming_soon_emails_indexed ) {
						$email = $item['student']['email'] ?? '';
						if ( isset( $coming_soon_emails_indexed[ $email ] ) ) {
							$item['subscribed']      = 'subscribed';
							$item['subscribed_time'] = $coming_soon_emails_indexed[ $email ]['time']->format( 'Y-m-d H:i:s' );
						}
						return $item;
					},
					$students
				);
			}
		}

		return array(
			'students' => $students,
			'total'    => $total,
		);
	}

	private function normalize_student_avatar_data( array $student ): array {
		$user_id = absint( $student['id'] ?? 0 );

		if ( $user_id && ! empty( $student['no_avatar'] ) ) {
			$student['avatar_url'] = $this->normalize_avatar_url( \STM_LMS_User::get_avatar_url( $user_id, array( 'size' => 96 ) ) );

			return $student;
		}

		if ( ! empty( $student['avatar_url'] ) ) {
			$student['avatar_url'] = $this->normalize_avatar_url( $student['avatar_url'] );

			return $student;
		}

		if ( ! empty( $student['avatar'] ) && preg_match( '/src=["\']([^"\']+)["\']/', $student['avatar'], $matches ) ) {
			$student['avatar_url'] = $this->normalize_avatar_url( $matches[1] );
		}

		return $student;
	}

	private function normalize_avatar_url( string $url ): string {
		$charset = get_bloginfo( 'charset' );
		if ( empty( $charset ) ) {
			$charset = 'UTF-8';
		}
		$url = html_entity_decode( $url, ENT_QUOTES, $charset );

		return esc_url_raw( $url );
	}

	private function sort_students( $students, $params, $order ) {
		usort(
			$students,
			function ( $a, $b ) use ( $params, $order ) {
				switch ( $params['orderby'] ) {
					case 'username':
						$value_a = strtolower( $a['student']['login'] ?? '' );
						$value_b = strtolower( $b['student']['login'] ?? '' );
						break;
					case 'email':
						$value_a = strtolower( $a['student']['email'] ?? '' );
						$value_b = strtolower( $b['student']['email'] ?? '' );
						break;
					default:
						return 0;
				}

				if ( $value_a === $value_b ) {
					return 0;
				}

				return ( 'ASC' === $order )
					? ( $value_a < $value_b ? -1 : 1 )
					: ( $value_a > $value_b ? -1 : 1 );
			}
		);

		return $students;
	}

	public function get_course_students_count( $course_id ) {
		global $wpdb;
		$course_table = stm_lms_user_courses_name( $wpdb );

		return $wpdb->get_var(
			$wpdb->prepare(
				//phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT COUNT(*) FROM {$course_table} WHERE course_id = %d",
				$course_id
			)
		);
	}

	public function get_course_students_stats( int $course_id ): array {
		global $wpdb;

		$course_table = stm_lms_user_courses_name( $wpdb );
		$threshold    = (int) \STM_LMS_Options::get_option( 'certificate_threshold', 70 );

		$stats = $wpdb->get_row(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT COUNT(*) AS total_students, SUM(CASE WHEN progress_percent >= %d THEN 1 ELSE 0 END) AS completed, SUM(CASE WHEN progress_percent > 0 AND progress_percent < %d THEN 1 ELSE 0 END) AS in_progress FROM {$course_table}
				WHERE course_id = %d",
				$threshold,
				$threshold,
				$course_id
			),
			ARRAY_A
		);

		return array(
			'total_students' => (int) ( $stats['total_students'] ?? 0 ),
			'completed'      => (int) ( $stats['completed'] ?? 0 ),
			'in_progress'    => (int) ( $stats['in_progress'] ?? 0 ),
		);
	}

	public function is_student_enrolled_in_course( int $course_id, int $student_id ): bool {
		if ( $course_id <= 0 || $student_id <= 0 ) {
			return false;
		}

		$user_course = \STM_LMS_Course::get_user_course( $student_id, $course_id );

		return ! empty( $user_course ) && (int) ( $user_course['course_id'] ?? 0 ) === $course_id;
	}

	public function course_has_material( int $course_id, int $material_id ): bool {
		$course_materials = ( new CurriculumMaterialRepository() )->get_course_materials( $course_id );

		if ( empty( $course_materials ) ) {
			return false;
		}

		return in_array( $material_id, array_map( 'intval', $course_materials ), true );
	}

	public function get_student_progress( int $course_id, int $student_id ): array {
		return ( new StudentProgressRepository() )->get_student_progress( $course_id, $student_id );
	}

	public function get_student_progress_material_details( int $course_id, int $student_id, int $material_id ): array {
		return ( new StudentProgressRepository() )->get_student_progress_material_details( $course_id, $student_id, $material_id );
	}

	public function add_student( $course_id, $data ) {
		$course_id          = (int) $course_id;
		$email              = sanitize_email( $data['email'] ?? '' );
		$user               = $email ? get_user_by( 'email', $email ) : false;
		$is_enrolled        = false;
		$is_enrolled_before = false;

		if ( $user ) {
			$course             = \STM_LMS_Course::get_user_course( $user->ID, $course_id );
			$is_enrolled_before = ! empty( $course ) && (int) $course['course_id'] === $course_id;
		}

		$added = \STM_LMS_Instructor::add_student_to_course( array( $course_id ), array( $email ) );

		if ( empty( $added['error'] ) ) {
			$first_name = sanitize_text_field( trim( (string) ( $data['first_name'] ?? '' ) ) );
			$last_name  = sanitize_text_field( trim( (string) ( $data['last_name'] ?? '' ) ) );

			$user        = get_user_by( 'email', $email );
			$is_enrolled = true;

			if ( $user && ( $first_name || $last_name ) ) {
				wp_update_user(
					array(
						'ID'           => $user->ID,
						'first_name'   => $first_name,
						'last_name'    => $last_name,
						'display_name' => trim( $first_name . ' ' . $last_name ),
					)
				);
			}
		}

		return array(
			'email'              => $email,
			'student_id'         => $user ? $user->ID : 0,
			'is_enrolled'        => $is_enrolled,
			'is_enrolled_before' => $is_enrolled_before,
		);
	}

	public function add_students_bulk( int $course_id, array $students ): array {
		$emails_map = array(); // email => student row.
		foreach ( $students as $row ) {
			$email = sanitize_email( $row['email'] ?? '' );
			if ( empty( $email ) ) {
				continue;
			}
			$emails_map[ $email ] = $row; // de-duplicate by email.
		}

		$emails = array_keys( $emails_map );
		if ( empty( $emails ) ) {
			return array(
				'added'   => array(),
				'failed'  => array(),
				'total'   => 0,
				'message' => 'No valid emails found.',
			);
		}

		// Detect already-enrolled BEFORE adding.
		$enrolled_before = array();
		foreach ( $emails as $email ) {
			$user = get_user_by( 'email', $email );
			if ( ! $user ) {
				continue;
			}
			$course = \STM_LMS_Course::get_user_course( $user->ID, $course_id );
			if ( ! empty( $course ) && (int) $course['course_id'] === $course_id ) {
				$enrolled_before[ $email ] = true;
			}
		}

		$added = \STM_LMS_Instructor::add_student_to_course( array( $course_id ), $emails );

		// If the LMS function returns per-email errors, map them; otherwise treat as success.
		$failed = array();
		if ( ! empty( $added['error'] ) && ! empty( $added['errors'] ) && is_array( $added['errors'] ) ) {
			$failed = $added['errors'];
		}

		$results = array();
		foreach ( $emails as $email ) {
			if ( isset( $failed[ $email ] ) ) {
				$results[] = array(
					'email'              => $email,
					'student_id'         => 0,
					'is_enrolled'        => false,
					'is_enrolled_before' => ! empty( $enrolled_before[ $email ] ),
					'error'              => (string) $failed[ $email ],
				);
				continue;
			}

			$user = get_user_by( 'email', $email );

			// Update names if provided.
			$row        = $emails_map[ $email ];
			$first_name = sanitize_text_field( trim( (string) ( $row['first_name'] ?? '' ) ) );
			$last_name  = sanitize_text_field( trim( (string) ( $row['last_name'] ?? '' ) ) );

			if ( $user && ( $first_name || $last_name ) ) {
				wp_update_user(
					array(
						'ID'           => $user->ID,
						'first_name'   => $first_name,
						'last_name'    => $last_name,
						'display_name' => trim( $first_name . ' ' . $last_name ),
					)
				);
			}

			$results[] = array(
				'email'              => $email,
				'student_id'         => $user ? $user->ID : 0,
				'is_enrolled'        => true,
				'is_enrolled_before' => ! empty( $enrolled_before[ $email ] ),
			);
		}

		return array(
			'total' => count( $results ),
			'added' => $results,
		);
	}

	private function cleanup_course_after_removal( int $course_id, string $user_email, bool $coming_soon_on ): void {
		$count = (int) get_post_meta( $course_id, 'current_students', true );
		if ( $count > 0 ) {
			update_post_meta( $course_id, 'current_students', $count - 1 );
		}

		if ( $coming_soon_on ) {
			$emails   = (array) get_post_meta( $course_id, 'coming_soon_student_emails', true );
			$filtered = array_filter(
				$emails,
				static fn( $entry ) => empty( $entry['email'] ) || $entry['email'] !== $user_email
			);

			if ( count( $filtered ) !== count( $emails ) ) {
				update_post_meta( $course_id, 'coming_soon_student_emails', array_values( $filtered ) );
			}
		}

		if ( class_exists( 'STM_LMS_Mails' ) ) {
			$user            = \STM_LMS_User::get_current_user( \STM_LMS_Helpers::masterstudy_lms_get_user_by_email( $user_email ) );
			$user_login      = $user['login'];
			$course_title    = get_the_title( $course_id );
			$instructor_name = \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( \STM_LMS_User::get_current_user()['id'] );
			$message         = sprintf(
			/* translators: %1$s Course Title, %2$s User Login */
				esc_html__( 'Dear %1$s, %2$s has removed you from the course - %3$s. Now you don’t have access to the course content.', 'masterstudy-lms-learning-management-system' ),
				$user_login,
				$instructor_name,
				$course_title
			);

			$email_data = array(
				'user_login'      => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user['id'] ) ?? $user_login,
				'instructor_name' => $instructor_name,
				'course_title'    => $course_title,
				'blog_name'       => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
				'site_url'        => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
				'date'            => current_time( 'mysql' ),
			);

			\STM_LMS_Helpers::send_email(
				$user['email'],
				esc_html__( 'Your Enrollment Has Been Cancelled', 'masterstudy-lms-learning-management-system' ),
				$message,
				'stm_lms_email_remove_student_from_course',
				$email_data
			);
		}
	}

	public function delete_student( array $student_ids ): void {
		if ( empty( $student_ids ) ) {
			return;
		}
		do_action( 'masterstudy_lms_delete_students_demo_mode' );

		$response = stm_lms_delete_users_in_courses( $student_ids )['data'];

		foreach ( $response as $item ) {
			$user = get_userdata( $item['user_id'] ?? 0 );

			if ( ! $user ) {
				continue;
			}

			if ( ! ( new CourseRepository() )->exists( $item['course_id'] ) ) {
				continue;
			}

			$is_course_author = \STM_LMS_Course::check_course_author( $item['course_id'], get_current_user_id() );

			if ( ! current_user_can( 'administrator' ) && ! $is_course_author ) {
				continue;
			}

			$this->cleanup_course_after_removal( absint( $item['course_id'] ), sanitize_email( $user->user_email ), is_ms_lms_addon_enabled( 'coming_soon' ) );
		}
	}

	public function delete_student_by_course( int $course_id, int $student_id, ?string $subscribed_email = null ): void {
		$user = get_userdata( $student_id );
		if ( $user ) {
			stm_lms_get_delete_user_course( $student_id, $course_id );
			$this->cleanup_course_after_removal( $course_id, sanitize_email( $subscribed_email ), is_ms_lms_addon_enabled( 'coming_soon' ) );
		}
	}

	public function export_students_by_course( $course_id ): array {
		$users      = stm_lms_get_course_users( $course_id );
		$users_data = array();

		foreach ( $users as $user ) {
			if ( isset( $user['user_id'] ) ) {
				$user_data    = get_userdata( $user['user_id'] );
				$users_data[] = array(
					'email'      => $user_data->user_email,
					'first_name' => $user_data->first_name,
					'last_name'  => $user_data->last_name,
				);
			}
		}

		return $users_data;
	}

	public function export_students( array $params = array() ): array {
		$date_from     = $params['date_from'] ? wp_date( 'Y-m-d H:i:s', strtotime( $params['date_from'] ) ) : '';
		$date_to       = $params['date_to'] ? wp_date( 'Y-m-d H:i:s', strtotime( $params['date_to'] . ' 23:59:59' ) ) : '';
		$students      = stm_lms_get_users_enrolled_export( $params['s'], $params['course_id'], $date_from, $date_to, '', '', -1 );
		$students_data = array();

		if ( ! empty( $students ) ) {
			foreach ( $students as $student ) {
				$user_data = get_userdata( $student['ID'] );
				if ( ! $user_data || ! $user_data->exists() ) {
					continue;
				}

				$courses = $student['courses'] ?? array();

				$students_data[] = array(
					'email'         => $user_data->user_email,
					'first_name'    => $user_data->first_name,
					'last_name'     => $user_data->last_name,
					'course_ids'    => $courses,
					'course_titles' => array_map(
						fn( $course_id ) => esc_html( get_the_title( $course_id ) ),
						$courses
					),
				);
			}
		}

		return $students_data;
	}

	public function set_student_progress( $course_id, $student_id, $data ) {
		$item_id   = $data['item_id'];
		$completed = rest_sanitize_boolean( $data['completed'] );

		$course_materials = ( new CurriculumMaterialRepository() )->get_course_materials( $course_id );
		// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		if ( empty( $course_materials ) || ! in_array( $item_id, $course_materials ) ) {
			return array();
		}

		switch ( get_post_type( $item_id ) ) {
			case 'stm-lessons':
				\STM_LMS_User_Manager_Course_User::complete_lesson( $student_id, $course_id, $item_id );
				break;
			case 'stm-assignments':
				\STM_LMS_User_Manager_Course_User::complete_assignment( $student_id, $course_id, $item_id, $completed );
				break;
			case 'stm-quizzes':
				\STM_LMS_User_Manager_Course_User::complete_quiz( $student_id, $course_id, $item_id, $completed );
				break;
		}

		\STM_LMS_Course::update_course_progress( $student_id, $course_id );

		return \STM_LMS_User_Manager_Course_User::_student_progress( $course_id, $student_id );
	}

	public function reset_student_progress( $course_id, $student_id ) {
		$curriculum = ( new CurriculumRepository() )->get_curriculum( $course_id );

		if ( empty( $curriculum['materials'] ) ) {
			return array();
		}

		foreach ( $curriculum['materials'] as $material ) {
			switch ( $material['post_type'] ) {
				case 'stm-lessons':
					\STM_LMS_User_Manager_Course_User::reset_lesson( $student_id, $course_id, $material['post_id'] );
					break;
				case 'stm-assignments':
					\STM_LMS_User_Manager_Course_User::reset_assignment( $student_id, $course_id, $material['post_id'] );
					break;
				case 'stm-quizzes':
					\STM_LMS_User_Manager_Course_User::reset_quiz( $student_id, $course_id, $material['post_id'] );
					break;
			}
		}

		stm_lms_reset_user_answers( $course_id, $student_id );

		\STM_LMS_Course::update_course_progress( $student_id, $course_id, true );

		return \STM_LMS_User_Manager_Course_User::_student_progress( $course_id, $student_id );
	}

	public function student_reviews_count( $student_id ) {
		global $wpdb;

		$review_post_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(p.ID) AS review_post_count
				FROM {$wpdb->prefix}posts AS p
				INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id
				WHERE pm.meta_key = 'review_user' AND pm.meta_value = %s AND p.post_type = 'stm-reviews' AND p.post_status = 'publish'",
				$student_id
			)
		);

		return intval( $review_post_count );
	}

	public function student_courses_statuses( $student_id ) {
		global $wpdb;

		$lng_code = get_locale();

		$user_courses = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT course_id, bundle_id, progress_percent FROM {$wpdb->prefix}stm_lms_user_courses WHERE user_id = %d AND lng_code = %s",
				$student_id,
				$lng_code
			),
			ARRAY_A
		);

		$statuses = array(
			'summary'     => 0,
			'completed'   => 0,
			'not_started' => 0,
			'failed'      => 0,
			'in_progress' => 0,
		);

		if ( empty( $user_courses ) ) {
			return $statuses;
		}

		$bundle_ids = array();
		foreach ( $user_courses as $c ) {
			if ( '0' !== $c['bundle_id'] ) {
				$bundle_ids[ $c['bundle_id'] ] = true;
			}
		}

		foreach ( $user_courses as $course ) {
			if ( '0' === $course['bundle_id'] && isset( $bundle_ids[ $course['course_id'] ] ) ) {
				continue;
			}

			$course_id        = $course['course_id'];
			$curriculum       = ( new CurriculumRepository() )->get_curriculum( $course_id, true );
			$course_materials = array_reduce(
				$curriculum,
				function ( $carry, $section ) {
					return array_merge( $carry, $section['materials'] ?? array() );
				},
				array()
			);
			$material_ids     = array_column( $course_materials, 'post_id' );
			$last_lesson      = ! empty( $material_ids ) ? end( $material_ids ) : 0;
			$lesson_post_type = get_post_type( $last_lesson );

			if ( PostType::QUIZ === $lesson_post_type ) {
				$last_quiz        = stm_lms_get_user_last_quiz( $student_id, $last_lesson, array( 'progress' ) );
				$passing_grade    = get_post_meta( $last_lesson, 'passing_grade', true );
				$lesson_completed = ! empty( $last_quiz['progress'] ) && $last_quiz['progress'] >= ( $passing_grade ?? 0 ) ? 'completed' : '';
			} else {
				$lesson_completed = \STM_LMS_Lesson::is_lesson_completed( $student_id, $course_id, $last_lesson ) ? 'completed' : '';
			}

			$course_passed = intval( \STM_LMS_Options::get_option( 'certificate_threshold', 70 ) ) <= intval( $course['progress_percent'] );

			if ( ! empty( $lesson_completed ) && ! $course_passed ) {
				++$statuses['failed'];
			} elseif ( intval( $course['progress_percent'] ) > 0 ) {
				if ( $course_passed ) {
					++$statuses['completed'];
				} else {
					++$statuses['in_progress'];
				}
			} else {
				++$statuses['not_started'];
			}

			++$statuses['summary'];
		}

		return $statuses;
	}

	public function student_courses_types( $student_id ) {
		if ( ! \STM_LMS_Helpers::is_pro() ) {
			return array(
				'bundle_count'     => 0,
				'enterprise_count' => 0,
			);
		}

		global $wpdb;
		$user_email = get_user_by( 'id', $student_id )->user_email;
		$results    = $wpdb->get_row(
			$wpdb->prepare(
				"
				SELECT
				(SELECT COUNT(DISTINCT bundle_id) FROM {$wpdb->prefix}stm_lms_user_courses WHERE bundle_id > 0 AND user_id = %d) AS bundle_count,
				(SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->prefix}posts p
				JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id
				WHERE p.post_type = 'stm-ent-groups'
				AND (
					(pm.meta_key = 'emails' AND pm.meta_value LIKE %s) OR
					(pm.meta_key = 'author_id' AND pm.meta_value = %d)
				)) AS enterprise_count
				",
				$student_id,
				'%' . $wpdb->esc_like( $user_email ) . '%',
				$student_id
			),
			ARRAY_A
		);

		return array_map( 'intval', $results );
	}

	public function student_completed_courses( $student_id, $fields = array(), $limit = 1 ) {
		global $wpdb;

		$table     = $wpdb->prefix . 'stm_lms_user_courses';
		$fields    = ( empty( $fields ) ) ? '*' : implode( ',', $fields );
		$threshold = \STM_LMS_Options::get_option( 'certificate_threshold', 70 );

		$query = $wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT {$fields} FROM {$table} WHERE user_ID = %d AND progress_percent >= %d",
			$student_id,
			$threshold
		);

		if ( -1 !== $limit ) {
			$query .= $wpdb->prepare( ' LIMIT %d', $limit );
		}
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return $wpdb->get_results( $query, ARRAY_A );
	}

	public function student_certificates_count( $courses ) {
		if ( ! \STM_LMS_Helpers::is_pro() || ! is_ms_lms_addon_enabled( 'certificate_builder' ) ) {
			return array();
		}

		global $wpdb;
		$certificates = array();

		foreach ( $courses as $course ) {
			if ( ! masterstudy_lms_course_has_certificate( $course['course_id'] ) ) {
				continue;
			}
			$course_terms    = wp_get_post_terms( $course['course_id'], 'stm_lms_course_taxonomy', array( 'fields' => 'ids' ) );
			$categories_list = implode( ',', array_map( 'intval', $course_terms ) );

			$certificate_ids = $wpdb->get_col(
				$wpdb->prepare(
					"
					SELECT p.ID
					FROM {$wpdb->posts} AS p
					INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
					WHERE p.post_type = 'stm-certificates'
					AND pm.meta_key = 'stm_category'
					AND (pm.meta_value REGEXP CONCAT('(^|,)', %s, '(,|$)'))
					ORDER BY pm.meta_value ASC
					LIMIT 1
					",
					$categories_list
				)
			);

			if ( empty( $certificate_ids ) ) {
				$certificate_ids = get_option( 'stm_default_certificate', '' );
			}

			$course_certificate = get_post_meta( $course['course_id'], 'course_certificate', true );

			if ( 'none' === $course_certificate ) {
				$certificates[ $course['course_id'] ] = false;
			}

			$certificates[ $course['course_id'] ] = ! empty( $course_certificate ) || ! empty( $certificate_ids );
		}

		return count( $certificates );
	}

	public function student_total_points( $student_id ) {
		if ( ! \STM_LMS_Helpers::is_pro() || ! is_ms_lms_addon_enabled( 'point_system' ) ) {
			return array();
		}

		global $wpdb;

		$table        = $wpdb->prefix . 'stm_lms_user_points';
		$total_points = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT SUM(score) FROM {$table} WHERE `user_id` = %d",
				$student_id
			)
		);

		return (int) $total_points;
	}

	public function student_total_quizzes( $student_id ) {
		global $wpdb;

		$table         = $wpdb->prefix . 'stm_lms_user_quizzes';
		$total_quizzes = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT COUNT(*) FROM {$table} WHERE `user_id` = %d",
				$student_id
			)
		);

		return (int) $total_quizzes;
	}

	public function student_total_assignments( $student_id ) {
		if ( ! \STM_LMS_Helpers::is_pro() || ! is_ms_lms_addon_enabled( 'assignments' ) ) {
			return array();
		}

		global $wpdb;

		$total_assignments = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*)
				FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
				WHERE p.post_author = %d
				AND p.post_type = 'stm-user-assignment'
				AND pm.meta_key = 'status'
				AND pm.meta_value = 'passed'",
				$student_id
			)
		);

		return (int) $total_assignments;
	}
}
