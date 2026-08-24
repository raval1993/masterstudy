<?php

STM_LMS_Templates::load_templates();

class STM_LMS_Templates {


	private static $instance;

	public static function load_templates() {
		add_filter( 'the_content', array( self::get_instance(), 'courses_archive_content' ), 100 );
		add_filter( 'the_content', array( self::get_instance(), 'instructors_archive_content' ), 100 );
		add_filter( 'single_template', array( self::get_instance(), 'lms_template' ) );
		add_action( 'stm-lms-content-stm-courses', array( self::get_instance(), 'single_course' ), 100 );
		add_action( 'stm-lms-content-stm-course-bundles', array( self::get_instance(), 'single_bundle' ), 100 );

		add_filter( 'taxonomy_template', array( self::get_instance(), 'taxonomy_archive_content' ), 100, 1 );

	}

	public static function taxonomy_archive_content( $template ) {
		if ( is_admin() ) {
			return $template;
		}
		$taxonomy = get_query_var( 'taxonomy' );
		if ( 'stm_lms_course_taxonomy' === $taxonomy ) {
			$template = self::locate_template( 'stm-lms-taxonomy-archive' );

		}
		return $template;
	}

	public static function courses_archive_content( $content ) {
		$courses_page = STM_LMS_Options::courses_page();

		/* Do nothing if no courses page */
		if ( empty( $courses_page ) || ! is_page( $courses_page ) ) {
			return $content;
		}

		if ( is_page( $courses_page ) ) {

			remove_filter( 'the_content', array( self::get_instance(), 'courses_archive_content' ), 100 );
			remove_filter( 'wp_get_attachment_image_attributes', 'twenty_twenty_one_get_attachment_image_attributes', 10 );

			$courses_page_type = get_post_meta( $courses_page, 'courses_page_type', true );

			if ( 'elementor' === $courses_page_type || 'gutenberg' === $courses_page_type ) {
				$courses = null;
			} else {
				$courses = self::load_lms_template( 'courses/archive' );
			}

			add_filter( 'the_content', array( self::get_instance(), 'courses_archive_content' ), 100 );

			return $content . $courses;
		}

		return $content;
	}

	public static function instructors_archive_content( $content ) {
		$instructors_page         = STM_LMS_Options::instructors_page();
		$separate_registration    = STM_LMS_Options::get_option( 'separate_instructor_registration', '' );
		$instructor_register_page = STM_LMS_Options::get_option( 'instructor_registration_page', '' );
		$instructor_register_page = ! empty( $instructor_register_page ) ? is_page( $instructor_register_page ) : false;
		$extra_content            = '';

		remove_filter( 'the_content', array( self::get_instance(), 'instructors_archive_content' ), 100 );

		if ( ! empty( $instructors_page ) && is_page( $instructors_page ) ) {
			$extra_content  = '<div class="stm_lms_instructors_grid_wrapper">';
			$extra_content .= '<h1 class="text-center">' . esc_html__( 'Instructors', 'masterstudy-lms-learning-management-system' ) . '</h1>';
			$extra_content .= '<div class="stm_lms_courses stm_lms_courses__archive">';
			$extra_content .= self::load_lms_template(
				'instructors/grid'
			);
			$extra_content .= '</div>';
			$extra_content .= '</div>';
		}

		if ( $separate_registration && $instructor_register_page ) {
			$extra_content = self::load_lms_template(
				'components/authorization/main',
				array(
					'modal'               => false,
					'type'                => 'register',
					'is_instructor'       => STM_LMS_Instructor::is_instructor(),
					'only_for_instructor' => true,
					'dark_mode'           => false,
				)
			);
		}

		add_filter( 'the_content', array( self::get_instance(), 'instructors_archive_content' ), 100 );

		return $content . $extra_content;
	}

	public static function single_course() {
		if ( STM_LMS_Helpers::is_ms_starter_purchased() && ! STM_LMS_Helpers::is_pro() ) {
			$course_id = get_the_ID();
			$style     = function_exists( 'ms_plugin_get_course_page_style' )
				? ms_plugin_get_course_page_style( $course_id )
				: ( isset( $_GET['course_style'] ) ? sanitize_text_field( wp_unslash( $_GET['course_style'] ) ) : 'default' );

			if ( empty( $style ) || 'default' === $style ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo self::load_lms_template( 'course', array( 'course_style' => 'default' ) );

				return;
			}

			$elementor_templates = function_exists( 'masterstudy_lms_get_my_templates' )
				? masterstudy_lms_get_my_templates( false )
				: array();
			$native_templates    = function_exists( 'masterstudy_lms_get_native_templates' )
				? masterstudy_lms_get_native_templates()
				: array();

			$matched_elementor = array();
			if ( is_array( $elementor_templates ) ) {
				$matched_elementor = array_values(
					array_filter(
						$elementor_templates,
						function ( $existing_style ) use ( $style ) {
							return isset( $existing_style['name'] ) && $existing_style['name'] === $style;
						}
					)
				);
			}

			if ( ! empty( $matched_elementor ) && isset( $matched_elementor[0]['id'] ) && class_exists( '\Elementor\Plugin' ) ) {
				global $masterstudy_single_page_course_id;
				$masterstudy_single_page_course_id = $course_id;

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $matched_elementor[0]['id'] );

				return;
			}

			$matched_native = array_filter(
				$native_templates,
				function ( $existing_style ) use ( $style ) {
					return isset( $existing_style['name'] ) && $existing_style['name'] === $style;
				}
			);

			if ( ! empty( $matched_native ) ) {
				self::show_lms_template( 'course/' . $style );

				return;
			}
		} else {
			if ( isset( $_GET['course_style'] ) ) {
				if ( 'default' === $_GET['course_style'] ) {
					self::show_lms_template( 'course' );
				}
				self::show_lms_template( 'course/' . sanitize_text_field( wp_unslash( $_GET['course_style'] ) ) );

				return;
			}
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo self::load_lms_template( 'course', array( 'course_style' => 'default' ) );
	}

	public static function single_bundle() {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo self::load_lms_template( 'bundle/single' );
	}

	public static function lms_template( $template ) {
		global $post;
		$post_types = array(
			'stm-courses',
			'stm-course-bundles',
		);
		if ( in_array( $post->post_type, $post_types ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			return self::locate_template( 'masterstudy-lms-learning-management-system' );
		}

		return $template;
	}

	public static function locate_template( $template_name, $stm_lms_vars = array() ) {
		$template_name = self::sanitize_template_name( $template_name );
		$template_name = '/stm-lms-templates/' . $template_name . '.php';
		$template_name = apply_filters( 'stm_lms_template_name', $template_name, $stm_lms_vars );
		$lms_template  = apply_filters( 'stm_lms_template_file', STM_LMS_PATH, $template_name ) . $template_name;

		return ( locate_template( $template_name ) ) ? locate_template( $template_name ) : realpath( $lms_template );
	}

	public static function vc_locate_template( $template_name ) {
		$plugin_path         = STM_LMS_PATH . '/includes/visual_composer/' . $template_name . '.php';
		$theme_template_name = '/' . $template_name . '.php';
		return ( locate_template( $theme_template_name ) ) ? locate_template( $theme_template_name ) : $plugin_path;

	}

	public static function load_lms_template( $template_name, $stm_lms_vars = array() ) {
		ob_start();
		extract( $stm_lms_vars ); // phpcs:ignore WordPress.PHP.DontExtract

		$tpl = self::locate_template( $template_name, $stm_lms_vars );
		if ( file_exists( $tpl ) ) {
			include $tpl;
		}

		return apply_filters( "stm_lms_{$template_name}", ob_get_clean(), $stm_lms_vars );
	}

	public static function show_lms_template( $template_name, $stm_lms_vars = array() ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo self::load_lms_template( $template_name, $stm_lms_vars );
	}


	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function stm_lms_locate_vc_element( $templates, $template_name = '', $custom_path = '' ) {
		$located = false;

		foreach ( (array) $templates as $template ) {

			$folder = $template;

			if ( ! empty( $template_name ) ) {
				$template = $template_name;
			}

			if ( substr( $template, -4 ) !== '.php' ) {
				$template .= '.php';
			}

			if ( empty( $custom_path ) ) {
				$located = locate_template( 'partials/vc_parts/' . $folder . '/' . $template );
				if ( ! ( $located ) ) {
					$located = STM_LMS_PATH . '/includes/shortcodes/partials/' . $folder . '/' . $template;
				}
			} else {
				$located = locate_template( $custom_path );
				if ( ! ( $located ) ) {
					$located = STM_LMS_PATH . '/' . $custom_path . '.php';
				}
			}

			if ( file_exists( $template_name ) ) {
				break;
			}
		}

		return apply_filters( 'stm_lms_locate_vc_element', $located, $templates );
	}

	public static function stm_lms_load_vc_element( $__template, $__vars = array(), $__template_name = '', $custom_path = '' ) {
		extract( $__vars ); // phpcs:ignore WordPress.PHP.DontExtract
		$element = self::stm_lms_locate_vc_element( $__template, $__template_name, $custom_path );
		if ( ! file_exists( $element ) && strpos( $__template_name, 'style_' ) !== false ) {
			$element = str_replace( $__template_name, 'style_1', $element );
		}
		if ( file_exists( $element ) ) {
			include $element;
		} else {
			echo esc_html__( 'Element not found', 'masterstudy-lms-learning-management-system' );
		}
	}

	public static function sanitize_template_name( $template_name ) {
		$pattern = '/\.\.?\/|\\\+/';

		if ( preg_match( $pattern, $template_name ) ) {
			return false;
		}

		return $template_name;
	}
}
