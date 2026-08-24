<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Student;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetStudentStats extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array();
	}

	public function response(): array {
		return array(
			'stats' => array(
				'type' => 'array',
			),
		);
	}

	public function get_summary(): string {
		return "Returns a student's statistics";
	}

	public function get_description(): string {
		return "Returns a student's statistics";
	}
}
