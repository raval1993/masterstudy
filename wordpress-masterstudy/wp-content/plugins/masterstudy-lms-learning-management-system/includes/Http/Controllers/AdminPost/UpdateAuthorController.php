<?php

namespace MasterStudy\Lms\Http\Controllers\AdminPost;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminPostAuthorRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class UpdateAuthorController {
	public function __invoke( int $post_id, WP_REST_Request $request ): WP_REST_Response {
		$payload = $request->get_json_params();

		$validator = new Validator(
			is_array( $payload ) ? $payload : $request->get_params(),
			array(
				'post_type' => 'required|string|contains_list,' . implode( ';', AdminPostAuthorRepository::supported_post_types() ),
				'author_id' => 'required|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data      = $validator->get_validated();
		$post_type = (string) $data['post_type'];
		$author_id = (int) $data['author_id'];

		try {
			( new AdminPostAuthorRepository() )->update_author( $post_id, $post_type, $author_id );
		} catch ( \RuntimeException $e ) {
			if ( 404 === $e->getCode() ) {
				return WpResponseFactory::not_found();
			}

			if ( 403 === $e->getCode() ) {
				return WpResponseFactory::forbidden();
			}

			if ( 400 === $e->getCode() ) {
				return WpResponseFactory::bad_request( $e->getMessage() );
			}

			return WpResponseFactory::error( $e->getMessage() );
		}

		return WpResponseFactory::ok_with_data(
			array(
				'success'   => true,
				'id'        => $post_id,
				'author_id' => $author_id,
			)
		);
	}
}
