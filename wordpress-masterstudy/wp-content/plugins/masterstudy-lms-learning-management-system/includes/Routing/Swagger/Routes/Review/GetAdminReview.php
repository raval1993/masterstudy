<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Review;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetAdminReview extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'id'      => array( 'type' => 'integer' ),
			'title'   => array( 'type' => 'string' ),
			'content' => array( 'type' => 'string' ),
			'status'  => array( 'type' => 'string' ),
			'date'    => array( 'type' => 'string' ),
			'course'  => array(
				'type'     => 'object',
				'nullable' => true,
			),
			'user'    => array( 'type' => 'object' ),
			'mark'    => array( 'type' => 'integer' ),
		);
	}

	public function get_summary(): string {
		return 'Get Admin Review';
	}

	public function get_description(): string {
		return 'Returns a single review by ID.';
	}
}
