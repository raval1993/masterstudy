<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

register_activation_hook( MS_LMS_FILE, 'stm_lms_user_bookmarks' );

function stm_lms_user_bookmarks() {
	global $wpdb;

	$table_name = stm_lms_user_bookmarks_name( $wpdb );

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		title varchar(250) NOT NULL DEFAULT '',
		user_id bigint NOT NULL,
		course_id int(11) NOT NULL,
		lesson_id int(11) NOT NULL,
		page_number int(11) NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	dbDelta( $sql );
}
