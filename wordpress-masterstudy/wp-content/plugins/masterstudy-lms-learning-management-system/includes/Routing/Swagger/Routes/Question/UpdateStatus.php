<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Question;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class UpdateStatus extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'status' => array(
				'type'        => 'string',
				'description' => 'Question status',
				'required'    => true,
				'enum'        => array( 'publish', 'pending', 'draft', 'trash', 'private' ),
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
		return 'Update question status';
	}

	public function get_description(): string {
		return 'Updates question post status.';
	}
}
