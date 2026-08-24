<?php
/**
 * Template: Invoice
 * Version: 3.1
 *
 * @version 3.1
 */

?>

<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_invoice_wrap' ) ); ?>">
	<?php
	global $wpdb, $pmpro_invoice, $pmpro_msg, $pmpro_msgt, $current_user;

	if ( $pmpro_msg ) {
		?>
		<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_message ' . $pmpro_msgt, $pmpro_msgt ) ); ?>"><?php echo wp_kses_post( $pmpro_msg ); ?></div>
		<?php
	}
	?>

<?php
if ( $pmpro_invoice ) {
	$pmpro_invoice->getUser();
	$pmpro_invoice->getMembershipLevel();
	?>
	<h3><?php printf( esc_html__( 'Invoice #%1$s on %2$s', 'masterstudy-lms-learning-management-system' ), esc_html( $pmpro_invoice->code ), esc_html( date_i18n( get_option( 'date_format' ) ), $pmpro_invoice->getTimestamp() ) ); ?></h3>
	<ul>
	<?php do_action( 'pmpro_invoice_bullets_top', $pmpro_invoice ); ?>
		<li><strong><?php esc_html_e( 'Account', 'masterstudy-lms-learning-management-system' ); ?>:</strong> <?php echo esc_html( $pmpro_invoice->user->display_name ); ?> (<?php echo esc_html( $pmpro_invoice->user->user_email ); ?>)</li>
		<li><strong><?php esc_html_e( 'Membership Plan', 'masterstudy-lms-learning-management-system' ); ?>:</strong> <?php echo esc_html( $pmpro_invoice->membership_level->name ); ?></li>
	<?php if ( ! empty( $pmpro_invoice->status ) ) { ?>
		<li><strong><?php esc_html_e( 'Status', 'masterstudy-lms-learning-management-system' ); ?>:</strong>
		<?php
		if ( in_array( $pmpro_invoice->status, array( '', 'success', 'cancelled' ) ) ) {
			$display_status = __( 'Paid', 'masterstudy-lms-learning-management-system' );
		} else {
			$display_status = ucwords( $pmpro_invoice->status );
		}
			echo esc_html( $display_status );
		?>
		</li>
		<?php
	}
	if ( $pmpro_invoice->getDiscountCode() ) {
		?>
		<li><strong><?php esc_html_e( 'Discount Code', 'masterstudy-lms-learning-management-system' ); ?>:</strong> <?php echo esc_html( $pmpro_invoice->discount_code->code ); ?></li>
	<?php } ?>
	<?php do_action( 'pmpro_invoice_bullets_bottom', $pmpro_invoice ); ?>
	</ul>
	<hr />
	<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_invoice_details' ) ); ?>">
		<?php if ( ! empty( $pmpro_invoice->billing->street ) ) { ?>
			<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_invoice-billing-address' ) ); ?>">
				<h2><?php esc_html_e( 'Billing Address', 'masterstudy-lms-learning-management-system' ); ?></h2>
				<p>
					<span class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_invoice-field-billing_name' ) ); ?>"><?php echo esc_html( $pmpro_invoice->billing->name ); ?></span>
					<span class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_invoice-field-billing_street' ) ); ?>"><?php echo esc_html( $pmpro_invoice->billing->street ); ?></span>
					<?php if ( $pmpro_invoice->billing->city && $pmpro_invoice->billing->state ) { ?>
						<span><?php echo esc_html( $pmpro_invoice->billing->city ) . ', ' . esc_html( $pmpro_invoice->billing->state ) . ', ' . esc_html( $pmpro_invoice->billing->zip ); ?></span>
						<span class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_invoice-field-billing_country' ) ); ?>"><?php echo esc_html( $pmpro_invoice->billing->country ); ?></span>
					<?php } ?>
					<span class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_invoice-field-billing_phone' ) ); ?>"><?php echo esc_html( formatPhone( $pmpro_invoice->billing->phone ) ); ?></span>
				</p>
			</div> <!-- end pmpro_invoice-billing-address -->
			<?php
		}
		if ( ! empty( $pmpro_invoice->accountnumber ) || ! empty( $pmpro_invoice->payment_type ) ) {
			?>
			<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_invoice-payment-method' ) ); ?>">
				<h2><?php esc_html_e( 'Payment Method', 'masterstudy-lms-learning-management-system' ); ?></h2>
				<p>
				<?php if ( $pmpro_invoice->accountnumber ) { ?>
					<span><strong><?php esc_html_e( 'Card Number', 'masterstudy-lms-learning-management-system' ); ?>:</strong> **** **** **** <?php echo esc_html( last4( $pmpro_invoice->accountnumber ) ); ?></span>
					<span><strong><?php esc_html_e( 'Expiration Date', 'masterstudy-lms-learning-management-system' ); ?>:</strong> <?php echo esc_html( $pmpro_invoice->expirationmonth ); ?>/<?php echo esc_html( $pmpro_invoice->expirationyear ); ?></span>
				<?php } else { ?>
					<span><?php echo esc_html( $pmpro_invoice->payment_type ); ?></span>
				<?php } ?>
				</p>
			</div> <!-- end pmpro_invoice-payment-method -->
		<?php } ?>
		<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_invoice-total' ) ); ?>">
			<h2><?php esc_html_e( 'Total Billed', 'masterstudy-lms-learning-management-system' ); ?></h2>
			<p>
				<span><strong><?php esc_html_e( 'Total', 'masterstudy-lms-learning-management-system' ); ?>:</strong><?php echo wp_kses_post( pmpro_formatPrice( $pmpro_invoice->total ) ); ?></span>
			</p>
		</div> <!-- end pmpro_invoice-total -->
	</div> <!-- end pmpro_invoice_details -->
	<?php
} else {
	// Show all invoices for user if no invoice ID is passed
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$invoices = $wpdb->get_results( "SELECT o.*, UNIX_TIMESTAMP(CONVERT_TZ(o.timestamp, '+00:00', @@global.time_zone)) as timestamp, l.name as membership_level_name FROM $wpdb->pmpro_membership_orders o LEFT JOIN $wpdb->pmpro_membership_levels l ON o.membership_id = l.id WHERE o.user_id = '$current_user->ID' AND o.status NOT IN('review', 'token', 'error') ORDER BY timestamp DESC" );
	if ( $invoices ) {
		?>
		<div class="pmpro_invoices_table_wrapper">
			<table id="pmpro_invoices_table" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_table pmpro_invoice', 'pmpro_invoices_table' ) ); ?>" width="100%" cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Date', 'masterstudy-lms-learning-management-system' ); ?></th>
						<th><?php esc_html_e( 'Invoice #', 'masterstudy-lms-learning-management-system' ); ?></th>
						<th><?php esc_html_e( 'Level', 'masterstudy-lms-learning-management-system' ); ?></th>
						<th><?php esc_html_e( 'Total Billed', 'masterstudy-lms-learning-management-system' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $invoices as $invoice ) {
					?>
					<tr>
						<td><a href="<?php echo esc_url( pmpro_url( 'invoice', '?invoice=' . $invoice->code ) ); ?>"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', $invoice->timestamp ) ) ) ) ); ?></a></td>
						<td><a href="<?php echo esc_url( pmpro_url( 'invoice', '?invoice=' . $invoice->code ) ); ?>"><?php echo esc_html( $invoice->code ); ?></a></td>
						<td><?php echo esc_html( $invoice->membership_level_name ); ?></td>
						<td><?php echo wp_kses_post( pmpro_formatPrice( $invoice->total ) ); ?></td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		<?php
	} else {
		?>
		<p><?php esc_html_e( 'No invoices found.', 'masterstudy-lms-learning-management-system' ); ?></p>
		<?php
	}
}
?>
	</div>
</div> <!-- end pmpro_invoice_wrap -->
<p class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_actions_nav' ) ); ?>">
	<?php if ( $pmpro_invoice ) { ?>
		<span><a href="<?php echo esc_url( pmpro_url( 'invoice' ) ); ?>"><?php esc_html_e( '&larr; View All Invoices', 'masterstudy-lms-learning-management-system' ); ?></a></span>
	<?php } ?>
	<span class="view_account"><a href="<?php echo esc_url( STM_LMS_User::my_pmpro_url() ); ?>"><?php esc_html_e( 'View Your Membership Account &rarr;', 'masterstudy-lms-learning-management-system' ); ?></a></span>
</p> <!-- end pmpro_actions_nav -->
