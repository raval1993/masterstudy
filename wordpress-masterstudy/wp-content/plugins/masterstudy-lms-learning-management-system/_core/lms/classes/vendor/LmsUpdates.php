<?php

namespace stmLms\Classes\Vendor;

abstract class LmsUpdates {
	private static $updates = array(
		'2.6.0'  => array( 'lms_chat_columns' ), // LMS Chat system update with fixes
		'2.6.4'  => array( 'lms_page_routes' ), // LMS Chat system update with fixes
		'2.6.7'  => array( 'lms_admin_notification_transient' ), // LMS Rate Us Admin Notification
		'2.9.22' => array( 'lms_add_lesson_video_sources' ), // Added lesson video sources
		'3.0.0'  => array(
			'lms_create_curriculum_tables', // Curriculum Refactoring
			'lms_migrate_course_data', // Curriculum & Course Files Refactoring
			'lms_migrate_lesson_data', // Lesson Files Refactoring & Changing Slide Lesson Type
			'lms_instructor_role_add_capability', // Add "list_users" capability to Instructor User Role
		),
		'3.0.6'  => array(
			'lms_remove_url_from_widgets', // Clear stm links from widgets
			'lms_remove_stm_links_from_content', // Clear stm links from pages content
			'lms_remove_copyright_url', // Clear stm links from copyright
		),
		'3.0.19' => array( 'lms_udemy_course_additional_info' ), // Add additional info for Udemy Courses
		'3.0.25' => array( 'lms_generate_required_pages' ), // Generate LMS Pages for old Users
		'3.1.0'  => array( 'lms_reset_page_routes' ), // Reset Page Routes for new Course Player
		'3.1.3'  => array(
			'lms_remove_url_from_widgets', // Clear stm links from widgets
			'lms_remove_copyright_url', // Clear stm links from copyright
		),
		'3.1.5'  => array( 'lms_remove_url_from_widgets' ),
		'3.1.7'  => array( 'lms_composite_index_to_user_lessons' ),
		'3.1.19' => array( 'lms_move_student_assignment_attachments' ), // Move Student Assignment Attachments to Post Meta
		'3.2.2'  => array( 'lms_replaced_auth_settings_values' ), // Profile Authorization settings changed
		'3.3.0'  => array( 'lms_set_default_certificate' ), // Find default certificate and set its ID to wp_option
		'3.3.1'  => array( 'lms_composite_index_to_user_courses_table' ), // Added indexing to user courses table
		'3.3.9'  => array( 'lms_reset_page_routes', 'lms_update_db_tables' ), // Reset Page Routes and Update Database Tables
		'3.3.14' => array( 'lms_reset_page_routes' ), // Reset Page Routes for new Manage Students
		'3.3.16' => array( 'lms_replaced_single_course_style' ), // Find course style option and rename it
		'3.3.35' => array( 'lms_flush_rewrite_rules' ), // Update permalinks for thank you page endpoints
		'3.4.10' => array( 'lms_rewrite_profile_url_option', 'lms_reset_page_routes' ),
		'3.4.13' => array( 'lms_flush_rewrite_rules', 'lms_reset_page_routes' ), // Update permalinks for thank you page endpoints
		'3.5.0'  => array( 'lms_update_grades' ), // Update Grades tables and data
		'3.5.1'  => array( 'lms_reset_page_routes' ),
		'3.5.5'  => array( 'lms_flush_rewrite_rules', 'lms_reset_page_routes' ), // Refresh Page Routes for Public Profile pages
		'3.5.7'  => array( 'lms_rename_lazyload_settings' ),
		'3.5.13' => array( 'lms_update_user_lesson_table' ), // Update User Lessons Table
		'3.5.18' => array( 'lms_migration_order_lms_courses' ),
		'3.5.22' => array( 'lms_update_certificate_fonts' ),
		'3.5.23' => array( 'lms_add_coming_soon_meta' ),
		'3.5.26' => array( 'lms_update_lesson_video_markers' ), //Update Database Tables
		'3.5.30' => array( 'update_table_lms_user_quizzes' ),
		'3.6.5'  => array( 'lms_update_elementor_templates' ),
		'3.6.6'  => array( 'lms_update_media_library_ext_types_vtt' ), // Update default media library extensions to include vtt
		'3.6.7'  => array( 'lms_flush_rewrite_rules', 'lms_reset_page_routes' ), // Update default media library extensions to include vtt
		'3.6.14' => array( 'lms_create_bookmarks_table' ), // Create bookmark table
		'3.6.16' => array( 'lms_update_woocommerce_checkout_setting' ), // If the 'wocommerce_checkout' setting is enabled, set 'ecommerce_engine' to 'woocommerce'.
		'3.6.17' => array( 'lms_update_quiz_attempt_history_retake_after_passing' ), // Update default media library extensions to include vtt
		'3.6.18' => array( 'lms_transactions_currency_backfill_from_payments' ),
		'3.6.19' => array( 'lms_update_user_answers_table' ), // Update answers table
		'3.7.0'  => array( 'lms_update_db_tables' ),
		'3.7.3'  => array( 'lms_update_zero_sale_price' ), // If user had sale_price 0 then make it an empty string, otherwise keep the value,
		'3.7.5'  => array( 'lms_add_course_statuses' ), // Add default course statuses
		'3.7.15' => array( 'lms_update_pricing' ), // Update old pricing to new separated pricing
		'3.7.17' => array( 'lms_flush_rewrite_rules', 'lms_reset_page_routes' ),
		'3.7.18' => array( 'lms_add_assignments_times_table' ), // Add times table for assignments
		'3.7.32' => array( 'lms_update_point_system_table' ), // Add course context to point system table
		'3.7.34' => array( 'lms_create_lesson_notes_table' ), // Create Pro Plus lesson notes table
	);

	/**
	 * Init LMS Updates
	 */
	public static function init() {
		if ( version_compare( get_option( 'stm_lms_version', '1.0.0' ), STM_LMS_VERSION, '<' ) ) {
			self::update_version();
		}
	}

	/**
	 * Get All Updates
	 * @return array
	 */
	public static function get_updates() {
		return self::$updates;
	}

	/**
	 * Check If Needs Updates
	 * @return bool
	 */
	public static function needs_to_update() {
		$current_db_version = get_option( 'stm_lms_db_version', '1.0.0' );
		$update_versions    = array_keys( self::get_updates() );
		usort( $update_versions, 'version_compare' );

		return ! empty( $current_db_version ) && version_compare( $current_db_version, end( $update_versions ), '<' );
	}

	/**
	 * Run Needed Updates
	 */
	private static function maybe_update_db_version() {
		if ( self::needs_to_update() ) {
			$current_db_version = get_option( 'stm_lms_db_version', '1.0.0' );
			$updates            = self::get_updates();

			foreach ( $updates as $version => $callback_arr ) {
				if ( version_compare( $current_db_version, $version, '<' ) ) {
					foreach ( $callback_arr as $callback ) {
						call_user_func( array( '\\stmLms\\Classes\\Vendor\\LmsUpdateCallbacks', $callback ) );
					}
				}
			}
		}

		update_option( 'stm_lms_db_version', STM_LMS_DB_VERSION, true );
	}

	/**
	 * Update Plugin Version
	 */
	public static function update_version() {
		update_option( 'stm_lms_version', STM_LMS_VERSION, true );
		self::maybe_update_db_version();
	}
}
