<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Quiz;

use MasterStudy\Lms\Routing\Swagger\Fields\Post;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetQuizAttempts extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'attempt_id' => array(
				'type'        => 'integer',
				'description' => 'Attempt ID.',
			),
			'per_page'   => array(
				'type'        => 'integer',
				'description' => 'Posts per page. Default is 10.',
			),
			'page'       => array(
				'type'        => 'integer',
				'description' => 'Current page. Default is 1.',
			),
		);
	}

	public function response(): array {
		return array(
			'attempts' => Post::as_array(),
			'total'    => array(
				'type'        => 'integer',
				'description' => 'Total number of quiz attempts.',
			),
			'pages'    => array(
				'type'        => 'integer',
				'description' => 'Total number of pages.',
			),
		);
	}

	public function get_summary(): string {
		return 'Get Attempts';
	}

	public function get_description(): string {
		return 'Returns a list of quiz attempts based on the provided parameters.';
	}
}
