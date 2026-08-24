<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Quiz;

use MasterStudy\Lms\Routing\Swagger\Fields\Post;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetEnrolledQuizzes extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'per_page' => array(
				'type'        => 'integer',
				'description' => 'Posts per page. Default is 10.',
			),
			'page'     => array(
				'type'        => 'integer',
				'description' => 'Current page. Default is 1.',
			),
			'author'   => array(
				'type'        => 'integer',
				'description' => 'Author ID.',
			),
			's'        => array(
				'type'        => 'string',
				'description' => 'Search course or quiz...',
			),
		);
	}

	public function response(): array {
		return array(
			'courses' => Post::as_array(),
			'total'   => array(
				'type'        => 'integer',
				'description' => 'Total number of quizzes.',
			),
			'pages'   => array(
				'type'        => 'integer',
				'description' => 'Total number of pages.',
			),
		);
	}

	public function get_summary(): string {
		return 'Get Quizzes';
	}

	public function get_description(): string {
		return 'Returns a list of quizzes based on the provided parameters.';
	}
}
