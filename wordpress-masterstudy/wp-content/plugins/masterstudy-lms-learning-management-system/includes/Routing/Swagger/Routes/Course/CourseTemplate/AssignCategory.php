<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class AssignCategory extends Route implements RequestInterface, ResponseInterface {

	public function response(): array {
		return array(
			'status' => array(
				'type'        => 'string',
				'description' => 'Status of the category template assignment process',
			),
		);
	}

	public function request(): array {
		return array(
			'course_style' => array(
				'type'        => 'string',
				'description' => 'Style of the course template to be assigned',
			),
			'term_id'      => array(
				'type'        => 'integer',
				'description' => 'The ID of the category (term) to assign the template to',
			),
		);
	}

	public function get_summary(): string {
		return 'Assign a template to a course category';
	}

	public function get_description(): string {
		return 'Assigns the given course template style to a specific category in the system.';
	}
}
