<?php
/**
 * Template: Confirmation
 * Version: 3.1
 *
 * @version 3.1
 */

global $wpdb, $current_user, $pmpro_invoice, $pmpro_msg, $pmpro_msgt;
?>
<h1 class="pmpro_confirmation_title"><?php esc_html_e( 'Membership confirmation', 'masterstudy-lms-learning-management-system' ); ?></h1>
<?php
if ( $pmpro_msg ) {
	?>
	<div class="pmpro_message <?php echo esc_attr( $pmpro_msgt ); ?>"><?php echo wp_kses_post( $pmpro_msg ); ?></div>
	<?php
}
?>
<div class="pmpro_message_wrapper">
<?php
if ( empty( $current_user->membership_level ) ) {
	$confirmation_message = '<p>' . __( 'Your payment has been submitted. Your membership will be activated shortly.', 'masterstudy-lms-learning-management-system' ) . '</p>';
} else {
	/* translators: %s Level Name */
	$confirmation_message = '<h3>' . sprintf( __( 'Thank you for your membership to %1$s. Your <span>%2$s</span> membership is now active.', 'masterstudy-lms-learning-management-system' ), get_bloginfo( 'name' ), $current_user->membership_level->name ) . '</h3>';
}

// Confirmation message for this level
//phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
$level_message = $wpdb->get_var( "SELECT l.confirmation FROM $wpdb->pmpro_membership_levels l LEFT JOIN $wpdb->pmpro_memberships_users mu ON l.id = mu.membership_id WHERE mu.status = 'active' AND mu.user_id = '" . $current_user->ID . "' LIMIT 1" );
if ( ! empty( $level_message ) ) {
	$confirmation_message .= "\n" . stripslashes( $level_message ) . "\n";
}

if ( ! empty( $pmpro_invoice ) && ! empty( $pmpro_invoice->id ) ) {
	$pmpro_invoice->getUser();
	$pmpro_invoice->getMembershipLevel();

	/* translators: %s User Email */
	$confirmation_message .= '<p>' . sprintf( __( 'Below are details about your membership account and a receipt for your initial membership invoice. A welcome email with a copy of your initial membership invoice has been sent to <span>%s</span>.', 'masterstudy-lms-learning-management-system' ), $pmpro_invoice->user->user_email ) . '</p>';

	// check instructions
	if ( 'check' === $pmpro_invoice->gateway && ! pmpro_isLevelFree( $pmpro_invoice->membership_level ) ) {
		$confirmation_message .= wpautop( wp_unslash( pmpro_getOption( 'instructions' ) ) );
	}

	/**
	 * All devs to filter the confirmation message.
	 * We also have a function in includes/filters.php that applies the the_content filters to this message.
	 *
	 * @param string $confirmation_message The confirmation message.
	 * @param object $pmpro_invoice The PMPro Invoice/Order object.
	 */
	$confirmation_message = apply_filters( 'pmpro_confirmation_message', $confirmation_message, $pmpro_invoice );

	echo wp_kses_post( stm_lms_filtered_output( $confirmation_message ) );
	?>
	<h3 class="invoice_info">
		<?php
		/* translators: %s Datetime */
		printf( wp_kses_post( __( 'Invoice #%1$s on %2$s', 'masterstudy-lms-learning-management-system' ) ), esc_html( $pmpro_invoice->code ), esc_html( date_i18n( get_option( 'date_format' ), $pmpro_invoice->timestamp ) ) );
		?>
	</h3>
	<a class="pmpro_a-print" href="javascript:window.print()"><?php esc_html_e( 'Print', 'masterstudy-lms-learning-management-system' ); ?></a>
	<ul>
		<?php do_action( 'pmpro_invoice_bullets_top', $pmpro_invoice ); ?>
		<li>
			<strong><?php esc_html_e( 'Account', 'masterstudy-lms-learning-management-system' ); ?>:</strong>
			<?php echo esc_html( $current_user->display_name ); ?>
			(<?php echo esc_html( $current_user->user_email ); ?>)
		</li>
		<li>
			<strong><?php esc_html_e( 'Membership Plan', 'masterstudy-lms-learning-management-system' ); ?>:</strong>
			<?php echo esc_html( $current_user->membership_level->name ); ?>
		</li>
		<?php if ( $current_user->membership_level->enddate ) { ?>
			<li><strong><?php esc_html_e( 'Membership Expires', 'masterstudy-lms-learning-management-system' ); ?>:</strong>
				<?php echo esc_html( date_i18n( get_option( 'date_format' ), $current_user->membership_level->enddate ) ); ?>
			</li>
		<?php } ?>
		<?php if ( $pmpro_invoice->getDiscountCode() ) { ?>
			<li><strong><?php esc_html_e( 'Discount Code', 'masterstudy-lms-learning-management-system' ); ?>:</strong>
			<?php echo esc_html( $pmpro_invoice->discount_code->code ); ?>
			</li>
		<?php } ?>
		<li class="total_billed">
		<?php if ( ! empty( $pmpro_invoice->tax ) ) { ?>
			<div class="total_billed_wrapper">
				<strong><?php esc_html_e( 'Subtotal', 'masterstudy-lms-learning-management-system' ); ?>:</strong>
				<?php echo wp_kses_post( pmpro_formatPrice( $pmpro_invoice->subtotal ) ); ?>
			</div>
			<div class="total_billed_wrapper">
				<strong><?php esc_html_e( 'Tax', 'masterstudy-lms-learning-management-system' ); ?>:</strong>
				<?php echo wp_kses_post( pmpro_formatPrice( $pmpro_invoice->tax ) ); ?>
			</div>
			<div class="total_billed_wrapper">
				<strong><?php esc_html_e( 'Total', 'masterstudy-lms-learning-management-system' ); ?>:</strong>
				<?php echo wp_kses_post( pmpro_formatPrice( $pmpro_invoice->total ) ); ?>
			</div>
		<?php } else { ?>
			<div class="total_billed_wrapper">
				<strong><?php esc_html_e( 'Total Billed', 'masterstudy-lms-learning-management-system' ); ?>:</strong>
				<?php echo wp_kses_post( pmpro_formatPrice( $pmpro_invoice->total ) ); ?>
			</div>
		<?php } ?>
		</li>
		<?php do_action( 'pmpro_invoice_bullets_bottom', $pmpro_invoice ); ?>
	</ul>
</div>
	<div class="pmpro_invoice_details">
		<?php if ( ! empty( $pmpro_invoice->billing->name ) ) { ?>
			<div class="pmpro_invoice-billing-address">
				<strong><?php esc_html_e( 'Billing Address', 'masterstudy-lms-learning-management-system' ); ?></strong>
				<p><?php echo esc_html( $pmpro_invoice->billing->name ); ?></p>
				<p><?php echo esc_html( $pmpro_invoice->billing->street ); ?></p>
				<?php if ( $pmpro_invoice->billing->city && $pmpro_invoice->billing->state ) { ?>
					<p><?php echo esc_html( $pmpro_invoice->billing->city ); ?>,
						<?php echo esc_html( $pmpro_invoice->billing->state ); ?>,
						<?php echo esc_html( $pmpro_invoice->billing->zip ); ?>
					</p>
				<?php } ?>
				<p><?php echo esc_html( $pmpro_invoice->billing->country ); ?></p>
				<p><?php echo esc_html( formatPhone( $pmpro_invoice->billing->phone ) ); ?></p>
			</div>
			<?php
		}
		if ( $pmpro_invoice->accountnumber ) {
			$pmpro_card_images = array(
				'Visa'             => 'visa_card.svg',
				'Mastercard'       => 'mastercard.svg',
				'American Express' => 'americanexpress_card.svg',
				'Discover'         => 'discover_card.svg',
			)
			?>
			<div class="pmpro_invoice-payment-method">
				<div class="payment-method-wrapper">
					<strong><?php esc_html_e( 'Payment Method', 'masterstudy-lms-learning-management-system' ); ?></strong>
					<?php
					foreach ( $pmpro_card_images as $key => $value ) {
						if ( $key === $pmpro_invoice->cardtype ) {
							?>
							<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pmpro_img/' . $value ); ?>" alt="">
							<?php
						}
					}
					?>
				</div>
				<p><span><?php esc_html_e( 'Card Number', 'masterstudy-lms-learning-management-system' ); ?>:</span> **** **** **** <?php echo esc_html( last4( $pmpro_invoice->accountnumber ) ); ?></p>
				<p><span><?php esc_html_e( 'Expiration Date', 'masterstudy-lms-learning-management-system' ); ?>:</span>
					<?php echo esc_html( $pmpro_invoice->expirationmonth ); ?>/<?php echo esc_html( $pmpro_invoice->expirationyear ); ?>
				</p>
			</div> <!-- end pmpro_invoice-payment-method -->
		<?php } elseif ( $pmpro_invoice->payment_type ) { ?>
			<div class="other_payment_method">
				<strong><?php esc_html_e( 'Payment Method', 'masterstudy-lms-learning-management-system' ); ?></strong>
				<span><?php echo esc_html( $pmpro_invoice->payment_type ); ?></span>
			</div>
		<?php } ?>
	</div>
	<?php
} else {
	/* translators: %s User Email */
	$confirmation_message .= '<p>' . sprintf( __( 'Below are details about your membership account. A welcome email has been sent to %s.', 'masterstudy-lms-learning-management-system' ), $current_user->user_email ) . '</p>';

	/**
	 * All devs to filter the confirmation message.
	 * Documented above.
	 * We also have a function in includes/filters.php that applies the the_content filters to this message.
	 */
	$confirmation_message = apply_filters( 'pmpro_confirmation_message', $confirmation_message, false );

	echo wp_kses_post( stm_lms_filtered_output( $confirmation_message ) );
	?>
	<ul>
		<li>
			<strong><?php esc_html_e( 'Account', 'masterstudy-lms-learning-management-system' ); ?>:</strong>
			<?php echo esc_html( $current_user->display_name ); ?>
			(<?php echo esc_html( $current_user->user_email ); ?>)
		</li>
		<li><strong><?php esc_html_e( 'Membership Plan', 'masterstudy-lms-learning-management-system' ); ?>:</strong>
			<?php
			if ( ! empty( $current_user->membership_level ) ) {
				echo esc_html( $current_user->membership_level->name );
			} else {
				esc_html_e( 'Pending', 'masterstudy-lms-learning-management-system' );
			}
			?>
		</li>
	</ul>
</div>
	<?php
}

if ( ! empty( $current_user->membership_level ) && isset( $_COOKIE['stm_lms_course_buy'] ) ) {
	/*MS Redirect to course*/
	$course_id = intval( $_COOKIE['stm_lms_course_buy'] );
	if ( get_post_type( $course_id ) === 'stm-courses' ) {
		stm_lms_register_script( 'buy/redirect_to_cookie', array( 'jquery.cookie' ), true );
		?>
		<br/>
		<h3><?php esc_html_e( 'Redirecting to course...', 'masterstudy-lms-learning-management-system' ); ?></h3>
		<?php
	}
} else {
	?>
	<nav id="nav-below" class="navigation" role="navigation">
		<div class="nav-next">
			<?php if ( ! empty( $current_user->membership_level ) ) { ?>
				<a href="<?php echo esc_url( STM_LMS_User::user_page_url() ); ?>" class="btn btn-default"><?php esc_html_e( 'View Your Account', 'masterstudy-lms-learning-management-system' ); ?></a>
			<?php } else { ?>
				<?php esc_html_e( 'If your account is not activated within a few minutes, please contact the site owner.', 'masterstudy-lms-learning-management-system' ); ?>
			<?php } ?>
		</div>
	</nav>
	<?php
}
