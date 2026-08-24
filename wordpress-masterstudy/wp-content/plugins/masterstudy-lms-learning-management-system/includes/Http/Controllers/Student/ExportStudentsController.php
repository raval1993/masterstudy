<?php

namespace MasterStudy\Lms\Http\Controllers\Student;

use WP_REST_Response;
use WP_REST_Request;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\CourseRepository;
use MasterStudy\Lms\Repositories\StudentsRepository;
use MasterStudy\Lms\Validation\Validator;

class ExportStudentsController {
	public function __invoke( WP_REST_Request $request ) {
		$repo      = new StudentsRepository();
		$validator = new Validator(
			$request->get_params(),
			array(
				'show_all_enrolled' => 'nullable|string',
				's'                 => 'nullable|string',
				'date_from'         => 'nullable|string',
				'date_to'           => 'nullable|string',
				'course_id'         => 'nullable|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$params = $validator->get_validated();

		if ( empty( $params['show_all_enrolled'] ) ) {
			if ( ! ( new CourseRepository() )->exists( $params['course_id'] ) ) {
				return WpResponseFactory::not_found();
			}

			$students = $repo->export_students_by_course( $params['course_id'] );
		} else {
			$students = $repo->export_students( $params );
		}

		return new WP_REST_Response( $students );
	}
}
