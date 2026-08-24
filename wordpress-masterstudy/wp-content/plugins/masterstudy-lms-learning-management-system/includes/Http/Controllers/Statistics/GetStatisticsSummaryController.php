<?php

namespace MasterStudy\Lms\Http\Controllers\Statistics;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\StatisticsRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class GetStatisticsSummaryController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_query_params(),
			array(
				'id'         => 'nullable|integer',
				'user'       => 'nullable|integer',
				'author'     => 'nullable|integer',
				'date_range' => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		return WpResponseFactory::ok_with_data(
			( new StatisticsRepository() )->get_summary( $validator->get_validated() )
		);
	}
}
