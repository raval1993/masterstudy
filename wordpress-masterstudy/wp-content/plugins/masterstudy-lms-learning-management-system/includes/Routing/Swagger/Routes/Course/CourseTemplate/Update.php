<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Update extends Route implements RequestInterface, ResponseInterface {

	public function response(): array {
		return array(
			'message' => array(
				'type'        => 'string',
				'description' => 'Message about successfully update course templates',
			),
		);
	}

	public function request(): array {
		return array(
			'course_style' => array(
				'type'        => 'string',
				'description' => 'second for the marker video point',
			),
		);
	}

	public function get_summary(): string {
		return 'Update course style for the settings';
	}

	public function get_description(): string {
		return 'update course style for the course settings in LMS Settings';
	}
}
