<?php

/**
 * Select instructor courses component
 *
 * @var array $select_options - refer to 'components/select.php'
 * @var string $select_id - required - select id
 * @var boolean $select_on_load - select first option on load
 *
 * @package masterstudy
 */

wp_enqueue_script( 'masterstudy-instructor-courses-select' );
wp_enqueue_style( 'masterstudy-loader' );

wp_localize_script(
	'masterstudy-instructor-courses-select',
	'instructor_courses_data',
	array(
		'select_id'      => $select_id,
		'select_on_load' => $select_on_load ?? false,
		'post_types'     => $post_types ?? array(),
	)
);

$select_options = $select_options ?? array();

STM_LMS_Templates::show_lms_template(
	'components/select',
	array_merge(
		array(
			'default'      => '',
			'is_queryable' => false,
			'options'      => array(),
			'clearable'    => false,
			'select_id'    => $select_id,
			'select_name'  => $select_id,
		),
		$select_options
	)
);
