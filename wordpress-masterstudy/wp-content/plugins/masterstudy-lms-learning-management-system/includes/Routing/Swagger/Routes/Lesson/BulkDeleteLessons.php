<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Lesson;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class BulkDeleteLessons extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'lessons' => array(
				'type'        => 'array',
				'description' => 'Lesson items selected for bulk deletion.',
				'required'    => true,
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'id' => array(
							'type' => 'integer',
						),
					),
				),
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
		return 'Bulk Delete Lessons';
	}

	public function get_description(): string {
		return 'Permanently deletes lessons from the admin lessons list.';
	}
}
