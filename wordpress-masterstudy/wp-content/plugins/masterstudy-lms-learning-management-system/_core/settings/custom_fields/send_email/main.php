<?php
add_action( 'wp_ajax_stm_lms_send_test_email_ajax', 'stm_lms_send_test_email_ajax' );
function stm_lms_send_test_email_ajax() {
	if ( ! empty( $_POST['emailId'] ) ) {
		$email_manager = STM_LMS_Email_Manager::stm_lms_get_settings();
		$email_id      = $_POST['emailId'];

		$email_roles = array(
			'stm_lms_reports_student_checked'    => array(
				'role'          => 'subscriber',
				'enable_option' => 'stm_lms_reports_student_checked_enable',
			),
			'stm_lms_reports_instructor_checked' => array(
				'role'          => 'stm_lms_instructor',
				'enable_option' => 'stm_lms_reports_instructor_checked_enable',
			),
			'stm_lms_reports_admin_checked'      => array(
				'role'                    => 'administrator',
				'enable_option'           => 'stm_lms_reports_admin_checked_enable',
				'requires_settings_check' => true,
			),
		);

		if ( isset( $email_roles[ $email_id ] ) ) {
			$role_data  = $email_roles[ $email_id ];
			$is_enabled = is_digest_enabled( $role_data['enable_option'] );

			$send_test_email = ! ( 'administrator' === $role_data['role'] );

			if ( $is_enabled || ( ! empty( $role_data['requires_settings_check'] ) && empty( get_option( 'stm_lms_email_manager_settings', array() ) ) ) ) {
				process_user_emails( $role_data['role'], $send_test_email );
				wp_send_json_success();
			}
		}

		$template_name = 'emails/order-template-plus';

		$settings      = class_exists( 'STM_LMS_Email_Manager' ) ? STM_LMS_Email_Manager::stm_lms_get_settings() : array();

		if ( 'stm_lms_new_order' === $email_id ) {
			STM_LMS_Mails::send_email_to_admin( 'John doe', 0, $settings, $template_name, 0, true );
			wp_send_json_success();
		}
		if ( 'stm_lms_new_order_instructor' === $email_id ) {
			STM_LMS_Mails::send_email_to_instructor( array(), 'John doe', 0, $settings, $template_name, 0, true );
			wp_send_json_success();
		}
		if ( 'stm_lms_new_order_accepted' === $email_id ) {
			STM_LMS_Mails::send_email_to_student( get_option( 'admin_email' ), 0, $settings, $template_name, true );
			wp_send_json_success();
		}
		if ( 'stm_lms_certificates_preview_checked' === $email_id ) {
			global $wpdb;

			// Fetch the first course ID directly from the database (much faster than get_posts())
			$first_course_id = $wpdb->get_var(
				"SELECT ID FROM {$wpdb->posts}
			WHERE post_type = 'stm-courses'
			AND post_status = 'publish'
			ORDER BY ID ASC LIMIT 1"
			);

			// Fetch the first admin user ID directly from the database (faster than get_users())
			$admin_id = $wpdb->get_var(
				"SELECT ID FROM {$wpdb->users}
			WHERE ID IN (
			SELECT user_id FROM {$wpdb->usermeta}
			WHERE meta_key = '{$wpdb->prefix}capabilities'
			AND meta_value LIKE '%administrator%'
			)
			ORDER BY ID ASC LIMIT 1"
			);

			if ( $admin_id && $first_course_id ) {
				do_action( 'masterstudy_plugin_student_course_completion', $admin_id, $first_course_id, true );
				wp_send_json_success();
			} else {
				wp_send_json_error( 'No admin or course found.' );
			}
		}

		if ( ! empty( $email_manager ) && is_array( $email_manager ) ) {
			$subject      = $email_manager[ $email_id . '_subject' ];
			$result       = $email_manager[ $email_id ] ?? '';
			$current_user = wp_get_current_user();
			$result       = str_replace( '{{', 'Sample ', $result );
			$result       = str_replace( '}}', ' ', $result );
			$subject      = str_replace( '{{', 'Sample ', $subject );
			$subject      = str_replace( '}}', ' ', $subject );

			$data = apply_filters(
				'stm_lms_filter_email_data',
				array(
					'subject' => $subject,
					'message' => $result,
				)
			);

			add_filter( 'wp_mail_content_type', array( STM_LMS_Helpers::class, 'set_html_content_type' ) );

			add_filter(
				'wp_mail_from',
				function ( $from_email ) use ( $email_manager ) {
					return $email_manager['stm_lms_email_template_header_email'] ?? $from_email;
				}
			);

			$response = wp_mail( $current_user->user_email, $data['subject'], $data['message'] );

			remove_filter( 'wp_mail_content_type', array( STM_LMS_Helpers::class, 'set_html_content_type' ) );

			if ( $response ) {
				wp_send_json_success();
			}
		}
	}
	wp_send_json_error();
}

add_filter(
	'wpcfto_field_send_email',
	function () {
		return STM_LMS_PATH . '/settings/custom_fields/send_email/fields/send_email.php';
	}
);
