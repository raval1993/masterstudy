<?php
/**
 * @var int $post_id
 * @var int $user_id
 * @var boolean $is_course_coming_soon
 * @var array $button_classes
 */
?>
<div class="<?php echo esc_attr( implode( ' ', $button_classes ) ); ?>">
	<?php
	$course                 = STM_LMS_Helpers::simplify_db_array(
		stm_lms_get_user_course(
			$user_id,
			$post_id,
			array(
				'current_lesson_id',
				'progress_percent',
			)
		)
	);
	$current_lesson         = $course['current_lesson_id'] ?? '0';
	$progress               = intval( $course['progress_percent'] ?? 0 );
	$lesson_url             = STM_LMS_Lesson::get_lesson_url( $post_id, $current_lesson );
	$btn_label              = esc_html__( 'Start course', 'masterstudy-lms-learning-management-system' );
	$trial_addon            = is_ms_lms_addon_enabled( 'shareware' );
	$guest_trial_enabled    = false;
	$is_certificate_enabled = \MasterStudy\Lms\Repositories\PricingRepository::is_certificate_enabled( $post_id );
	$price_info             = get_post_meta( $post_id, 'free_price_info', true );
	$cert_info              = empty( get_post_meta( $post_id, 'free_do_not_provide_certificate', true ) ) && $is_certificate_enabled;

	if ( $trial_addon ) {
		$is_trial_course = get_post_meta( $post_id, 'shareware', true );
		if ( 'on' === $is_trial_course ) {
			$shareware_settings  = get_option( 'stm_lms_shareware_settings' );
			$guest_trial_enabled = $shareware_settings['shareware_guest_trial'] ?? false;
		}
	}

	if ( empty( $user_id ) && $trial_addon && $guest_trial_enabled ) {
		?>
		<a class="masterstudy-buy-button__link masterstudy-buy-button__link_centered"
			href="<?php echo esc_url( $lesson_url ); ?>">
			<span
				class="masterstudy-buy-button__title"><?php echo esc_html( sanitize_text_field( $btn_label ) ); ?></span>
		</a>
		<?php
	} elseif ( empty( $user_id ) ) {
		?>
		<a class="masterstudy-buy-button__link masterstudy-buy-button__link_centered" href="#"
			data-authorization-modal="login">
			<span
				class="masterstudy-buy-button__title"><?php echo esc_html__( 'Enroll course', 'masterstudy-lms-learning-management-system' ); ?></span>
		</a>
		<?php
	} else {
		if ( $progress > 0 ) {
			$btn_label = esc_html__( 'Continue', 'masterstudy-lms-learning-management-system' );
		}

		if ( $is_course_coming_soon ) {
			?>
			<a href="#"
				class="masterstudy-buy-button__link masterstudy-buy-button__link_centered masterstudy-buy-button__link_disabled">
				<span
					class="masterstudy-buy-button__title"><?php echo esc_html__( 'Coming soon', 'masterstudy-lms-learning-management-system' ); ?></span>
			</a>
			<?php
		} else {
			?>
			<a class="masterstudy-buy-button__link masterstudy-buy-button__link_centered"
				href="<?php echo esc_url( $lesson_url ); ?>">
				<span
					class="masterstudy-buy-button__title"><?php echo esc_html( sanitize_text_field( $btn_label ) ); ?></span>
			</a>
			<?php
		}
	}
	?>
</div>
<?php if ( ( ! empty( $price_info ) || $cert_info ) && empty( $course ) ) : ?>
	<span class="masterstudy-buy-button__single-price-info-text">
			<?php
			echo esc_html(
				STM_LMS_Helpers::masterstudy_lms_pricing_concat_info(
					array(
						$price_info ?? '',
						$cert_info ? esc_html__( 'Certificate included', 'masterstudy-lms-learning-management-system' ) : null,
					)
				)
			);
			?>
	</span>
<?php endif ?>
