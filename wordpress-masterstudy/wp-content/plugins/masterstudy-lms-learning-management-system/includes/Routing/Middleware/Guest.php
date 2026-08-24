<?php

namespace MasterStudy\Lms\Routing\Middleware;

use MasterStudy\Lms\Routing\MiddlewareInterface;

class Guest implements MiddlewareInterface {
	public function process( $request, callable $next ) {
		if ( empty( $request->get_header( 'X-WP-Nonce' ) ) ) {
			return new \WP_REST_Response(
				array(
					'error_code' => 'rest_nonce_missed',
					'message'    => esc_html__( 'REST Nonce header is required!', 'masterstudy-lms-learning-management-system' ),
				),
				403
			);
		}

		return $next( $request );
	}
}
