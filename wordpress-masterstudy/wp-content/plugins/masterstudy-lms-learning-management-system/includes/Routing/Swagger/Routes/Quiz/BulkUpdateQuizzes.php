<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Quiz;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class BulkUpdateQuizzes extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'action'  => array(
				'type'        => 'string',
				'description' => 'Status action to apply.',
				'required'    => true,
				'enum'        => array( 'publish', 'draft' ),
			),
			'quizzes' => array(
				'type'        => 'array',
				'description' => 'Quiz items selected for bulk status update.',
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
		return 'Bulk Update Quizzes';
	}

	public function get_description(): string {
		return 'Updates quiz statuses to published or draft.';
	}
}
