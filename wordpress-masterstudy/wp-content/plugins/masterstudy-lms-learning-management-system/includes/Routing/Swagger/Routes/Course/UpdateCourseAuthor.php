<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Course;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class UpdateCourseAuthor extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'author_id' => array(
				'type'        => 'integer',
				'description' => 'New author user ID.',
				'required'    => true,
			),
		);
	}

	public function response(): array {
		return array(
			'success'   => array(
				'type' => 'boolean',
			),
			'id'        => array(
				'type' => 'integer',
			),
			'author_id' => array(
				'type'    => 'integer',
				'example' => 1,
			),
		);
	}

	public function get_summary(): string {
		return 'Update Course Author';
	}

	public function get_description(): string {
		return 'Updates the author of a course.';
	}
}
