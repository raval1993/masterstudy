<?php

add_action(
	'admin_init',
	function() {
		if ( class_exists( 'STMBulkNotices' ) ) {
			$bulk_data = array(
				'dependency_plugins'    => array(
					'masterstudy-lms-learning-management-system'     => 'MasterStudy LMS WordPress Plugin',
					'masterstudy-lms-learning-management-system-pro' => 'MasterStudy LMS – Online Courses, eLearning PRO Plus',
				),
				'notice_type'           => 'bulk-update notice',
				'notice_logo'           => 'attent_triangle.svg',
				'notice_title'          => esc_html__( 'Update Needed for MasterStudy Ecosystem', 'masterstudy-lms-learning-management-system' ),
				'notice_desc'           => sprintf(
					esc_html__( 'There have been updates in MasterStudy ecosystem. To make sure everything works correctly, click %s to update the following:', 'masterstudy-lms-learning-management-system' ),
					'<b>' . esc_html__( 'Update all', 'masterstudy-lms-learning-management-system' ) . '</b>'
				),
				'notice_btn_one_title'  => esc_html__( 'Update All', 'masterstudy-lms-learning-management-system' ),
				'notice_btn_one_update' => esc_html__( 'Updating...', 'masterstudy-lms-learning-management-system' ),
				'notice_btn_one_class'  => 'stm-button-bulk-update',
				'notice_btn_one'        => '#',
				'notice_btn_two_attrs'  => 'data-type=discard data-key=dependencies target=_blank',
				'notice_error_message'  => esc_html__( 'Something went wrong, try again', 'masterstudy-lms-learning-management-system' ),
			);

			/**
			 * Initialize STMBulkNotices with bulk update data.
			 * The theme name parameter is now optional.
			 */
			new STMBulkNotices( $bulk_data, 'MasterStudy' );
		}
	}
);
