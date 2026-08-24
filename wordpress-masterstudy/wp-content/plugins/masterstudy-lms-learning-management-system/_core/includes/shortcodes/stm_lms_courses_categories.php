<?php

use MasterStudy\Lms\Plugin\Taxonomy;

add_shortcode( 'stm_lms_courses_categories', 'stm_lms_courses_categories_shortcode' );

/**
 * Function to transform old style naming to new styles
 * @param string $old_style
 *
 * @return string|void
 */
function get_new_style( string $old_style ) {
	switch ( $old_style ) {
		case 'style_1':
			return 'style-5';
		case 'style_2':
			return 'style-4';
		case 'style_3':
			return 'style-1';
		case 'style_4':
			return 'style-2';
		case 'style_5':
			return 'style-3';
	}
}

function stm_lms_courses_categories_shortcode( $atts, $content = null, $tag = '' ) {
	$atts = shortcode_atts(
		array(
			'taxonomy' => '',
			'style'    => 'style_1',
		),
		$atts,
		$tag
	);

	$atts['style'] = get_new_style( basename( sanitize_file_name( $atts['style'] ) ) );

	$terms = get_terms(
		array(
			'taxonomy'   => Taxonomy::COURSE_CATEGORY,
			'include'    => $atts['taxonomy'],
			'orderby'    => 'include',
			'hide_empty' => false,
		)
	);

	wp_enqueue_style( 'masterstudy-fonts', STM_LMS_URL . 'assets/css/variables/fonts.css', array(), STM_LMS_VERSION, false );
	wp_enqueue_style( 'ms_lms_courses_categories', STM_LMS_URL . 'assets/css/elementor-widgets/courses-categories/courses-categories.css', array(), STM_LMS_VERSION, false );

	ob_start();

	STM_LMS_Templates::show_lms_template(
		"elementor-widgets/courses-categories/{$atts['style']}",
		array(
			'taxonomy' => $atts['taxonomy'],
			'terms'    => $terms,
		)
	);

	return ob_get_clean();
}
