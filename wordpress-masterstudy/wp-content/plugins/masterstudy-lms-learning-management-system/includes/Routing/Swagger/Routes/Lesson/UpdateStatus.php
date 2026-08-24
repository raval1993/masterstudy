<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Lesson;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class UpdateStatus extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'status' => array(
				'type'        => 'string',
				'description' => 'Lesson status',
				'required'    => true,
				'enum'        => array( 'publish', 'draft' ),
			),
		);
	}

	public function response(): array {
		return array(
			'success' => array(
				'type' => 'boolean',
			),
			'id'      => array(
				'type' => 'integer',
			),
			'status'  => array(
				'type' => 'string',
			),
		);
	}

	public function get_summary(): string {
		return 'Update lesson status';
	}

	public function get_description(): string {
		return 'Updates lesson post status to published or draft.';
	}
}
