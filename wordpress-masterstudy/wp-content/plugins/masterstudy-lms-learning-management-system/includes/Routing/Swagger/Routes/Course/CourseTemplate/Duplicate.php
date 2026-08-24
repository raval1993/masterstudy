<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Duplicate extends Route implements RequestInterface, ResponseInterface {

	public function response(): array {
		return array(
			'status'   => array(
				'type'        => 'string',
				'description' => 'Status of the template copy process',
			),
			'template' => array(
				'type'        => 'object',
				'description' => 'The newly copied course template',
				'properties'  => array(
					'id'        => array(
						'type'        => 'integer',
						'description' => 'Copied template ID',
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
			'title'        => array(
				'type'        => 'string',
				'description' => 'Title for the copied template',
			),
			'duplicate_id' => array(
				'type'        => 'integer',
				'description' => 'ID of the template to copy from',
			),
		);
	}

	public function get_summary(): string {
		return 'Copy an existing course template';
	}

	public function get_description(): string {
		return 'Creates a copy of the existing course template, using the provided template ID.';
	}
}
