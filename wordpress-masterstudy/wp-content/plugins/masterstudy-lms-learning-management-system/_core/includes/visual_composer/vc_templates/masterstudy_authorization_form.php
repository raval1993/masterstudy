<?php
$atts      = vc_map_get_attributes( $this->getShortcode(), $atts );
$form_type = $atts['type'] ? $atts['type'] : 'login';
$atts      = array(
	'modal'               => false,
	'type'                => $form_type,
	'is_instructor'       => STM_LMS_Instructor::is_instructor(),
	'only_for_instructor' => false,
	'dark_mode'           => false,
);

STM_LMS_Templates::show_lms_template( 'components/authorization/main', $atts );
