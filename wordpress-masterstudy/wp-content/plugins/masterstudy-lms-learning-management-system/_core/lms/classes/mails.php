<?php
STM_LMS_Mails::init();

class STM_LMS_Mails {

	public static function init() {
		add_action( 'masterstudy_lms_order_completed', array( self::class, 'masterstudy_lms_order_completed' ), 10, 4 );
		add_action( 'add_user_course', array( self::class, 'add_user_course' ), 10, 2 );
		add_action( 'masterstudy_lms_course_saved', array( self::class, 'course_saved' ), 10, 2 );
	}

	public static function wp_mail_html() {
		return 'text/html';
	}

	public static function send_email_to_instructor( $cart_items, $user_login, $order_id, $settings, $template_name, $user_id, $send_test_mode = false ) {
		$instructor_items  = array();
		$instructor_emails = array();

		if ( $send_test_mode ) {
			self::process_instructor_order_mail( $template_name, $user_login, $order_id, array(), get_option( 'admin_email' ), $settings, $user_id, $send_test_mode );
			die;
		}

		if ( ! empty( $cart_items ) ) {
			foreach ( $cart_items as $item ) {
				$item_id = $item['item_id'];
				$post    = get_post( $item_id );

				if ( $post ) {
					$author_id = $post->post_author;
					$author    = get_userdata( $author_id );

					if ( in_array( 'stm_lms_instructor', $author->roles ) ) {
						$instructor_email = $author->user_email;
						if ( ! in_array( $instructor_email, $instructor_emails ) ) {
							$instructor_emails[] = $instructor_email;
						}
						$instructor_items[ $instructor_email ][] = $item;
					}
				}
			}
		}
		foreach ( $instructor_emails as $email ) {
			if ( ! empty( $instructor_items[ $email ] ) ) {
				self::process_instructor_order_mail( $template_name, $user_login, $order_id, $instructor_items[ $email ], $email, $settings, $user_id, $send_test_mode );
			}
		}
	}

	public static function masterstudy_lms_order_subject_renderer( $text, $user_login ) {
		$email_data_orders = array(
			'site_url'   => \STM_LMS_Helpers::masterstudy_lms_get_site_url(),
			'user_login' => $user_login,
		);
		$search            = array( '{{site_url}}', '{{user_login}}' );
		$replace           = array(
			'<a href="' . esc_url( $email_data_orders['site_url'] ) . '" target="_blank">' . esc_url( $email_data_orders['site_url'] ) . '</a>',
			esc_html( $email_data_orders['user_login'] ),
		);

		return str_replace( $search, $replace, $text );
	}

	public static function process_instructor_order_mail( $template_name, $user_login, $order_id, $instructor_emails, $email, $settings, $user_id, $send_test_mode ) {
		$email_data = array(
			'user_login'    => $user_login,
			'blog_name'     => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
			'site_url'      => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
			'student_email' => STM_LMS_Helpers::masterstudy_lms_get_user_email_by_user_id( $user_id ),
			'date'          => current_time( 'mysql' ),
		);

		$template = $settings['stm_lms_new_order_instructor'] ?? 'You made a Sale';
		$message  = \MS_LMS_Email_Template_Helpers::render( $template, $email_data );

		$order_title   = \MS_LMS_Email_Template_Helpers::render( $settings['stm_lms_new_order_instructor_title'], $email_data );
		$order_subject = \MS_LMS_Email_Template_Helpers::render( $settings['stm_lms_new_order_instructor_subject'], $email_data );

		if ( empty( $order_subject ) && ! is_ms_lms_addon_enabled( 'email_manager' ) ) {
			$order_subject = 'You made a Sale';
		}
		if ( empty( $order_title ) && ! is_ms_lms_addon_enabled( 'email_manager' ) ) {
			$order_title = 'You made a Sale';
		}

		$context = \STM_LMS_Templates::load_lms_template(
			$template_name,
			array(
				'send_test_mode'   => $send_test_mode,
				'order_id'         => $order_id,
				'message'          => $message,
				'is_instructor'    => true,
				'settings'         => $settings,
				'instructor_items' => $instructor_emails, // Items specific to the instructor
				'title'            => $order_title,
				'customer_section' => true,
			)
		);

		add_filter( 'wp_mail_content_type', 'STM_LMS_Helpers::set_html_content_type' );
		wp_mail( $email, $order_subject ?? 'You made a Sale!', $context );
		remove_filter( 'wp_mail_content_type', 'STM_LMS_Helpers::set_html_content_type' );
	}

	public static function send_email_to_admin( $user_login, $order_id, $settings, $template_name, $user_id, $send_test_mode = false ) {
		$email_data = array(
			'user_login'    => $user_login,
			'student_email' => STM_LMS_Helpers::masterstudy_lms_get_user_email_by_user_id( $user_id ),
			'blog_name'     => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
			'site_url'      => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
			'date'          => current_time( 'mysql' ),
		);

		$template = $settings['stm_lms_new_order'] ?? 'New Order';
		$message  = \MS_LMS_Email_Template_Helpers::render( $template, $email_data );

		$order_title   = \MS_LMS_Email_Template_Helpers::render( $settings['stm_lms_new_order_title'], $email_data );
		$order_subject = \MS_LMS_Email_Template_Helpers::render( $settings['stm_lms_new_order_subject'], $email_data );

		if ( empty( $order_subject ) && ! is_ms_lms_addon_enabled( 'email_manager' ) ) {
			$order_subject = 'New Order';
		}
		if ( empty( $order_title ) && ! is_ms_lms_addon_enabled( 'email_manager' ) ) {
			$order_title = 'New Order';
		}

		$context = \STM_LMS_Templates::load_lms_template(
			$template_name,
			array(
				'send_test_mode'   => $send_test_mode,
				'order_id'         => $order_id,
				'message'          => $message,
				'instructor_items' => array(),
				'settings'         => $settings,
				'is_instructor'    => false,
				'title'            => $order_title ?? '',
				'customer_section' => true,
			)
		);

		// Assume admin email is set in settings
		$admin_email = $settings['admin_email'] ?? get_option( 'admin_email' );
		add_filter( 'wp_mail_content_type', 'STM_LMS_Helpers::set_html_content_type' );
		wp_mail( $admin_email, $order_subject ?? 'New Order Received', $context );
		remove_filter( 'wp_mail_content_type', 'STM_LMS_Helpers::set_html_content_type' );
	}

	public static function send_email_to_student( $user, $order_id, $settings, $template_name, $send_test_mode = false ) {
		$user_value = $send_test_mode ? $user : $user['login'];
		$user_email = $send_test_mode ? $user : $user['email'];

		$email_data = array(
			'user_login' => $user_value,
			'blog_name'  => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
			'site_url'   => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
			'date'       => current_time( 'mysql' ),
		);

		$template = $settings['stm_lms_new_order_accepted'] ?? 'Your Order has been Accepted.';
		$message  = \MS_LMS_Email_Template_Helpers::render( $template, $email_data );

		$order_title   = \MS_LMS_Email_Template_Helpers::render( $settings['stm_lms_new_order_accepted_title'] ?? 'Thank you for purchase!', $email_data );
		$order_subject = \MS_LMS_Email_Template_Helpers::render( $settings['stm_lms_new_order_accepted_subject'] ?? 'Thank you for purchase!', $email_data );

		if ( empty( $order_subject ) && ! is_ms_lms_addon_enabled( 'email_manager' ) ) {
			$order_subject = 'Thank you for purchase!';
		}
		if ( empty( $order_title ) && ! is_ms_lms_addon_enabled( 'email_manager' ) ) {
			$order_title = 'Thank you for purchase!';
		}

		$context = \STM_LMS_Templates::load_lms_template(
			$template_name,
			array(
				'send_test_mode'   => $send_test_mode,
				'order_id'         => $order_id,
				'instructor_items' => array(),
				'is_instructor'    => false,
				'settings'         => $settings,
				'message'          => $message,
				'title'            => $order_title ?? 'Order Confirmation',
				'customer_section' => false,
			)
		);

		add_filter( 'wp_mail_content_type', 'STM_LMS_Helpers::set_html_content_type' );
		wp_mail( $user_email, $order_subject ?? 'Order Accepted', $context );
		remove_filter( 'wp_mail_content_type', 'STM_LMS_Helpers::set_html_content_type' );
	}

	public static function masterstudy_lms_order_completed( $user, $cart_items, $payment_code, $order_id ) {
		if ( empty( $order_id ) || ! get_post( (int) $order_id ) ) {
			return;
		}

		$settings      = class_exists( 'STM_LMS_Email_Manager' ) ? STM_LMS_Email_Manager::stm_lms_get_settings() : array();
		$template_name = 'emails/order-template';

		if ( STM_LMS_Helpers::is_pro_plus() && is_ms_lms_addon_enabled( 'email_manager' ) ) {
			$template_name = 'emails/order-template-plus';
		}

		$user       = STM_LMS_User::get_current_user( $user );
		$user_login = $user['login'];

		$send_admin      = $settings['stm_lms_new_order_enable'] ?? true;
		$send_instructor = $settings['stm_lms_new_order_instructor_enable'] ?? true;
		$send_student    = $settings['stm_lms_new_order_accepted_enable'] ?? true;

		if ( $send_instructor ) {
			self::send_email_to_instructor( $cart_items, $user_login, $order_id, $settings, $template_name, $user['id'], false );
		}

		if ( $send_admin ) {
			self::send_email_to_admin( $user_login, $order_id, $settings, $template_name, $user['id'] );
		}

		if ( $send_student ) {
			self::send_email_to_student( $user, $order_id, $settings, $template_name );

			$user_id = $user['id'];

			if ( ! empty( $cart_items ) && is_array( $cart_items ) ) {
				foreach ( $cart_items as $item ) {
					if ( ! empty( $item['item_id'] ) ) {
						$course_id = intval( $item['item_id'] );
						$meta_key  = '_stm_lms_course_expiration_email_sent_' . $course_id;

						$already_sent = get_user_meta( $user_id, $meta_key, true );

						if ( ! empty( $already_sent ) ) {
							// Reset the flag
							delete_user_meta( $user_id, $meta_key );
						}
					}
				}
			}
		}

	}

	public static function add_user_course( $user_id, $course_id ) {
		$user    = STM_LMS_User::get_current_user( $user_id );
		$authors = array();

		if ( ! empty( $course_id ) ) {
			if ( function_exists( 'icl_object_id' ) ) {
				$post_type         = get_post_type( $course_id );
				$default_lang      = apply_filters( 'wpml_default_language', null );
				$default_course_id = apply_filters( 'wpml_object_id', $course_id, $post_type, false, $default_lang );

				if ( did_action( 'stm_lms_user_course_added_' . $default_course_id . '_' . $user_id ) ) {
					return;
				}
				do_action( 'stm_lms_user_course_added_' . $default_course_id . '_' . $user_id );
				$course_id = apply_filters( 'wpml_object_id', $course_id, 'post' );
			}

			$post_author   = get_post_field( 'post_author', $course_id );
			$co_instructor = get_post_meta( $course_id, 'co_instructor', true );

			if ( ! empty( $post_author ) ) {
				$post_author_info = get_userdata( $post_author );

				if ( ! empty( $co_instructor_info->user_email ) ) {
					$authors[] = $post_author_info->user_email;
				}
			}

			if ( ! empty( $co_instructor ) ) {
				$co_instructor_info = get_userdata( $co_instructor );

				if ( ! empty( $co_instructor_info->user_email ) ) {
					$authors[] = $co_instructor_info->user_email;
				}
			}
		}

		$course_title = get_the_title( $course_id );
		$login        = $user['login'];
		$message      = 'Hello admin, <br>
		This is to notify you that a student account {{login}} has been successfully enrolled in the course {{course_title}}.<br>
		Here are the event details:<br>
		<b>Student</b>: {{login}}<br>
		<b>Course</b>: {{course_title}} <b>url</b>: {{course_url}}<br>
		<b>Date</b>: {{date}}<br>
		If everything is OK, no further actions are required.';

		$email_data = array(
			'course_title' => $course_title,
			'login'        => $login,
			'blog_name'    => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
			'site_url'     => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
			'date'         => current_time( 'mysql' ),
			'course_url'   => \MS_LMS_Email_Template_Helpers::link( get_permalink( $course_id ) ),
			'user_login'   => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
		);

		$subject = 'Student enrolled in {{course_title}}';
		$subject = \MS_LMS_Email_Template_Helpers::render( $subject, $email_data );
		$message = \MS_LMS_Email_Template_Helpers::render( $message, $email_data );

		if ( apply_filters( 'stm_lms_send_admin_course_notice', true ) ) {
			self::send_email( $subject, $message, '', $authors, 'stm_lms_course_added_to_user', $email_data );
		}

		$subject = esc_html__(
			'You have Been Added to {{course_title}}!',
			'masterstudy-lms-learning-management-system-pro'
		);

		$subject = \MS_LMS_Email_Template_Helpers::render( $subject, $email_data );

		$message = 'Hello {{user_login}},<br>
		We\'re excited to inform you that you have added to the course {{course_title}}.<br>
		You can access the course content and resources using the following link:<br>
		<b>Course Link</b>:  {{course_url}} <br>
		Enjoy your learning journey!';
		$message = \MS_LMS_Email_Template_Helpers::render( $message, $email_data );

		self::send_email( $subject, $message, $user['email'], array(), 'stm_lms_course_available_for_user', $email_data );

		$template = wp_kses_post(
			'Great news! <br>
				{{user_login}} has just enrolled in your course {{course_title}} on {{date}}.<br>
				Thank you for your valuable contribution to our platform. Keep up the fantastic work!'
		);

		$email_data_enrollment = array(
			'user_login'    => STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
			'course_title'  => $course_title,
			'date' => current_time( 'mysql' ),
		);
		$search                = array( '{{user_login}}', '{{course_title}}', '{{date}}' );
		$replace               = array(
			$email_data_enrollment['user_login'],
			$email_data_enrollment['course_title'],
			$email_data_enrollment['date'],
		);
		$subject               = esc_html__( 'New Enrollment in {{course_title}}!', 'masterstudy-lms-learning-management-system' );

		$message = str_replace( $search, $replace, $template );
		$subject = str_replace( $search, $replace, $subject );

		STM_LMS_Helpers::send_email(
			\STM_LMS_Helpers::masterstudy_lms_get_post_author_email_by_post_id( $course_id ),
			$subject,
			$message,
			'stm_lms_student_enrollment_in_course_to_author',
			$email_data_enrollment,
		);

	}

	public static function course_saved( $post_id, $course ) {
		$action  = ! empty( $course['id'] )
			? esc_html__( 'updated', 'masterstudy-lms-learning-management-system' )
			: esc_html__( 'created', 'masterstudy-lms-learning-management-system' );
		$title   = $course['title'] ?? get_the_title( $post_id );
		$user    = STM_LMS_User::get_current_user();

		$email_data = array(
			'action'          => $action,
			'user_login'      => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user['id'] ),
			'course_title'    => $title,
			'blog_name'       => \STM_LMS_Helpers::masterstudy_lms_get_site_name(),
			'site_url'        => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
			'date'            => current_time( 'mysql' ),
			'dashboard_url'   => \MS_LMS_Email_Template_Helpers::link( admin_url() ),
			'course_edit_url' => \MS_LMS_Email_Template_Helpers::link( ms_plugin_manage_course_url( $post_id ) ),
			'course_url'      => \MS_LMS_Email_Template_Helpers::link( get_permalink( $post_id ) ),
		);

		$template = wp_kses_post(
			'Course {{course_title}} {{action}} by instructor, <br> your ({{user_login}}). <br> Please review this information from the admin Dashboard'
		);

		$message = \MS_LMS_Email_Template_Helpers::render( $template, $email_data );

		if ( class_exists( 'STM_LMS_Email_Manager' ) ) {
			$email_manager = STM_LMS_Email_Manager::stm_lms_get_settings();
			$subject       = $email_manager['stm_lms_course_added_subject'] ?? esc_html__( 'Course added/updated', 'masterstudy-lms-learning-management-system' );
		}
		$subject = \MS_LMS_Email_Template_Helpers::render( $subject, $email_data );

		self::send_email(
			$subject,
			$message,
			get_option( 'admin_email' ),
			array(),
			'stm_lms_course_added',
			$email_data
		);
	}

	public static function send_email( $subject, $message, $to = '', $additional_receivers = array(), $filter = 'stm_lms_send_email_filter', $data = array() ) {
		$to        = ( ! empty( $to ) ) ? $to : get_option( 'admin_email' );
		$receivers = array_unique( array_merge( array( $to ), $additional_receivers ) );

		add_filter( 'wp_mail_content_type', array( self::class, 'wp_mail_html' ) );

		$data = apply_filters(
			'stm_lms_filter_email_data',
			array(
				'subject'     => $subject,
				'message'     => $message,
				'vars'        => $data,
				'filter_name' => $filter,
			)
		);

		if ( class_exists( 'STM_LMS_Email_Manager' ) ) {
			$email_manager = STM_LMS_Email_Manager::stm_lms_get_settings();

			add_filter(
				'wp_mail_from',
				function ( $from_email ) use ( $email_manager ) {
					return $email_manager['stm_lms_email_template_header_email'] ?? $from_email;
				}
			);
		}

		if ( ! isset( $data['enabled'] ) || ( isset( $data['enabled'] ) && $data['enabled'] ) ) {
			wp_mail( $receivers, $data['subject'], $data['message'] );
		}

		remove_filter( 'wp_mail_content_type', array( self::class, 'wp_mail_html' ) );
	}

}
