<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Question;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class BulkUpdateQuestions extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'action'    => array(
				'type'        => 'string',
				'description' => 'Action to perform on questions',
				'required'    => true,
				'enum'        => array( 'delete', 'update_status' ),
			),
			'questions' => array(
				'type'        => 'array',
				'description' => 'Question ids or question objects with id field',
				'required'    => true,
				'items'       => array(
					'type' => 'object',
				),
			),
			'status'    => array(
				'type'        => 'string',
				'description' => 'Required for update_status action',
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
		return 'Perform bulk update of questions';
	}

	public function get_description(): string {
		return 'Perform bulk update actions for questions.';
	}
}
