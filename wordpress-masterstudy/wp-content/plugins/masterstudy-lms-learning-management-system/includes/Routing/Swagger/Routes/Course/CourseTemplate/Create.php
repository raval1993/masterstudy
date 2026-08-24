<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Create extends Route implements RequestInterface, ResponseInterface {

	public function response(): array {
		return array(
			'status'   => array(
				'type'        => 'string',
				'description' => 'Status of the template creation process',
			),
			'template' => array(
				'type'        => 'object',
				'description' => 'The created course template',
				'properties'  => array(
					'id'        => array(
						'type'        => 'integer',
						'description' => 'Template ID',
					),
					'title'     => array(
						'type'        => 'string',
						'description' => 'Template title',
					),
					'name'      => array(
						'type'        => 'string',
						'description' => 'Template name',
					),
					'elementor' => array(
						'type'        => 'boolean',
						'description' => 'Whether the template is Elementor-based',
					),
				),
			),
		);
	}

	public function request(): array {
		return array(
			'title' => array(
				'type'        => 'string',
				'description' => 'Title of the course template to be created',
			),
		);
	}

	public function get_summary(): string {
		return 'Create a new course template';
	}

	public function get_description(): string {
		return 'Creates a new course template in the system with the provided title.';
	}
}
