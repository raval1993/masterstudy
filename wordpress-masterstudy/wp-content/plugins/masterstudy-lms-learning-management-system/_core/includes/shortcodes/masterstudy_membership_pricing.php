<?php
add_shortcode( 'masterstudy_membership_pricing', 'masterstudy_membership_pricing_shortcode' );
/**
 * Shortcode function for rendering
 */
function masterstudy_membership_pricing_shortcode() {
	return STM_LMS_Templates::load_lms_template( 'shortcodes/masterstudy_membership_pricing' );
}
