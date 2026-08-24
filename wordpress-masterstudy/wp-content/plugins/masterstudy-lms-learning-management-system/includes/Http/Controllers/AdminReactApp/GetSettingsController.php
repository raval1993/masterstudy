<?php

namespace MasterStudy\Lms\Http\Controllers\AdminReactApp;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminReactSettingsRepository;
use WP_REST_Response;

final class GetSettingsController {
	public function __invoke( string $app_slug ): WP_REST_Response {
		if ( ! in_array( $app_slug, AdminReactSettingsRepository::allowed_app_slugs(), true ) ) {
			return WpResponseFactory::not_found();
		}

		if ( ! $this->can_access_settings( $app_slug ) ) {
			return WpResponseFactory::forbidden();
		}

		$app_vars = AdminReactSettingsRepository::app_vars_by_slug( $app_slug );

		return WpResponseFactory::ok_with_data(
			array(
				'react_default_vars' => AdminReactSettingsRepository::default_vars(),
				'app_settings'       => null === $app_vars ? array() : $app_vars,
			)
		);
	}

	private function can_access_settings( string $app_slug ): bool {
		return (bool) apply_filters(
			'masterstudy_lms_admin_react_can_access_settings',
			current_user_can( 'administrator' )
				|| (
					current_user_can( 'stm_lms_instructor' )
					&& in_array( $app_slug, AdminReactSettingsRepository::allowed_instructor_app_slugs(), true )
				),
			$app_slug
		);
	}
}
