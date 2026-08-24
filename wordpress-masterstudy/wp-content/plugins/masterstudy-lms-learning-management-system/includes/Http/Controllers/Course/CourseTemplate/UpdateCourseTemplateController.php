<?php

namespace MasterStudy\Lms\Http\Controllers\Course\CourseTemplate;

use MasterStudy\Lms\Repositories\CourseTemplateRepository;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

class UpdateCourseTemplateController {

	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_json_params(),
			array(
				'course_style' => 'required|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data = $request->get_json_params();

		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_REST_Response(
				array(
					'error_code' => 'modify_template_access_error',
					esc_html__( 'You do not have permission to update course templates.', 'masterstudy-lms-learning-management-system' ),
				),
				403
			);
		}

		$result = ( new CourseTemplateRepository() )->update( $data['course_style'] );

		if ( ! $result ) {
			return WpResponseFactory::error(
				esc_html__( 'Course update template is failed', 'masterstudy-lms-learning-management-system' )
			);
		}

		return new \WP_REST_Response(
			array(
				'status' => 'success',
			)
		);
	}
}
