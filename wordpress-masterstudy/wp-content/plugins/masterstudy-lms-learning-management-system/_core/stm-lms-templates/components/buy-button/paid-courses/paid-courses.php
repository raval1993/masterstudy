<?php
/**
 * @var int $post_id
 * @var array $button_classes
 * @var bool $prerequisite_preview
 * @var bool $prerequisite_passed
 * @var bool $hide_group_course
 * @var mixed $only_membership
 */

use MasterStudy\Lms\Plugin\Addons;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;
use MasterStudy\Lms\Repositories\PricingRepository;

wp_enqueue_style( 'masterstudy-button' );

$logged_in                = is_user_logged_in();
$price                    = get_post_meta( $post_id, 'price', true );
$sale_price               = get_post_meta( $post_id, 'sale_price', true );
$single_sale              = get_post_meta( $post_id, 'single_sale', true );
$not_in_membership        = get_post_meta( $post_id, 'not_membership', true );
$is_subscriptions_enabled = get_post_meta( $post_id, 'subscriptions', true );
$price_info               = PricingRepository::get_price_info( $post_id );
$cert_info                = PricingRepository::get_certificates_info( $post_id );
$points_price             = class_exists( 'STM_LMS_Point_System' ) ? STM_LMS_Point_System::course_price( $post_id ) : null;
$points_enabled           = get_post_meta( $post_id, 'points', true );
$enterprise_price         = class_exists( 'STM_LMS_Enterprise_Courses' ) ? STM_LMS_Enterprise_Courses::get_enterprise_price( $post_id ) : null;
$enterprise_enabled       = get_post_meta( $post_id, 'enterprise', true );
$group_course_show        = $prerequisite_passed && empty( $hide_group_course ) && ! empty( $enterprise_enabled ) && $logged_in;
$show_buttons             = apply_filters( 'stm_lms_pro_show_button', true, $post_id );
$sale_price_active        = STM_LMS_Helpers::is_sale_price_active( $post_id );
$is_sale                  = ! empty( $sale_price ) && ! empty( $sale_price_active );
$guest_checkout           = STM_LMS_Options::get_option( 'guest_checkout', false );
$cert_included_text       = esc_html__( 'Certificate included', 'masterstudy-lms-learning-management-system' );

$pmpro_plans_courses        = array();
$pmpro_plans_have_quota     = false;
$pmpro_subscription_enabled = ( empty( $not_in_membership ) && STM_LMS_Subscriptions::subscription_enabled() && STM_LMS_Course::course_in_plan( $post_id ) );

$ms_subscription_enabled = is_ms_lms_addon_enabled( Addons::SUBSCRIPTIONS );
$ms_membership_plans     = array();
$ms_subscription_plans   = array();
$ms_payment_plans        = array();

if ( $ms_subscription_enabled ) {
	$subs_repo           = new SubscriptionPlanRepository();
	$ms_membership_plans = $subs_repo->get_enabled_plans_for_course( (int) $post_id );
	if ( ! empty( $is_subscriptions_enabled ) ) {
		$ms_subscription_plans = $subs_repo->get_course_plans( $post_id );
	}
}

if ( $pmpro_subscription_enabled ) {
	$pmpro_plans_courses  = STM_LMS_Course::course_in_plan( $post_id );
	$pmpro_subs           = STM_LMS_Subscriptions::user_subscription_levels();
	$pmpro_plans_post_ids = wp_list_pluck( $pmpro_plans_courses, 'id' );
	$needs_approval       = false;
	$pmpro_subs_info      = array();

	foreach ( $pmpro_subs as $sub ) {
		if ( ! in_array( $sub->ID, $pmpro_plans_post_ids, true ) ) {
			continue;
		}

		if ( $sub->course_number > 0 ) {
			$pmpro_plans_have_quota = true;
			$user_approval          = get_user_meta( get_current_user_id(), 'pmpro_approval_' . $sub->ID, true );

			if ( ! empty( $user_approval['status'] ) && in_array( $user_approval['status'], array( 'pending', 'denied' ), true ) ) {
				$needs_approval = true;
			}
		}
	}

	foreach ( $pmpro_subs as $sub ) {
		if ( ! in_array( $sub->ID, $pmpro_plans_post_ids, true ) ) {
			continue;
		}

		$pmpro_subs_info[] = array(
			'id'            => $sub->subscription_id,
			'course_id'     => $post_id,
			'name'          => $sub->name,
			'course_number' => $sub->course_number,
			'used_quotas'   => $sub->used_quotas,
			'quotas_left'   => $sub->quotas_left,
		);
	}
}

if ( ! empty( $ms_membership_plans ) || ! empty( $ms_subscription_plans['plans'] ) ) {
	wp_enqueue_script( 'masterstudy-buy-button-membership' );
	wp_localize_script(
		'masterstudy-buy-button-membership',
		'buy_button_subs_data',
		array(
			'guest_checkout' => $guest_checkout && ! $logged_in,
			'guest_nonce'    => wp_create_nonce( 'stm_lms_add_to_cart_guest' ),
			'logged_in'      => $logged_in,
		)
	);
}

$dropdown_enabled = (
	( ! empty( $pmpro_plans_courses ) && ! $not_in_membership )
	|| ( ! empty( $points_enabled ) && $logged_in )
	|| ( $group_course_show && $logged_in )
	|| ( ! empty( $ms_membership_plans ) && ! $not_in_membership )
	|| ( ! empty( $ms_subscription_plans['plans'] ) )
);

$button_classes = array(
	implode( ' ', $button_classes ),
	( $dropdown_enabled ) ? 'masterstudy-buy-button_dropdown' : '',
);

if ( $logged_in && ! $only_membership ) {
	$attributes = array(
		'data-purchased-course="' . intval( $post_id ) . '"',
	);
} else {
	$attributes = apply_filters(
		'stm_lms_buy_button_auth',
		array(
			'data-authorization-modal="login"',
		),
		$post_id
	);
}

if ( $show_buttons ) {
	?>
	<div class="<?php echo esc_attr( implode( ' ', $button_classes ) ); ?>">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/buy-button/paid-courses/buy-course',
			array(
				'attributes'        => $attributes,
				'price'             => 'on' !== $single_sale ? '' : $price,
				'sale_price'        => 'on' !== $single_sale ? '' : $sale_price,
				'sale_price_active' => $sale_price_active,
				'price_info'        => $price_info,
			)
		);
		$single_price_info = $price_info['single_sale_price_info'];
		if ( $dropdown_enabled ) {
			?>

			<div class="masterstudy-buy-button-dropdown">
				<?php
				if ( ! $only_membership ) {
					?>
					<div class="masterstudy-buy-button-dropdown__section">
						<div class="masterstudy-buy-button-dropdown__head">
							<span class="masterstudy-buy-button-dropdown__head-title">
								<?php echo esc_html__( 'One time purchase', 'masterstudy-lms-learning-management-system' ); ?>
							</span>
							<span class="masterstudy-buy-button-dropdown__head-checkbox"></span>
						</div>
						<div class="masterstudy-buy-button-dropdown__body">
							<div class="masterstudy-buy-button-dropdown__body-wrapper">
								<div class="masterstudy-buy-button__price-info">
									<?php if ( $is_sale ) { ?>
										<span class="masterstudy-buy-button__price-value">
											<?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $sale_price ) ); ?>
										</span>
									<?php } ?>
									<span class="masterstudy-buy-button__price-value <?php echo $is_sale ? 'masterstudy-buy-button__price-value_sale' : ''; ?>">
										<?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $price ) ); ?>
									</span>
								</div>
								<a href="#" <?php echo wp_kses_post( implode( ' ', apply_filters( 'stm_lms_buy_button_auth', $attributes, $post_id ) ) ); ?>
									class="masterstudy-purchase-button">
									<span class="masterstudy-purchase-button__title">
										<?php echo esc_html__( 'Buy course', 'masterstudy-lms-learning-management-system' ); ?>
									</span>
								</a>
								<?php if ( ! empty( $price_info['single_sale_price_info'] ) || $cert_info['single_sale'] ) : ?>
									<span class="masterstudy-buy-button__price-info-text">
										<?php echo esc_html( STM_LMS_Helpers::masterstudy_lms_pricing_concat_info( array( $price_info['single_sale_price_info'] ?? '', $cert_info['single_sale'] ? $cert_included_text : null ) ) ); ?>
									</span>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php
				}

				if ( ! empty( $pmpro_plans_courses ) ) {
					?>
					<div class="masterstudy-buy-button-dropdown__section">
						<div class="masterstudy-buy-button-dropdown__head">
							<span class="masterstudy-buy-button-dropdown__head-title">
								<?php echo esc_html__( 'Available with Memberships', 'masterstudy-lms-learning-management-system' ); ?>
							</span>
							<span class="masterstudy-buy-button-dropdown__head-checkbox"></span>
						</div>
						<div class="masterstudy-buy-button-dropdown__body">
							<div class="masterstudy-buy-button-dropdown__body-wrapper">
								<?php
								STM_LMS_Templates::show_lms_template(
									'components/buy-button/paid-courses/pmpro-membership',
									array(
										'post_id'          => $post_id,
										'plans_have_quota' => $pmpro_plans_have_quota,
										'plans_courses'    => $pmpro_plans_courses,
									)
								);
								?>

								<?php if ( ! empty( $price_info['membership_price_info'] ) || $cert_info['pmpro'] ) : ?>
									<span class="masterstudy-buy-button__price-info-text">
										<?php echo esc_html( STM_LMS_Helpers::masterstudy_lms_pricing_concat_info( array( $price_info['membership_price_info'] ?? '', $cert_info['pmpro'] ? $cert_included_text : null ) ) ); ?>
									</span>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php
				}

				if ( ! empty( $ms_membership_plans ) && ! $not_in_membership ) {
					?>
					<div class="masterstudy-buy-button-dropdown__section">
						<div class="masterstudy-buy-button-dropdown__head">
							<span class="masterstudy-buy-button-dropdown__head-title">
								<?php echo esc_html__( 'Available with Memberships', 'masterstudy-lms-learning-management-system' ); ?>
							</span>
							<span class="masterstudy-buy-button-dropdown__head-checkbox"></span>
						</div>
						<div class="masterstudy-buy-button-dropdown__body">
							<div class="masterstudy-buy-button-dropdown__body-wrapper">
								<?php
								STM_LMS_Templates::show_lms_template(
									'components/buy-button/paid-courses/membership',
									array(
										'plans'          => $ms_membership_plans,
										'logged_in'      => $logged_in,
										'guest_checkout' => $guest_checkout,
										'course_id'      => $post_id,
									)
								);
								?>
								<?php if ( ! empty( $price_info['membership_price_info'] ) ) : ?>
									<span class="masterstudy-buy-button__price-info-text"><?php echo esc_html( $price_info['membership_price_info'] ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php
				}

				if ( ! empty( $ms_subscription_plans['plans'] ) ) {
					?>
					<div class="masterstudy-buy-button-dropdown__section">
						<div class="masterstudy-buy-button-dropdown__head">
							<span class="masterstudy-buy-button-dropdown__head-title">
								<?php echo esc_html__( 'Course Subscription', 'masterstudy-lms-learning-management-system' ); ?>
							</span>
							<span class="masterstudy-buy-button-dropdown__head-checkbox"></span>
						</div>
						<div class="masterstudy-buy-button-dropdown__body">
							<div class="masterstudy-buy-button-dropdown__body-wrapper">
								<?php
								STM_LMS_Templates::show_lms_template(
									'components/buy-button/paid-courses/subscription',
									array(
										'plans'          => $ms_subscription_plans['plans'],
										'logged_in'      => $logged_in,
										'guest_checkout' => $guest_checkout,
										'course_id'      => $post_id,
									)
								);
								?>
								<?php if ( ! empty( $price_info['subscriptions_price_info'] ) ) : ?>
									<span class="masterstudy-buy-button__price-info-text"><?php echo esc_html( $price_info['subscriptions_price_info'] ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php
				}

				if ( $group_course_show ) {
					?>
					<div class="masterstudy-buy-button-dropdown__section">
						<div class="masterstudy-buy-button-dropdown__head">
								<span class="masterstudy-buy-button-dropdown__head-title">
									<?php echo esc_html__( 'Group course', 'masterstudy-lms-learning-management-system' ); ?>
								</span>
							<span class="masterstudy-buy-button-dropdown__head-checkbox"></span>
						</div>
						<div class="masterstudy-buy-button-dropdown__body">
							<div class="masterstudy-buy-button-dropdown__body-wrapper">
								<?php do_action( 'masterstudy_group_course_button', $post_id ); ?>
								<?php if ( ! empty( $price_info['enterprise_price_info'] ) || $cert_info['enterprise'] ) : ?>
									<span class="masterstudy-buy-button__price-info-text">
										<?php echo esc_html( STM_LMS_Helpers::masterstudy_lms_pricing_concat_info( array( $price_info['enterprise_price_info'] ?? '', $cert_info['enterprise'] ? $cert_included_text : null ) ) ); ?>
									</span>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php
				}

				if ( ! empty( $points_enabled ) && $logged_in ) {
					?>
					<div class="masterstudy-buy-button-dropdown__section">
						<div class="masterstudy-buy-button-dropdown__head">
							<span class="masterstudy-buy-button-dropdown__head-title">
								<?php echo esc_html__( 'Buy with points', 'masterstudy-lms-learning-management-system' ); ?>
							</span>
							<span class="masterstudy-buy-button-dropdown__head-checkbox"></span>
						</div>
						<div class="masterstudy-buy-button-dropdown__body">
							<div class="masterstudy-buy-button-dropdown__body-wrapper">
								<?php do_action( 'masterstudy_point_system', $post_id ); ?>
								<?php if ( ! empty( $price_info['points_price_info'] ) || $cert_info['points'] ) : ?>
									<span class="masterstudy-buy-button__price-info-text">
										<?php echo esc_html( STM_LMS_Helpers::masterstudy_lms_pricing_concat_info( array( $price_info['points_price_info'] ?? '', $cert_info['points'] ? $cert_included_text : null ) ) ); ?>
									</span>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		<?php } ?>
	</div>

	<?php if ( ! $dropdown_enabled ) : ?>
		<span class="masterstudy-buy-button__single-price-info-text">
			<?php echo esc_html( STM_LMS_Helpers::masterstudy_lms_pricing_concat_info( array( $price_info['single_sale_price_info'] ?? '', $cert_info['single_sale'] ? $cert_included_text : null ) ) ); ?>
		</span>
	<?php endif; ?>

	<?php
	if ( $group_course_show ) {
		do_action( 'masterstudy_group_course_modal', $post_id );
	}

	if ( ! empty( $pmpro_plans_courses ) && $pmpro_plans_have_quota ) {
		STM_LMS_Templates::show_lms_template(
			'components/modals/membership',
			array(
				'post_id'         => $post_id,
				'membership_list' => $pmpro_subs_info,
			)
		);
	}
} else {
	do_action( 'masterstudy_prerequisite_button', $post_id, $prerequisite_preview );
}
