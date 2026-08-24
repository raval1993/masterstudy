<?php

namespace MasterStudy\Lms\Http\Controllers\Lesson;

use MasterStudy\Lms\Http\Serializers\LessonListSerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\LessonAdminRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class GetLessonsController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'per_page'   => 'nullable|integer',
				'page'       => 'nullable|integer',
				'search'     => 'nullable|string',
				'status'     => 'nullable|string|contains_list,any;publish;pending;draft;trash;private',
				'sort'       => 'nullable|string',
				'date_range' => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data            = ( new LessonAdminRepository() )->get_list( $validator->get_validated() );
		$data['lessons'] = ( new LessonListSerializer() )->collectionToArray( $data['posts'] );
		unset( $data['posts'] );

		return new WP_REST_Response( $data );
	}
}
