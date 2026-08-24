<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Blocks\Course;

use MasterStudy\Lms\Routing\Swagger\Fields\Status;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetStatuses extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'statuses' => Status::as_array(),
		);
	}

	public function get_summary(): string {
		return esc_html__( 'Get Course Statuses', 'masterstudy-lms-learning-management-system' );
	}

	public function get_description(): string {
		return esc_html__( 'Returns all Course Statuses.', 'masterstudy-lms-learning-management-system' );
	}
}
