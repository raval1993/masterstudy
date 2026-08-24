<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Blocks\Course;

use MasterStudy\Lms\Routing\Swagger\Fields\Level;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetLevels extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'levels' => Level::as_array(),
		);
	}

	public function get_summary(): string {
		return esc_html__( 'Get Course Level', 'masterstudy-lms-learning-management-system' );
	}

	public function get_description(): string {
		return esc_html__( 'Returns all Course Levels.', 'masterstudy-lms-learning-management-system' );
	}
}
