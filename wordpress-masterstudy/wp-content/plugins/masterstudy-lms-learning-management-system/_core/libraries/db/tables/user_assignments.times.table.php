<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

register_activation_hook( MS_LMS_FILE, 'stm_lms_user_assignments_times_table' );

function stm_lms_user_assignments_times_table() {
	global $wpdb;

	$table_name = stm_lms_user_assignments_times_name( $wpdb );

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		user_assignment_time_id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id bigint NOT NULL,
		assignment_id int(11) NOT NULL,
		start_time DATETIME NOT NULL,
		end_time DATETIME NOT NULL,
		PRIMARY KEY (user_assignment_time_id),
		KEY end_time (end_time)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	dbDelta( $sql );
}
