<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\AdminPost;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class UpdateAuthor extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'post_id'   => array(
				'type'        => 'integer',
				'description' => 'Target post ID.',
				'required'    => true,
			),
			'post_type' => array(
				'type'        => 'string',
				'description' => 'Target post type.',
				'enum'        => array( 'stm-courses', 'stm-lessons' ),
				'required'    => true,
			),
			'author_id' => array(
				'type'        => 'integer',
				'description' => 'New author user ID.',
				'required'    => true,
			),
		);
	}

	public function response(): array {
		return array(
			'success'   => array(
				'type' => 'boolean',
			),
			'id'        => array(
				'type' => 'integer',
			),
			'author_id' => array(
				'type' => 'integer',
			),
		);
	}

	public function get_summary(): string {
		return 'Update Admin Post Author';
	}

	public function get_description(): string {
		return 'Updates the author of a supported admin post type.';
	}
}
