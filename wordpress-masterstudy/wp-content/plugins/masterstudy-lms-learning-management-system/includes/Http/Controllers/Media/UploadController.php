<?php

namespace MasterStudy\Lms\Http\Controllers\Media;

use MasterStudy\Lms\Plugin\Media;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;

class UploadController {
	public function __invoke( WP_REST_Request $request ) {
		$is_assignment = filter_var( $request->get_param( 'assignment' ), FILTER_VALIDATE_BOOLEAN );

		if ( ! $is_assignment && ! current_user_can( 'upload_files' ) ) {
			return new \WP_REST_Response(
				array(
					'error_code' => 'media_upload_access_error',
					'message'    => esc_html__( 'You do not have permission to upload media files.', 'masterstudy-lms-learning-management-system' ),
				),
				403
			);
		}

		$extensions = implode( ';', array_keys( apply_filters( 'upload_mimes', Media::MIMES ) ) );
		$validator  = new Validator(
			$request->get_file_params(),
			array(
				'file' => 'required|extension,' . str_replace( '|', ';', $extensions ),
			)
		);

		if ( $validator->fails() ) {
			return new \WP_REST_Response(
				array(
					'error_code' => 'media_upload_validation_error',
					'errors'     => $validator->get_errors_array(),
				),
				422
			);
		}

		return ( new \WP_REST_Attachments_Controller( 'attachment' ) )->create_item( $request );
	}
}
