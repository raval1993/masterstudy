<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\CourseCategory;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetIcons extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'icons' => array( 'type' => 'object' ),
		);
	}

	public function get_summary(): string {
		return 'Get Available Icons';
	}

	public function get_description(): string {
		return 'Returns all available icons for Course Categories.';
	}
}
