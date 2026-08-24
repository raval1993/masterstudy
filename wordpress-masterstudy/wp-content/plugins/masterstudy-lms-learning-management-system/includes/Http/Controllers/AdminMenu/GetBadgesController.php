<?php

namespace MasterStudy\Lms\Http\Controllers\AdminMenu;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminMenuBadgeRepository;
use WP_REST_Request;
use WP_REST_Response;

final class GetBadgesController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$badges = sanitize_text_field( (string) $request->get_param( 'badges' ) );
		$keys   = array_filter( array_map( 'trim', explode( ',', $badges ) ) );

		return WpResponseFactory::ok_with_data(
			array(
				'badges' => ( new AdminMenuBadgeRepository() )->get_badges( $keys ),
			)
		);
	}
}
