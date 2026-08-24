<?php

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$order_id        = get_query_var( 'masterstudy-orders-received' );
$payment_methods = STM_LMS_Options::get_option( 'payment_methods' );

stm_lms_register_style( 'user-orders' );

STM_LMS_Templates::show_lms_template( 'header' );

$order_info    = \STM_LMS_Order::get_order_info( $order_id );
$taxes_display = STM_LMS_Helpers::taxes_display();
$taxes_enabled = ! empty( $taxes_display['enabled'] ) && ! empty( $order_info['taxes'] );
$has_coupon    = STM_LMS_Helpers::is_coupons_enabled() && ! empty( $order_info['coupon_value'] );
$subscription  = array();

if ( ! empty( $order_info ) ) :
	$personal_data        = isset( $order_info['personal_data'] ) && is_array( $order_info['personal_data'] ) ? $order_info['personal_data'] : array();
	$personal_data_fields = masterstudy_lms_personal_data_fields();
	$countries            = masterstudy_lms_get_countries( false );
	$states               = masterstudy_lms_get_us_states( false );

	if ( ! empty( $order_info['is_subscription'] ) ) {
		$subscription        = class_exists( SubscriptionRepository::class ) ? ( new SubscriptionRepository() )->get( $order_info['subscription_id'] ) : '';
		$subscription_status = function_exists( 'masterstudy_lms_get_subscription_status_labels' ) ? masterstudy_lms_get_subscription_status_labels() : array();
		$renew_price         = ! empty( $order_info['items'][0]['price'] ) ? $order_info['items'][0]['price'] : 0;

		if ( $has_coupon && ! empty( $subscription['trial_end_date'] ) ) {
			$renew_price = max( 0, $renew_price - STM_LMS_Helpers::calculate_coupon_discount( $renew_price, $order_info['original_coupon_value'], $order_info['coupon_type'] ) );
		}

		$subscription_fields = array(
			'subscription_id' => array(
				'label' => ! empty( $order_info['plan']['type'] ) && 'course' === $order_info['plan']['type']
					? esc_html__( 'Subscription ID', 'masterstudy-lms-learning-management-system' )
					: esc_html__( 'Membership ID', 'masterstudy-lms-learning-management-system' ),
				'value' => ! empty( $order_info['subscription_id'] ) ? '#' . $order_info['subscription_id'] : '',
			),
			'plan_type'       => array(
				'label' => esc_html__( 'Membership Access', 'masterstudy-lms-learning-management-system' ),
				'value' => ! empty( $order_info['plan']['type'] ) && 'course' === $order_info['plan']['type']
					? esc_html__( 'Subscription', 'masterstudy-lms-learning-management-system' )
					: esc_html__( 'Membership', 'masterstudy-lms-learning-management-system' ),
			),
			'timezone'        => array(
				'label' => esc_html__( 'Timezone', 'masterstudy-lms-learning-management-system' ),
				'value' => '+00:00',
			),
			'renew'           => array(
				'label' => esc_html__( 'Renew', 'masterstudy-lms-learning-management-system' ),
				'value' => ( ! empty( $order_info['items'][0]['price'] ) && ! empty( $order_info['plan']['recurring_interval'] ) )
					? STM_LMS_Helpers::display_price_with_taxes( $renew_price, $order_info['user']['id'] ) . '/' . $order_info['plan']['recurring_interval']
					: '',
			),
			'trial_end_date'  => array(
				'label' => esc_html__( 'Trial End Date', 'masterstudy-lms-learning-management-system' ),
				'value' => ! empty( $subscription['trial_end_date'] )
					? date_i18n( get_option( 'date_format' ), strtotime( $subscription['trial_end_date'] ) )
					: '',
			),
			'status'          => array(
				'label' => esc_html__( 'Status', 'masterstudy-lms-learning-management-system' ),
				'value' => ! empty( $subscription['status'] ) ? $subscription_status[ $subscription['status'] ] : '',
			),
		);
		$course_id           = ! empty( $order_info['plan']['items'][0]['object_id'] )
			? $order_info['plan']['items'][0]['object_id']
			: '';
		$categories          = wp_get_post_terms( $course_id, 'stm_lms_course_taxonomy' );
		$billing_cycles      = 0;
		$cycles_limit        = 0;

		if (
			function_exists( 'masterstudy_lms_subscription_plan_billing_cycles_limit' )
			&& ! empty( $order_info['plan'] )
		) {
			$cycles_limit = masterstudy_lms_subscription_plan_billing_cycles_limit( $order_info['plan'] );
		}

		$subscription_fields['plan_billing_cycles'] = array(
			'label' => esc_html__( 'Billing Cycles', 'masterstudy-lms-learning-management-system' ),
			'value' => intval( $cycles_limit ) > 0
				? esc_html( $cycles_limit ) . esc_html__( ' time(s)', 'masterstudy-lms-learning-management-system' )
				: esc_html__( 'Until Cancelled', 'masterstudy-lms-learning-management-system' ),
		);

		if ( intval( $cycles_limit ) !== 1 ) {
			$subscription_fields['next_payment_date'] = array(
				'label' => esc_html__( 'Next Payment Date', 'masterstudy-lms-learning-management-system' ),
				'value' => ! empty( $subscription['next_payment_date'] )
					? date_i18n( get_option( 'date_format' ), strtotime( $subscription['next_payment_date'] ) )
					: '',
			);
		}
	}
	?>

<div class="stm-lms-wrapper">
	<div class="container">
		<div class="masterstudy-orders masterstudy-thank-you-page">
			<div class="masterstudy-orders-box">
				<div class="masterstudy-orders-box__title"><?php echo esc_html__( 'Thank you for your order!', 'masterstudy-lms-learning-management-system' ); ?></div>
				<div class="masterstudy-orders-box__info">
					<div class="masterstudy-orders-box__info-label"><?php echo esc_html__( 'Order ID:', 'masterstudy-lms-learning-management-system' ); ?></div>
					<div class="masterstudy-orders-box__info-value">
						<div class="masterstudy-orders-box__info-label"><?php echo esc_attr( $order_id ); ?></div>
					</div>
				</div>
				<div class="masterstudy-orders-box__info">
					<div class="masterstudy-orders-box__info-label"><?php echo esc_html__( 'Date:', 'masterstudy-lms-learning-management-system' ); ?></div>
					<div class="masterstudy-orders-box__info-value"><?php echo esc_attr( $order_info['date_formatted'] ); ?></div>
				</div>
			</div>
			<?php
			if ( ! empty( $payment_methods['wire_transfer'] ) && $payment_methods['wire_transfer']['enabled'] && 'wire_transfer' === $order_info['payment_code'] ) :
				$wire_transfer = $payment_methods['wire_transfer']['fields'];
				?>
			<div class="masterstudy-payment-methods">
				<div class="masterstudy-payment-methods__title">
					<?php echo esc_html__( 'Bank Details', 'masterstudy-lms-learning-management-system' ); ?>
				</div>

				<div class="masterstudy-payment-methods__table">
					<div class="masterstudy-payment-methods__table-column">
						<div class="masterstudy-payment-methods__name"><?php echo esc_html__( 'Bank', 'masterstudy-lms-learning-management-system' ); ?></div>
						<div class="masterstudy-payment-methods__value"><?php echo esc_html( $wire_transfer['bank_name'] ); ?></div>
					</div>
					<div class="masterstudy-payment-methods__table-column">
						<div class="masterstudy-payment-methods__name"><?php echo esc_html__( 'Recipient', 'masterstudy-lms-learning-management-system' ); ?></div>
						<div class="masterstudy-payment-methods__value"><?php echo esc_html( $wire_transfer['holder_name'] ); ?></div>
					</div>
					<div class="masterstudy-payment-methods__table-column">
						<div class="masterstudy-payment-methods__name"><?php echo esc_html__( 'Account Number', 'masterstudy-lms-learning-management-system' ); ?></div>
						<div class="masterstudy-payment-methods__value"><?php echo esc_html( $wire_transfer['account_number'] ); ?></div>
					</div>
					<div class="masterstudy-payment-methods__table-column">
						<div class="masterstudy-payment-methods__name"><?php echo esc_html__( 'Amount to be paid', 'masterstudy-lms-learning-management-system' ); ?></div>
						<div class="masterstudy-payment-methods__value"><?php echo esc_attr( $order_info['total'] ); ?></div>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<div class="masterstudy-orders-container">
				<div class="masterstudy-orders-table">
					<?php
					if ( ! empty( $order_info['is_subscription'] ) ) :
						$is_trial          = ! empty( $order_info['plan']['trial_period'] );
						$trial_period_days = intval( $order_info['plan']['trial_period'] ?? 0 );
						$is_course         = ! empty( $course_id ) && ! empty( $order_info['plan']['type'] ) && 'course' === $order_info['plan']['type'] ? $course_id : false;
						?>
						<div class="masterstudy-orders-table__body">
							<?php foreach ( $order_info['cart_items'] as $key => $item ) : ?>
							<div class="masterstudy-orders-table__body-row <?php echo ! empty( $order_info['is_subscription'] ) ? 'memberships' : ''; ?>">
								<div class="masterstudy-orders-course-info <?php echo ! empty( $order_info['plan']['billing_cycles'] ) ? 'billing-cycles' : ''; ?>">
									<div class="masterstudy-orders-course-info__image">
										<?php if ( $is_course ) : ?>
											<a href="<?php echo esc_url( get_the_permalink( $course_id ) ); ?>" target="_blank">
												<img src="<?php echo esc_url( get_the_post_thumbnail_url( $course_id, 'thumbnail' ) ); ?>">
											</a>
										<?php endif; ?>
									</div>
									<div class="masterstudy-orders-course-info__common">
										<div class="masterstudy-orders-course-info__title">
											<?php if ( $is_course ) : ?>
												<a href="<?php echo esc_url( get_the_permalink( $course_id ) ); ?>"><?php echo esc_html( get_the_title( $course_id ) ); ?></a>
											<?php else : ?>
												<em><?php echo ! empty( $order_info['plan']['name'] ) ? esc_html( $order_info['plan']['name'] ) : esc_html__( 'N/A', 'masterstudy-lms-learning-management-system' ); ?></em>
											<?php endif; ?>

											<?php if ( ! empty( $item['bundle'] ) ) : ?>
												<span class="order-status"><?php echo esc_html__( 'bundle', 'masterstudy-lms-learning-management-system' ); ?></span>
											<?php endif; ?>
											<?php if ( ! empty( $item['enterprise'] ) ) : ?>
												<span class="order-status"><?php echo esc_html__( 'enterprise', 'masterstudy-lms-learning-management-system' ); ?></span>
											<?php endif; ?>
										</div>
										<div class="masterstudy-orders-course-info__category">
											<?php
											echo esc_html( implode( ', ', wp_list_pluck( $categories, 'name' ) ) );

											if ( isset( $item['bundle_courses_count'] ) && $item['bundle_courses_count'] > 0 ) {
												echo esc_html( $item['bundle_courses_count'] . ' ' . esc_html__( 'courses in bundle', 'masterstudy-lms-learning-management-system' ) );
											}
											?>
										</div>
										<?php if ( ! empty( $order_info['plan']['billing_cycles'] ) ) : ?>
											<div class="masterstudy-orders-course-info__timeline">
												<div class="masterstudy-orders-course-info__timeline-plan-title">
													<?php esc_html_e( 'Payment Plan:', 'masterstudy-lms-learning-management-system' ); ?>
													<?php
													$recurring_interval = ! empty( $order_info['plan']['recurring_interval'] )
														? STM_LMS_Helpers::masterstudy_lms_get_subscription_interval_label( $order_info['plan']['recurring_interval'] )
														: '';
													?>
													<span>
														<?php
														$billing_cycles_limit = function_exists( 'masterstudy_lms_subscription_plan_billing_cycles_limit' )
															? masterstudy_lms_subscription_plan_billing_cycles_limit( $order_info['plan'] )
															: '';
														printf(
														// translators: %1$s - billing cycles, %2$s - recurring interval
															esc_html__( '%1$s %2$s', 'masterstudy-lms-learning-management-system' ),
															esc_html( $billing_cycles_limit ),
															esc_html( $recurring_interval )
														);
														?>
													</span>
												</div>
												<?php
												$billing_cycles_limit = function_exists( 'masterstudy_lms_subscription_plan_billing_cycles_limit' )
													? masterstudy_lms_subscription_plan_billing_cycles_limit( $order_info['plan'] )
													: $order_info['plan']['billing_cycles'];

												for ( $i = 1; $i <= $billing_cycles_limit; $i++ ) :
													$order_total = $order_info['_order_subtotal'];

													// Set item price for next payments, if trial period is active
													if ( $is_trial && 1 !== $i ) {
														$order_total = $order_info['items'][0]['price'];
													}

													if ( $has_coupon ) {
														$first_payment_idx = 1;
														if ( $is_trial ) {
															++$first_payment_idx;
														}

														if ( $i === $first_payment_idx ) {
															$order_total = max( 0, $order_total - STM_LMS_Helpers::calculate_coupon_discount( $order_total, $order_info['original_coupon_value'], $order_info['coupon_type'] ) );
														}
													}
													?>
													<div class="masterstudy-orders-course-info__timeline-step <?php echo 1 === $i ? 'checked' : ''; ?><?php echo 2 === $i ? 'active' : ''; ?>">
														<div class="masterstudy-orders-course-info__timeline-circle"></div>
														<div class="masterstudy-orders-course-info__timeline-content">
															<span class="masterstudy-orders-course-info__timeline-title">
																<?php
																if ( $is_trial && 1 === $i ) {
																	printf(
																		// translators: %s - trial period days
																		esc_html__( 'Trial %s day(s)', 'masterstudy-lms-learning-management-system' ),
																		esc_html( $trial_period_days )
																	);
																} else {
																	printf(
																	// translators: %s - payment number
																		esc_html__( '%s payment', 'masterstudy-lms-learning-management-system' ),
																		esc_html( $i )
																	);
																}
																?>
															</span>
															<span class="masterstudy-orders-course-info__timeline-date">
																<?php
																$start_date        = ! empty( $subscription['start_date'] ) ? $subscription['start_date'] : current_time( 'Y-m-d' );
																$interval          = ! empty( $order_info['plan']['recurring_interval'] ) ? $order_info['plan']['recurring_interval'] : 'month';
																$allowed_intervals = array( 'day', 'week', 'month', 'year' );

																if ( ! in_array( $interval, $allowed_intervals, true ) ) {
																	$interval = 'month';
																}

																if ( $is_trial ) {
																	if ( 1 === $i ) {
																		$timestamp = strtotime( $start_date );
																	} else {
																		$timestamp = strtotime( "$start_date +" . ( $i - 2 ) . " $interval +$trial_period_days days" );
																	}
																} else {
																	$timestamp = strtotime( "$start_date +" . ( $i - 1 ) . " $interval" );
																}

																echo esc_html( gmdate( 'd F Y', $timestamp ) );
																?>

															</span>
															<span class="masterstudy-orders-course-info__timeline-amount">
																<?php
																$billing_cycles += $order_total;
																echo esc_html( STM_LMS_Helpers::display_price( $order_total ) );
																?>
															</span>
														</div>
													</div>
												<?php endfor; ?>
											</div>
											<div class="masterstudy-orders-course-info__timeline-total">
												<span><?php esc_html_e( 'Total:', 'masterstudy-lms-learning-management-system' ); ?></span>
												<strong><?php echo esc_html( STM_LMS_Helpers::display_price( $billing_cycles ) ); ?></strong>
											</div>
										<?php endif; ?>
									</div>
									<div class="masterstudy-orders-course-info__action">
										<div class="masterstudy-orders-course-info__price"><?php echo esc_html( $item['price_formatted'] ); ?></div>
										<?php
										if ( ! empty( $item['link'] ) ) {
											STM_LMS_Templates::show_lms_template(
												'components/button',
												array(
													'title' => $is_course
														? esc_html__( 'View course', 'masterstudy-lms-learning-management-system' )
														: esc_html__( 'Go to account', 'masterstudy-lms-learning-management-system' ),
													'link' => $is_course ? esc_url( get_the_permalink( $course_id ) ) : home_url( 'user-account' ),
													'style' => 'secondary masterstudy-orders-course-info__button',
													'size' => 'sm',
												)
											);
										}
										?>
									</div>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<div class="masterstudy-orders-table__body">
							<?php foreach ( $order_info['cart_items'] as $key => $item ) : ?>
								<div class="masterstudy-orders-table__body-row">
									<div class="masterstudy-orders-course-info">
										<div class="masterstudy-orders-course-info__image">
											<?php if ( ! empty( $item['image'] ) ) : ?>
												<a href="<?php echo esc_url( $item['link'] ); ?>"><?php echo wp_kses_post( $item['image'] ); ?></a>
											<?php else : ?>
												<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/image_not_found.png' ); ?>" alt="<?php echo esc_html( $item['title'] ); ?>">
											<?php endif; ?>
										</div>
										<div class="masterstudy-orders-course-info__common">
											<div class="masterstudy-orders-course-info__title">
												<?php if ( ! empty( $item['image'] ) ) : ?>
													<a href="<?php echo esc_url( $item['link'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
												<?php else : ?>
													<em><?php echo ! empty( $item['title'] ) ? esc_html( $item['title'] ) : esc_html__( 'N/A', 'masterstudy-lms-learning-management-system' ); ?></em>
												<?php endif; ?>

												<?php if ( ! empty( $item['bundle'] ) ) : ?>
													<span class="order-status"><?php echo esc_html__( 'bundle', 'masterstudy-lms-learning-management-system' ); ?></span>
												<?php endif; ?>
												<?php if ( ! empty( $item['enterprise'] ) ) : ?>
													<span class="order-status"><?php echo esc_html__( 'enterprise', 'masterstudy-lms-learning-management-system' ); ?></span>
												<?php endif; ?>
											</div>
											<div class="masterstudy-orders-course-info__category">
												<?php
												if ( ! empty( $item['enterprise'] ) ) {
													printf( esc_html__( 'for group', 'masterstudy-lms-learning-management-system' ) . ' %s', esc_html( get_the_title( $item['enterprise'] ) ) );
												} else {
													echo esc_html( implode( ', ', $item['terms'] ) );
												}

												if ( isset( $item['bundle_courses_count'] ) && $item['bundle_courses_count'] > 0 ) {
													echo esc_html( $item['bundle_courses_count'] . ' ' . esc_html__( 'courses in bundle', 'masterstudy-lms-learning-management-system' ) );
												}
												?>
											</div>
										</div>
										<div class="masterstudy-orders-course-info__price"><?php echo esc_html( $item['price_formatted'] ); ?></div>
										<?php
										if ( ! empty( $item['link'] ) ) {
											STM_LMS_Templates::show_lms_template(
												'components/button',
												array(
													'title' => esc_html__( 'Go to course', 'masterstudy-lms-learning-management-system' ),
													'link' => esc_url( $item['link'] ),
													'style' => 'secondary masterstudy-orders-course-info__button',
													'size' => 'sm',
												)
											);
										}
										?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<div class="masterstudy-orders-table__footer">
						<div class="masterstudy-orders-course-info">
							<?php if ( $taxes_enabled || $has_coupon ) { ?>
								<div id="subtotal" class="masterstudy-orders-course-info__block">
									<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Subtotal', 'masterstudy-lms-learning-management-system' ); ?>:</div>
									<div class="masterstudy-orders-course-info__price"><?php echo esc_html( STM_LMS_Helpers::display_price( $order_info['subtotal'] ) ); ?></div>
								</div>
							<?php } ?>

							<?php if ( $has_coupon ) : ?>
								<div id="coupon" class="masterstudy-orders-course-info__block">
									<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Coupon', 'masterstudy-lms-learning-management-system' ); ?>:</div>
									<div class="masterstudy-orders-course-info__price"><?php echo esc_html( $order_info['coupon_value'] ); ?></div>
								</div>
							<?php endif; ?>

							<?php if ( $taxes_enabled ) { ?>
								<div id="taxes" class="masterstudy-orders-course-info__block">
									<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Tax', 'masterstudy-lms-learning-management-system' ); ?>:</div>
									<div class="masterstudy-orders-course-info__price"><?php echo esc_html( STM_LMS_Helpers::display_price( $order_info['taxes'] ) ); ?></div>
								</div>
							<?php } ?>
							<div id="total" class="masterstudy-orders-course-info__block">
								<div class="masterstudy-orders-course-info__label">
									<?php echo esc_html__( 'Total', 'masterstudy-lms-learning-management-system' ); ?>:
								</div>
								<div class="masterstudy-orders-course-info__price">
									<?php echo esc_html( STM_LMS_Helpers::display_price( $order_info['total'] ) ); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="masterstudy-orders-row">
				<?php if ( ! empty( $order_info['is_subscription'] ) ) : ?>
					<div class="masterstudy-orders-column">
						<div class="masterstudy-orders-table">
							<div class="masterstudy-orders-table__header">
								<div class="masterstudy-orders-course-info"><?php echo esc_html__( 'Subscription Details', 'masterstudy-lms-learning-management-system' ); ?></div>
							</div>
							<div class="masterstudy-orders-table__body">
								<?php foreach ( $subscription_fields as $field ) : ?>
									<?php if ( ! empty( $field['value'] ) ) : ?>
										<div class="masterstudy-orders-table__body-row">
											<div class="masterstudy-orders-course-info">
												<div class="masterstudy-orders-course-info__label"><?php echo esc_html( $field['label'] ) ?? ''; ?>:</div>
												<div class="masterstudy-orders-course-info__value"><?php echo esc_html( $field['value'] ) ?? ''; ?></div>
											</div>
										</div>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<div class="masterstudy-orders-column">
					<div class="masterstudy-orders-table">
						<div class="masterstudy-orders-table__header">
							<div class="masterstudy-orders-course-info"><?php echo esc_html__( 'Student info', 'masterstudy-lms-learning-management-system' ); ?></div>
						</div>
						<div class="masterstudy-orders-table__body">
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Full name:', 'masterstudy-lms-learning-management-system' ); ?></div>
									<div class="masterstudy-orders-course-info__value"><?php echo esc_html( $order_info['user']['login'] ); ?></div>
								</div>
							</div>
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Email:', 'masterstudy-lms-learning-management-system' ); ?></div>
									<div class="masterstudy-orders-course-info__value"><?php echo esc_html( $order_info['user']['email'] ); ?></div>
								</div>
							</div>
							<?php

							if ( ! empty( $personal_data ) && is_array( $personal_data ) ) {
								foreach ( $personal_data as $field => $value ) {
									$label = isset( $personal_data_fields[ $field ] )
										? $personal_data_fields[ $field ]
										: ucfirst( str_replace( '_', ' ', $field ) );
									?>
									<div class="masterstudy-orders-table__body-row">
										<div class="masterstudy-orders-course-info">
											<div class="masterstudy-orders-course-info__label"><?php echo esc_html( $label ); ?>:</div>
											<div class="masterstudy-orders-course-info__value">
												<?php
												if ( 'country' === $field ) {
													$matched       = array_filter(
														$countries,
														function ( $country ) use ( $value ) {
															return strtoupper( $country['code'] ) === strtoupper( $value );
														}
													);
													$country_label = ! empty( $matched ) ? reset( $matched )['name'] : $value;

													echo esc_html( $country_label );
												} elseif ( 'state' === $field ) {
													$matched     = array_filter(
														$states,
														function ( $state ) use ( $value ) {
															return strtoupper( $state['code'] ) === strtoupper( $value );
														}
													);
													$state_label = ! empty( $matched ) ? reset( $matched )['name'] : $value;

													echo esc_html( $state_label );
												} else {
													echo esc_html( $value );
												}
												?>
											</div>
										</div>
									</div>
								<?php } ?>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php if ( empty( $order_info['is_subscription'] ) ) : ?>
					<div class="masterstudy-orders-column">
						<div class="masterstudy-orders-table">
							<div class="masterstudy-orders-table__header">
								<div class="masterstudy-orders-course-info"><?php echo esc_html__( 'Payment info', 'masterstudy-lms-learning-management-system' ); ?></div>
							</div>
							<div class="masterstudy-orders-table__body">
								<div class="masterstudy-orders-table__body-row">
									<div class="masterstudy-orders-course-info">
										<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Payment method:', 'masterstudy-lms-learning-management-system' ); ?></div>
										<div class="masterstudy-orders-course-info__value masterstudy-payment-method"><?php echo wp_kses_post( STM_LMS_Order::get_payment_method_name( $order_info['payment_code'] ) ); ?></div>
									</div>
								</div>
								<?php if ( $taxes_enabled || $has_coupon ) : ?>
									<div id="subtotal_payment" class="masterstudy-orders-table__body-row">
										<div class="masterstudy-orders-course-info">
											<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Subtotal', 'masterstudy-lms-learning-management-system' ); ?>:</div>
											<div class="masterstudy-orders-course-info__value"><?php echo esc_html( STM_LMS_Helpers::display_price( $order_info['subtotal'] ) ); ?></div>
										</div>
									</div>
								<?php endif ?>
								<?php if ( $has_coupon ) : ?>
									<div id="coupon_payment" class="masterstudy-orders-table__body-row">
										<div class="masterstudy-orders-course-info">
											<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Coupon', 'masterstudy-lms-learning-management-system' ); ?>:</div>
											<div class="masterstudy-orders-course-info__value"><?php echo esc_html( $order_info['coupon_value'] ); ?></div>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( $taxes_enabled ) : ?>
									<div id="taxes_payment" class="masterstudy-orders-table__body-row">
										<div class="masterstudy-orders-course-info">
											<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Tax', 'masterstudy-lms-learning-management-system' ); ?>:</div>
											<div class="masterstudy-orders-course-info__value"><?php echo esc_html( STM_LMS_Helpers::display_price( $order_info['taxes'] ) ); ?></div>
										</div>
									</div>
								<?php endif ?>
								<div id="total_payment" class="masterstudy-orders-table__body-row">
									<div class="masterstudy-orders-course-info">
										<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Total', 'masterstudy-lms-learning-management-system' ); ?>:</div>
										<div class="masterstudy-orders-course-info__value">
											<?php echo esc_html( STM_LMS_Helpers::display_price( $order_info['total'] ) ); ?>
										</div>
									</div>
								</div>
								<div class="masterstudy-orders-table__body-row">
									<div class="masterstudy-orders-course-info">
										<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Status:', 'masterstudy-lms-learning-management-system' ); ?></div>
										<div class="masterstudy-orders-course-info__value"><span class="order-status <?php echo esc_attr( $order_info['status'] ); ?>"><?php echo esc_attr( STM_LMS_Order::get_status_name( $order_info['status'] ) ); ?></span></div>
									</div>
								</div>
								<div class="masterstudy-orders-table__body-row">
									<div class="masterstudy-orders-course-info">
										<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Order ID:', 'masterstudy-lms-learning-management-system' ); ?></div>
										<div class="masterstudy-orders-course-info__value"><?php echo esc_html( $order_id ); ?></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<div class="masterstudy-orders-button">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => esc_html__( 'View all orders', 'masterstudy-lms-learning-management-system' ),
						'link'  => esc_url( get_permalink( STM_LMS_Options::get_option( 'user_url' ) ) . 'my-orders/' ),
						'style' => 'secondary',
						'size'  => 'sm',
					)
				);
				?>
			</div>
		</div>
	</div>
</div>

	<?php
endif;
STM_LMS_Templates::show_lms_template( 'footer' );
