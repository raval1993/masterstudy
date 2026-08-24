<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\AdminPost;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetAuthors extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'authors' => array(
				'type'  => 'array',
				'items' => array(
					'type'       => 'object',
					'properties' => array(
						'id'    => array(
							'type' => 'integer',
						),
						'label' => array(
							'type' => 'string',
						),
					),
				),
			),
		);
	}

	public function get_summary(): string {
		return 'Get Admin Post Authors';
	}

	public function get_description(): string {
		return 'Returns the assignable LMS authors for admin post tables.';
	}
}
