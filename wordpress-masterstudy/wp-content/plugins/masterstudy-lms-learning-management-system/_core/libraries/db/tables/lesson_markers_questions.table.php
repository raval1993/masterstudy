<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

register_activation_hook( MS_LMS_FILE, 'stm_lms_lesson_marker_questions' );

function stm_lms_lesson_marker_questions() {
	global $wpdb;

	$table_name = stm_lms_lesson_marker_questions_name( $wpdb );

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id BIGINT NOT NULL AUTO_INCREMENT,
		lesson_id INT(11) NOT NULL,
		marker INT(9) NOT NULL,
		rewatch INT(9) NOT NULL,
		caption TEXT NOT NULL,
		type varchar(45) NOT NULL,
		content TEXT NOT NULL,
		answers TEXT NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	dbDelta( $sql );
}
