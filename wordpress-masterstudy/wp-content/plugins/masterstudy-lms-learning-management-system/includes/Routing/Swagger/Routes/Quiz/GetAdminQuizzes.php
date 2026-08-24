<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Quiz;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetAdminQuizzes extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'per_page'   => array(
				'type'        => 'integer',
				'description' => 'Quizzes per page. Default is 10.',
			),
			'page'       => array(
				'type'        => 'integer',
				'description' => 'Current page. Default is 1.',
			),
			'search'     => array(
				'type'        => 'string',
				'description' => 'Search quizzes by title.',
			),
			'status'     => array(
				'type'        => 'string',
				'description' => 'Filter by quiz status.',
				'enum'        => array( 'any', 'publish', 'pending', 'draft', 'trash', 'private' ),
			),
			'sort'       => array(
				'type'        => 'string',
				'description' => 'Sort field and direction (e.g. title_asc, date_desc).',
			),
			'date_range' => array(
				'type'        => 'string',
				'description' => 'Date range filter.',
			),
		);
	}

	public function response(): array {
		return array(
			'quizzes'       => array(
				'type'  => 'array',
				'items' => array(
					'type'       => 'object',
					'properties' => array(
						'id'                   => array(
							'type' => 'integer',
						),
						'title'                => array(
							'type' => 'string',
						),
						'date'                 => array(
							'type' => 'string',
						),
						'status'               => array(
							'type' => 'string',
							'enum' => array( 'publish', 'pending', 'draft', 'trash', 'private' ),
						),
						'question_banks_count' => array(
							'type' => 'integer',
						),
						'questions_count'      => array(
							'type' => 'integer',
						),
						'linked_courses_count' => array(
							'type' => 'integer',
						),
						'author'               => array(
							'type'       => 'object',
							'nullable'   => true,
							'properties' => array(
								'id'    => array(
									'type' => 'integer',
								),
								'label' => array(
									'type' => 'string',
								),
							),
						),
					),
				),
			),
			'pages'         => array(
				'type'        => 'integer',
				'description' => 'Total number of pages.',
			),
			'current_page'  => array(
				'type'        => 'integer',
				'description' => 'Current page.',
			),
			'total_quizzes' => array(
				'type'        => 'integer',
				'description' => 'Total number of quizzes.',
			),
		);
	}

	public function get_summary(): string {
		return 'Get Admin Quizzes';
	}

	public function get_description(): string {
		return 'Returns a paginated list of quizzes for the admin panel.';
	}
}
