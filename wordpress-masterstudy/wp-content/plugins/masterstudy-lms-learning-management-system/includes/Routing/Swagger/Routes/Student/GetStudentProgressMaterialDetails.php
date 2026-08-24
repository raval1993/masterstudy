<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Student;

use MasterStudy\Lms\Routing\Swagger\Route;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;

class GetStudentProgressMaterialDetails extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'course_id'   => array(
				'type'     => 'integer',
				'required' => true,
			),
			'student_id'  => array(
				'type'     => 'integer',
				'required' => true,
			),
			'material_id' => array(
				'type'     => 'integer',
				'required' => true,
			),
		);
	}

	public function response(): array {
		return array(
			'material_id' => array(
				'type' => 'integer',
			),
			'type'        => array(
				'type' => 'string',
			),
			'title'       => array(
				'type' => 'string',
			),
			'details'     => array(
				'type' => 'array',
			),
		);
	}

	public function get_summary(): string {
		return 'Get student course progress material details';
	}

	public function get_description(): string {
		return 'Returns structured assignment or quiz details for a student progress material.';
	}
}
