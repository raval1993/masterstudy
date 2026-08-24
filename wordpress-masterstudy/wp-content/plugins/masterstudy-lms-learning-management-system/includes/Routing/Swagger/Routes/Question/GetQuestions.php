<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Question;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetQuestions extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'per_page'   => array(
				'type'        => 'integer',
				'description' => 'Posts per page. Default is 10.',
			),
			'page'       => array(
				'type'        => 'integer',
				'description' => 'Current page. Default is 1.',
			),
			'search'     => array(
				'type'        => 'string',
				'description' => 'Question title search',
			),
			'category'   => array(
				'type'        => 'string',
				'description' => 'Question category slug or term id',
			),
			'status'     => array(
				'type'        => 'string',
				'description' => 'Question status',
				'enum'        => array( 'any', 'publish', 'pending', 'draft', 'trash', 'private' ),
			),
			'sort'       => array(
				'type'        => 'string',
				'description' => 'Sort questions by',
				'enum'        => array( 'id:asc', 'id:desc', 'title:asc', 'title:desc', 'status:asc', 'status:desc', 'date:asc', 'date:desc' ),
			),
			'date_range' => array(
				'type'        => 'string',
				'description' => 'Date range. Comma-separated YYYY-MM-DD,YYYY-MM-DD',
			),
		);
	}

	public function response(): array {
		return array(
			'questions'       => array(
				'type'  => 'array',
				'items' => array(
					'type'       => 'object',
					'properties' => array(
						'id'       => array(
							'type' => 'integer',
						),
						'title'    => array(
							'type' => 'string',
						),
						'type'     => array(
							'type' => 'string',
						),
						'category' => array(
							'type'       => 'object',
							'nullable'   => true,
							'properties' => array(
								'id'   => array(
									'type' => 'integer',
								),
								'name' => array(
									'type' => 'string',
								),
								'slug' => array(
									'type' => 'string',
								),
							),
						),
						'date'     => array(
							'type' => 'string',
						),
						'status'   => array(
							'type' => 'string',
						),
					),
				),
			),
			'total_questions' => array(
				'type'        => 'integer',
				'description' => 'Total number of questions.',
			),
			'current_page'    => array(
				'type'        => 'integer',
				'description' => 'Current page.',
			),
			'pages'           => array(
				'type'        => 'integer',
				'description' => 'Total number of pages.',
			),
		);
	}

	public function get_summary(): string {
		return 'Get questions';
	}

	public function get_description(): string {
		return 'Returns a list of questions based on the provided parameters.';
	}
}
