<?php

namespace MasterStudy\Lms\Http\Controllers\Statistics;

use MasterStudy\Lms\Http\WpResponseFactory;
use WP_REST_Request;
use WP_REST_Response;

final class CreateStatisticsPayoutController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$result = \stmLms\Classes\Models\StmLmsPayout::pay_now();

		return WpResponseFactory::ok_with_data( $result );
	}
}
