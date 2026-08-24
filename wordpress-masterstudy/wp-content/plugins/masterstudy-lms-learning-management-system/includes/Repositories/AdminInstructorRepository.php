<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Plugin\Addons;
use MasterStudy\Lms\Utility\WpDate;
use WP_User;
use WP_User_Query;

final class AdminInstructorRepository {
	private const DEFAULT_PER_PAGE = 20;

	private const DATE_FORMAT = 'M j, Y - H:i';

	/**
	 * @param array<string, mixed> $params
	 *
	 * @return array{
	 *   instructors: array<int, array<string, mixed>>,
	 *   total: int,
	 *   pages: int,
	 *   ai_enabled_for_all: bool
	 * }
	 */
	public function get_instructors( array $params ): array {
		$page     = max( 1, absint( $params['page'] ?? 1 ) );
		$per_page = max( 1, absint( $params['per_page'] ?? self::DEFAULT_PER_PAGE ) );
		$order    = strtoupper( sanitize_text_field( $params['order'] ?? 'DESC' ) );

		if ( ! in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
			$order = 'DESC';
		}

		$query = new WP_User_Query(
			array(
				'role__in'   => array( 'subscriber', 'stm_lms_instructor' ),
				'paged'      => $page,
				'number'     => $per_page,
				'meta_key'   => 'submission_date',
				'orderby'    => 'meta_value_num',
				'order'      => $order,
				'meta_query' => array(
					array(
						'key'     => 'submission_date',
						'compare' => 'EXISTS',
					),
				),
			)
		);

		$total = (int) $query->get_total();

		return array(
			'instructors'        => array_map( array( $this, 'map_instructor' ), $query->get_results() ),
			'total'              => $total,
			'pages'              => (int) ceil( $total / $per_page ),
			'ai_enabled_for_all' => $this->is_ai_enabled_for_all(),
		);
	}

	/**
	 * @param array<string, mixed> $data
	 *
	 * @return array{
	 *   status: 'created'|'validation_error'|'forbidden'|'error',
	 *   instructor?: array<string, mixed>,
	 *   errors?: array<string, array<int, string>>
	 * }
	 */
	public function create_instructor( array $data ): array {
		$mode = sanitize_text_field( $data['mode'] ?? '' );

		if ( ! in_array( $mode, array( 'new', 'existing' ), true ) ) {
			return array(
				'status' => 'error',
			);
		}

		$validation_result = $this->validate_create_instructor_data( $data, $mode );

		if ( 'created' !== $validation_result['status'] ) {
			return $validation_result;
		}

		if ( 'new' === $mode ) {
			$user_id = $this->create_user( $data );

			if ( $user_id <= 0 ) {
				return array(
					'status' => 'error',
				);
			}

			wp_send_new_user_notifications( $user_id, 'user' );
		} else {
			$user_id = absint( $data['user_id'] ?? 0 );
			$user    = get_user_by( 'ID', $user_id );

			if ( ! $user instanceof WP_User ) {
				return array(
					'status' => 'error',
				);
			}

			$user->add_role( 'stm_lms_instructor' );
		}

		$this->approve_instructor_request(
			$user_id,
			(string) ( $data['degree'] ?? '' ),
			(string) ( $data['expertize'] ?? '' ),
			(string) ( $data['admin_note'] ?? '' )
		);

		$instructor = $this->get_instructor( $user_id );

		if ( null === $instructor ) {
			return array(
				'status' => 'error',
			);
		}

		return array(
			'status'     => 'created',
			'instructor' => $instructor,
		);
	}

	/**
	 * @return array<string, mixed>|null
	 */
	public function get_instructor( int $user_id ): ?array {
		$user = get_user_by( 'ID', $user_id );

		if ( ! $user instanceof WP_User ) {
			return null;
		}

		return $this->map_instructor( $user );
	}

	/**
	 * @return array{status: string, submission_history: array<int, array<string, mixed>>}|null
	 */
	public function update_status( int $user_id, string $status, string $admin_message ): ?array {
		$user = get_user_by( 'ID', $user_id );

		if ( ! $user instanceof WP_User ) {
			return null;
		}

		$status             = sanitize_text_field( $status );
		$admin_message      = sanitize_text_field( $admin_message );
		$submission_date    = (int) get_user_meta( $user_id, 'submission_date', true );
		$submission_data    = get_user_meta( $user_id, 'become_instructor', true );
		$submission_history = $this->normalize_submission_history(
			get_user_meta( $user_id, 'submission_history', true )
		);
		$custom_fields      = ! empty( $submission_data['fields'] ) && is_array( $submission_data['fields'] )
			? $submission_data['fields']
			: array();

		update_user_meta( $user_id, 'submission_status', $status );

		$email_data = array(
			'user_login'    => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
			'user_id'       => $user_id,
			'admin_message' => $admin_message,
		);

		foreach ( $custom_fields as $field ) {
			if ( ! is_array( $field ) || empty( $field['value'] ) ) {
				continue;
			}

			$label = '';
			if ( ! empty( $field['label'] ) ) {
				$label = $field['label'];
			} elseif ( ! empty( $field['slug'] ) ) {
				$label = $field['slug'];
			} elseif ( ! empty( $field['field_name'] ) ) {
				$label = $field['field_name'];
			}

			if ( '' === $label ) {
				continue;
			}

			$email_data[ $label ] = $field['value'];
		}

		if ( 'approved' === $status ) {
			if ( ! in_array( 'stm_lms_instructor', (array) $user->roles, true ) ) {
				wp_update_user(
					array(
						'ID'   => $user_id,
						'role' => 'stm_lms_instructor',
					)
				);
			}

			$email_data_approve = array(
				'instructor_name' => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
				'blog_name'       => \STM_LMS_Helpers::masterstudy_lms_get_site_name(),
				'login_url'       => \STM_LMS_Helpers::masterstudy_lms_get_login_url(),
				'site_url'        => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
				'date'            => gmdate( 'Y-m-d H:i:s' ),
				'admin_comment'   => $admin_message,
			);

			$template = wp_kses_post(
				'Hi {{instructor_name}},<br>
				Congratulations! Your application to become an instructor on {{blog_name}} has been approved.<br>
				You can now log in to your instructor account using the following link:<br>
				Login URL: <a href="{{login_url}}" target="_blank">Login URL</a> <br>
				We are excited to have you on board and look forward to your contributions!'
			);

			$message = \MS_LMS_Email_Template_Helpers::render( $template, $email_data_approve );
			$subject = esc_html__( 'Instructor application approved', 'masterstudy-lms-learning-management-system' );

			if ( '' !== $admin_message ) {
				$message .= '<br>' . $admin_message;
			}

			\STM_LMS_Helpers::send_email(
				$user->user_email,
				$subject,
				$message,
				'stm_lms_email_update_user_status_approved',
				$email_data_approve
			);
		} else {
			$email_data_reject = array(
				'user_login'    => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
				'blog_name'     => \STM_LMS_Helpers::masterstudy_lms_get_site_name(),
				'site_url'      => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
				'date'          => gmdate( 'Y-m-d H:i:s' ),
				'admin_comment' => $admin_message,
			);

			$template = wp_kses_post(
				'Hi {{user_login}},<br>
				Thank you for your interest in becoming an instructor on {{blog_name}} <br>
				After careful review, we regret to inform you that your application has not been approved at this time.
				We appreciate the time and effort you put into your submission.
				You\'re welcome to update your application and reapply in the future.
				If you have any questions or would like feedback, feel free to reach out to our team.<br>
				Best regards.'
			);

			$message = \MS_LMS_Email_Template_Helpers::render( $template, $email_data_reject );
			$subject = esc_html__( 'Update on Your Instructor Application', 'masterstudy-lms-learning-management-system' );

			if ( '' !== $admin_message ) {
				$message .= '<br>' . $admin_message;
			}

			\STM_LMS_Helpers::send_email(
				$user->user_email,
				$subject,
				$message,
				'stm_lms_email_update_user_status_rejected',
				$email_data_reject
			);
		}

		$answer_date     = time();
		$submission_info = array(
			'request_date'           => $submission_date,
			'request_display_date'   => gmdate( self::DATE_FORMAT, $submission_date ),
			'request_date_formatted' => $this->format_history_date( $submission_date ),
			'status'                 => $status,
			'message'                => $admin_message,
			'answer_date'            => $answer_date,
			'answer_display_date'    => gmdate( self::DATE_FORMAT, $answer_date ),
			'answer_date_formatted'  => $this->format_history_date( $answer_date ),
			'viewed'                 => '',
		);
		array_unshift( $submission_history, $submission_info );
		update_user_meta( $user_id, 'submission_history', $submission_history );

		return array(
			'status'             => $status,
			'submission_history' => $submission_history,
		);
	}

	public function update_ban( int $user_id, bool $banned ): bool {
		if ( ! get_userdata( $user_id ) ) {
			return false;
		}

		update_user_meta( $user_id, 'stm_lms_user_banned', $banned );

		return true;
	}

	public function update_ai_access( int $user_id, bool $ai_enabled ): bool {
		if ( ! $this->is_ai_lab_available() ) {
			return false;
		}

		if ( ! get_userdata( $user_id ) ) {
			return false;
		}

		update_user_meta( $user_id, 'stm_lms_ai_enabled', $ai_enabled );

		return true;
	}

	public function update_all_ai_access( bool $ai_enabled ): void {
		if ( ! $this->is_ai_lab_available() ) {
			return;
		}

		$query = new WP_User_Query(
			array(
				'role__in'   => array( 'subscriber', 'stm_lms_instructor' ),
				'number'     => -1,
				'fields'     => 'ids',
				'meta_query' => array(
					array(
						'key'     => 'submission_date',
						'compare' => 'EXISTS',
					),
				),
			)
		);

		foreach ( $query->get_results() as $instructor_id ) {
			update_user_meta( (int) $instructor_id, 'stm_lms_ai_enabled', $ai_enabled );
		}

		update_option( 'stm_lms_ai_enabled_for_all', $ai_enabled );
	}

	public function is_ai_enabled_for_all(): bool {
		return $this->is_ai_lab_available()
			&& rest_sanitize_boolean( get_option( 'stm_lms_ai_enabled_for_all', false ) );
	}

	public function is_ai_lab_available(): bool {
		return \STM_LMS_Helpers::is_pro_plus()
			&& function_exists( 'is_ms_lms_addon_enabled' )
			&& is_ms_lms_addon_enabled( Addons::AI_LAB );
	}

	/**
	 * @param array<string, mixed> $data
	 */
	private function create_user( array $data ): int {
		$username   = sanitize_user( (string) ( $data['username'] ?? '' ), true );
		$email      = sanitize_email( (string) ( $data['email'] ?? '' ) );
		$first_name = sanitize_text_field( (string) ( $data['first_name'] ?? '' ) );
		$last_name  = sanitize_text_field( (string) ( $data['last_name'] ?? '' ) );
		$url        = $this->normalize_user_url( (string) ( $data['url'] ?? '' ) );
		$password   = wp_generate_password( 24, true, true );
		$name       = trim( $first_name . ' ' . $last_name );

		$user_id = wp_insert_user(
			array(
				'user_login'   => $username,
				'user_email'   => $email,
				'user_pass'    => $password,
				'first_name'   => $first_name,
				'last_name'    => $last_name,
				'user_url'     => $url,
				'display_name' => '' !== $name ? $name : $username,
				'role'         => 'stm_lms_instructor',
			)
		);

		if ( is_wp_error( $user_id ) ) {
			return 0;
		}

		return (int) $user_id;
	}

	/**
	 * @param array<string, mixed> $data
	 *
	 * @return array{
	 *   status: 'created'|'validation_error'|'forbidden',
	 *   errors?: array<string, array<int, string>>
	 * }
	 */
	private function validate_create_instructor_data( array &$data, string $mode ): array {
		$errors = array();

		if ( 'new' === $mode ) {
			$username = isset( $data['username'] ) ? sanitize_user( wp_unslash( (string) $data['username'] ), true ) : '';
			$email    = isset( $data['email'] ) ? sanitize_email( (string) $data['email'] ) : '';

			if ( '' === $username || ! validate_username( $username ) ) {
				$errors['username'] = array(
					esc_html__( 'Please enter a valid username.', 'masterstudy-lms-learning-management-system' ),
				);
			} elseif ( username_exists( $username ) ) {
				$errors['username'] = array(
					esc_html__( 'This username is already registered. Please choose another one.', 'masterstudy-lms-learning-management-system' ),
				);
			} else {
				$data['username'] = $username;
			}

			if ( email_exists( $email ) ) {
				$errors['email'] = array(
					esc_html__( 'This email is already registered. Please choose another one.', 'masterstudy-lms-learning-management-system' ),
				);
			}
		} else {
			$user_id = absint( $data['user_id'] ?? 0 );
			$user    = get_userdata( $user_id );

			if ( ! $user ) {
				$errors['user_id'] = array(
					esc_html__( 'The selected user does not exist.', 'masterstudy-lms-learning-management-system' ),
				);
			} else {
				if ( ! current_user_can( 'promote_user', $user_id ) ) {
					return array(
						'status' => 'forbidden',
					);
				}

				if ( in_array( 'stm_lms_instructor', (array) $user->roles, true ) ) {
					$errors['user_id'] = array(
						esc_html__( 'This user is already an instructor.', 'masterstudy-lms-learning-management-system' ),
					);
				}
			}
		}

		if ( ! empty( $errors ) ) {
			return array(
				'status' => 'validation_error',
				'errors' => $errors,
			);
		}

		return array(
			'status' => 'created',
		);
	}

	private function approve_instructor_request( int $user_id, string $degree, string $expertize, string $admin_note ): void {
		$submission_date = time();
		$note            = '' !== trim( $admin_note )
			? sanitize_textarea_field( $admin_note )
			: esc_html__( 'Instructor created by administrator.', 'masterstudy-lms-learning-management-system' );
		$submission_data = array(
			'become_instructor' => true,
			'fields_type'       => 'default',
			'degree'            => sanitize_text_field( $degree ),
			'expertize'         => sanitize_text_field( $expertize ),
		);

		update_user_meta( $user_id, 'become_instructor', $submission_data );
		update_user_meta( $user_id, 'submission_date', $submission_date );
		update_user_meta( $user_id, 'stm_lms_ai_enabled', $this->is_ai_enabled_for_all() );

		$this->update_status( $user_id, 'approved', $note );
	}

	/**
	 * @param WP_User $user
	 *
	 * @return array<string, mixed>
	 */
	private function map_instructor( WP_User $user ): array {
		$user_id              = $user->ID;
		$submission_data      = get_user_meta( $user_id, 'become_instructor', true );
		$submission_date      = (int) get_user_meta( $user_id, 'submission_date', true );
		$custom_fields        = ! empty( $submission_data['fields'] ) && is_array( $submission_data['fields'] )
			? array_values( $submission_data['fields'] )
			: array();
		$degree               = ! empty( $submission_data['degree'] )
			? $submission_data['degree']
			: esc_html__( 'N/A', 'masterstudy-lms-learning-management-system' );
		$expertize            = ! empty( $submission_data['expertize'] )
			? $submission_data['expertize']
			: esc_html__( 'N/A', 'masterstudy-lms-learning-management-system' );
		$submission_history   = $this->normalize_submission_history(
			get_user_meta( $user_id, 'submission_history', true )
		);
		$submission_timestamp = $submission_date;
		$submission_date_gmt  = $this->timestamp_to_gmt_datetime( $submission_timestamp );
		$submission_date      = WpDate::format_gmt_for_site( $submission_date_gmt );

		return array(
			'id'                        => $user_id,
			'edit_link'                 => get_edit_user_link( $user_id ),
			'display_name'              => $user->display_name,
			'user_email'                => $user->user_email,
			'degree'                    => $degree,
			'status'                    => (string) get_user_meta( $user_id, 'submission_status', true ),
			'expertize'                 => $expertize,
			'submission_date'           => gmdate( self::DATE_FORMAT, $submission_timestamp ),
			'submission_date_gmt'       => $submission_date_gmt,
			'submission_date_formatted' => $submission_date['formatted'],
			'submission_time'           => $submission_timestamp,
			'submission_history'        => $submission_history,
			'ai_enabled'                => $this->is_ai_lab_available()
				&& rest_sanitize_boolean( get_user_meta( $user_id, 'stm_lms_ai_enabled', true ) ),
			'banned'                    => rest_sanitize_boolean( get_user_meta( $user_id, 'stm_lms_user_banned', true ) ),
			'custom_fields'             => $custom_fields,
		);
	}

	/**
	 * @param mixed $history
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function normalize_submission_history( $history ): array {
		if ( empty( $history ) || ! is_array( $history ) ) {
			return array();
		}

		return array_values(
			array_filter(
				array_map( array( $this, 'normalize_submission_history_item' ), $history )
			)
		);
	}

	/**
	 * @param mixed $item
	 *
	 * @return array<string, mixed>|null
	 */
	private function normalize_submission_history_item( $item ): ?array {
		if ( ! is_array( $item ) ) {
			return null;
		}

		$request_date = absint( $item['request_date'] ?? 0 );
		$answer_date  = absint( $item['answer_date'] ?? 0 );

		$item['request_date']           = $request_date;
		$item['request_display_date']   = isset( $item['request_display_date'] ) ? (string) $item['request_display_date'] : '';
		$item['request_date_formatted'] = $this->format_history_date( $request_date, $item['request_display_date'] );
		$item['status']                 = isset( $item['status'] ) ? (string) $item['status'] : '';
		$item['message']                = isset( $item['message'] ) ? (string) $item['message'] : '';
		$item['answer_date']            = $answer_date;
		$item['answer_display_date']    = isset( $item['answer_display_date'] ) ? (string) $item['answer_display_date'] : '';
		$item['answer_date_formatted']  = $this->format_history_date( $answer_date, $item['answer_display_date'] );
		$item['viewed']                 = isset( $item['viewed'] ) ? (string) $item['viewed'] : '';

		return $item;
	}

	private function normalize_user_url( string $url ): string {
		$url = trim( $url );

		if ( '' === $url || 'http://' === $url ) {
			return '';
		}

		$url = esc_url_raw( $url );

		if ( '' === $url ) {
			return '';
		}

		$protocols = implode( '|', array_map( 'preg_quote', wp_allowed_protocols() ) );

		return preg_match( '/^(' . $protocols . '):/is', $url ) ? $url : 'http://' . $url;
	}

	/**
	 * @return array<string, string>
	 */
	private function format_history_date( int $timestamp, string $fallback = '' ): array {
		if ( $timestamp > 0 ) {
			return WpDate::format_gmt_for_site( $this->timestamp_to_gmt_datetime( $timestamp ) )['formatted'];
		}

		if ( '' !== $fallback ) {
			return \STM_LMS_Helpers::format_date( $fallback );
		}

		return array();
	}

	private function timestamp_to_gmt_datetime( int $timestamp ): string {
		return $timestamp > 0 ? gmdate( 'Y-m-d H:i:s', $timestamp ) : '';
	}
}
