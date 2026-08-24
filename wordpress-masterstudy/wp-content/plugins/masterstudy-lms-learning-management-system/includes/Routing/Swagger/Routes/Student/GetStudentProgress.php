<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Student;

use MasterStudy\Lms\Routing\Swagger\Route;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;

class GetStudentProgress extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'course_id'  => array(
				'type'     => 'integer',
				'required' => true,
			),
			'student_id' => array(
				'type'     => 'integer',
				'required' => true,
			),
		);
	}

	public function response(): array {
		return array(
			'course'    => array(
				'type' => 'array',
			),
			'student'   => array(
				'type' => 'array',
			),
			'summary'   => array(
				'type' => 'array',
			),
			'sections'  => array(
				'type' => 'array',
			),
			'materials' => array(
				'type' => 'array',
			),
		);
	}

	public function get_summary(): string {
		return 'Get student course progress page data';
	}

	public function get_description(): string {
		return 'Returns structured progress data for the student course progress page.';
	}
}
