<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

register_activation_hook( MS_LMS_FILE, 'stm_lms_lesson_marker_user_answers' );

function stm_lms_lesson_marker_user_answers() {
	global $wpdb;

	$table_name = stm_lms_lesson_marker_user_answers_name( $wpdb );

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		user_answer_id BIGINT NOT NULL AUTO_INCREMENT,
		user_id BIGINT NOT NULL,
		course_id int NOT NULL,
		lesson_id int NOT NULL,
		question_id int NOT NULL,
		user_answers TEXT NOT NULL,
		submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (user_answer_id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	dbDelta( $sql );
}
