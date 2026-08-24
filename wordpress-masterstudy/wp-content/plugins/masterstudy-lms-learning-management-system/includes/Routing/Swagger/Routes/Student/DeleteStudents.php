<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Student;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class DeleteStudents extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'status' => array(
				'type'    => 'string',
				'example' => 'ok',
			),
		);
	}

	public function get_summary(): string {
		return 'Delete students from course';
	}

	public function get_description(): string {
		return 'Delete students from course';
	}
}
