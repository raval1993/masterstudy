<?php

namespace MasterStudy\Lms\Http\Controllers\AdminInstructor;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\AdminInstructorRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class CreateInstructorController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params();

		if ( ! is_array( $params ) ) {
			return WpResponseFactory::validation_failed(
				array(
					'body' => array( esc_html__( 'Invalid request body.', 'masterstudy-lms-learning-management-system' ) ),
				)
			);
		}

		$mode = sanitize_text_field( $params['mode'] ?? '' );

		if ( ! in_array( $mode, array( 'new', 'existing' ), true ) ) {
			return WpResponseFactory::validation_failed(
				array(
					'mode' => array( esc_html__( 'The mode field is not a valid option', 'masterstudy-lms-learning-management-system' ) ),
				)
			);
		}

		if ( 'new' === $mode ) {
			if ( ! current_user_can( 'create_users' ) || ! current_user_can( 'promote_users' ) ) {
				return WpResponseFactory::forbidden();
			}
		} elseif ( ! current_user_can( 'promote_users' ) ) {
			return WpResponseFactory::forbidden();
		}

		$rules = array(
			'mode'       => 'required|string|contains_list,new;existing',
			'degree'     => 'nullable|string',
			'expertize'  => 'nullable|string',
			'admin_note' => 'nullable|string',
		);

		if ( 'new' === $mode ) {
			$rules = array_merge(
				$rules,
				array(
					'username'   => 'required|string',
					'email'      => 'required|email',
					'first_name' => 'nullable|string',
					'last_name'  => 'nullable|string',
					'url'        => 'nullable|string',
				)
			);
		} else {
			$rules['user_id'] = 'required|integer';
		}

		$validator = new Validator( $params, $rules );

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$repository = new AdminInstructorRepository();
		$result     = $repository->create_instructor( $validator->get_validated() );

		if ( 'validation_error' === $result['status'] ) {
			return WpResponseFactory::validation_failed( $result['errors'] ?? array() );
		}

		if ( 'forbidden' === $result['status'] ) {
			return WpResponseFactory::forbidden();
		}

		if ( 'created' !== $result['status'] || empty( $result['instructor'] ) ) {
			return WpResponseFactory::bad_request(
				esc_html__( 'Unable to create instructor.', 'masterstudy-lms-learning-management-system' )
			);
		}

		return WpResponseFactory::created(
			array(
				'instructor' => $result['instructor'],
			)
		);
	}
}
