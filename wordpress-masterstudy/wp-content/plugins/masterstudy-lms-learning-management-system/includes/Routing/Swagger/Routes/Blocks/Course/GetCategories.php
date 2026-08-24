<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Blocks\Course;

use MasterStudy\Lms\Routing\Swagger\Fields\Category;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetCategories extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'categories' => Category::as_array(),
		);
	}

	public function get_summary(): string {
		return 'Get Course Categories';
	}

	public function get_description(): string {
		return 'Returns all Course Categories.';
	}
}
