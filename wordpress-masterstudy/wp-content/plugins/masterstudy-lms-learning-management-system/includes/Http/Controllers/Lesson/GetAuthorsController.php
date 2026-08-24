<?php

namespace MasterStudy\Lms\Http\Controllers\Lesson;

use MasterStudy\Lms\Http\Serializers\LessonAuthorSerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\LessonAdminRepository;
use WP_REST_Response;

final class GetAuthorsController {
	public function __invoke(): WP_REST_Response {
		return WpResponseFactory::ok_with_data(
			array(
				'authors' => ( new LessonAuthorSerializer() )->collectionToArray(
					( new LessonAdminRepository() )->get_authors()
				),
			)
		);
	}
}
