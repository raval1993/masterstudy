<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Question;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class BulkDeleteCategories extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'ids' => array(
				'type'     => 'array',
				'items'    => array( 'type' => 'integer' ),
				'required' => true,
			),
		);
	}

	public function response(): array {
		return array(
			'success' => array( 'type' => 'boolean' ),
		);
	}

	public function get_summary(): string {
		return 'Bulk Delete Question Categories';
	}

	public function get_description(): string {
		return 'Permanently deletes multiple Question Categories by their IDs.';
	}
}
