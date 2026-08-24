<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\CourseCategory;

use MasterStudy\Lms\Routing\Swagger\Fields\CourseCategory as CourseCategoryField;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetCategoriesList extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'page'     => array(
				'type'     => 'integer',
				'required' => false,
			),
			'per_page' => array(
				'type'     => 'integer',
				'required' => false,
			),
			'search'   => array(
				'type'     => 'string',
				'required' => false,
			),
		);
	}

	public function response(): array {
		return array(
			'categories' => array(
				'type'  => 'array',
				'items' => CourseCategoryField::as_object(),
			),
			'total'      => array( 'type' => 'integer' ),
			'pages'      => array( 'type' => 'integer' ),
		);
	}

	public function get_summary(): string {
		return 'List Course Categories (Paginated)';
	}

	public function get_description(): string {
		return 'Returns a paginated list of Course Categories with optional search filtering.';
	}
}
