<?php

namespace MasterStudy\Lms\Http\Controllers\Course\CourseTemplate;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\CourseTemplateRepository;

class DeleteCourseTemplateController {

	public function __invoke( int $template_id ) {
		if ( ! current_user_can( 'delete_post', $template_id ) ) {
			return new \WP_REST_Response(
				array(
					'error_code' => 'delete_template_access_error',
					esc_html__( 'You do not have permission to delete course templates.', 'masterstudy-lms-learning-management-system' ),
				),
				403
			);
		}

		$result = ( new CourseTemplateRepository() )->delete( $template_id );

		if ( ! $result ) {
			return WpResponseFactory::error(
				esc_html__( 'Course delete template is failed', 'masterstudy-lms-learning-management-system' )
			);
		}

		return new \WP_REST_Response(
			array(
				'status' => 'success',
			)
		);
	}
}
