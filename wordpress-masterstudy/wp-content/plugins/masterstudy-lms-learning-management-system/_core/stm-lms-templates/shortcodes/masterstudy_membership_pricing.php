<?php

use MasterStudy\Lms\Plugin\Addons;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;

wp_enqueue_style( 'masterstudy_membership_pricing' );
wp_enqueue_script( 'masterstudy_membership_pricing' );

if ( ! is_ms_lms_addon_enabled( Addons::SUBSCRIPTIONS ) ) {
	return;
}

$guest_checkout        = STM_LMS_Guest_Checkout::guest_enabled();
$user_subscription_ids = array();
$logged_in             = is_user_logged_in();

if ( class_exists( SubscriptionRepository::class ) ) {
	$user               = class_exists( 'STM_LMS_User' ) ? STM_LMS_User::get_current_user() : null;
	$user_subscriptions = ( new SubscriptionRepository() )->get_active_subscriptions_by_user( $user['id'] );

	if ( ! empty( $user_subscriptions ) && is_array( $user_subscriptions ) ) {
		foreach ( $user_subscriptions as $subscription ) {
			if ( ! empty( $subscription['plan_id'] ) ) {
				$user_subscription_ids[] = (int) $subscription['plan_id'];
			}
		}
	}

	wp_localize_script(
		'masterstudy_membership_pricing',
		'buy_button_subs_data',
		array(
			'guest_checkout' => $guest_checkout && ! $logged_in,
			'guest_nonce'    => wp_create_nonce( 'stm_lms_add_to_cart_guest' ),
			'logged_in'      => $logged_in,
		)
	);
}

$subscription_plans = ( new SubscriptionPlanRepository() )->get_enabled_plans();
$button_position    = isset( $button_position ) && ! empty( $button_position ) ? $button_position : 'before_membership_items';

?>
<div class="masterstudy_memberships__head">
	<h1 class="masterstudy_memberships__head_title"><?php esc_html_e( 'Membership plans', 'masterstudy-lms-learning-management-system' ); ?></h1>
</div>
<div class="masterstudy_memberships__wrapper">
	<?php foreach ( $subscription_plans as $plan ) : ?>
		<?php $is_subscribed = in_array( (int) $plan['id'], $user_subscription_ids, true ); ?>
		<div class="masterstudy_memberships__container">
			<div class="masterstudy_memberships">
				<?php
				if ( isset( $membership_mark_list ) && ! empty( $membership_mark_list ) ) {
					foreach ( $membership_mark_list as $item ) {
						if ( $plan['name'] === $item['membership_mark_relation'] ) {
							?>
							<div class="masterstudy_memberships__mark <?php echo esc_attr( $item['membership_mark_position'] ); ?>">
								<h3 class="masterstudy_memberships__mark_title elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>">
									<span><?php echo esc_html( $item['membership_mark_title'] ); ?></span>
								</h3>
							</div>
							<?php
						}
					}
				} elseif ( isset( $plan_label ) && ! empty( $plan_label ) ) {
					$plan_labels = vc_param_group_parse_atts( $plan_label );
					if ( ! empty( $plan_labels ) ) {
						foreach ( $plan_labels as $item ) {
							if ( $plan['name'] === $item['plan_label_relation'] ) {
								if ( isset( $item['plan_title'] ) && ! empty( $item['plan_title'] ) ) {
									?>
									<div class="masterstudy_memberships__mark">
										<h3 class="masterstudy_memberships__mark_title">
											<span><?php echo esc_html( $item['plan_title'] ); ?></span>
										</h3>
									</div>
									<?php
								}
							}
						}
					} elseif ( ! empty( $plan['featured_text'] ) && empty( $membership_mark_list ) ) {
						?>
						<div class="masterstudy_memberships__mark">
							<h3 class="masterstudy_memberships__mark_title">
								<span><?php echo esc_html( $plan['featured_text'] ); ?></span>
							</h3>
						</div>
						<?php
					}
				} elseif ( ! empty( $plan['featured_text'] ) && empty( $membership_mark_list ) ) {
					?>
					<div class="masterstudy_memberships__mark">
						<h3 class="masterstudy_memberships__mark_title">
							<span><?php echo esc_html( $plan['featured_text'] ); ?></span>
						</h3>
					</div>
				<?php } ?>
				<?php if ( ! empty( $plan['name'] ) ) : ?>
					<div class="masterstudy_memberships__name">
						<h3 class="masterstudy_memberships__name_title">
							<span><?php echo esc_html( $plan['name'] ); ?></span>
						</h3>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $plan['price'] ) ) : ?>
					<div class="masterstudy_memberships__price">
						<div class="masterstudy_memberships__price_value">
							<h3>
								<?php
								echo esc_html( STM_LMS_Helpers::display_price_with_taxes( SubscriptionPlanRepository::get_actual_price( $plan ) ) );
								?>
							</h3>
							<span class="masterstudy_memberships__price_value-interval">
								<?php
								$recurring_interval = ! empty( $plan['recurring_interval'] ) ? $plan['recurring_interval'] : 'month';
								$allowed_intervals  = array( 'day', 'week', 'month', 'year' );

								if ( ! in_array( $recurring_interval, $allowed_intervals, true ) ) {
									$recurring_interval = 'month';
								}
								printf(
									esc_html__( '/ per %s', 'masterstudy-lms-learning-management-system' ),
									esc_html( $recurring_interval )
								);
								?>
							</span>
						</div>
						<?php
						if ( ! empty( $plan['sale_price'] ) ) {
							$show_sale_price = false;
							if ( ! empty( $plan['sale_price_from'] ) && ! empty( $plan['sale_price_to'] ) ) {
								$sale_price_from = strtotime( $plan['sale_price_from'] );
								$sale_price_to   = strtotime( $plan['sale_price_to'] );
								$current_time    = time();

								if ( $current_time >= $sale_price_from && $current_time <= $sale_price_to ) {
									$show_sale_price = true;
									$price           = (float) $plan['price'];
								}
							} else {
								$show_sale_price = true;
								$price           = (float) $plan['price'];
							}
							?>
							<?php if ( ! empty( $show_sale_price ) ) : ?>
								<span class="masterstudy_memberships__price-sale">
									<?php
									echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $price ) );
									?>
								</span>
							<?php endif; ?>
						<?php } ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $plan['description'] ) ) : ?>
					<div class="masterstudy_memberships__price_description">
						<?php echo wp_kses_post( ent2ncr( $plan['description'] ) ); ?>
					</div>
				<?php endif; ?>
				<div class="masterstudy_memberships__order">
					<div class="masterstudy_memberships__button <?php echo esc_attr( $button_position ); ?>">
						<?php if ( $is_subscribed ) : ?>
							<button
								class="masterstudy_memberships__button_element masterstudy-button-disabled"
								disabled
								data-plan-id="<?php echo esc_attr( $plan['id'] ); ?>"
							>
								<?php esc_html_e( 'Your Current Plan', 'masterstudy-lms-learning-management-system' ); ?>
							</button>
						<?php else : ?>
							<button
								<?php echo ! $logged_in && ! $guest_checkout ? 'data-authorization-modal="login"' : ''; ?>
								data-plan-id="<?php echo esc_attr( $plan['id'] ); ?>"
								class="masterstudy_memberships__button_element masterstudy-add-to-cart-subscription"
							>
								<span class="masterstudy-purchase-button__title">
									<?php esc_html_e( 'Get Started', 'masterstudy-lms-learning-management-system' ); ?>
								</span>
							</button>
						<?php endif; ?>
					</div>
					<?php if ( ! empty( $plan['plan_features'] ) ) { ?>
						<?php
						$features = $plan['plan_features'];
						if ( is_string( $features ) ) {
							$decoded  = json_decode( $features, true );
							$features = $decoded ? $decoded : array();
						}
						?>
						<div class="masterstudy_memberships__items">
							<?php foreach ( $features as $feature ) : ?>
								<?php if ( ! empty( $feature['title'] ) ) : ?>
									<div class="masterstudy_memberships__item">
										<span class="masterstudy_memberships__items_icon">
											<?php
											if ( isset( $membership_items_icons ) && ! empty( $membership_items_icons ) ) {
												if ( 'svg' === $membership_items_icons['library'] ) {
													echo wp_kses_post( $membership_items_icons['value'] );
												} else {
													?>
													<i class="<?php echo esc_attr( $membership_items_icons['value'] ); ?>"></i>
													<?php
												}
											} else {
												?>
												<i class="stmlms-course-check"></i>
												<?php
											}
											?>
										</span>
										<?php echo esc_html( $feature['title'] ); ?>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
