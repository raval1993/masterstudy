<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Media;

use MasterStudy\Lms\Routing\Swagger\Fields\Attachment;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

final class UploadFromUrl extends Route implements RequestInterface, ResponseInterface {

	public function request(): array {
		return array(
			'url' => array(
				'type'        => 'string',
				'description' => 'URL of the image to upload',
			),
		);
	}

	public function response(): array {
		return array(
			'file' => Attachment::as_object(),
		);
	}

	/**
	 * Route Summary
	 * @return string
	 */
	public function get_summary(): string {
		return 'Upload image from URL';
	}

	/**
	 * Route Description
	 * @return string
	 */
	public function get_description(): string {
		return 'Uploads image from URL. Returns attachment object.';
	}
}
