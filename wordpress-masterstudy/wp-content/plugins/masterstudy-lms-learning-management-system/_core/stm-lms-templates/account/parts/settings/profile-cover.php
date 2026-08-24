<div class="masterstudy-account-settings__field masterstudy-account-settings__field_full">
	<div class="masterstudy-account-settings__field-wrapper">
		<?php
		$cover_id = get_user_meta( get_current_user_id(), 'stm_lms_user_cover', true );
		STM_LMS_Templates::show_lms_template(
			'components/file-upload',
			array(
				'id'                     => 'profile-cover',
				'attachments'            => ! empty( $cover_id ) ? array( get_post( $cover_id ) ) : array(),
				'allowed_extensions'     => array( '.png', '.jpg', '.jpeg' ),
				'files_limit'            => '',
				'allowed_filesize'       => 10,
				'allowed_filesize_label' => 'mb',
				'file_dimensions'        => '1140px x 220px',
				'upload_nonce'           => 'stm_lms_change_cover',
				'delete_nonce'           => 'stm_lms_delete_cover',
				'file_upload_action'     => 'stm_lms_change_cover',
				'file_delete_action'     => 'stm_lms_delete_cover',
				'readonly'               => false,
				'multiple'               => false,
				'dark_mode'              => false,
				'full_image_view'        => true,
				'title'                  => esc_html__( 'Upload Cover', 'masterstudy-lms-learning-management-system' ),
				'style'                  => 'compact',
			)
		);
		STM_LMS_Templates::show_lms_template(
			'components/alert',
			array(
				'id'                  => 'file_upload_file_alert',
				'title'               => esc_html__( 'Delete file', 'masterstudy-lms-learning-management-system' ),
				'text'                => esc_html__( 'Are you sure you want to delete this file?', 'masterstudy-lms-learning-management-system' ),
				'submit_button_text'  => esc_html__( 'Delete', 'masterstudy-lms-learning-management-system' ),
				'cancel_button_text'  => esc_html__( 'Cancel', 'masterstudy-lms-learning-management-system' ),
				'submit_button_style' => 'danger',
				'cancel_button_style' => 'tertiary',
				'dark_mode'           => false,
			)
		);
		?>
	</div>
</div>
