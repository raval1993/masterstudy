<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function stm_lms_user_assignments_name( $wpdb ) {
	return $wpdb->prefix . 'stm_lms_user_assignments';
}

function stm_lms_user_assignments_table() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();
	$table_name      = stm_lms_user_assignments_name( $wpdb );

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		user_id bigint NOT NULL,
		course_id int(11) NOT NULL,
		assignment_id int(11) NOT NULL,
		user_assignment_id int(11) NOT NULL,
		grade tinyint(4) DEFAULT NULL,
		status varchar(45) NOT NULL DEFAULT '',
		updated_at int(11) NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";

	dbDelta( $sql );
}
