<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Course;

use MasterStudy\Lms\Routing\Swagger\Fields\Category;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetCategoriesList extends Route implements ResponseInterface, RequestInterface {
	public function request(): array {
		return array(
			'per_page' => array(
				'type'        => 'integer',
				'description' => 'Posts per page. Default is 10.',
			),
			'page'     => array(
				'type'        => 'integer',
				'description' => 'Current page. Default is 1.',
			),
			'search'   => array(
				'type'        => 'string',
				'description' => 'Search categories by name.',
			),
		);
	}
	public function response(): array {
		return array(
			'categories' => Category::as_array(),
			'total'      => 'number',
		);
	}

	public function get_summary(): string {
		return 'Get Course Categories';
	}

	public function get_description(): string {
		return 'Returns all Course Categories.';
	}
}
