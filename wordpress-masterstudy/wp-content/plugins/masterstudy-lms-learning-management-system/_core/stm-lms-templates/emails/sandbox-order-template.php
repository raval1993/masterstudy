<?php // phpcs:ignoreFile
/**
 * Email Order template
 *
 * @var $send_test_mode
 * @var $order_id
 * @var $message
 * @var $is_instructor
 * @var $settings
 * @var $instructor_items
 * @var $title
 * @var $customer_section
 */
$title = 'Thank you for purchase!';
if ( $customer_section && $is_instructor ) {
	$title = 'You made a Sale!';
} else if ( $customer_section && ! $is_instructor ) {
	$title = 'New order';
}

?>
<!-- Wrapper Table -->
<table align="center" width="100%" cellpadding="0" cellspacing="0" border="0"
       style=" max-width: 620px;margin: 0 auto;text-align: center;margin-bottom: 30px !important;padding: 0;font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; font-style: normal;font-weight: 500;font-size: 15px;line-height: 26px;color: #808C98;">
	<tr>
		<td align="center">
			<!-- Email Content Table -->
			<table cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
				<!-- Header -->
				<tr>
					<td style="text-align: center; padding: 20px; font-size: 16px; color: #333333;">
						<p style="margin: 0; font-weight: 700; font-size: 12px;text-transform: uppercase; font-style: normal; color: #4D5E6F;"><?php echo esc_html( date( "F j, Y g:i a" ) ); ?></p>
						<h2 style="margin: 10px 0; font-size: 24px; color: #001931; font-weight: 700"><?php echo $title; ?></h2>
						<table cellpadding="0" cellspacing="0" border="0" style="padding-top: 25px;margin: 0 auto; line-height: 1; width: auto;">
							<tr>
								<!-- Order Text -->
								<td style="padding-right: 5px; vertical-align: middle;">
									<span style="font-size: 14px; color: #333;"> Order ID: #00001 </span>
								</td>
								<!-- Status Badge -->
								<td style="padding-left: 5px; vertical-align: middle;">
									<span
										style="padding: 3px 5px; color: white; font-weight: bold; font-size: 10px; border-radius: 4px; text-transform: uppercase; background: gray; display: inline-block;">pending</span>
								</td>
							</tr>
						</table>
						<p style="margin-top: 30px; color: #666666; font-size: 15px; font-weight: 400">
							Send Test Email message is now ready. Dive in and start your journey toward new skills and
							achievements today!
						</p>
					</td>
				</tr>

				<tr>
					<td style="padding: 20px;">
						<!-- Item Table -->
						<?php
						echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
						?>
				<tr>
					<td width="20%" style="padding: 10px;">
						<!-- Display Item Image -->
						<img src="https://picsum.photos/110/80" alt="Product Image" style="width: 110px; height: auto; border-radius: 4px;">
					</td>
					<td width="80%" style="padding: 10px; vertical-align: top;">
						<!-- Display Item Title -->
						<p style="margin: 0; font-weight: bold; color: #001931; line-height: 2.0;">
							React – The Complete Guide 2024
						</p>
						<!-- Display Terms -->
						<p style="margin: 0; font-size: 14px; color: #666666;">
							Development, Frontend
						</p>
						<!-- Display Price -->
						<p style="margin: 0; font-size: 14px; color: #333333; font-weight: 700">
							<span style="font-weight: 400">Price:</span> $94
						</p>
					</td>
				</tr>
				<!-- Add Divider Row -->
				<tr>
					<td colspan="3" style="padding: 0; border-bottom: 1px solid #ddd;"></td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- Customer Information -->
	<?php
	if ( $customer_section ) {
		?>
		<tr>
			<td style="padding: 20px;">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-spacing: 10px; background-color: #EEF1F7;border-radius: 4px;">
					<tr>
						<td width="40px" style="padding: 10px 0 10px 10px;">
							<img src="https://picsum.photos/40" alt="Product Image"
							     style="width: 40px; height: auto; border-radius: 40px; margin-bottom: -12px;">
						</td>
						<td style="padding: 10px; padding-top: 5px;">
							<p style="margin: 0; font-weight: bold; color: #333333;">Gregor Garrity</p>
							<p style="margin: 0; font-size: 14px; color: #195EC8;     line-height: 1; ">
								example@example.com</p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php
	}
	?>

	<!-- Order Summary -->
	<tr>
		<td style="padding: 10px;">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-spacing: 10px; border-collapse: separate;">
				<tr>
					<td width="33%" style="padding: 15px 20px; text-align: center; font-size: 14px; color: #333333; background-color: #EEF1F7; border-radius: 4px;">
						<p style="margin: 0; text-align: left;"><?php echo esc_html__( 'Order Items:', 'masterstudy-lms-learning-management-system' ); ?></p>
						<p style="margin: 0; font-weight: bold; text-align: left; color: #001931">1</p>
					</td>
					<td width="33%" style="padding: 15px 20px; text-align: center; font-size: 14px; color: #333333; background-color: #EEF1F7; border-radius: 4px;">
						<p style="margin: 0; text-align: left;"><?php echo esc_html__( 'Payment Method:', 'masterstudy-lms-learning-management-system' ); ?></p>
						<p style="text-transform: capitalize; margin: 0; font-weight: bold; text-align: left; color: #001931">
							PayPal</p>
					</td>
					<td width="33%" style="padding: 15px 20px; text-align: center; font-size: 14px; color: #333333; background-color: #EEF1F7; border-radius: 4px;">
						<p style="margin: 0;  text-align: left;"><?php echo esc_html__( 'Total:', 'masterstudy-lms-learning-management-system' ); ?></p>
						<p style="margin: 0; font-weight: bold; text-align: left; color: #001931">94$</p>
					</td>
				</tr>
			</table>

		</td>
	</tr>
	<tr>
		<td style="text-align: center; padding: 20px;">
			<a href="#url_for_order" target="_blank" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 16px;"><?php echo esc_html__( 'View Order', 'masterstudy-lms-learning-management-system' ); ?></a>
		</td>
	</tr>
		</td>
	</tr>
</table>
