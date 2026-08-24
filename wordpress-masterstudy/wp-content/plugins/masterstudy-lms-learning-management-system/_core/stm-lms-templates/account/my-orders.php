<?php
$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );

wp_enqueue_style( 'masterstudy-account-main' );

stm_lms_register_style( 'user-orders' );
wp_enqueue_style( 'masterstudy-button' );
wp_enqueue_style( 'masterstudy-pagination' );
wp_enqueue_script( 'masterstudy-orders' );
wp_localize_script(
	'masterstudy-orders',
	'masterstudy_orders',
	array(
		'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
		'nonce'                      => wp_create_nonce( 'ms_lms_nonce' ),
		'no_order_title'             => esc_html__( 'No orders yet', 'masterstudy-lms-learning-management-system' ),
		'button_title'               => esc_html__( 'Explore courses', 'masterstudy-lms-learning-management-system' ),
		'courses_page'               => esc_url( STM_LMS_Course::courses_page_url() ),
		'payment_code_wire_transfer' => esc_html__( 'Wire Transfer', 'masterstudy-lms-learning-management-system' ),
		'payment_code_cash'          => esc_html__( 'Cash', 'masterstudy-lms-learning-management-system' ),
		'payment_code_mollie'        => esc_html__( 'Mollie', 'masterstudy-lms-learning-management-system' ),
		'payment_code_paystack'      => esc_html__( 'Paystack', 'masterstudy-lms-learning-management-system' ),
		'bundle'                     => esc_html__( 'Bundle', 'masterstudy-lms-learning-management-system' ),
		'enterprise'                 => esc_html__( 'Enterprise', 'masterstudy-lms-learning-management-system' ),
		'subscription'               => esc_html__( 'Subscription', 'masterstudy-lms-learning-management-system' ),
		'statuses'                   => array(
			'completed' => esc_html__( 'Completed', 'masterstudy-lms-learning-management-system' ),
			'pending'   => esc_html__( 'Pending', 'masterstudy-lms-learning-management-system' ),
			'cancelled' => esc_html__( 'Cancelled', 'masterstudy-lms-learning-management-system' ),
		),
	),
);

$taxes_display     = STM_LMS_Helpers::taxes_display();
$is_coupon_enabled = STM_LMS_Helpers::is_coupons_enabled();
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<?php
		if ( ! STM_LMS_Cart::woocommerce_checkout_enabled() ) :
			?>
			<div class="masterstudy-orders student-orders">
				<h1 class="masterstudy-orders__title">
					<?php echo esc_html__( 'My Orders', 'masterstudy-lms-learning-management-system' ); ?>
				</h1>
				<div class="masterstudy-orders-container">
					<div class="masterstudy-orders__loader">
						<div class="masterstudy-orders__loader-body"></div>
					</div>
					<template id="masterstudy-order-template">
						<div class="masterstudy-orders-table">
							<div class="masterstudy-orders-table__header">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__id" data-order-id></div>
									<div class="order-status" data-order-status></div>
								</div>
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Date', 'masterstudy-lms-learning-management-system' ); ?>:</div>
									<div class="masterstudy-orders-course-info__value" data-order-date></div>
								</div>
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Payment Method', 'masterstudy-lms-learning-management-system' ); ?>:</div>
									<div class="masterstudy-orders-course-info__value" data-order-payment></div>
								</div>
							</div>
							<div class="masterstudy-orders-table__body"></div>
							<div class="masterstudy-orders-table__footer">
								<div class="masterstudy-orders-course-info">
									<?php if ( $taxes_display['enabled'] || $is_coupon_enabled ) { ?>
										<div data-id="subtotal" class="masterstudy-orders-course-info__block">
											<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Subtotal', 'masterstudy-lms-learning-management-system' ); ?>:</div>
											<div class="masterstudy-orders-course-info__price" data-order-subtotal></div>
										</div>
									<?php } ?>
									<?php if ( $is_coupon_enabled ) : ?>
										<div data-id="coupon" class="masterstudy-orders-course-info__block">
											<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Coupon', 'masterstudy-lms-learning-management-system' ); ?>:</div>
											<div class="masterstudy-orders-course-info__price" data-order-coupon></div>
										</div>
									<?php endif; ?>
									<?php if ( $taxes_display['enabled'] ) : ?>
										<div data-id="taxes" class="masterstudy-orders-course-info__block">
											<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Tax', 'masterstudy-lms-learning-management-system' ); ?>:</div>
											<div class="masterstudy-orders-course-info__price" data-order-taxes></div>
										</div>
									<?php endif; ?>
									<div data-id="total" class="masterstudy-orders-course-info__block">
										<div class="masterstudy-orders-course-info__label">
											<?php echo esc_html__( 'Total', 'masterstudy-lms-learning-management-system' ); ?>:</div>
										<div class="masterstudy-orders-course-info__price">
											<span class="masterstudy-orders-course-info__price-value" data-order-total></span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</template>
				</div>
				<div class="masterstudy-orders-table-navigation">
					<div class="masterstudy-orders-table-navigation__pagination"></div>
				</div>
			</div>
			<?php
		else :
			STM_LMS_Templates::show_lms_template( 'account/parts/woocommerce-orders' );
		endif;
		?>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
