<?php

namespace MasterStudy\Lms\Http\Controllers\Student;

use WP_REST_Request;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\StudentsRepository;

final class DeleteStudentsController {

	public function __invoke( WP_REST_Request $request ) {
		( new StudentsRepository() )->delete_student( $request->get_param( 'students' ) );

		return WpResponseFactory::ok();
	}
}
