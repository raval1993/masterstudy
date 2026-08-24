<?php

namespace MasterStudy\Lms\Routing\Middleware;

use MasterStudy\Lms\Routing\MiddlewareInterface;

class AdminOrInstructor implements MiddlewareInterface {
	public function process( $request, callable $next ) {
		if ( current_user_can( 'manage_options' ) || current_user_can( 'stm_lms_instructor' ) ) {
			return $next( $request );
		}

		return new \WP_REST_Response(
			array(
				'error_code' => 'forbidden_access',
				'message'    => esc_html__( 'Only admins and instructors can access this route!', 'masterstudy-lms-learning-management-system' ),
			),
			403
		);
	}
}
