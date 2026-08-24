<?php // phpcs:ignoreFile

defined( 'ABSPATH' ) || die();

/**
 * Utilities for extracting scalar values from Divi 5 attributes.
 */
final class DMSLMS_D5_Module_Utils {
	/**
	 * Recursively extract a scalar value from nested D5 attribute arrays.
	 *
	 * @param mixed $value Raw attribute value.
	 *
	 * @return string
	 */
	public static function extract_scalar( $value ) {
		if ( is_scalar( $value ) ) {
			return (string) $value;
		}

		if ( ! is_array( $value ) ) {
			return '';
		}

		if ( isset( $value['desktop']['value'] ) && is_scalar( $value['desktop']['value'] ) ) {
			return (string) $value['desktop']['value'];
		}

		if ( isset( $value['value'] ) && is_scalar( $value['value'] ) ) {
			return (string) $value['value'];
		}

		if ( isset( $value['innerContent'] ) ) {
			$inner = self::extract_scalar( $value['innerContent'] );
			if ( '' !== $inner ) {
				return $inner;
			}
		}

		foreach ( $value as $child ) {
			$child_value = self::extract_scalar( $child );
			if ( '' !== $child_value ) {
				return $child_value;
			}
		}

		return '';
	}
}

/**
 * Divi 5 render callbacks for migrated modules.
 */
final class DMSLMS_D5_Render_Callbacks {
	/**
	 * Normalize visibility values to LMS shortcode format.
	 *
	 * Shortcode expects: hidden|showing
	 *
	 * @param string $value Raw value from D5 settings.
	 *
	 * @return string
	 */
	private static function normalize_visibility( $value ) {
		$value = sanitize_key( (string) $value );

		if ( in_array( $value, array( 'hide', 'hidden' ), true ) ) {
			return 'hidden';
		}

		return 'showing';
	}

	/**
	 * Build a safe module title with fallback.
	 *
	 * @param array  $attrs         Module attrs.
	 * @param string $default_title Default title when setting is empty.
	 *
	 * @return string
	 */
	private static function get_module_title( $attrs, $default_title ) {
		$title = sanitize_text_field( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['title'] ?? '' ) );

		return '' !== $title ? $title : $default_title;
	}

	/**
	 * Render callback for Courses Searchbox (Divi 5).
	 *
	 * @param array $attrs Module attrs.
	 *
	 * @return string
	 */
	public static function render_courses_searchbox( $attrs ) {
		$title = self::get_module_title( $attrs, 'MS Courses Search' );
		$style = DMSLMS_D5_Module_Utils::extract_scalar( $attrs['searchStyle'] ?? '' );

		$style = sanitize_key( $style );

		if ( empty( $style ) ) {
			$style = 'style_1';
		}

		return do_shortcode(
			sprintf(
				'[stm_courses_searchbox title="%1$s" style=%2$s]',
				esc_attr( $title ),
				esc_attr( $style )
			)
		);
	}

	/**
	 * Render callback for Courses Grid (Divi 5).
	 *
	 * @param array $attrs Module attrs.
	 *
	 * @return string
	 */
	public static function render_courses_grid( $attrs ) {
		$hide_top_bar = DMSLMS_D5_Render_Callbacks::normalize_visibility(
			DMSLMS_D5_Module_Utils::extract_scalar( $attrs['hideTopBar'] ?? 'showing' )
		);
		$title        = self::get_module_title( $attrs, 'MS Courses' );
		$load_more    = DMSLMS_D5_Render_Callbacks::normalize_visibility(
			DMSLMS_D5_Module_Utils::extract_scalar( $attrs['loadMore'] ?? 'showing' )
		);
		$sort_courses = DMSLMS_D5_Render_Callbacks::normalize_visibility(
			DMSLMS_D5_Module_Utils::extract_scalar( $attrs['sortCourses'] ?? 'showing' )
		);
		$image_size   = sanitize_key( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['imageSize'] ?? '' ) );
		$per_row      = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['perRow'] ?? 6 ) );
		$per_page     = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['perPage'] ?? '' ) );

		$shortcode = sprintf(
			'[stm_lms_courses_grid hide_top_bar="%1$s" title="%2$s" hide_load_more="%3$s" hide_sort="%4$s" per_row="%5$d" image_size="%6$s" posts_per_page="%7$s"]',
			esc_attr( $hide_top_bar ?: 'showing' ),
			esc_attr( $title ),
			esc_attr( $load_more ?: 'showing' ),
			esc_attr( $sort_courses ?: 'showing' ),
			(int) ( $per_row ?: 6 ),
			esc_attr( $image_size ),
			esc_attr( $per_page ? (string) $per_page : '' )
		);

		return do_shortcode( $shortcode );
	}

	/**
	 * Render callback for Courses Carousel (Divi 5).
	 *
	 * @param array $attrs Module attrs.
	 *
	 * @return string
	 */
	public static function render_courses_carousel( $attrs ) {
		$title            = self::get_module_title( $attrs, 'MS Courses Carousel' );
		$query            = sanitize_key( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['query'] ?? 'none' ) );
		$prev_next        = sanitize_key( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['prevNext'] ?? 'enable' ) );
		$show_categories  = sanitize_key( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['showCategories'] ?? 'disable' ) );
		$per_row          = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['perRow'] ?? 4 ) );
		$posts_per_page   = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['postsPerPage'] ?? '' ) );
		$image_size       = sanitize_key( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['imageSize'] ?? '' ) );
		$taxonomy         = sanitize_text_field( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['taxonomy'] ?? '' ) );
		$taxonomy_default = $taxonomy;

		$shortcode = sprintf(
			'[stm_lms_courses_carousel title="%1$s" query="%2$s" prev_next="%3$s" show_categories="%4$s" per_row="%5$d" taxonomy="%6$s" taxonomy_default="%7$s" image_size="%8$s"%9$s]',
			esc_attr( $title ),
			esc_attr( $query ?: 'none' ),
			esc_attr( $prev_next ?: 'enable' ),
			esc_attr( $show_categories ?: 'disable' ),
			(int) ( $per_row ?: 4 ),
			esc_attr( $taxonomy ),
			esc_attr( $taxonomy_default ),
			esc_attr( $image_size ),
			$posts_per_page ? ' posts_per_page="' . esc_attr( (string) $posts_per_page ) . '"' : ''
		);

		return do_shortcode( $shortcode );
	}

	public static function render_courses_categories( $attrs ) {
		$style    = sanitize_key( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['style'] ?? 'style_1' ) );
		$taxonomy = sanitize_text_field( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['taxonomy'] ?? '' ) );

		return do_shortcode( sprintf( '[stm_lms_courses_categories style=%1$s taxonomy="%2$s"]', esc_attr( $style ?: 'style_1' ), esc_attr( $taxonomy ) ) );
	}

	public static function render_recent_courses( $attrs ) {
		$posts_per_page = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['postsPerPage'] ?? 4 ) );
		$per_row        = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['perRow'] ?? 6 ) );
		$style          = sanitize_key( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['style'] ?? 'style_1' ) );

		return do_shortcode( sprintf( '[stm_lms_recent_courses posts_per_page="%1$d" per_row="%2$d" style="%3$s"]', (int) ( $posts_per_page ?: 4 ), (int) ( $per_row ?: 6 ), esc_attr( $style ?: 'style_1' ) ) );
	}

	public static function render_single_course_carousel( $attrs ) {
		$query     = sanitize_key( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['query'] ?? 'none' ) );
		$prev_next = sanitize_key( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['prevNext'] ?? 'enable' ) );
		$taxonomy  = sanitize_text_field( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['taxonomy'] ?? '' ) );

		return do_shortcode( sprintf( '[stm_lms_single_course_carousel query="%1$s" prev_next="%2$s" taxonomy="%3$s"]', esc_attr( $query ?: 'none' ), esc_attr( $prev_next ?: 'enable' ), esc_attr( $taxonomy ) ) );
	}

	public static function render_instructors_carousel( $attrs ) {
		$title       = self::get_module_title( $attrs, 'MS Instructors Carousel' );
		$per_row     = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['perRow'] ?? 4 ) );
		$per_row_md  = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['perRowMd'] ?? 3 ) );
		$per_row_sm  = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['perRowSm'] ?? 2 ) );
		$per_row_xs  = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['perRowXs'] ?? 1 ) );
		$style       = sanitize_key( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['style'] ?? 'style_1' ) );
		$sort        = sanitize_key( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['sort'] ?? '' ) );
		$prev_next   = sanitize_key( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['prevNext'] ?? 'enable' ) );
		$title_color = sanitize_hex_color( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['titleColor'] ?? '' ) );

		$atts = array(
			'title'       => $title,
			'per_row'     => $per_row ?: 4,
			'per_row_md'  => $per_row_md ?: 3,
			'per_row_sm'  => $per_row_sm ?: 2,
			'per_row_xs'  => $per_row_xs ?: 1,
			'title_color' => $title_color ?: '',
			'style'       => $style ?: 'style_1',
			'sort'        => ( 'default' === $sort ) ? '' : $sort,
			'prev_next'   => $prev_next ?: 'enable',
			'pagination'  => '',
			'css'         => '',
			'limit'       => 10,
		);

		return \STM_LMS_Templates::load_lms_template( 'shortcodes/stm_lms_instructors_carousel', $atts );
	}

	public static function render_featured_teacher( $attrs ) {
		$instructor = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['instructor'] ?? '' ) );
		$position   = sanitize_text_field( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['position'] ?? '' ) );
		$bio_raw    = DMSLMS_D5_Module_Utils::extract_scalar( $attrs['bio'] ?? '' );
		$image_url  = esc_url_raw( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['imageUrl'] ?? '' ) );
		$bio        = str_replace( array( '</p>', '<p>' ), '', $bio_raw );

		$atts = array(
			'css'        => '',
			'instructor' => $instructor,
			'position'   => $position,
			'bio'        => $bio,
			'image'      => attachment_url_to_postid( $image_url ),
		);

		return \STM_LMS_Templates::load_lms_template( 'shortcodes/stm_lms_featured_teacher', $atts );
	}

	public static function render_google_classrooms( $attrs ) {
		$title          = self::get_module_title( $attrs, 'MS Google Classrooms' );
		$number_of_room = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['numberOfRooms'] ?? 3 ) );

		return do_shortcode( sprintf( '[stm_lms_google_classroom title="%1$s" number_of_rooms="%2$d"]', esc_attr( $title ), (int) ( $number_of_room ?: 3 ) ) );
	}

	public static function render_course_bundles( $attrs ) {
		$title    = self::get_module_title( $attrs, 'MS Course Bundles' );
		$columns  = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['columns'] ?? 3 ) );
		$per_page = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['perPage'] ?? 3 ) );
		if ( class_exists( 'MasterStudy\Lms\Pro\addons\CourseBundle\CourseBundle' ) ) {
			$atts = array(
				'css'            => '',
				'title'          => $title,
				'columns'        => (string) ( $columns ?: 3 ),
				'posts_per_page' => (string) ( $per_page ?: 3 ),
			);

			return \STM_LMS_Templates::load_lms_template( 'shortcodes/stm_lms_course_bundles', $atts );
		}

		return '';
	}

	public static function render_certificate_checker( $attrs ) {
		$title = self::get_module_title( $attrs, 'MS Certificate Checker' );

		return sprintf( '<h3>%1$s</h3>%2$s', esc_html( $title ), do_shortcode( sprintf( '[stm_lms_certificate_checker title="%s"]', esc_attr( $title ) ) ) );
	}

	public static function render_icon_box( $attrs ) {
		$image_url   = esc_url_raw( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['imageUrl'] ?? '' ) );
		$button_url  = esc_url_raw( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['buttonUrl'] ?? '' ) );
		$body_title  = sanitize_text_field( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['bodyTitle'] ?? '' ) );
		$body_text   = sanitize_text_field( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['bodyText'] ?? '' ) );
		$body_btn    = sanitize_text_field( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['bodyButton'] ?? '' ) );
		$image_width = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['imageWidth'] ?? 220 ) );
		$title_color = sanitize_hex_color( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['titleColor'] ?? '#fff' ) );
		$back_color  = sanitize_hex_color( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['backgroundColor'] ?? '#385bce' ) );
		$inline      = sprintf( 'background-color:%1$s; color:%2$s!important;', esc_attr( $back_color ?: '#385bce' ), esc_attr( $title_color ?: '#fff' ) );

		return '<div class="icon-box"><div class="image" style="width:' . (int) ( $image_width ?: 220 ) . 'px;"><img src="' . esc_url( $image_url ) . '" alt=""></div><div class="icon-body"><h2>' . esc_html( $body_title ) . '</h2><p>' . esc_html( $body_text ) . '</p><a href="' . esc_url( $button_url ) . '" class="icon-box-btn et_pb_button_0 et_pb_bg_layout_dark" style="' . esc_attr( $inline ) . '">' . esc_html( $body_btn ) . '</a></div></div>';
	}

	public static function render_blog_list( $attrs ) {
		$count       = absint( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['count'] ?? 3 ) );
		$type        = sanitize_key( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['type'] ?? 'list' ) );
		$title_color = sanitize_hex_color( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['titleColor'] ?? '#fff' ) );
		$back_color  = sanitize_hex_color( DMSLMS_D5_Module_Utils::extract_scalar( $attrs['backgroundColor'] ?? '#F5B830' ) );
		$args        = array( 'posts_per_page' => $count ?: 3 );
		$main_class  = ( 'grid' === $type ) ? 'blog-list-ms-main-grid' : 'blog-list-ms-main-list';
		$my_query    = new \WP_Query( $args );
		$content     = "<div class={$main_class}>";
		if ( $my_query->have_posts() ) {
			while ( $my_query->have_posts() ) {
				$my_query->the_post();
				$day      = get_the_date( 'd' );
				$month    = get_the_date( 'M' );
				$category = get_the_category_list( ',', '', get_the_ID() );
				$tags     = get_the_tag_list( '', ',' );
				$inline   = "color:{$title_color}; background-color:{$back_color}; border-color:{$back_color}";
				$content  .= "<div class='blog-list-ms-single-post'><div class='blog-list-ms-image'>" . get_the_post_thumbnail( get_the_ID(), 'full' ) . "</div><div class='blog-list-ms-item'><div class='blog-list-ms-item-inner'><div class='blog-list-ms-post-time' style='{$inline}'><div class='date-d'>{$day}</div><div class='date-m'>{$month}</div></div></div><div class='blog-list-ms-item-inner'><div class='blog-list-ms-title'><a href='" . esc_url( get_permalink() ) . "'>" . esc_html( get_the_title() ) . "</a></div><div class='blog-list-ms-excerpt'>" . esc_html( get_the_excerpt() ) . "</div><div class='blog-list-ms-separator'></div><div class='blog-list-ms-cats'><div class='ms-cats-label'>" . esc_html__( 'Posted in:', 'masterstudy-lms-divi' ) . '</div>' . $category . '</div>';
				if ( ! empty( $tags ) ) {
					$content .= "<div class='blog-list-ms-cats'><div class='ms-cats-label'>" . esc_html__( 'Tags:', 'masterstudy-lms-divi' ) . '</div>' . $tags . '</div>';
				}
				$content .= "</div></div><div class='blog-list-ms-separator-after'><div class='blog-list-ms-separator-left'></div><div class='blog-list-ms-separator-right'></div></div></div>";
			}
		}
		$content .= '</div>';
		wp_reset_postdata();

		return $content;
	}
}

/**
 * Register migrated Divi 5 module on backend.
 */
function dmslms_register_divi5_modules() {
	if ( ! class_exists( '\ET\Builder\Packages\ModuleLibrary\ModuleRegistration' ) ) {
		return;
	}

	$module_json_path = DMSLMS_DIR_PATH . '/includes/d5/modules-json/courses-searchbox/';

	\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
		$module_json_path,
		array(
			'render_callback' => array( 'DMSLMS_D5_Render_Callbacks', 'render_courses_searchbox' ),
		)
	);

	$grid_module_json_path = DMSLMS_DIR_PATH . '/includes/d5/modules-json/courses-grid/';

	\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
		$grid_module_json_path,
		array(
			'render_callback' => array( 'DMSLMS_D5_Render_Callbacks', 'render_courses_grid' ),
		)
	);

	$carousel_module_json_path = DMSLMS_DIR_PATH . '/includes/d5/modules-json/courses-carousel/';
	\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
		$carousel_module_json_path,
		array(
			'render_callback' => array( 'DMSLMS_D5_Render_Callbacks', 'render_courses_carousel' ),
		)
	);

	$recent_courses_module_json_path = DMSLMS_DIR_PATH . '/includes/d5/modules-json/recent-courses/';
	\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
		$recent_courses_module_json_path,
		array(
			'render_callback' => array( 'DMSLMS_D5_Render_Callbacks', 'render_recent_courses' ),
		)
	);

	$courses_categories_module_json_path = DMSLMS_DIR_PATH . '/includes/d5/modules-json/courses-categories/';
	\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
		$courses_categories_module_json_path,
		array(
			'render_callback' => array( 'DMSLMS_D5_Render_Callbacks', 'render_courses_categories' ),
		)
	);

	$single_course_carousel_module_json_path = DMSLMS_DIR_PATH . '/includes/d5/modules-json/single-course-carousel/';
	\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
		$single_course_carousel_module_json_path,
		array(
			'render_callback' => array( 'DMSLMS_D5_Render_Callbacks', 'render_single_course_carousel' ),
		)
	);

	$instructors_carousel_module_json_path = DMSLMS_DIR_PATH . '/includes/d5/modules-json/instructors-carousel/';
	\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
		$instructors_carousel_module_json_path,
		array(
			'render_callback' => array( 'DMSLMS_D5_Render_Callbacks', 'render_instructors_carousel' ),
		)
	);

	$featured_teacher_module_json_path = DMSLMS_DIR_PATH . '/includes/d5/modules-json/featured-teacher/';
	\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
		$featured_teacher_module_json_path,
		array(
			'render_callback' => array( 'DMSLMS_D5_Render_Callbacks', 'render_featured_teacher' ),
		)
	);

	$google_classrooms_module_json_path = DMSLMS_DIR_PATH . '/includes/d5/modules-json/google-classrooms/';
	\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
		$google_classrooms_module_json_path,
		array(
			'render_callback' => array( 'DMSLMS_D5_Render_Callbacks', 'render_google_classrooms' ),
		)
	);

	$course_bundles_module_json_path = DMSLMS_DIR_PATH . '/includes/d5/modules-json/course-bundles/';
	\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
		$course_bundles_module_json_path,
		array(
			'render_callback' => array( 'DMSLMS_D5_Render_Callbacks', 'render_course_bundles' ),
		)
	);

	$certificate_checker_module_json_path = DMSLMS_DIR_PATH . '/includes/d5/modules-json/certificate-checker/';
	\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
		$certificate_checker_module_json_path,
		array(
			'render_callback' => array( 'DMSLMS_D5_Render_Callbacks', 'render_certificate_checker' ),
		)
	);

	$icon_box_module_json_path = DMSLMS_DIR_PATH . '/includes/d5/modules-json/icon-box/';
	\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
		$icon_box_module_json_path,
		array(
			'render_callback' => array( 'DMSLMS_D5_Render_Callbacks', 'render_icon_box' ),
		)
	);

	$blog_list_module_json_path = DMSLMS_DIR_PATH . '/includes/d5/modules-json/blog-list/';
	\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::register_module(
		$blog_list_module_json_path,
		array(
			'render_callback' => array( 'DMSLMS_D5_Render_Callbacks', 'render_blog_list' ),
		)
	);
}

add_action( 'init', 'dmslms_register_divi5_modules' );

/**
 * Enqueue Divi 5 Visual Builder registration script.
 */
function dmslms_get_registered_script_deps( $deps ) {
	$registered_deps = array();

	foreach ( $deps as $dep ) {
		if ( wp_script_is( $dep, 'registered' ) ) {
			$registered_deps[] = $dep;
		}
	}

	return $registered_deps;
}

/**
 * Enqueue Divi 5 Visual Builder registration script.
 */
function dmslms_enqueue_divi5_vb_scripts() {
	if ( ! function_exists( 'et_core_is_fb_enabled' ) || ! et_core_is_fb_enabled() ) {
		return;
	}

	if ( ! class_exists( '\ET\Builder\VisualBuilder\Assets\PackageBuildManager' ) ) {
		return;
	}

	$vb_script_deps = dmslms_get_registered_script_deps(
		array(
			'divi-module-library',
			'divi-vendor-wp-hooks',
		)
	);

	$package_build_scripts = array(
		'dmslms-d5-courses-searchbox-vb'    => 'courses-searchbox.js',
		'dmslms-d5-courses-grid-vb'         => 'courses-grid.js',
		'dmslms-d5-courses-carousel-vb'     => 'courses-carousel.js',
		'dmslms-d5-recent-courses-vb'       => 'recent-courses.js',
		'dmslms-d5-courses-categories-vb'   => 'courses-categories.js',
		'dmslms-d5-single-course-carousel-vb' => 'single-course-carousel.js',
		'dmslms-d5-instructors-carousel-vb' => 'instructors-carousel.js',
		'dmslms-d5-featured-teacher-vb'     => 'featured-teacher.js',
		'dmslms-d5-google-classrooms-vb'    => 'google-classrooms.js',
		'dmslms-d5-course-bundles-vb'       => 'course-bundles.js',
		'dmslms-d5-certificate-checker-vb'  => 'certificate-checker.js',
		'dmslms-d5-icon-box-vb'             => 'icon-box.js',
		'dmslms-d5-blog-list-vb'            => 'blog-list.js',
	);

	foreach ( $package_build_scripts as $name => $script_file ) {
		\ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
			array(
				'name'    => $name,
				'version' => DMSLMS_VERSION,
				'script'  => array(
					'src'                => DMSLMS_DIR_URL . 'includes/d5/scripts/' . $script_file,
					'deps'               => $vb_script_deps,
					'enqueue_top_window' => true,
					'enqueue_app_window' => true,
				),
			)
		);
	}
}

add_action( 'divi_visual_builder_assets_before_enqueue_scripts', 'dmslms_enqueue_divi5_vb_scripts', 99 );
