<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\AdminReactApp;

use MasterStudy\Lms\Repositories\AdminReactSettingsRepository;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetSettings extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'app_slug' => array(
				'type'        => 'string',
				'description' => 'React admin app slug to load settings for.',
				'enum'        => AdminReactSettingsRepository::allowed_app_slugs(),
				'required'    => true,
			),
		);
	}

	public function response(): array {
		return array(
			'react_default_vars' => array(
				'type'       => 'object',
				'properties' => array(
					'admin_url'      => array(
						'type'   => 'string',
						'format' => 'uri',
					),
					'wp_time_format' => array(
						'type' => 'string',
					),
					'is_pro'         => array(
						'type' => 'boolean',
					),
					'is_pro_plus'    => array(
						'type' => 'boolean',
					),
					'has_ai_access'  => array(
						'type' => 'boolean',
					),
					'currency_info'  => array(
						'type'       => 'object',
						'properties' => array(
							'currency_symbol'    => array(
								'type' => 'string',
							),
							'decimals_num'       => array(
								'type' => 'string',
							),
							'currency_thousands' => array(
								'type' => 'string',
							),
							'currency_decimals'  => array(
								'type' => 'string',
							),
							'currency_position'  => array(
								'type' => 'string',
							),
						),
					),
					'enabled_addons' => array(
						'type'                 => 'object',
						'description'          => 'Enabled addon flags keyed by addon slug.',
						'additionalProperties' => true,
					),
				),
			),
			'app_settings'       => array(
				'type'                 => 'object',
				'description'          => 'App-specific settings. The object shape depends on the requested app slug.',
				'additionalProperties' => true,
			),
		);
	}

	public function get_summary(): string {
		return 'Get Admin App Settings';
	}

	public function get_description(): string {
		return 'Returns shared default variables and app-specific settings for a React wp-admin application.';
	}
}
