<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Student;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class AddStudentsBulk extends Route implements RequestInterface, ResponseInterface {

	public function request(): array {
		return array(
			'course_id' => array(
				'type'        => 'integer',
				'required'    => true,
				'description' => 'Course ID.',
			),
			'students'  => array(
				'type'        => 'array',
				'required'    => true,
				'description' => 'List of students to enroll.',
				'items'       => array(
					'type'       => 'object',
					'required'   => true,
					'properties' => array(
						'email'      => array(
							'type'        => 'string',
							'required'    => true,
							'description' => 'Student email.',
						),
						'first_name' => array(
							'type'        => 'string',
							'description' => 'Student first name.',
						),
						'last_name'  => array(
							'type'        => 'string',
							'description' => 'Student last name.',
						),
					),
				),
			),
		);
	}

	public function response(): array {
		return array(
			'total' => array(
				'type'        => 'integer',
				'description' => 'Total processed students.',
			),
			'added' => array(
				'type'        => 'array',
				'description' => 'Per-student result list.',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
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
						'error'              => array(
							'type'        => 'string',
							'description' => 'Error message if failed.',
						),
					),
				),
			),
		);
	}

	public function get_summary(): string {
		return 'Add students to course (bulk).';
	}

	public function get_description(): string {
		return 'Enroll multiple students into a course in one request. Supports optional first_name/last_name.';
	}
}
