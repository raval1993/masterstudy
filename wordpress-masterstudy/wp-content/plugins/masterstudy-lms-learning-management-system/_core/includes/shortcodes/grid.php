<?php
/**
 * Render courses grid shortcode.
 *
 * @param array|string $atts Shortcode attributes.
 *
 * @return string
 */
function stm_lms_courses_grid_display( $atts ) {
	$atts = (array) $atts;

	$defaults = array(
		'load_more' => 0,
	);

	$parsed_atts    = shortcode_atts( $defaults, $atts, 'stm_lms_courses_grid_display' );
	$sanitized_atts = array();

	// we are killing attribute injection with this minimalistic fix
	$sanitized_atts['load_more'] = ! empty( $parsed_atts['load_more'] ) ? 1 : 0;
	ob_start();
	?>
	<div class="stm_lms_courses_grid stm_lms_courses">
		<?php
		STM_LMS_Templates::show_lms_template(
			'courses/grid',
			array(
				'args' => $sanitized_atts,
			)
		);

		if ( ! empty( $sanitized_atts['load_more'] ) ) {
			STM_LMS_Templates::show_lms_template(
				'courses/load_more',
				array(
					'args' => $sanitized_atts,
				)
			);
		}
		?>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'stm_lms_courses_grid_display', 'stm_lms_courses_grid_display' );
