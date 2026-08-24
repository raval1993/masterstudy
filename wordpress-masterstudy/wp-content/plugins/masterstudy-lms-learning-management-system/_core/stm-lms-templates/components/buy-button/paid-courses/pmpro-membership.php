<?php
/**
 * @var int $post_id
 * @var array plans_courses
 * @var bool $plans_have_quota
 */

if ( $plans_have_quota ) {
	?>
	<button type="button" class="masterstudy-membership-button" data-masterstudy-modal="masterstudy-membership-modal">
		<span class="masterstudy-membership-button__title">
			<?php echo esc_html__( 'Enroll with Membership', 'masterstudy-lms-learning-management-system' ); ?>
		</span>
	</button>
	<?php
} else {
	global $pmpro_currency_symbol;

	$buy_url = STM_LMS_Subscriptions::level_url();

	foreach ( $plans_courses as $plan_course ) {
		$plan_course_limit = get_option( "stm_lms_course_number_{$plan_course->id}", 0 );

		if ( empty( $plan_course_limit ) ) {
			continue;
		}

		stm_lms_register_script( 'buy/plan_cookie', array( 'jquery.cookie' ), true );

		$buy_url = add_query_arg( 'level', $plan_course->id, STM_LMS_Subscriptions::checkout_url() );
		$period  = ( $plan_course->cycle_period ) ? $plan_course->cycle_period : $plan_course->expiration_period;
		?>
		<a href="<?php echo esc_url( $buy_url ); ?>" data-course-id="<?php echo esc_attr( $post_id ); ?>" class="masterstudy-membership-plan-link">
			<div class="masterstudy-membership-plan__label"><?php echo esc_html( $plan_course->name ); ?></div>
			<?php if ( '0' !== $plan_course->initial_payment && ! empty( $plan_course->initial_payment ) ) { ?>
				<div class="masterstudy-membership-plan__price">
					<?php
					echo esc_html( $pmpro_currency_symbol . $plan_course->initial_payment );
					if ( ! empty( $plan_course->cycle_period ) ) {
						?>
						<div class="masterstudy-membership-plan__period">
							<?php echo '/' . esc_html( $plan_course->cycle_period ); ?>
						</div>
						<?php
					}
					?>
				</div>
			<?php } ?>
		</a>
		<?php
	}
}
