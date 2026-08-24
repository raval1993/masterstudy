<?php
/**
 * @var $current_user
 */

$current_user['roles'] = $current_user['roles'] ?? array();

if ( ! in_array( 'stm_lms_instructor', $current_user['roles'], true ) ) {
	$history = get_user_meta( $current_user['id'], 'submission_history', true );

	if ( ! empty( $history ) && is_array( $history ) && ! empty( $history[0] && empty( $history[0]['viewed'] ) ) ) {
		$submission_status = ! empty( $history[0]['status'] ) ? $history[0]['status'] : '';
		$message           = ! empty( $history[0]['message'] ) ? $history[0]['message'] : '';
		?>
		<div class="masterstudy-account-become-instructor-info <?php echo esc_attr( $submission_status ); ?>">
			<span class="masterstudy-account-become-instructor-info__close" data-user-id="<?php echo esc_attr( $current_user['id'] ); ?>"></span>
			<h3 class="masterstudy-account-become-instructor-info__title">
				<?php echo esc_html__( 'Your request to become an Instructor has been declined', 'masterstudy-lms-learning-management-system' ); ?>
			</h3>
			<?php if ( ! empty( $message ) ) { ?>
				<p class="masterstudy-account-become-instructor-info__text">
					<?php echo esc_html( $message ); ?>
				</p>
			<?php } ?>
		</div>
		<?php
	}
}
