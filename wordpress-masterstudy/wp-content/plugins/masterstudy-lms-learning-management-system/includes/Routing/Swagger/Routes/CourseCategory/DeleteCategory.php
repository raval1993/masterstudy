<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\CourseCategory;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class DeleteCategory extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'success' => array( 'type' => 'boolean' ),
		);
	}

	public function get_summary(): string {
		return 'Delete a Course Category';
	}

	public function get_description(): string {
		return 'Permanently deletes a Course Category by ID.';
	}
}
