<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Course;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetAdminCourses extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'per_page'   => array(
				'type'        => 'integer',
				'description' => 'Courses per page. Default is 10.',
			),
			'page'       => array(
				'type'        => 'integer',
				'description' => 'Current page. Default is 1.',
			),
			'search'     => array(
				'type'        => 'string',
				'description' => 'Search courses by title.',
			),
			'category'   => array(
				'type'        => 'string',
				'description' => 'Category slug to filter by.',
			),
			'lesson_id'  => array(
				'type'        => 'integer',
				'description' => 'Lesson post ID to filter courses by curriculum membership.',
			),
			'quiz_id'    => array(
				'type'        => 'integer',
				'description' => 'Quiz post ID to filter courses by curriculum membership.',
			),
			'status'     => array(
				'type'        => 'string',
				'description' => 'Filter by course status.',
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
			'courses' => array(
				'type'  => 'array',
				'items' => array(
					'type'       => 'object',
					'properties' => array(
						'id'                => array(
							'type' => 'integer',
						),
						'title'             => array(
							'type' => 'string',
						),
						'category'          => array(
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
						'date'              => array(
							'type' => 'string',
						),
						'status'            => array(
							'type' => 'string',
							'enum' => array( 'publish', 'pending', 'draft', 'trash', 'private' ),
						),
						'current_students'  => array(
							'type' => 'integer',
						),
						'lessons_count'     => array(
							'type' => 'integer',
						),
						'quizzes_count'     => array(
							'type' => 'integer',
						),
						'assignments_count' => array(
							'type' => 'integer',
						),
						'author'            => array(
							'type'       => 'object',
							'nullable'   => true,
							'properties' => array(
								'id'   => array(
									'type' => 'integer',
								),
								'name' => array(
									'type' => 'string',
								),
							),
						),
					),
				),
			),
			'pages'   => array(
				'type'        => 'integer',
				'description' => 'Total number of pages.',
			),
			'total'   => array(
				'type'        => 'integer',
				'description' => 'Total number of courses.',
			),
		);
	}

	public function get_summary(): string {
		return 'Get Admin Courses';
	}

	public function get_description(): string {
		return 'Returns a paginated list of courses for the admin panel.';
	}
}
