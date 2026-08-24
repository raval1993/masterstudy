<?php

/**
 * @var string $id
 * @var array $attachments
 * @var int $files_limit
 * @var array $allowed_extensions
 * @var int $allowed_filesize
 * @var string $allowed_filesize_label
 * @var string $file_dimensions
 * @var string $upload_nonce
 * @var string $delete_nonce
 * @var string $file_upload_action
 * @var string $file_delete_action
 * @var boolean $readonly
 * @var boolean $multiple
 * @var boolean $dark_mode
 * @var boolean $full_image_view
 * @var string $title
 * @var string $style
 *
 * masterstudy-file-upload_dark-mode - for dark mode
 * masterstudy-file-upload__field_loading - for show loading progress in file upload field
 * masterstudy-file-upload__field_highlight - for highlight file upload field when file dragged to it
 * add style "width: ...%" to masterstudy-file-upload__field-progress-bar-filled to show progress
 * styles: compact
 */

$full_image_view = isset( $full_image_view ) ? $full_image_view : false;
$style           = isset( $style ) ? $style : '';
$title           = isset( $title ) ? $title : __( 'Upload file', 'masterstudy-lms-learning-management-system' );

wp_enqueue_style( 'masterstudy-file-upload' );
wp_enqueue_script( 'masterstudy-file-upload' );
wp_localize_script(
	'masterstudy-file-upload',
	'masterstudy_file_upload_data',
	array(
		'ajax_url'           => admin_url( 'admin-ajax.php' ),
		'file_upload_nonce'  => wp_create_nonce( $upload_nonce ),
		'file_delete_nonce'  => wp_create_nonce( $delete_nonce ),
		'file_upload_action' => $file_upload_action,
		'file_delete_action' => $file_delete_action,
		'full_image_view'    => $full_image_view,
		'icon_url'           => STM_LMS_URL . 'assets/icons/files/new/',
		'only_one_file'      => __( 'Only one file allowed', 'masterstudy-lms-learning-management-system' ),
	)
);

$files_limit     = isset( $files_limit ) ? $files_limit : '';
$file_dimensions = isset( $file_dimensions ) ? $file_dimensions : '';
$classes         = ! empty( $style ) ? ' masterstudy-file-upload_style-' . esc_attr( $style ) : '';
$classes        .= $dark_mode ? ' masterstudy-file-upload_dark-mode' : '';
?>

<div class="masterstudy-file-upload <?php echo esc_attr( $classes ); ?>">
	<div class="masterstudy-file-upload__item-wrapper">
		<?php
		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $attachment ) {
				$file = ms_plugin_attachment_data( $attachment );
				?>
				<div class="masterstudy-file-upload__item <?php echo $full_image_view ? 'masterstudy-file-upload__item_full-image' : ''; ?>">
					<img src="<?php echo esc_url( $full_image_view ? $file['url'] : STM_LMS_URL . "/assets/icons/files/new/{$file['current_format']}.svg" ); ?>" class="masterstudy-file-upload__image">
					<div class="masterstudy-file-upload__wrapper">
						<span class="masterstudy-file-upload__title"><?php echo esc_html( $file['file_title'] ); ?></span>
						<span class="masterstudy-file-upload__size"><?php echo esc_html( $file['filesize'] . ' ' . $file['filesize_label'] ); ?></span>
						<?php
						if ( $readonly ) {
							?>
							<a class="masterstudy-file-upload__link masterstudy-file-upload__link_readonly" href="<?php echo esc_url( $file['url'] ); ?>" target="_blank" download>
								<?php echo esc_html__( 'Download', 'masterstudy-lms-learning-management-system' ); ?>
							</a>
							<?php
						} else {
							?>
							<a class="masterstudy-file-upload__link" href="#" data-id="<?php echo esc_attr( $file['file_id'] ); ?>"></a>
						<?php } ?>
					</div>
					<?php if ( $full_image_view ) { ?>
						<span class="masterstudy-file-upload__item-cover"></span>
					<?php } ?>
				</div>
				<?php
			}
		}
		?>
	</div>
	<?php
	if ( ! $readonly ) {
		?>
		<div id="<?php echo esc_attr( $id ); ?>" class="masterstudy-file-upload__field <?php echo $full_image_view && ! empty( $attachments ) ? 'masterstudy-file-upload__field_disabled' : ''; ?>">
			<span class="masterstudy-file-upload__field-button">
				<?php echo esc_html( $title ); ?>
			</span>
			<div class="masterstudy-file-upload__field-text">
				<p>
					<?php
					$label = $multiple ? __( 'files', 'masterstudy-lms-learning-management-system' ) : __( 'file', 'masterstudy-lms-learning-management-system' );
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
					<div class="masterstudy-file-upload__field-hint">
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/hint',
							array(
								'content'   => esc_html( $extensions_string ),
								'side'      => 'left',
								'dark_mode' => $dark_mode,
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
						echo esc_html( $allowed_filesize . ' ' . $allowed_filesize_label );
						?>
					</p>
					<?php
				} if ( ! empty( $files_limit ) ) {
					?>
					<p>
						<?php
						echo esc_html__( 'Max files limit: ', 'masterstudy-lms-learning-management-system' );
						echo esc_html( $files_limit );
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
			<div class="masterstudy-file-upload__field-error"></div>
			<div class="masterstudy-file-upload__field-progress">
				<div class="masterstudy-file-upload__field-progress-bars">
					<span class="masterstudy-file-upload__field-progress-bar-empty"></span>
					<span class="masterstudy-file-upload__field-progress-bar-filled"></span>
				</div>
				<div class="masterstudy-file-upload__field-progress-title">
					<?php echo esc_html__( 'Uploading...', 'masterstudy-lms-learning-management-system' ); ?>
				</div>
			</div>
			<input type="file" name="<?php echo esc_attr( $id ); ?>" class="masterstudy-file-upload__input" <?php echo esc_attr( $multiple ? 'multiple' : '' ); ?> accept="<?php echo esc_html( $extensions_string ); ?>"/>
		</div>
	<?php } ?>
</div>
