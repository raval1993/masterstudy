<?php
/**
 * @var string $username
 * @var string $user_id
 * @var boolean $logged_in
 */

wp_enqueue_style( 'masterstudy-message-modal' );
wp_enqueue_script( 'masterstudy-message-modal' );
wp_localize_script(
	'masterstudy-message-modal',
	'message_modal_data',
	array(
		'user_id'   => $user_id,
		'logged_in' => $logged_in,
	)
);
?>

<div class="masterstudy-message-modal" style="opacity:0">
	<div class="masterstudy-message-modal__wrapper">
		<div class="masterstudy-message-modal__container">
			<div class="masterstudy-message-modal__header">
				<span class="masterstudy-message-modal__header-title">
					<?php echo esc_html__( 'Send message', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<span class="masterstudy-message-modal__header-close"></span>
			</div>
			<div class="masterstudy-message-modal__user">
				<span class="masterstudy-message-modal__user-title">
					<?php echo esc_html__( 'send message to', 'masterstudy-lms-learning-management-system' ); ?>:
				</span>
				<span class="masterstudy-message-modal__username">
					<?php echo esc_html( $username ); ?>
				</span>
			</div>
			<div class="masterstudy-message-modal__close"></div>
			<div class="masterstudy-message-modal__form">
				<textarea name="modal-text" id="masterstudy-message-modal-text" placeholder="<?php esc_html_e( 'Enter message', 'masterstudy-lms-learning-management-system' ); ?>" rows="6" class="masterstudy-message-modal__form-textarea"></textarea>
				<span class="masterstudy-message-modal__error">
					<?php echo esc_html__( 'You need to write a message', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
			</div>
			<div class="masterstudy-message-modal__actions">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'id'    => 'masterstudy-message-modal-confirm',
						'title' => __( 'Send', 'masterstudy-lms-learning-management-system' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
					)
				);
				?>
			</div>
			<div class="masterstudy-message-modal__success">
				<div class="masterstudy-message-modal__success-icon-wrapper">
					<span class="masterstudy-message-modal__success-icon"></span>
				</div>
				<span class="masterstudy-message-modal__success-title">
					<?php echo esc_html__( 'Message sent', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'id'    => 'masterstudy-message-modal-close-button',
						'title' => __( 'Close', 'masterstudy-lms-learning-management-system' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
					)
				);
				?>
			</div>
		</div>
	</div>
</div>
