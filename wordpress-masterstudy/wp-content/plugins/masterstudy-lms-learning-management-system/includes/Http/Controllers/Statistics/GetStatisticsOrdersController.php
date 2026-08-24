<?php

namespace MasterStudy\Lms\Http\Controllers\Statistics;

use MasterStudy\Lms\Http\Serializers\StatisticsOrderSerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\StatisticsRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class GetStatisticsOrdersController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_query_params(),
			array(
				'page'       => 'nullable|integer',
				'per_page'   => 'nullable|integer',
				'sort'       => 'nullable|string',
				'id'         => 'nullable|integer',
				'user'       => 'nullable|integer',
				'author'     => 'nullable|integer',
				'date_range' => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$result = ( new StatisticsRepository() )->get_orders( $validator->get_validated() );

		return WpResponseFactory::ok_with_data(
			array(
				'items'        => ( new StatisticsOrderSerializer() )->collectionToArray( $result['items'] ),
				'total'        => $result['total'],
				'pages'        => $result['pages'],
				'current_page' => $result['current_page'],
			)
		);
	}
}
