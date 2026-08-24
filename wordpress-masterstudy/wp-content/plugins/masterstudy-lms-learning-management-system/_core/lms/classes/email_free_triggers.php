<?php
/**
 * MasterStudy LMS - Course Access Expiration Reminder (per-student one-time cron)
 *
 * Notes:
 * - Schedules one-time WP-Cron event per student/course.
 * - When cron runs, it queries ONLY that student's start_time (no full course students fetch).
 * - Reschedules all events when course access settings change OR "send before" setting changes.
 */


if ( ! function_exists( 'ms_parse_duration_to_seconds' ) ) { // phpcs:ignoreFile

	/**
	 * Parse duration string into seconds.
	 *
	 * Supports:
	 * - "2w 1d 5h 4m"
	 * - "12h"
	 * - "30m"
	 * - plain number "12" (treated as hours)
	 *
	 * @param mixed $value Raw value.
	 *
	 * @return int Seconds.
	 */
	function ms_parse_duration_to_seconds( $value ) {
		$value = is_scalar( $value ) ? (string) $value : '';
		$value = trim( $value );

		if ( '' === $value ) {
			return 0;
		}

		// If plain number, treat as hours (backward-compatible).
		if ( preg_match( '/^\d+$/', $value ) ) {
			return (int) $value * HOUR_IN_SECONDS;
		}

		$regex = '/^(?:(\d+)\s*w\s*)?(?:(\d+)\s*d\s*)?(?:(\d+)\s*h\s*)?(?:(\d+)\s*m\s*)?$/i';
		if ( ! preg_match( $regex, $value, $m ) ) {
			return 0;
		}

		$weeks   = isset( $m[1] ) ? (int) $m[1] : 0;
		$days    = isset( $m[2] ) ? (int) $m[2] : 0;
		$hours   = isset( $m[3] ) ? (int) $m[3] : 0;
		$minutes = isset( $m[4] ) ? (int) $m[4] : 0;

		// Must have at least one unit.
		if ( 0 === $weeks && 0 === $days && 0 === $hours && 0 === $minutes ) {
			return 0;
		}

		$seconds = $weeks * 7 * DAY_IN_SECONDS;
		$seconds += $days * DAY_IN_SECONDS;
		$seconds += $hours * HOUR_IN_SECONDS;
		$seconds += $minutes * MINUTE_IN_SECONDS;

		return $seconds;
	}

	/**
	 * Get "send before" value (seconds) from Email Manager settings.
	 *
	 * @return int Seconds.
	 */
	function ms_get_send_before_seconds() {
		$email_settings = get_option( 'stm_lms_email_manager_settings', array() );
		$raw            = $email_settings['stm_lms_time_limit_expiration_reminder_email_send_email_before'] ?? 0;

		$seconds = ms_parse_duration_to_seconds( $raw );

		// Default fallback: 12 hours.
		if ( $seconds <= 0 ) {
			$seconds = 12 * HOUR_IN_SECONDS;
		}

		return $seconds;
	}

	/**
	 * Course access length in seconds.
	 *
	 * Uses course meta:
	 * - expiration_course = enabled
	 * - end_time = number of days
	 *
	 * @param int $course_id Course ID.
	 *
	 * @return int Seconds.
	 */
	function ms_get_course_access_seconds( $course_id ) {
		$course_id = absint( $course_id );
		if ( ! $course_id ) {
			return 0;
		}

		$expiration_enabled = get_post_meta( $course_id, 'expiration_course', true );
		if ( empty( $expiration_enabled ) ) {
			return 0;
		}

		$end_time_days = absint( get_post_meta( $course_id, 'end_time', true ) );
		if ( ! $end_time_days ) {
			return 0;
		}

		return $end_time_days * DAY_IN_SECONDS;
	}

	/**
	 * Get course students with their enrollment start time (for scheduling).
	 *
	 * @param int $course_id Course ID.
	 *
	 * @return array[]
	 */
	function ms_get_course_students_with_start( $course_id ) {
		global $wpdb;

		$course_id = absint( $course_id );
		if ( ! $course_id ) {
			return array();
		}

		$table = $wpdb->prefix . 'stm_lms_user_courses';

		$query = $wpdb->prepare(
			"SELECT user_id, start_time
			 FROM {$table}
			 WHERE course_id = %d",
			$course_id
		);

		$rows = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		if ( empty( $rows ) || ! is_array( $rows ) ) {
			return array();
		}

		$result = array();

		foreach ( $rows as $row ) {
			$user_id    = isset( $row['user_id'] ) ? absint( $row['user_id'] ) : 0;
			$start_time = isset( $row['start_time'] ) ? (int) $row['start_time'] : 0;

			if ( ! $user_id || $start_time <= 0 ) {
				continue;
			}

			$result[] = array(
				'user_id'    => $user_id,
				'start_time' => $start_time,
			);
		}

		return $result;
	}

	/**
	 * Get start_time for a single user/course (for cron execution).
	 *
	 * @param int $user_id User ID.
	 * @param int $course_id Course ID.
	 *
	 * @return int
	 */
	function ms_get_user_course_start_time( $user_id, $course_id ) {
		global $wpdb;

		$user_id   = absint( $user_id );
		$course_id = absint( $course_id );

		if ( ! $user_id || ! $course_id ) {
			return 0;
		}

		$table = $wpdb->prefix . 'stm_lms_user_courses';

		$start_time = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT start_time
				FROM {$table}
				WHERE user_id = %d
				  AND course_id = %d
				LIMIT 1",
				$user_id,
				$course_id
			)
		); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		return (int) $start_time;
	}

	/**
	 * Unschedule existing event for student/course if any.
	 *
	 * @param int $user_id User ID.
	 * @param int $course_id Course ID.
	 *
	 * @return void
	 */
	function ms_unschedule_reminder_event( $user_id, $course_id ) {
		$ts = wp_next_scheduled(
			'stm_lms_course_expiration_reminder_event',
			array(
				absint( $user_id ),
				absint( $course_id ),
			)
		);
		if ( $ts ) {
			wp_unschedule_event(
				$ts,
				'stm_lms_course_expiration_reminder_event',
				array(
					absint( $user_id ),
					absint( $course_id ),
				)
			);
		}
	}

	/**
	 * Schedule one-time reminder event for a student.
	 *
	 * @param int $user_id User ID.
	 * @param int $course_id Course ID.
	 * @param int $expiration_ts Expiration timestamp.
	 * @param int $send_before Seconds before expiration.
	 *
	 * @return void
	 */
	function ms_schedule_reminder_for_student( $user_id, $course_id, $expiration_ts, $send_before ) {
		$user_id       = absint( $user_id );
		$course_id     = absint( $course_id );
		$expiration_ts = (int) $expiration_ts;
		$send_before   = (int) $send_before;

		if ( ! $user_id || ! $course_id || $expiration_ts <= 0 || $send_before <= 0 ) {
			return;
		}

		ms_unschedule_reminder_event( $user_id, $course_id );

		$run_at = $expiration_ts - $send_before;

		// If reminder time is in the past, schedule soon.
		if ( $run_at <= time() ) {
			$run_at = time() + MINUTE_IN_SECONDS;
		}

		wp_schedule_single_event(
			$run_at,
			'stm_lms_course_expiration_reminder_event',
			array( $user_id, $course_id )
		);
	}

	/**
	 * Cron handler: send email if time left <= threshold and not expired.
	 *
	 * @param int $user_id User ID.
	 * @param int $course_id Course ID.
	 *
	 * @return void
	 */
	function ms_send_course_expiration_reminder( $user_id, $course_id ) {
		$user_id   = absint( $user_id );
		$course_id = absint( $course_id );

		if ( ! $user_id || ! $course_id ) {
			return;
		}

		$email_settings = get_option( 'stm_lms_email_manager_settings', array() );

		if ( empty( $email_settings['stm_lms_time_limit_expiration_reminder_email_enable'] ) ) {
			return;
		}

		$access_seconds = ms_get_course_access_seconds( $course_id );
		if ( $access_seconds <= 0 ) {
			return;
		}

		$start_ts = ms_get_user_course_start_time( $user_id, $course_id );
		if ( $start_ts <= 0 ) {
			return;
		}

		$expiration_ts = $start_ts + $access_seconds;

		$user = get_userdata( $user_id );
		if ( ! $user || empty( $user->user_email ) ) {
			return;
		}

		$course_title = get_the_title( $course_id );
		$course_url   = get_permalink( $course_id );

		$email_data = array(
			'user_login'             => class_exists( 'STM_LMS_Helpers' )
				? STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id )
				: $user->user_login,
			'course_title'           => $course_title,
			'course_url'             => class_exists( 'MS_LMS_Email_Template_Helpers' )
				? MS_LMS_Email_Template_Helpers::link( $course_url )
				: $course_url,
			'course_expiration_date' => wp_date(
				get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
				$expiration_ts
			),
			'blog_name'              => get_bloginfo( 'name' ),
			'site_url'               => class_exists( 'MS_LMS_Email_Template_Helpers' )
				? MS_LMS_Email_Template_Helpers::link( home_url( '/' ) )
				: home_url( '/' ),
			'date'                   => current_time( 'mysql' ),
		);

		if ( class_exists( 'STM_LMS_Helpers' ) && method_exists( 'STM_LMS_Helpers', 'send_email' ) ) {
			STM_LMS_Helpers::send_email(
				$user->user_email,
				$email_settings['stm_lms_time_limit_expiration_reminder_email_subject'] ?? esc_html__( 'Your access to {{course_title}} is expiring soon', 'masterstudy-lms-learning-management-system-pro' ),
				$email_settings['stm_lms_time_limit_expiration_reminder_email'] ?? '',
				'stm_lms_time_limit_expiration_reminder_email',
				$email_data
			);
		} else {
			$subject = sprintf(
			/* translators: %s course title */
				esc_html__( 'Your access to %s is expiring soon', 'masterstudy-lms-learning-management-system-pro' ),
				$course_title
			);

			$message = sprintf(
			/* translators: 1: user, 2: course, 3: date */
				esc_html__( 'Hello %1$s. Your access to %2$s will expire on %3$s.', 'masterstudy-lms-learning-management-system-pro' ),
				$user->user_login,
				$course_title,
				$email_data['course_expiration_date']
			);

			wp_mail( $user->user_email, $subject, $message );
		}

	}

	/**
	 * Register cron hook.
	 */
	add_action( 'stm_lms_course_expiration_reminder_event', 'ms_send_course_expiration_reminder', 10, 2 );

	/**
	 * When course access settings updated - reschedule reminders for all students of that course.
	 */
	add_action(
		'masterstudy_lms_course_update_access',
		function ( $course_id, $data ) {
			$course_id = absint( $course_id );

			if ( ! $course_id ) {
				return;
			}

			$email_settings = get_option( 'stm_lms_email_manager_settings', array() );
			if ( empty( $email_settings['stm_lms_time_limit_expiration_reminder_email_enable'] ) ) {
				return;
			}

			$access_seconds = ms_get_course_access_seconds( $course_id );
			if ( $access_seconds <= 0 ) {
				return;
			}

			$send_before = ms_get_send_before_seconds();
			$students    = ms_get_course_students_with_start( $course_id );

			if ( empty( $students ) ) {
				return;
			}

			foreach ( $students as $row ) {
				$user_id  = absint( $row['user_id'] );
				$start_ts = (int) $row['start_time'];

				if ( ! $user_id || $start_ts <= 0 ) {
					continue;
				}

				$expiration_ts = $start_ts + $access_seconds;

				ms_schedule_reminder_for_student( $user_id, $course_id, $expiration_ts, $send_before );
			}
		},
		10,
		2
	);

	/**
	 * When "send before" value changes - reschedule reminders for all courses with expiration enabled.
	 */
	add_action(
		'update_option_stm_lms_email_manager_settings',
		function ( $old_value, $new_value ) {
			$old_before = $old_value['stm_lms_time_limit_expiration_reminder_email_send_email_before'] ?? '';
			$new_before = $new_value['stm_lms_time_limit_expiration_reminder_email_send_email_before'] ?? '';

			if ( (string) $old_before === (string) $new_before ) {
				return;
			}

			do_action( 'stm_lms_reschedule_all_expiration_reminders' );
		},
		10,
		2
	);

	add_action(
		'stm_lms_reschedule_all_expiration_reminders',
		function () {
			$courses = get_posts(
				array(
					'post_type'      => 'stm-courses',
					'post_status'    => 'any',
					'fields'         => 'ids',
					'posts_per_page' => - 1,
					'meta_key'       => 'expiration_course',
					'meta_value'     => '1',
				)
			);

			if ( empty( $courses ) ) {
				return;
			}

			foreach ( $courses as $course_id ) {
				do_action( 'masterstudy_lms_course_update_access', (int) $course_id, array() );
			}
		}
	);
}
