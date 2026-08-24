<?php
wp_enqueue_style( 'masterstudy-account-manage-students-import-modal' );
wp_enqueue_script( 'masterstudy-account-manage-students-import-modal' );
?>
<div class="masterstudy-manage-students-import__modal" data-course-id="<?php echo esc_attr( $course_id ); ?>">
	<div class="masterstudy-manage-students-import__modal-wrapper">
		<div class="masterstudy-manage-students-import__modal-header">
			<span class="masterstudy-manage-students-import__modal-title">
				<span data-step="1,2,3"><?php esc_html_e( 'Import students from CSV', 'masterstudy-lms-learning-management-system' ); ?></span>
				<span data-step="6"><?php esc_html_e( 'Import partially complete', 'masterstudy-lms-learning-management-system' ); ?></span>
			</span>
			<span class="masterstudy-manage-students-import__modal-close"></span>
		</div>
		<div class="masterstudy-manage-students-import__modal-text">
			<span data-step="1,2,3">
				<?php esc_html_e( 'Invalid email addresses will not be imported.', 'masterstudy-lms-learning-management-system' ); ?>
			</span>
			<span data-step="6">
				<span class="masterstudy-manage-students-import__user-count"></span>
				<?php esc_html_e( 'users imported.', 'masterstudy-lms-learning-management-system' ); ?>
			</span>
		</div>
		<div class="masterstudy-manage-students-import__modal-download" data-step="1,2">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'title'         => esc_html__( 'Download a CSV file template', 'masterstudy-lms-learning-management-system' ),
					'link'          => esc_url( STM_LMS_URL . 'assets/samples/import_users.csv' ),
					'style'         => 'tertiary',
					'size'          => 'sm',
					'id'            => 'donwload-students-csv-template',
					'icon_position' => 'left',
					'icon_name'     => 'download-alt',
				)
			);
			?>
		</div>
		<div class="masterstudy-manage-students-import__info hidden" data-step="6">
			<span class="masterstudy-manage-students-import__warning">
				<i class="stmlms-exclamation-triangle"></i>
				<?php esc_html_e( 'The users below were not imported as they had already been enrolled in this course.', 'masterstudy-lms-learning-management-system' ); ?>
			</span>
			<div class="masterstudy-manage-students-import__list">
				<span class="masterstudy-manage-students-import__list-item"></span>
			</div>
		</div>
		<div class="masterstudy-manage-students-import__file-upload" data-step="1">
			<div class="masterstudy-manage-students-import__file-upload__item-wrapper"></div>
			<div class="masterstudy-manage-students-import__file-upload__field">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title'         => esc_html__( 'Import CSV', 'masterstudy-lms-learning-management-system' ),
						'link'          => '#',
						'style'         => 'tertiary',
						'size'          => 'sm',
						'id'            => 'import-students-upload-csv-btn',
						'icon_position' => 'left',
						'icon_name'     => 'upload',
					)
				);
				?>
				<div class="masterstudy-manage-students-import__file-upload__field-text">
					<p><?php esc_html_e( 'Drag file here or click the button.', 'masterstudy-lms-learning-management-system' ); ?></p>
				</div>
				<div class="masterstudy-manage-students-import__file-upload__field-error" data-step="1">
					<i class="stmlms-exclamation-triangle"></i>
					<span class="masterstudy-manage-students-import__unsupported-file-type hidden">
						<?php esc_html_e( 'Unsupported file type.', 'masterstudy-lms-learning-management-system' ); ?>
					</span>
					<span class="masterstudy-manage-students-empty-file hidden">
						<?php esc_html_e( 'CSV file is empty.', 'masterstudy-lms-learning-management-system' ); ?>
					</span>
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'title' => esc_html__( 'Try again', 'masterstudy-lms-learning-management-system' ),
							'link'  => '#',
							'style' => 'primary',
							'size'  => 'sm',
							'id'    => 'import-students-next-attempt',
						)
					);
					?>
				</div>
				<input type="file" class="masterstudy-manage-students-import__file-upload__input" accept=".csv">
			</div>
		</div>
		<div class="masterstudy-manage-students-import__file-attachment__wrapper hidden" data-step="2">
			<div  class="masterstudy-manage-students-import__file-attachment__description">
				<?php echo esc_html__( 'Uploaded file', 'masterstudy-lms-learning-management-system' ); ?>:
			</div>
			<div class="masterstudy-manage-students-import__file-attachment">
				<div class="masterstudy-manage-students-import__file-attachment__info">
					<img src="<?php echo esc_url( STM_LMS_URL . 'assets/icons/files/new/excel.svg' ); ?>" class="masterstudy-manage-students-import__file-attachment__image">
					<div class="masterstudy-manage-students-import__file-attachment__wrapper">
						<span class="masterstudy-manage-students-import__file-attachment__title"></span>
						<span class="masterstudy-manage-students-import__file-attachment__size"></span>
						<span class="masterstudy-manage-students-import__file-attachment__delete"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="masterstudy-manage-students-import__progress hidden" data-step="3">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/progress',
				array(
					'title'     => esc_html__( 'Importing', 'masterstudy-lms-learning-management-system' ),
					'progress'  => 0,
					'dark_mode' => false,
					'is_hidden' => false,
				)
			);
			?>
		</div>
		<div class="masterstudy-manage-students-import__adding-box hidden" data-step="4,5,7,8">
			<div class="masterstudy-manage-students-import__adding-box__icon-wrapper">
				<span class="masterstudy-manage-students-import__adding-box__icon"></span>
			</div>
			<div class="masterstudy-manage-students-import__adding-box__message">
				<span class="masterstudy-manage-students-import__adding-box__title" data-step="4">
					<?php esc_html_e( 'Import successfully complete!', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<span class="masterstudy-manage-students-import__adding-box__description" data-step="4">
					<span class="masterstudy-manage-students-import__user-count">0</span>
					<?php esc_html_e( 'users were imported.', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<span class="masterstudy-manage-students-import__adding-box__title" data-step="5">
					<?php esc_html_e( 'Import has failed!', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<span class="masterstudy-manage-students-import__adding-box__description" data-step="5">
					<?php esc_html_e( 'Please check your CSV file and date and try again.', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<div data-step="5">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'title' => esc_html__( 'Try again', 'masterstudy-lms-learning-management-system' ),
							'link'  => '#',
							'style' => 'primary',
							'size'  => 'sm',
							'id'    => 'import-students-next-attempt',
						)
					);
					?>
				</div>
				<span class="masterstudy-manage-students-import__adding-box__title adding-title" data-step="7">
					<?php esc_html_e( 'Invite student to this course', 'masterstudy-lms-learning-management-system' ); ?>:
					<?php echo esc_html( get_the_title( $course_id ) ); ?>
				</span>
				<span class="masterstudy-manage-students-import__adding-box__description" data-step="7">
					<?php esc_html_e( "Enter a student's email. If the student isn't registered on this site, the system will create user credentials.", 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<input type="email" class="masterstudy-manage-students-import__email-input" value="" data-step="7" placeholder="<?php echo esc_html__( 'Enter student email.', 'masterstudy-lms-learning-management-system' ); ?>">
				<span class="masterstudy-manage-students-import__incorrect-email hidden"><?php esc_html_e( 'Please enter correct email.', 'masterstudy-lms-learning-management-system' ); ?></span>
				<div class="masterstudy-manage-students-import__send-invitation" data-step="7">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'title' => esc_html__( 'Send an invitation', 'masterstudy-lms-learning-management-system' ),
							'link'  => '#',
							'style' => 'primary',
							'size'  => 'sm',
							'id'    => 'send-invitation',
						)
					);
					?>
				</div>
				<span class="masterstudy-manage-students-import__adding-box__title adding-title" data-step="8">
					<?php esc_html_e( 'Student successfully invited', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
			</div>
			<div class="masterstudy-manage-students-import__adding-box__action" data-step="4,8">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => esc_html__( 'Close', 'masterstudy-lms-learning-management-system' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
						'id'    => 'import-students-close-modal',
					)
				);
				?>
			</div>
		</div>
		<div class="masterstudy-manage-students-import__modal-actions">
			<div data-step="1,2">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => esc_html__( 'Import', 'masterstudy-lms-learning-management-system' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
						'id'    => 'import-students-submit',
					)
				);
				?>
			</div>
			<div data-step="6">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => esc_html__( 'Close', 'masterstudy-lms-learning-management-system' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
						'id'    => 'import-students-close-modal',
					)
				);
				?>
			</div>
		</div>
	</div>
</div>
