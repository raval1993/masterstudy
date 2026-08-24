<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Student;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetCourseStudentsStats extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array();
	}

	public function response(): array {
		return array(
			'total_students' => array(
				'type' => 'integer',
			),
			'completed'      => array(
				'type' => 'integer',
			),
			'in_progress'    => array(
				'type' => 'integer',
			),
		);
	}

	public function get_summary(): string {
		return 'Returns course students stats';
	}

	public function get_description(): string {
		return 'Returns total, completed, and in progress student counts for a course';
	}
}
