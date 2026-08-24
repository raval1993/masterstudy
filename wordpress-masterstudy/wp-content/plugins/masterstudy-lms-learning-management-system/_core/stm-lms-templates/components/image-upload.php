<?php

/**
 * @var string $id
 * @var object $attachment
 * @var array $allowed_extensions
 * @var int $allowed_filesize
 *
 * masterstudy-image-upload__field_loading - for show loading progress in file upload field
 * masterstudy-image-upload__field_highlight - for highlight file upload field when file dragged to it
 * add style "width: ...%" to masterstudy-image-upload__field-progress-bar-filled to show progress
 */

$file_dimensions = isset( $file_dimensions ) ? $file_dimensions : '';

wp_enqueue_style( 'masterstudy-image-upload' );
wp_enqueue_script( 'masterstudy-image-upload' );
wp_localize_script(
	'masterstudy-image-upload',
	'masterstudy_image_upload_data',
	array(
		'allowed_extensions' => $allowed_extensions,
		'allowed_filesize'   => $allowed_filesize,
		'too_large'          => __( 'File too large', 'masterstudy-lms-learning-management-system' ),
		'type_not_allowed'   => __( 'File type not allowed', 'masterstudy-lms-learning-management-system' ),
	)
);
?>

<div class="masterstudy-image-upload">
	<div class="masterstudy-image-upload__item-wrapper">
		<?php
		if ( ! empty( $attachment ) ) {
			$file = ms_plugin_attachment_data( $attachment );
			?>
			<div class="masterstudy-image-upload__item">
				<img src="<?php echo esc_url( $file['url'] ); ?>" class="masterstudy-image-upload__image">
				<a class="masterstudy-image-upload__delete" href="#" data-id="<?php echo esc_attr( $file['file_id'] ); ?>"></a>
				<span class="masterstudy-image-upload__item-cover"></span>
			</div>
			<?php
		}
		?>
	</div>
	<div id="<?php echo esc_attr( $id ) . '_field'; ?>" class="masterstudy-image-upload__field <?php echo ! empty( $attachment ) ? 'masterstudy-image-upload__field_hide' : ''; ?>">
		<span class="masterstudy-image-upload__field-button">
			<?php echo esc_html__( 'Upload image', 'masterstudy-lms-learning-management-system' ); ?>
		</span>
		<div class="masterstudy-image-upload__field-text">
			<p>
				<?php
				$label = __( 'file', 'masterstudy-lms-learning-management-system' );
				echo esc_html(
					sprintf(
					/* translators: %s string */
						__( 'Drag %s here or click the button.', 'masterstudy-lms-learning-management-system' ),
						$label
					),
				);
				?>
			</p>
			<?php
			if ( ! empty( $allowed_extensions ) ) {
				$extensions_string = implode( ', ', $allowed_extensions );
				?>
				<div class="masterstudy-image-upload__field-hint">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/hint',
						array(
							'content'   => esc_html( $extensions_string ),
							'side'      => 'left',
							'dark_mode' => false,
						)
					);
					echo esc_html__( 'Supported file formats', 'masterstudy-lms-learning-management-system' );
					?>
				</div>
				<?php
			}
			if ( ! empty( $allowed_filesize ) ) {
				?>
				<p>
					<?php
					echo esc_html__( 'Max file size: ', 'masterstudy-lms-learning-management-system' );
					echo esc_html( $allowed_filesize . ' MB' );
					?>
				</p>
				<?php
			}
			if ( ! empty( $file_dimensions ) ) {
				?>
				<p>
					<?php
					echo esc_html__( 'File dimensions: ', 'masterstudy-lms-learning-management-system' );
					echo esc_html( $file_dimensions );
					?>
				</p>
				<?php
			}
			?>
		</div>
		</span>
		<div class="masterstudy-image-upload__field-error"></div>
		<div class="masterstudy-image-upload__field-progress">
			<div class="masterstudy-image-upload__field-progress-bars">
				<span class="masterstudy-image-upload__field-progress-bar-empty"></span>
				<span class="masterstudy-image-upload__field-progress-bar-filled"></span>
			</div>
			<div class="masterstudy-image-upload__field-progress-title">
				<?php echo esc_html__( 'Uploading...', 'masterstudy-lms-learning-management-system' ); ?>
			</div>
		</div>
		<input type="file" name="<?php echo esc_attr( $id ); ?>" class="masterstudy-image-upload__input" accept="<?php echo esc_html( $extensions_string ); ?>"/>
	</div>
</div>
