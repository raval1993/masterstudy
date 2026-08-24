<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\CourseCategory;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetCourseTemplatesModal extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'html'    => array( 'type' => 'string' ),
			'data'    => array( 'type' => 'object' ),
			'css_url' => array( 'type' => 'string' ),
			'js_url'  => array( 'type' => 'string' ),
		);
	}

	public function get_summary(): string {
		return 'Get Course Templates Modal';
	}

	public function get_description(): string {
		return 'Returns course templates modal HTML, data, and asset URLs.';
	}
}
