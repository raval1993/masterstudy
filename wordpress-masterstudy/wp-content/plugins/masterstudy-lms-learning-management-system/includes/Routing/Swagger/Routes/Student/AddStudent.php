<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Student;

use MasterStudy\Lms\Routing\Swagger\Route;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;

class AddStudent extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'email'      => array(
				'type' => 'string',
			),
			'first_name' => array(
				'type' => 'string',
			),
			'last_name'  => array(
				'type' => 'string',
			),
			'course_id'  => array(
				'type'     => 'integer',
				'required' => true,
			),
		);
	}

	public function response(): array {
		return array(
			'email'              => array(
				'type' => 'string',
			),
			'student_id'         => array(
				'type' => 'integer',
			),
			'is_enrolled'        => array(
				'type' => 'boolean',
			),
			'is_enrolled_before' => array(
				'type' => 'boolean',
			),
		);
	}

	public function get_summary(): string {
		return 'Add student to course';
	}

	public function get_description(): string {
		return 'Add student to course.';
	}
}
