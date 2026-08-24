<?php
$atts = shortcode_atts(
	array(
		'modal'               => false,
		'type'                => 'register',
		'is_instructor'       => STM_LMS_Instructor::is_instructor(),
		'only_for_instructor' => true,
		'dark_mode'           => false,
	),
	$atts
);

STM_LMS_Templates::show_lms_template( 'components/authorization/main', $atts );
