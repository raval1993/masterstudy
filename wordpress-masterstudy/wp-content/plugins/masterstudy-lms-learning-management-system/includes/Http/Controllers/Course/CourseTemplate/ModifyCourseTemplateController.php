<?php

namespace MasterStudy\Lms\Http\Controllers\Course\CourseTemplate;

use MasterStudy\Lms\Repositories\CourseTemplateRepository;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;

class ModifyCourseTemplateController {

	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		$validator = new Validator(
			$request->get_json_params(),
			array(
				'title'   => 'required|string',
				'post_id' => 'required|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data   = $request->get_json_params();

		if ( ! current_user_can( 'edit_post', $data['post_id'] ) ) {
			return new \WP_REST_Response(
				array(
					'error_code' => 'modify_template_access_error',
					esc_html__( 'You do not have permission to update course templates.', 'masterstudy-lms-learning-management-system' ),
				),
				403
			);
		}

		$result = ( new CourseTemplateRepository() )->modify_template( $data['title'], $data['post_id'] );

		if ( ! $result ) {
			return WpResponseFactory::error(
				esc_html__( 'Course modify template is failed', 'masterstudy-lms-learning-management-system' )
			);
		}

		return new \WP_REST_Response(
			array(
				'status' => 'success',
			)
		);
	}
}
