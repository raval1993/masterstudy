<?php

namespace MasterStudy\Lms\Http\Controllers\AdminPost;

use MasterStudy\Lms\Http\Serializers\AdminPostAuthorSerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminPostAuthorRepository;
use WP_REST_Response;

final class GetAuthorsController {
	public function __invoke(): WP_REST_Response {
		return WpResponseFactory::ok_with_data(
			array(
				'authors' => ( new AdminPostAuthorSerializer() )->collectionToArray(
					( new AdminPostAuthorRepository() )->get_authors()
				),
			)
		);
	}
}
