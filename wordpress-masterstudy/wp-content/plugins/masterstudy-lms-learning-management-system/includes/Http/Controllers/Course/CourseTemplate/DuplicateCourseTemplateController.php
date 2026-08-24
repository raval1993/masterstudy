<?php

namespace MasterStudy\Lms\Http\Controllers\Course\CourseTemplate;

use MasterStudy\Lms\Repositories\CourseTemplateRepository;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;

class DuplicateCourseTemplateController {

	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		$repository = new CourseTemplateRepository();

		$validator = new Validator(
			$request->get_json_params(),
			array(
				'title'        => 'required|string',
				'duplicate_id' => 'required|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data   = $request->get_json_params();
		$result = $repository->copy( $data['title'], $data['duplicate_id'] );

		if ( ! is_array( $result ) ) {
			return WpResponseFactory::error(
				esc_html__( 'Course duplicate template is failed', 'masterstudy-lms-learning-management-system' )
			);
		}

		return new \WP_REST_Response(
			$result
		);
	}
}
