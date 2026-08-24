<?php

new STM_LMS_User_Manager_Course();

class STM_LMS_User_Manager_Course {

	public function __construct() {
		add_action( 'wp_ajax_stm_lms_dashboard_get_course_students', array( $this, 'students' ) );
		add_action( 'wp_ajax_stm_lms_dashboard_delete_user_from_course', array( $this, 'delete_user' ) );
		add_action( 'wp_ajax_stm_lms_dashboard_export_course_students', array( $this, 'export_students' ) );
	}

	public function students() {
		check_ajax_referer( 'stm_lms_dashboard_get_course_students', 'nonce' );

		$course_id       = absint( $_GET['course_id'] ?? 0 );
		$current_user_id = get_current_user_id();

		if ( empty( $course_id ) || empty( $current_user_id ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Invalid request.', 'masterstudy-lms-learning-management-system' ),
				),
				400
			);
		}

		if ( ! STM_LMS_Course::check_course_author( $course_id, $current_user_id ) && ! current_user_can( 'edit_post', $course_id ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Unauthorized request.', 'masterstudy-lms-learning-management-system' ),
				),
				403
			);
		}

		$data               = array_reverse( array_map( array( $this, 'map_students' ), stm_lms_get_course_users( $course_id ) ) );
		$coming_soon_emails = get_post_meta( $course_id, 'coming_soon_student_emails', true );

		if ( is_ms_lms_addon_enabled( 'coming_soon' ) && ! empty( $coming_soon_emails ) ) {
			$subscribed_user_emails = array_column( $coming_soon_emails, 'email' );

			$course_enrolled_emails  = array_column( array_column( $data, 'student' ), 'email' );
			$subscribed_guest_emails = array_diff( $subscribed_user_emails, $course_enrolled_emails );

			foreach ( $subscribed_guest_emails as $guest_email ) {
				$user       = get_user_by( 'email', $guest_email );
				$avatar_url = get_avatar_url( 'guest@example.com' );

				if ( $user ) {
					$avatar_url = STM_LMS_User::get_avatar_url( $user->ID );
				}
				$avatar_img = "<img src='" . esc_url( $avatar_url ) . "' class='avatar' alt='User Avatar'>";

				if ( $user ) {
					$data[] = array(
						'course_id' => $course_id,
						'student'   => array(
							'id'     => $user->ID,
							'login'  => $user->user_login,
							'email'  => $guest_email,
							'avatar' => $avatar_img,
						),
					);
				} else {
					$data[] = array(
						'course_id' => $course_id,
						'student'   => array(
							'id'     => 0,
							'login'  => esc_html__( 'Guest', 'masterstudy-lms-learning-management-system' ),
							'email'  => $guest_email,
							'avatar' => $avatar_img,
						),
					);
				}
			}

			foreach ( $data as $key => $item ) {
				if ( $coming_soon_emails && is_array( $coming_soon_emails ) ) {
					$email_index = array_search( $item['student']['email'], array_column( $coming_soon_emails, 'email' ), true );

					if ( false !== $email_index ) {
						$data[ $key ]['subscribed']      = 'subscribed';
						$data[ $key ]['subscribed_time'] = $coming_soon_emails[ $email_index ]['time']->format( 'Y-m-d H:i:s' );
					}
				}
			}
		}

		$data['students']     = $data;
		$data['origin_title'] = html_entity_decode( get_the_title( $course_id ) );
		/* translators: %s: Course ID */
		$data['title'] = sprintf( esc_html__( 'Students of %s', 'masterstudy-lms-learning-management-system' ), html_entity_decode( get_the_title( $course_id ) ) );

		wp_send_json( $data );
	}

	public function export_students() {
		check_ajax_referer( 'stm_lms_dashboard_export_course_students_to_csv', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have permission to export users.' );
		}

		$request_body = file_get_contents( 'php://input' );
		$data         = json_decode( $request_body, true );
		$course_id    = intval( $data['course_id'] );
		$users        = stm_lms_get_course_users( $course_id );
		$output_data  = array();

		foreach ( $users as $user ) {
			if ( isset( $user['user_id'] ) ) {
				$user_data     = get_userdata( $user['user_id'] );
				$output_data[] = array(
					'email'      => $user_data->user_email,
					'first_name' => $user_data->first_name,
					'last_name'  => $user_data->last_name,
				);
			}
		}

		wp_send_json(
			array(
				'user_data' => $output_data,
				'filename'  => "course_{$course_id}_stundents.csv",
			)
		);
	}

	public function map_students( $student_course ) {
		$user_id = $student_course['user_id'];

		$student_course['ago']                  = stm_lms_time_elapsed_string( gmdate( 'Y-m-d\TH:i:s\Z', $student_course['start_time'] ) );
		$student_course['start_time_formatted'] = STM_LMS_Helpers::format_date( gmdate( 'Y-m-d\TH:i:s\Z', $student_course['start_time'] ) );

		$student_course['student'] = STM_LMS_User::get_current_user( $user_id, false, false, false, 215, true );

		if ( empty( $student_course['student']['login'] ) ) {
			$student_course['student']['login'] = esc_html__( 'Deleted user', 'masterstudy-lms-learning-management-system' );
		}

		return $student_course;
	}

	public function delete_user() {
		check_ajax_referer( 'stm_lms_dashboard_delete_user_from_course', 'nonce' );

		$course_id        = absint( $_GET['course_id'] ?? 0 );
		$user_id          = absint( $_GET['user_id'] ?? 0 );
		$subscribed_email = sanitize_text_field( $_GET['user_email'] ?? '' );
		$current_user_id  = get_current_user_id();

		if ( empty( $course_id ) || empty( $current_user_id ) || ( empty( $user_id ) && empty( $subscribed_email ) ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Invalid request.', 'masterstudy-lms-learning-management-system' ),
				),
				400
			);
		}

		if ( ! STM_LMS_Course::check_course_author( $course_id, $current_user_id ) && ! current_user_can( 'edit_post', $course_id ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Unauthorized request.', 'masterstudy-lms-learning-management-system' ),
				),
				403
			);
		}

		if ( ! empty( $subscribed_email ) && is_ms_lms_addon_enabled( 'coming_soon' ) ) {
			$coming_soon_emails      = get_post_meta( $course_id, 'coming_soon_student_emails', true ) ?? array();
			$unsubscribe_email_index = array_search( $subscribed_email, array_column( $coming_soon_emails, 'email' ), true );

			unset( $coming_soon_emails[ $unsubscribe_email_index ] );
			update_post_meta( $course_id, 'coming_soon_student_emails', array_values( $coming_soon_emails ) );
		}

		$option_key = "masterstudy_plugin_course_completion_{$user_id}_{$course_id}";
		update_option( $option_key, false );
		stm_lms_get_delete_user_course( $user_id, $course_id );
		$meta = STM_LMS_Helpers::parse_meta_field( $course_id );

		if ( ! empty( $meta['current_students'] ) && $meta['current_students'] > 0 ) {
			update_post_meta( $course_id, 'current_students', --$meta['current_students'] );
		}
		if ( class_exists( 'STM_LMS_Mails' ) ) {
			$user            = STM_LMS_User::get_current_user( $user_id );
			$user_login      = $user['login'];
			$course_title    = get_the_title( $course_id );
			$instructor_name = \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user['id'] );

			$message = sprintf(
			/* translators: %1$s Course Title, %2$s User Login */
				esc_html__( 'Dear %1$s, %2$s has removed you from the course - %3$s. Now you don’t have access to the course content.', 'masterstudy-lms-learning-management-system' ),
				$user_login,
				$instructor_name,
				$course_title
			);

			$email_data = array(
				'user_login'      => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
				'instructor_name' => $instructor_name,
				'course_title'    => $course_title,
				'blog_name'       => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
				'site_url'        => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
				'date'            => current_time( 'mysql' ),
			);

			STM_LMS_Helpers::send_email(
				$user['email'],
				esc_html__( 'Your Enrollment Has Been Cancelled', 'masterstudy-lms-learning-management-system' ),
				$message,
				'stm_lms_email_remove_student_from_course',
				$email_data
			);
		}
	}
}
