<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Student;

use MasterStudy\Lms\Routing\Swagger\Route;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;

class ExportStudents extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'email'      => array(
				'type' => 'string',
			),
			'first_name' => array(
				'type' => 'string',
			),
			'last_name'  => array(
				'type' => 'string',
			),
		);
	}

	public function get_summary(): string {
		return 'Export students data';
	}

	public function get_description(): string {
		return 'Export students data.';
	}
}
