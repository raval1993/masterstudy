<?php

namespace MasterStudy\Lms\Http\Controllers\Course;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Repositories\CourseRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class BulkUpdateCoursesController {
	private const MAX_BULK_COURSES = 100;

	private CourseRepository $course_repository;

	public function __construct() {
		$this->course_repository = new CourseRepository();
	}

	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'action'  => 'required|string|contains_list,delete;update_status',
				'courses' => 'required|array',
				'status'  => 'nullable|string|contains_list,publish;pending;draft;trash;private',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$params  = $validator->get_validated();
		$action  = (string) $params['action'];
		$courses = (array) $params['courses'];
		$status  = $params['status'] ?? null;

		if ( count( $courses ) > self::MAX_BULK_COURSES ) {
			return WpResponseFactory::bad_request(
				sprintf(
					/* translators: %d: maximum number of courses allowed in one bulk request. */
					esc_html__( 'Too many courses in one request. Maximum allowed is %d.', 'masterstudy-lms-learning-management-system' ),
					self::MAX_BULK_COURSES
				)
			);
		}

		if (
			'update_status' === $action &&
			empty( $status ) &&
			! empty( $courses ) &&
			is_array( $courses[0] ) &&
			! empty( $courses[0]['status'] )
		) {
			$status = (string) $courses[0]['status'];
		}

		if ( 'update_status' === $action && empty( $status ) ) {
			return WpResponseFactory::bad_request(
				esc_html__( 'Status is required for update_status action.', 'masterstudy-lms-learning-management-system' )
			);
		}

		$course_ids = $this->extract_course_ids( $courses );

		if ( null === $course_ids ) {
			return WpResponseFactory::bad_request(
				esc_html__( 'Invalid course provided for bulk action.', 'masterstudy-lms-learning-management-system' )
			);
		}

		try {
			if ( 'delete' === $action ) {
				$this->course_repository->bulk_delete( $course_ids );
			} elseif ( 'update_status' === $action ) {
				$this->course_repository->bulk_update_status( $course_ids, $status );
			}
		} catch ( \RuntimeException $e ) {
			if ( 403 === $e->getCode() ) {
				return WpResponseFactory::forbidden();
			}

			return WpResponseFactory::error( $e->getMessage() );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
			)
		);
	}

	/**
	 * @param array $courses
	 * @return int[]|null
	 */
	private function extract_course_ids( array $courses ): ?array {
		$course_ids = array();

		foreach ( $courses as $course ) {
			$course_id = is_array( $course ) ? ( $course['id'] ?? 0 ) : (int) $course;

			if ( ! $course_id || PostType::COURSE !== get_post_type( $course_id ) ) {
				return null;
			}

			$course_ids[] = $course_id;
		}

		return $course_ids;
	}
}
