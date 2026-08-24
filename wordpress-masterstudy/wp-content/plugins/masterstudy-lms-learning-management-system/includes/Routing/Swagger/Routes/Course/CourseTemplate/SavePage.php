<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class SavePage  extends Route implements RequestInterface, ResponseInterface {

	public function response(): array {
		return array(
			'status' => array(
				'type'        => 'string',
				'description' => 'Status of the page template creation process',
			),
			'course' => array(
				'type'        => 'string',
				'description' => 'Course name if page template applied successfully',
			),
		);
	}

	public function request(): array {
		return array(
			'course_style' => array(
				'type'        => 'string',
				'description' => 'Style of the course template to be applied',
			),
			'course_id'    => array(
				'type'        => 'integer',
				'description' => 'The ID of the course to apply the template to',
			),
			'post_id'      => array(
				'type'        => 'integer',
				'description' => 'The ID of the page template',
			),
		);
	}

	public function get_summary(): string {
		return 'Assign a page template to a course';
	}

	public function get_description(): string {
		return 'Applies the specified page template style to the given course.';
	}
}
