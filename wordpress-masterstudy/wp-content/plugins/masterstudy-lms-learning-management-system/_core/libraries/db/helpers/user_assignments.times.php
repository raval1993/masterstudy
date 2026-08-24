<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function stm_lms_add_user_assignment_time( $user_assignment_time ): bool {
	global $wpdb;
	$table_name = stm_lms_user_assignments_times_name( $wpdb );

	$result = $wpdb->insert(
		$table_name,
		$user_assignment_time
	);

	return false !== $result;
}

function stm_lms_get_user_assignments_time( $user_id, $assignment_id, $fields = array() ) {
	global $wpdb;
	$table = stm_lms_user_assignments_times_name( $wpdb );

	$allowed_columns = array( 'user_id', 'assignment_id', 'start_time', 'end_time' );
	$fields          = is_array( $fields ) ? array_intersect( $fields, $allowed_columns ) : array();
	$fields          = $fields ? $fields : $allowed_columns;
	$fields          = implode( ', ', array_map( 'sanitize_key', $fields ) );

	return $wpdb->get_results(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT {$fields} FROM {$table} WHERE user_id = %d AND assignment_id = %d",
			(int) $user_id,
			(int) $assignment_id
		),
		ARRAY_A
	);
}

function stm_lms_delete_user_assignment_time( $user_id, $item_id ): bool {
	global $wpdb;
	$table = stm_lms_user_assignments_times_name( $wpdb );

	$result = $wpdb->delete(
		$table,
		array(
			'user_id'       => (int) $user_id,
			'assignment_id' => (int) $item_id,
		)
	);

	return false !== $result;
}
