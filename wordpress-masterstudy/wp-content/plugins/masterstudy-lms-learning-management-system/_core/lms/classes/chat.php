<?php

STM_LMS_Chat::init();

class STM_LMS_Chat {

	public static function init() {
		add_action( 'wp_ajax_stm_lms_send_message', 'STM_LMS_Chat::add_message' );
		add_action( 'wp_ajax_stm_lms_get_user_conversations', 'STM_LMS_Chat::get_user_conversations' );
		add_action( 'wp_ajax_stm_lms_get_user_messages', 'STM_LMS_Chat::get_user_messages' );
		add_action( 'wp_ajax_stm_lms_clear_new_messages', 'STM_LMS_Chat::stm_lms_handle_clear_new_messages' );
		add_action( 'wp_ajax_nopriv_stm_lms_clear_new_messages', 'STM_LMS_Chat::stm_lms_handle_clear_new_messages' );
	}

	public static function add_message() {
		check_ajax_referer( 'stm_lms_send_message', 'nonce' );

		$request_body = file_get_contents( 'php://input' );
		$data         = json_decode( $request_body, true );
		$response     = array(
			'status'  => 'error',
			'message' => esc_html__( 'An unexpected error occurred, please try again later', 'masterstudy-lms-learning-management-system' ),
		);

		if ( empty( $data['to'] ) ) {
			wp_send_json( $response );
		}
		$user_to = intval( $data['to'] );

		$user = STM_LMS_User::get_current_user();
		if ( empty( $user['id'] ) ) {
			wp_send_json( $response );
		}
		$user_from = $user['id'];

		$transient_name = self::transient_name( $user_to, 'chat' );
		delete_transient( $transient_name );

		if ( empty( $data['message'] ) ) {
			$response['message'] = esc_html__( 'Empty message sent', 'masterstudy-lms-learning-management-system' );

			wp_send_json( $response );
		}
		$message   = sanitize_textarea_field( $data['message'] );
		$timestamp = time();
		$status    = 'pending';

		do_action( 'stm_lms_before_send_chat_message' );
		stm_lms_add_user_chat( compact( 'user_to', 'user_from', 'message', 'timestamp', 'status' ) );

		wp_send_json(
			array(
				'response' => esc_html__( 'Message Sent', 'masterstudy-lms-learning-management-system' ),
				'status'   => 'success',
			)
		);
	}

	public static function get_user_conversations() {
		check_ajax_referer( 'stm_lms_get_user_conversations', 'nonce' );

		$response = array(
			'status'  => 'error',
			'message' => esc_html__( 'An unexpected error occurred, please try again later', 'masterstudy-lms-learning-management-system' ),
		);
		$user     = STM_LMS_User::get_current_user();
		if ( empty( $user['id'] ) ) {
			wp_send_json( $response );
		}
		$user_id = $user['id'];

		$transient_name = self::transient_name( $user_id, 'chat' );
		delete_transient( $transient_name );

		$response = array();

		$conversations = stm_lms_get_user_conversations( $user['id'] );
		if ( ! empty( $conversations ) ) {
			foreach ( $conversations as $conversation ) {
				$companion_id        = ( absint( $user_id ) === absint( $conversation['user_from'] ) ) ? $conversation['user_to'] : $conversation['user_from'];
				$companion           = STM_LMS_User::get_current_user( $companion_id );
				$conversation['ago'] = stm_lms_time_elapsed_string( wp_date( 'Y-m-d H:i:s', $conversation['timestamp'] ) );

				// Remove Emails from response
				unset( $companion['email'] );
				unset( $user['email'] );

				$response[] = array(
					'conversation_info' => $conversation,
					'me'                => $user,
					'companion'         => $companion,
				);

			}
		}

		wp_send_json( $response );
	}

	public static function get_user_messages() {
		check_ajax_referer( 'stm_lms_get_user_messages', 'nonce' );

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$response = array(
			'status'  => 'error',
			'message' => esc_html__( 'An unexpected error occurred, please try again later', 'masterstudy-lms-learning-management-system' ),
		);
		$user     = STM_LMS_User::get_current_user();
		if ( empty( $user['id'] ) ) {
			wp_send_json( $response );
		}
		$user_id = $user['id'];

		if ( empty( $_GET['id'] ) ) {
			wp_send_json( $response );
		}
		$conversation_id = intval( $_GET['id'] );

		$just_send = ( ! empty( $_GET['just_send'] ) && 'true' === $_GET['just_send'] );

		$messages = stm_lms_get_user_messages( $conversation_id, $user_id, array(), $just_send );

		$users = array();

		if ( ! empty( $messages ) ) {
			foreach ( $messages as $message_key => $message ) {
				$user_from_id = $message['user_from'];

				if ( empty( $users[ $user_from_id ] ) ) {
					$users[ $user_from_id ] = STM_LMS_User::get_current_user( $user_from_id );

					// Remove Email from response
					unset( $users[ $user_from_id ]['email'] );
				}

				$messages[ $message_key ]['message']   = STM_LMS_Quiz::deslash( nl2br( $message['message'] ) );
				$messages[ $message_key ]['isOwner']   = ( absint( $user_id ) === absint( $message['user_from'] ) );
				$messages[ $message_key ]['companion'] = $users[ $user_from_id ];
				$messages[ $message_key ]['ago']       = stm_lms_time_elapsed_string( wp_date( 'Y-m-d H:i:s', $message['timestamp'] ) );
			}
		}

		$messages = array_reverse( $messages );

		wp_send_json(
			array(
				'messages' => $messages,
			)
		);
	}

	public static function transient_name( $user_id, $name = '' ) {
		return "stm_lms_chat_{$user_id}_{$name}";
	}

	public static function user_new_messages( $user_id ) {
		$transient_name = self::transient_name( $user_id, 'chat' );
		$messages_num   = get_transient( $transient_name );

		if ( false === $messages_num ) {

			$conversations = stm_lms_get_user_conversations( $user_id );
			$messages_num  = 0;

			if ( ! empty( $conversations ) ) {
				foreach ( $conversations as $conversation ) {
					if ( (int) $user_id === (int) $conversation['user_from'] ) {
						$messages_num += $conversation['uf_new_messages'];
					} elseif ( (int) $user_id === (int) $conversation['user_to'] ) {
						$messages_num += $conversation['ut_new_messages'];
					}
				}
			}

			set_transient( $transient_name, $messages_num, 30 * 24 * 60 * 60 );
		}

		return $messages_num;
	}

	/**
	 * @deprecated
	 */
	public static function chat_url() {
		return ms_plugin_user_account_url( 'chat' );
	}

	public static function stm_lms_handle_clear_new_messages() {
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'stm_lms_clear_new_messages' ) || ! isset( $_GET['conversation_id'] ) ) {
			wp_die();
		}

		stm_lms_clear_new_messages( intval( $_GET['conversation_id'] ) );
	}

}
