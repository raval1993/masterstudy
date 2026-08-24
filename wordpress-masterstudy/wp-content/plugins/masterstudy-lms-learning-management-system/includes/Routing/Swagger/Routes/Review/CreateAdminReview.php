<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Review;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class CreateAdminReview extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'content'    => array( 'type' => 'string' ),
			'course_id'  => array(
				'type'     => 'integer',
				'required' => true,
			),
			'student_id' => array(
				'type'     => 'integer',
				'required' => true,
			),
			'mark'       => array(
				'type'     => 'integer',
				'required' => true,
			),
			'status'     => array(
				'type' => 'string',
				'enum' => array( 'publish', 'pending', 'draft' ),
			),
		);
	}

	public function response(): array {
		return array(
			'id' => array( 'type' => 'integer' ),
		);
	}

	public function get_summary(): string {
		return 'Create Admin Review';
	}

	public function get_description(): string {
		return 'Creates a new review.';
	}
}
