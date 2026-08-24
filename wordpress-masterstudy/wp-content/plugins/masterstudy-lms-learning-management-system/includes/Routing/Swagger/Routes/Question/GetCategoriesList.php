<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Question;

use MasterStudy\Lms\Routing\Swagger\Fields\QuestionCategory;
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
			'sort'     => array(
				'type'        => 'string',
				'description' => 'Sort by name/count. Prefix with "-" for descending order.',
				'required'    => false,
			),
		);
	}

	public function response(): array {
		return array(
			'categories' => array(
				'type'  => 'array',
				'items' => QuestionCategory::as_object(),
			),
			'total'      => array( 'type' => 'integer' ),
			'pages'      => array( 'type' => 'integer' ),
		);
	}

	public function get_summary(): string {
		return 'List Question Categories (Paginated)';
	}

	public function get_description(): string {
		return 'Returns a paginated list of Question Categories with optional search filtering.';
	}
}
