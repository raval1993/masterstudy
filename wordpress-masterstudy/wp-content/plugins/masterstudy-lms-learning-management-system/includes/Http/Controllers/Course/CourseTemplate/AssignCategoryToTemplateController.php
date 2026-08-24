<?php

namespace MasterStudy\Lms\Http\Controllers\Course\CourseTemplate;

use MasterStudy\Lms\Repositories\CourseTemplateRepository;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;

class AssignCategoryToTemplateController {

	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		$validator = new Validator(
			$request->get_json_params(),
			array(
				'course_style' => 'required|string',
				'term_id'      => 'required|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data   = $request->get_json_params();
		$result = ( new CourseTemplateRepository() )->category_template( $data['course_style'], $data['term_id'] );

		if ( ! $result ) {
			return WpResponseFactory::error(
				esc_html__( 'Course category assign template is failed', 'masterstudy-lms-learning-management-system' )
			);
		}

		return new \WP_REST_Response(
			$result
		);
	}
}
