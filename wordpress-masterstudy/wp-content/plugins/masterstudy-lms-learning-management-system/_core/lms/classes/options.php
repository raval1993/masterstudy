<?php

class STM_LMS_Options {

	private static $instance;

	private $settings = array();

	private function __construct() {
		$this->settings = get_option( 'stm_lms_settings', array() );
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function get_option( $option_name, $default = '' ) {
		$instance = self::get_instance();
		$options  = $instance->settings;

		if ( ! isset( $options[ $option_name ] ) ) {
			return $default;
		}

		if ( false === $options[ $option_name ] ) {
			return false;
		}

		// Return even an empty array
		if ( is_array( $options[ $option_name ] ) ) {
			return $options[ $option_name ];
		}

		return ! empty( $options[ $option_name ] ) ? $options[ $option_name ] : $default;
	}

	public static function instructor_registration_enabled() {
		return (bool) self::get_option( 'register_as_instructor', true );
	}

	public static function instructor_premoderation_enabled() {
		return (bool) self::get_option( 'instructor_premoderation', true );
	}

	public static function courses_page() {
		return apply_filters( 'stm_lms_courses_page', self::get_option( 'courses_page' ) );
	}

	public static function courses_page_slug() {
		$courses_page = self::courses_page();

		return ! empty( $courses_page ) ? get_post_field( 'post_name', $courses_page ) : 'courses';
	}

	public static function instructors_page() {
		return apply_filters( 'stm_lms_instructors_page', self::get_option( 'instructors_page' ) );
	}
}
