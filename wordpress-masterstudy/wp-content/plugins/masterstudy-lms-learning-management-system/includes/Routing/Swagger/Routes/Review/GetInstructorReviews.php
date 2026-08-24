<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Review;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetInstructorReviews extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'page'   => array(
				'type'        => 'integer',
				'description' => 'Number of page to offset in the query. Default is 1.',
			),
			'user'   => array(
				'type'        => 'integer',
				'description' => 'Filter reviews by user ID. Default is null.',
			),
			'rating' => array(
				'type'        => 'integer',
				'description' => 'Filter reviews by rating. Default is null.',
			),
			'course' => array(
				'type'        => 'string',
				'description' => 'Filter reviews by course name. Default is null.',
			),
			'pp'     => array(
				'type'        => 'integer',
				'description' => 'Posts per page.',
			),
		);
	}

	public function response(): array {
		return array(
			'reviews'     => array(
				'type'        => 'array',
				'description' => 'List of review templates.',
			),
			'pagination'  => array(
				'type'        => 'string',
				'description' => 'HTML representation of the pagination.',
			),
			'total_pages' => array(
				'type'        => 'integer',
				'description' => 'Total number of pages.',
			),
			'total_posts' => array(
				'type'        => 'integer',
				'description' => 'Total number of reviews.',
			),
		);
	}

	public function get_summary(): string {
		return 'Get Instructor Reviews';
	}

	public function get_description(): string {
		return 'Returns a list of reviews based on the provided parameters.';
	}
}
