<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Course;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class UpdateCourseStatus extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'status' => array(
				'type'        => 'string',
				'description' => 'New course status.',
				'required'    => true,
				'enum'        => array( 'publish', 'pending', 'draft', 'trash', 'private' ),
			),
		);
	}

	public function response(): array {
		return array(
			'success' => array(
				'type' => 'boolean',
			),
			'id'      => array(
				'type' => 'integer',
			),
			'status'  => array(
				'type'    => 'string',
				'example' => 'publish',
			),
		);
	}

	public function get_summary(): string {
		return 'Update Course Status';
	}

	public function get_description(): string {
		return 'Updates a single course status (publish, pending, draft, trash, private).';
	}
}
