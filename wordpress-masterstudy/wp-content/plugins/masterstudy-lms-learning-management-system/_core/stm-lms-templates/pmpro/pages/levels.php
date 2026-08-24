<?php
/**
 * Template: Levels
 * Version: 3.1
 *
 * @version 3.1
 */

global $wpdb, $pmpro_msg, $pmpro_msgt, $current_user;

$pmpro_levels      = pmpro_getAllLevels( false, true );
$pmpro_level_order = pmpro_getOption( 'level_order' );
if ( ! empty( $pmpro_level_order ) ) {
	$order_array = explode( ',', $pmpro_level_order );
	if ( count( $order_array ) === count( $pmpro_levels ) ) {
		array_multisort( $order_array, SORT_ASC, $pmpro_levels );
	}
}
$pmpro_levels = apply_filters( 'pmpro_levels_array', $pmpro_levels );
if ( $pmpro_msg ) { ?>
	<div class="pmpro_message <?php echo esc_attr( $pmpro_msg ); ?>"><?php echo wp_kses_post( $pmpro_msg ); ?></div>
<?php } ?>
<div class="stm_lms_pmpro_head">
	<h1 class="stm_lms_pmpro_title"><?php esc_html_e( 'Membership plans', 'masterstudy-lms-learning-management-system' ); ?></h1>
	<div class="stm_lms_pmpro_groups heading_font"></div>
</div>
<div class="stm_lms_plans">
	<?php
	$count = 0;
	foreach ( $pmpro_levels as $level_number => $level ) {
		$current_level = isset( $current_user->membership_level->ID ) ? intval( $current_user->membership_level->ID ) === intval( $level->id ) : false;
		$odd           = ( 0 === $count % 2 ) ? 'odd' : 'even';
		$count++;
		$courses_included = get_option( "stm_lms_course_number_{$level->id}" );
		$featured_quotas  = get_option( "stm_lms_featured_courses_number_{$level->id}" );
		$plan_group       = get_option( "stm_lms_plan_group_{$level->id}" );
		if ( empty( $plan_group ) ) {
			$plan_group = esc_html__( 'All', 'masterstudy-lms-learning-management-system' );
		}
		if ( empty( $current_user->membership_level->ID ) || ! $current_level ) {
			$text = esc_html__( 'Get now', 'masterstudy-lms-learning-management-system' );
			$url  = pmpro_url( 'checkout', '?level=' . $level->id, 'https' );
		} elseif ( $current_level ) {
			if ( pmpro_isLevelExpiringSoon( $current_user->membership_level ) && $current_user->membership_level->allow_signups ) {
				$text = esc_html__( 'Renew', 'masterstudy-lms-learning-management-system' );
				$url  = pmpro_url( 'checkout', '?level=' . $level->id, 'https' );
			} else {
				$text = esc_html__( 'Your Level', 'masterstudy-lms-learning-management-system' );
				$url  = pmpro_url( 'account' );
			}
		}
		$level_price  = ( pmpro_isLevelFree( $level ) ) ? esc_html__( 'Free', 'masterstudy-lms-learning-management-system' ) : pmpro_formatPrice( $level->initial_payment );
		$level_period = pmpro_translate_billing_period( $level->cycle_period );
		$cost_text    = pmpro_getLevelCost( $level, false, false );
		if ( ( empty( $level->trial_amount ) && empty( $level->trial_limit ) ) || empty( $level->billing_amount ) ) {
			$cost_text = '';
		}
		?>
		<div class="stm_lms_plan stm_lms_plan__<?php echo esc_attr( $odd ); ?>" data-group="<?php echo esc_attr( $plan_group ); ?>">
			<div class="stm_lms_plan__inner">
				<div class="stm_lms_plan__inner_top">
					<div class="stm_lms_plan__title">
						<h3 class="text-center">
							<?php echo esc_html( $level->name ); ?>
						</h3>
					</div>
					<div class="stm_lms_plan__price">
						<div class="stm_lms_plan__with_btn">
							<p class="price heading_font secondary_color price_<?php echo esc_html( $level_price ); ?>"><?php echo wp_kses_post( $level_price ); ?></p>
							<div class="stm_lms_plan__button">
								<a class="btn btn-default" href="<?php echo esc_url( $url ); ?>">
									<?php echo esc_attr( $text ); ?>
								</a>
							</div>
						</div>
						<?php if ( ! empty( $level_period ) ) : ?>
							<div class="stm_lms_plan__period heading_font">
								<?php
								/* translators: %s: string */
								printf( esc_html__( 'per %s', 'masterstudy-lms-learning-management-system' ), esc_html( $level_period ) );
								?>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="stm_lms_plan__inner_content heading_font">
					<?php if ( ! empty( $courses_included ) ) : ?>
						<div class="stm_lms_plan__included">
							<?php
							/* translators: %s: string */
							printf( esc_html__( 'Courses included: %s', 'masterstudy-lms-learning-management-system' ), esc_html( $courses_included ) );
							?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $featured_quotas ) ) : ?>
						<div class="stm_lms_plan__included">
							<?php
							/* translators: %s: string */
							printf( esc_html__( 'Featured courses quote included: %s', 'masterstudy-lms-learning-management-system' ), esc_html( $featured_quotas ) );
							?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $cost_text ) && ! pmpro_isLevelFree( $level ) ) : ?>
						<div class="stm_lms_plan__included">
							<?php echo wp_kses_post( $cost_text ); ?>
						</div>
					<?php endif; ?>
					<div class="stm_lms_plan__description">
						<?php echo wp_kses_post( ent2ncr( $level->description ) ); ?>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
</div>
