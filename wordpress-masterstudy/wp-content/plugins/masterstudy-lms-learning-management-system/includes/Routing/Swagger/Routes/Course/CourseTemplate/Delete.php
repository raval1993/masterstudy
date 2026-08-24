<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Delete extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'status' => array(
				'type'        => 'string',
				'description' => 'Status of the template deletion process',
			),
		);
	}

	public function get_summary(): string {
		return 'Delete a course template';
	}

	public function get_description(): string {
		return 'Deletes a course template from the system based on the provided template ID.';
	}
}
