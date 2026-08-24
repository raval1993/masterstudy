<?php

namespace MasterStudy\Lms\Http\Controllers\Question;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Http\Serializers\QuestionCategorySerializer;
use MasterStudy\Lms\Repositories\QuestionCategoryRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class UpdateCategoryController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$id = (int) $request->get_param( 'category_id' );

		$validator = new Validator(
			$request->get_json_params(),
			array(
				'category'        => 'nullable|string',
				'slug'            => 'nullable|string',
				'description'     => 'nullable|string',
				'parent_category' => 'nullable|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$repo     = new QuestionCategoryRepository();
		$existing = $repo->get( $id );

		if ( ! $existing || is_wp_error( $existing ) ) {
			return WpResponseFactory::bad_request( 'Category not found.' );
		}

		$category = $repo->update( $id, $validator->get_validated() );

		if ( is_wp_error( $category ) ) {
			return WpResponseFactory::error( $category->get_error_message() ?? esc_html__( 'Error occurred while updating category' ) );
		}

		return new \WP_REST_Response(
			array( 'category' => ( new QuestionCategorySerializer() )->toArray( $category ) )
		);
	}
}
