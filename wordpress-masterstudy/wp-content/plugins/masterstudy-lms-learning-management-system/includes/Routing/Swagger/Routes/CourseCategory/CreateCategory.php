<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\CourseCategory;

use MasterStudy\Lms\Routing\Swagger\Fields\CourseCategory as CourseCategoryField;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class CreateCategory extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'category'          => array(
				'type'     => 'string',
				'required' => true,
			),
			'description'       => array(
				'type'     => 'string',
				'required' => false,
			),
			'parent_category'   => array(
				'type'     => 'integer',
				'required' => false,
			),
			'course_page_style' => array(
				'type'     => 'string',
				'required' => false,
			),
			'course_image'      => array(
				'type'     => 'integer',
				'required' => false,
			),
			'course_icon'       => array(
				'type'     => 'string',
				'required' => false,
			),
			'course_color'      => array(
				'type'     => 'string',
				'required' => false,
			),
		);
	}

	public function response(): array {
		return array(
			'category' => CourseCategoryField::as_object(),
		);
	}

	public function get_summary(): string {
		return 'Create a New Course Category';
	}

	public function get_description(): string {
		return 'Returns created Course Category Object.';
	}
}
