<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Review;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetAdminReviews extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'per_page'   => array(
				'type'        => 'integer',
				'description' => 'Reviews per page.',
			),
			'page'       => array(
				'type'        => 'integer',
				'description' => 'Current page.',
			),
			'search'     => array(
				'type'        => 'string',
				'description' => 'Search reviews by title.',
			),
			'status'     => array(
				'type'        => 'string',
				'description' => 'Filter by review status.',
				'enum'        => array( 'any', 'publish', 'pending', 'draft' ),
			),
			'sort'       => array(
				'type'        => 'string',
				'description' => 'Sort field and direction.',
			),
			'date_range' => array(
				'type'        => 'string',
				'description' => 'Date range filter.',
			),
		);
	}

	public function response(): array {
		return array(
			'reviews' => array(
				'type'  => 'array',
				'items' => array(
					'type' => 'object',
				),
			),
			'pages'   => array(
				'type' => 'integer',
			),
			'total'   => array(
				'type' => 'integer',
			),
		);
	}

	public function get_summary(): string {
		return 'Get Admin Reviews';
	}

	public function get_description(): string {
		return 'Returns a paginated list of reviews for the admin panel.';
	}
}
