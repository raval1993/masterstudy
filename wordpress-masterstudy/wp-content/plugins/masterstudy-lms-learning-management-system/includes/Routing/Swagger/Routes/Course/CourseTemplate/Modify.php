<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Modify extends Route implements RequestInterface, ResponseInterface {

	public function response(): array {
		return array(
			'status' => array(
				'type'        => 'string',
				'description' => 'Status of the template modification process',
			),
		);
	}

	public function request(): array {
		return array(
			'title'   => array(
				'type'        => 'string',
				'description' => 'Updated title for the course template',
			),
			'post_id' => array(
				'type'        => 'integer',
				'description' => 'The ID of the template being modified',
			),
		);
	}

	public function get_summary(): string {
		return 'Modify an existing course template';
	}

	public function get_description(): string {
		return 'Modifies an existing course template by updating its title and other details.';
	}
}
