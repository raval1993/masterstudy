<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use MasterStudy\Lms\Plugin\Addons;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;

/**
 * @var $user_id
 */

$items          = stm_lms_get_cart_items( $user_id, apply_filters( 'stm_lms_cart_items_fields', array( 'item_id', 'price' ) ) );
$taxes_display  = STM_LMS_Helpers::taxes_display();
$personal_data  = get_user_meta( get_current_user_id(), 'masterstudy_personal_data', true );
$personal_data  = is_array( $personal_data ) ? $personal_data : array();
$subtotal       = 0.0;
$is_trial       = false;
$settings       = get_option( 'stm_lms_settings' );
$coupon_enabled = STM_LMS_Helpers::is_coupons_enabled();

if ( empty( $items ) ) :
	STM_LMS_Templates::show_lms_template( 'checkout/empty-cart' );
else :
	?>
	<div class="masterstudy-checkout-container">
		<div class="masterstudy-checkout-container__left-column">
			<div class="masterstudy-checkout-table">
				<div class="masterstudy-checkout-table__header">
					<div class="masterstudy-checkout-course-info">
						<div class="masterstudy-checkout-course-info__value">
							<?php echo esc_html( STM_LMS_Helpers::masterstudy_lms_get_checkout_header_text( $items ) ); ?>
						</div>
					</div>
				</div>
				<div class="masterstudy-checkout-table__body">
				<?php
				foreach ( $items as $item ) :
					if ( isset( $item['is_subscription'] ) && $item['is_subscription'] && is_ms_lms_addon_enabled( Addons::SUBSCRIPTIONS ) ) {

						$plan      = ( new SubscriptionPlanRepository() )->get( $item['item_id'] );
						$subtotal += SubscriptionPlanRepository::get_actual_price( $plan );
						$is_trial  = ! empty( $plan['trial_period'] );

						STM_LMS_Templates::show_lms_template(
							'checkout/subscription-item',
							array(
								'item' => $item,
							)
						);

						continue;
					}

					if ( ! get_post_type( $item['item_id'] ) ) {
						continue;
					}

					$terms        = wp_get_post_terms( $item['item_id'], 'stm_lms_course_taxonomy', array( 'fields' => 'ids' ) );
					$category_ids = ! is_wp_error( $terms ) && ! empty( $terms ) ? array_map( 'intval', $terms ) : array();
					$price        = (float) ( $item['price'] ?? 0 );
					$subtotal    += $price;
					?>
					<div class="masterstudy-checkout-table__body-row">
						<div class="masterstudy-checkout-course-info">
							<div class="masterstudy-checkout-course-info__image">
								<a href="<?php echo esc_url( get_the_permalink( $item['item_id'] ) ); ?>">
									<?php
									if ( function_exists( 'stm_get_VC_attachment_img_safe' ) ) {
										echo wp_kses_post( stm_get_VC_attachment_img_safe( get_post_thumbnail_id( $item['item_id'] ), 'full' ) );
									} else {
										?>
										<img src="<?php echo esc_url( get_the_post_thumbnail_url( $item['item_id'], 'full' ) ); ?>" alt="">
										<?php
									}
									?>
								</a>
							</div>
							<div class="masterstudy-checkout-course-info__common">
								<div class="masterstudy-checkout-course-info__title">
									<a href="<?php echo esc_url( get_the_permalink( $item['item_id'] ) ); ?>">
										<?php echo esc_html( apply_filters( 'stm_lms_single_item_cart_title', sanitize_text_field( get_the_title( $item['item_id'] ) ), $item ) ); ?>
									</a>
									<?php
									$additional_info  = '';
									$enterprise_title = '';

									if ( ! empty( $item['enterprise'] ) && intval( $item['enterprise'] ) !== 0 ) {
										$additional_info  = '<span class="masterstudy-checkout-course-info__status">enterprise</span>';
										$enterprise_title = get_the_title( $item['enterprise'] );
									} elseif ( ! empty( $item['bundle'] ) && intval( $item['bundle'] ) !== 0 ) {
										$additional_info = '<span class="masterstudy-checkout-course-info__status">' . esc_html__( 'bundle', 'masterstudy-lms-learning-management-system' ) . '</span>';
									}

									echo wp_kses_post( $additional_info );
									?>
									</div>
									<div class="masterstudy-checkout-course-info__category">
										<?php
										if ( ! empty( $enterprise_title ) ) {
											echo esc_html__( 'for group', 'masterstudy-lms-learning-management-system' ) . ' ' . esc_html( $enterprise_title );
										} else {
											STM_LMS_Templates::show_lms_template(
												'components/course/categories',
												array(
													'term_ids' => $category_ids,
													'only_one' => false,
													'inline'   => true,
												)
											);
										}

										if ( isset( $item['bundle'] ) ) {
											$bundle_count = STM_LMS_Order::get_bundle_courses_count( $item['bundle'] );

											if ( isset( $bundle_count ) && $bundle_count > 0 ) {
												echo ' ' . esc_html( $bundle_count . ' ' . esc_html__( 'courses in bundle', 'masterstudy-lms-learning-management-system' ) );
											}
										}
										?>
									</div>
								</div>
								<div data-id="checkout-price" data-current-price="<?php echo esc_attr( $price ); ?>" class="masterstudy-checkout-course-info__price">
									<span><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $price ) ); ?></span>
									<div class="stm_lms_cart__item_delete">
										<i class="stmlms-trash1"
											<?php
											if ( ! empty( $item['enterprise'] ) ) {
												echo ' data-delete-enterprise="' . esc_attr( $item['enterprise'] ) . '"';
											}
											?>
											data-delete-course="<?php echo intval( $item['item_id'] ); ?>"></i>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="masterstudy-checkout-table__footer">
					<?php if ( $coupon_enabled ) { ?>
						<div class="masterstudy-checkout-coupon">
							<span
								id="masterstudy-checkout-coupon-toggle"
								class="masterstudy-checkout-coupon__toggle"
							>
								<?php echo esc_html__( 'Have a coupon code?', 'masterstudy-lms-learning-management-system' ); ?>
							</span>

							<div
								id="masterstudy-checkout-coupon-form"
								class="masterstudy-checkout-coupon__form"
								style="display:none;"
							>
								<input
									type="text"
									id="masterstudy-checkout-coupon-input"
									class="masterstudy-checkout-coupon__input"
									placeholder="<?php echo esc_attr__( 'Enter coupon code', 'masterstudy-lms-learning-management-system' ); ?>"
								/>
								<span
									id="masterstudy-checkout-coupon-apply"
									class="masterstudy-checkout-coupon__button masterstudy-checkout-coupon__button--apply"
								>
									<?php echo esc_html__( 'Apply', 'masterstudy-lms-learning-management-system' ); ?>
								</span>
								<span
									id="masterstudy-checkout-coupon-remove"
									class="masterstudy-checkout-coupon__button masterstudy-checkout-coupon__button--remove"
									style="display:none;"
								>
									<?php echo esc_html__( 'Remove', 'masterstudy-lms-learning-management-system' ); ?>
								</span>
							</div>

							<div
								class="masterstudy-checkout-coupon__message"
								style="display:none;"
							>
								<span id="masterstudy-checkout-coupon-message"></span>
							</div>
						</div>
					<?php } ?>
					<div class="masterstudy-checkout-course-info">
						<?php if ( $taxes_display['enabled'] || $coupon_enabled ) { ?>
							<div class="masterstudy-checkout-course-info__block">
								<div class="masterstudy-checkout-course-info__label">
									<?php echo esc_html__( 'Subtotal:', 'masterstudy-lms-learning-management-system' ); ?>
								</div>
								<div id="subtotal"
									class="masterstudy-checkout-course-info__price"
									data-subtotal="<?php echo esc_attr( $subtotal ); ?>"
									data-trial="<?php echo esc_attr( $is_trial ); ?>"
								>
									<?php
									if ( ! empty( $is_trial ) ) {
										echo esc_html( STM_LMS_Helpers::display_price( 0 ) );
									} else {
										echo esc_html( STM_LMS_Helpers::display_price( (float) $subtotal ) );
									}
									?>
								</div>
							</div>
							<?php
						}
						if ( $coupon_enabled ) {
							?>
							<div class="masterstudy-checkout-course-info__block" style="display:none;">
								<div class="masterstudy-checkout-course-info__label">
									<?php echo esc_html__( 'Coupon:', 'masterstudy-lms-learning-management-system' ); ?>
								</div>
								<div id="coupon"
									class="masterstudy-checkout-course-info__price"
									data-discount-type=""
									data-discount=""
								>
									<span class="masterstudy-checkout-course-info__price-value">
									</span>
								</div>
							</div>
							<?php
						}
						if ( $taxes_display['enabled'] ) {
							?>
							<div id="taxes_block" class="masterstudy-checkout-course-info__block">
								<div class="masterstudy-checkout-course-info__label">
									<?php
									echo $taxes_display['included']
									? esc_html__( 'Incl. tax:', 'masterstudy-lms-learning-management-system' ) :
									esc_html__( 'Tax:', 'masterstudy-lms-learning-management-system' );
									?>
								</div>
								<div id="taxes" class="masterstudy-checkout-course-info__price">
									<?php
									if ( ! empty( $is_trial ) ) {
										echo esc_html( STM_LMS_Helpers::display_price( 0 ) );
									} else {
										echo esc_html( STM_LMS_Helpers::display_taxes_amount( (float) $subtotal ) );
									}
									?>
								</div>
							</div>
						<?php } ?>
						<div class="masterstudy-checkout-course-info__block">
							<div class="masterstudy-checkout-course-info__label">
								<?php echo esc_html__( 'Total:', 'masterstudy-lms-learning-management-system' ); ?>
							</div>
							<div id="total"
								class="masterstudy-checkout-course-info__price"
								data-subtotal="<?php echo esc_attr( $subtotal ); ?>"
							>
								<span class="masterstudy-checkout-course-info__price-value">
									<?php
									if ( ! empty( $is_trial ) ) {
										echo esc_html( STM_LMS_Helpers::display_price( 0 ) );
									} else {
										echo esc_html( STM_LMS_Helpers::display_price_with_taxes( (float) $subtotal ) );
									}
									?>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php STM_LMS_Templates::show_lms_template( 'components/personal-info' ); ?>
		</div>
		<div class="masterstudy-checkout-payment">
			<div id="stm_lms_checkout">
				<div class="masterstudy-checkout-table">
					<div class="masterstudy-checkout-table__header">
						<div class="masterstudy-checkout-course-info">
							<div class="masterstudy-checkout-course-info__value">
								<?php echo esc_html__( 'Payment method', 'masterstudy-lms-learning-management-system' ); ?>
							</div>
						</div>
					</div>
					<div class="masterstudy-checkout-table__body">
						<?php STM_LMS_Templates::show_lms_template( 'checkout/payment_methods', compact( 'user_id' ) ); ?>
					</div>
					<div class="masterstudy-checkout-table__footer">
						<div class="masterstudy-checkout-course-info">
							<?php
							$payment_methods = STM_LMS_Options::get_option( 'payment_methods' );
							$payment_methods = is_array( $payment_methods ) ? $payment_methods : array();
							$enabled_methods = array_filter(
								$payment_methods,
								function ( $method ) {
									return isset( $method['enabled'] ) && $method['enabled'];
								}
							);

							if ( count( $enabled_methods ) > 0 ) {
								if ( ! empty( $settings['gdpr_page'] ) && ! empty( $settings['gdpr_warning'] ) ) {
									STM_LMS_Templates::show_lms_template(
										'checkout/gdpr',
										array(
											'gdpr_page'    => $settings['gdpr_page'],
											'gdpr_warning' => $settings['gdpr_warning'],
										)
									);
								}
								?>
								<a href="#" @click.prevent="purchase_courses()" class="btn btn-default stm_lms_pay_button" v-bind:class="{'loading' : loading, 'stm_lms_disabled_button': !agree_with_policy}">
									<?php echo esc_html__( 'Pay ', 'masterstudy-lms-learning-management-system' ); ?>
									<span>
										<?php
										if ( ! empty( $is_trial ) ) {
											echo esc_html( STM_LMS_Helpers::display_price( 0 ) );
										} else {
											echo esc_html( STM_LMS_Helpers::display_price_with_taxes( (float) $subtotal ) );
										}
										?>
									</span>
								</a>
								<?php
							} else {
								echo esc_html__( 'Please configure payment methods', 'masterstudy-lms-learning-management-system' );
							}
							?>
						</div>
					</div>
				</div>
				<transition-group name="slide-fade" tag="div">
					<div v-for="(msg, index) in messages"
						:key="index"
						class="stm-lms-message"
						:class="status">
						{{ msg }}
					</div>
				</transition-group>
			</div>
		</div>
	</div>
<?php endif; ?>
