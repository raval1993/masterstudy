<?php

STM_LMS_Bookmarks::init();

class STM_LMS_Bookmarks {
	public static function init() {
		add_action( 'wp_ajax_stm_lms_add_bookmark', 'STM_LMS_Bookmarks::add_bookmark' );
		add_action( 'wp_ajax_stm_lms_update_bookmark', 'STM_LMS_Bookmarks::update_bookmark' );
		add_action( 'wp_ajax_stm_lms_remove_bookmark', 'STM_LMS_Bookmarks::remove_bookmark' );
	}

	public static function get_user_bookmarks( $item_id, $course_id, $user_id ) {
		return stm_lms_get_user_bookmarks( $user_id, $item_id, $course_id );
	}

	public static function add_bookmark() {
		check_ajax_referer( 'add_bookmark', 'nonce' );

		if ( empty( $_GET['title'] ) || empty( $_GET['page_number'] ) || empty( $_GET['course_id'] ) || empty( $_GET['lesson_id'] ) ) {
			wp_send_json_error(
				array(
					'message' => 'No required params',
				),
				400
			);
		}

		$title     = $_GET['title'];
		$page_num  = $_GET['page_number'];
		$course_id = $_GET['course_id'];
		$lesson_id = $_GET['lesson_id'];
		$user_id   = get_current_user_id();

		if ( ! $user_id ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ) );
		}

		$result = stm_lms_add_user_bookmark( $user_id, $page_num, $title, $course_id, $lesson_id );

		if ( false !== $result['success'] ) {
			wp_send_json_success(
				array(
					'message'     => 'Bookmark added successfully',
					'bookmark_id' => $result['value'],
				)
			);
		} else {
			wp_send_json_error(
				array(
					'message' => 'Error adding bookmark',
					'error'   => $result['value'],
				),
				500
			);
		}
	}

	public static function remove_bookmark() {
		check_ajax_referer( 'remove_bookmark', 'nonce' );

		if ( empty( $_GET['id'] ) ) {
			wp_send_json_error(
				array(
					'message' => 'No required params',
				),
				400
			);
		}

		$bookmark_id = absint( $_GET['id'] );
		$user_id     = get_current_user_id();

		if ( ! $user_id ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ) );
		}

		$result = stm_lms_remove_user_bookmark( $bookmark_id, $user_id );

		if ( false !== $result['success'] ) {
			wp_send_json_success(
				array(
					'message' => 'Bookmark removed successfully',
				)
			);
		} else {
			wp_send_json_error(
				array(
					'message' => 'Error removing bookmark',
					'error'   => $result['value'],
				),
				500
			);
		}
	}

	public static function update_bookmark() {
		check_ajax_referer( 'update_bookmark', 'nonce' );

		if ( empty( $_GET['title'] ) || empty( $_GET['page_number'] ) || empty( $_GET['id'] ) ) {
			wp_send_json_error(
				array(
					'message' => 'No required params',
				),
				400
			);
		}

		$title       = sanitize_text_field( $_GET['title'] );
		$page_num    = absint( $_GET['page_number'] );
		$bookmark_id = absint( $_GET['id'] );
		$user_id     = get_current_user_id();

		if ( ! $user_id ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ) );
		}

		$result = stm_lms_update_user_bookmark( $bookmark_id, $title, $page_num, $user_id );

		if ( false !== $result['success'] ) {
			wp_send_json_success(
				array(
					'message' => 'Bookmark updated successfully',
				)
			);
		} else {
			wp_send_json_error(
				array(
					'message' => 'Error updating bookmark',
					'error'   => $result['value'],
				),
				500
			);
		}
	}
}
