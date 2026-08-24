<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Course;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class BulkUpdateCourses extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'action'  => array(
				'type'        => 'string',
				'description' => 'Action to perform on courses.',
				'required'    => true,
				'enum'        => array( 'delete', 'update_status' ),
			),
			'courses' => array(
				'type'        => 'array',
				'description' => 'Array of course IDs or course objects with id and status.',
				'required'    => true,
				'items'       => array(
					'type' => 'integer',
				),
			),
			'status'  => array(
				'type'        => 'string',
				'description' => 'New status for update_status action.',
				'nullable'    => true,
				'enum'        => array( 'publish', 'pending', 'draft', 'trash', 'private' ),
			),
		);
	}

	public function response(): array {
		return array(
			'success' => array(
				'type' => 'boolean',
			),
		);
	}

	public function get_summary(): string {
		return 'Bulk Update Courses';
	}

	public function get_description(): string {
		return 'Perform bulk actions on courses (permanently delete or update status). Maximum 100 courses per request.';
	}
}
