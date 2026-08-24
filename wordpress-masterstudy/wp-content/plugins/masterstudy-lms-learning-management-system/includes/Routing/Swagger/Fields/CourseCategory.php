<?php

namespace MasterStudy\Lms\Routing\Swagger\Fields;

use MasterStudy\Lms\Routing\Swagger\Field;

final class CourseCategory extends Field {
	/**
	 * Object Properties
	 */
	public static array $properties = array(
		'id'                => array(
			'type' => 'integer',
		),
		'term_id'           => array(
			'type' => 'integer',
		),
		'name'              => array(
			'type' => 'string',
		),
		'slug'              => array(
			'type' => 'string',
		),
		'description'       => array(
			'type' => 'string',
		),
		'parent'            => array(
			'type' => 'integer',
		),
		'parent_name'       => array(
			'type' => 'string',
		),
		'count'             => array(
			'type' => 'integer',
		),
		'course_page_style' => array(
			'type' => 'string',
		),
		'course_image'      => array(
			'type' => 'integer',
		),
		'course_image_url'  => array(
			'type' => 'string',
		),
		'course_icon'       => array(
			'type' => 'string',
		),
		'course_color'      => array(
			'type' => 'string',
		),
	);
}
