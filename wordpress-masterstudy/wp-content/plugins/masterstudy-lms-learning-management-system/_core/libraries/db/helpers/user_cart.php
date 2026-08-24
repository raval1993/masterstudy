<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function stm_lms_add_user_cart( $user_cart ) {
	global $wpdb;
	$table_name = stm_lms_user_cart_name( $wpdb );

	$wpdb->insert(
		$table_name,
		$user_cart
	);
}

function stm_lms_update_user_cart( $item_id, $price ) {
	global $wpdb;
	$table_name          = stm_lms_user_cart_name( $wpdb );

	$query = $wpdb->prepare(
		"SELECT price FROM $table_name WHERE item_id = %d", // phpcs:ignore
		$item_id
	);

	$existing_item_price = $wpdb->get_var( $query ); // phpcs:ignore

	if ( $existing_item_price && $existing_item_price !== $price ) {
		$wpdb->update(
			$table_name,
			array(
				'price' => $price,
			),
			array(
				'item_id' => $item_id,
			)
		);
	}
}

function stm_lms_get_item_in_cart( $user_id, $item_id, $fields = array() ) {
	global $wpdb;
	$table = stm_lms_user_cart_name( $wpdb );

	$fields = ( empty( $fields ) ) ? '*' : implode( ',', $fields );

	return $wpdb->get_results(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT {$fields} FROM {$table} WHERE user_ID = %d AND item_id = %d",
			$user_id,
			$item_id
		),
		ARRAY_N
	);
}

function stm_lms_get_cart_items( $user_id, $fields = array() ) {
	global $wpdb;
	$table = stm_lms_user_cart_name( $wpdb );

	$fields = ( empty( $fields ) ) ? '*' : implode( ',', $fields );

	return $wpdb->get_results(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT {$fields} FROM {$table} WHERE user_ID = %d",
			$user_id
		),
		ARRAY_A
	);
}

function stm_lms_get_delete_cart_item( $user_id, $item_id ) {
	global $wpdb;
	$table = stm_lms_user_cart_name( $wpdb );

	$wpdb->delete(
		$table,
		array(
			'user_id' => $user_id,
			'item_id' => $item_id,
		)
	);
}

function stm_lms_get_delete_cart_items( $user_id ) {
	global $wpdb;
	$table = stm_lms_user_cart_name( $wpdb );

	$wpdb->delete(
		$table,
		array(
			'user_id' => $user_id,
		)
	);
}

/**
 * Action hook to clear the user's cart after a successful course purchase.
 *
 * This action is triggered once the purchase process is completed, ensuring
 * that the user's cart is cleared to prevent duplicate items in subsequent transactions.
 *
 * @param int $user_id The ID of the user who completed the purchase.
 */
add_action(
	'stm_lms_purchase_action_done',
	function ( $user_id ) {
		stm_lms_get_delete_cart_items( $user_id );
	}
);

/**
 * Delete cart items associated with a specific course.
 *
 * Removes all entries from the cart database table where the given course ID exists.
 *
 * @param int $course_id The ID of the course to remove from user carts.
 */
function stm_lms_delete_course_from_cart( $course_id ) {
	global $wpdb;
	$table = stm_lms_user_cart_name( $wpdb );

	$wpdb->delete(
		$table,
		array(
			'item_id' => $course_id,
		),
		array( '%d' )
	);
}

/**
 * Clear user carts from deleted courses.
 *
 * Triggered when a course is deleted, this function removes the corresponding
 * entries from the user cart database table using a helper function.
 *
 * @param int $post_id The ID of the post being deleted.
 */
function stm_lms_clear_cart_on_course_deletion( $post_id ) {
	$allowed_post_types = array( 'stm-courses', 'stm-course-bundles' );

	if ( ! in_array( get_post_type( $post_id ), $allowed_post_types, true ) ) {
		return;
	}

	stm_lms_delete_course_from_cart( $post_id );
}

add_action( 'before_delete_post', 'stm_lms_clear_cart_on_course_deletion' );
