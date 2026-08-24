<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Blocks;

use MasterStudy\Lms\Routing\Swagger\Fields\Setting;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetSettings extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'settings' => Setting::as_array(),
		);
	}

	public function get_summary(): string {
		return esc_html__( 'Get Course Setting', 'masterstudy-lms-learning-management-system' );
	}

	public function get_description(): string {
		return esc_html__( 'Returns all Course Settings.', 'masterstudy-lms-learning-management-system' );
	}
}
