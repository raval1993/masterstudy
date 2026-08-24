<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function stm_lms_get_user_bookmarks( $user_id, $item_id, $course_id ) {
	global $wpdb;
	$table         = stm_lms_user_bookmarks_name( $wpdb );
	$select_fields = 'id, page_number, title';
	$sql           = "SELECT {$select_fields} FROM {$table} WHERE user_id = %d AND lesson_id = %d AND course_id = %d";
	$params        = array(
		$user_id,
		$item_id,
		$course_id,
	);

	$sql = $wpdb->prepare( $sql, ...$params ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	return $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

function stm_lms_add_user_bookmark( $user_id, $page_num, $title, $course_id, $lesson_id ) {
	global $wpdb;
	$table  = stm_lms_user_bookmarks_name( $wpdb );
	$sql    = "INSERT INTO {$table} (user_id, page_number, title, course_id, lesson_id) VALUES (%d, %d,  %s, %d, %d)";
	$params = array(
		$user_id,
		$page_num,
		$title,
		$course_id,
		$lesson_id,
	);

	$result = $wpdb->query(
		$wpdb->prepare( $sql, ...$params ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	);

	$success = false !== $result;

	return array(
		'success' => $success,
		'value'   => $success ? $wpdb->insert_id : $wpdb->last_error,
	);
}

function stm_lms_remove_user_bookmark( $bookmark_id, $user_id ) {
	global $wpdb;
	$table  = stm_lms_user_bookmarks_name( $wpdb );
	$sql    = "DELETE FROM {$table} WHERE id=%d AND user_id=%d";
	$params = array(
		$bookmark_id,
		$user_id,
	);

	$result = $wpdb->query(
		$wpdb->prepare( $sql, ...$params ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	);

	return array(
		'success' => false !== $result,
		'value'   => false !== $result ? '' : $wpdb->last_error,
	);
}

function stm_lms_update_user_bookmark( $bookmark_id, $title, $page_num, $user_id ) {
	global $wpdb;
	$table  = stm_lms_user_bookmarks_name( $wpdb );
	$sql    = "UPDATE {$table} SET title=%s, page_number=%d WHERE id=%d AND user_id=%d";
	$params = array(
		$title,
		$page_num,
		$bookmark_id,
		$user_id,
	);

	$result = $wpdb->query(
		$wpdb->prepare( $sql, ...$params ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	);

	return array(
		'success' => false !== $result,
		'value'   => $wpdb->last_error,
	);
}
