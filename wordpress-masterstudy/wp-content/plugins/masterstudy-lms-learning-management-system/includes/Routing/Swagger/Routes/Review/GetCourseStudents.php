<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Review;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetCourseStudents extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'per_page' => array( 'type' => 'integer' ),
			'page'     => array( 'type' => 'integer' ),
			'search'   => array( 'type' => 'string' ),
		);
	}

	public function response(): array {
		return array(
			'students' => array(
				'type'  => 'array',
				'items' => array(
					'type'       => 'object',
					'properties' => array(
						'id'   => array( 'type' => 'integer' ),
						'name' => array( 'type' => 'string' ),
					),
				),
			),
			'total'    => array( 'type' => 'integer' ),
			'pages'    => array( 'type' => 'integer' ),
		);
	}

	public function get_summary(): string {
		return 'Get Course Students';
	}

	public function get_description(): string {
		return 'Returns paginated list of students enrolled in a course.';
	}
}
