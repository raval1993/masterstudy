<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Review;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class UpdateAdminReview extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'title'      => array( 'type' => 'string' ),
			'content'    => array( 'type' => 'string' ),
			'course_id'  => array( 'type' => 'integer' ),
			'student_id' => array( 'type' => 'integer' ),
			'mark'       => array( 'type' => 'integer' ),
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
		return 'Update Admin Review';
	}

	public function get_description(): string {
		return 'Updates an existing review.';
	}
}
