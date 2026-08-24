<?php

namespace MasterStudy\Lms\Http\Controllers\Blocks;

use MasterStudy\Lms\Plugin\Addons;
use WP_REST_Request;

final class GetSettingsController {
	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		return new \WP_REST_Response(
			array(
				'addons' => Addons::enabled_addons(),
			)
		);
	}
}
