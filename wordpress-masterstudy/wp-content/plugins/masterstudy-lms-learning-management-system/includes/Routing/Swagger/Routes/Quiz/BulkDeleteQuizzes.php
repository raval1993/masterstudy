<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Quiz;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class BulkDeleteQuizzes extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'quizzes' => array(
				'type'        => 'array',
				'description' => 'Array of quiz IDs or quiz objects with id.',
				'required'    => true,
				'items'       => array(
					'type' => 'integer',
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
		return 'Bulk Delete Quizzes';
	}

	public function get_description(): string {
		return 'Delete multiple quizzes in one request. Maximum 100 quizzes per request.';
	}
}
