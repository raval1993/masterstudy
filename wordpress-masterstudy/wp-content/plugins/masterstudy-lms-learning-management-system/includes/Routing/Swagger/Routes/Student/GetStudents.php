<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Student;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetStudents extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array();
	}

	public function response(): array {
		return array(
			'students' => array(
				'type' => 'array',
			),
		);
	}

	public function get_summary(): string {
		return 'Returns course students';
	}

	public function get_description(): string {
		return 'Returns course students';
	}
}
