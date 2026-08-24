<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Review;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class BulkDeleteAdminReviews extends Route implements RequestInterface {
	public function request(): array {
		return array(
			'ids' => array(
				'type'     => 'array',
				'required' => true,
				'items'    => array( 'type' => 'integer' ),
			),
		);
	}

	public function get_summary(): string {
		return 'Bulk Delete Admin Reviews';
	}

	public function get_description(): string {
		return 'Deletes multiple reviews by IDs.';
	}
}
