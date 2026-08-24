<?php // phpcs:ignoreFile
/**
 * Email Order template
 *
 * @var $order_id
 * @var $message
 * @var $is_instructor
 * @var $settings
 * @var $instructor_items
 * @var $title
 * @var $customer_section
 */
$order             = STM_LMS_Order::get_order_info( $order_id );
$cart_items_render = $order['cart_items'];
$totalPrice        = $order['total'];
$itemCount         = count( $order['cart_items'] ); // Initialize item count correctly

if ( ! empty( $instructor_items ) ) {
	$itemCount  = 0;
	$totalPrice = 0;
	$itemIds    = array_column( $instructor_items, 'item_id' );

	// Filter the cart items to keep only elements with matching item IDs
	$cart_items_render = array_filter( $cart_items_render, function ( $key ) use ( $itemIds ) {
		return in_array( $key, $itemIds );
	}, ARRAY_FILTER_USE_KEY );

	// Calculate total price and item count
	foreach ( $cart_items_render as $item ) {
		$totalPrice += $item['price'];
		$itemCount ++;
	}

	$totalPrice = STM_LMS_Options::get_option( 'currency_symbol', '$' ) . $totalPrice;
}

$view_order_url = ms_plugin_user_account_url( 'my-orders' );
if ( $customer_section ) {
	$view_order_url = ms_plugin_user_account_url( 'sales' );
}

$title_render            = true;
$date_render             = true;
$order_id_render         = true;
$items_list_render       = true;
$customer_section_render = true;
$button_view_render      = true;

if ( ! empty( $settings ) ) {
	if ( $is_instructor ) {
		$title_render            = $settings['stm_lms_new_order_instructor_title_order_render'] ?? true;
		$date_render             = $settings['stm_lms_new_order_instructor_date_order_render'] ?? true;
		$order_id_render         = $settings['stm_lms_new_order_instructor_order_order_render'] ?? true;
		$items_list_render       = $settings['stm_lms_new_order_instructor_items_order_render'] ?? true;
		$customer_section_render = $settings['stm_lms_new_order_instructor_customer_order_render'] ?? true;
		$button_view_render      = $settings['stm_lms_new_order_instructor_button_order_render'] ?? true;
	}
	if ( $customer_section && ! $is_instructor ) {
		$title_render            = $settings['stm_lms_new_order_title_order_render'] ?? true;
		$date_render             = $settings['stm_lms_new_order_date_order_render'] ?? true;
		$order_id_render         = $settings['stm_lms_new_order_order_order_render'] ?? true;
		$items_list_render       = $settings['stm_lms_new_order_items_order_render'] ?? true;
		$customer_section_render = $settings['stm_lms_new_order_customer_order_render'] ?? true;
		$button_view_render      = $settings['stm_lms_new_order_button_order_render'] ?? true;
	}
	if ( ! $customer_section ) {
		$title_render            = $settings['stm_lms_new_order_accepted_title_order_render'] ?? true;
		$date_render             = $settings['stm_lms_new_order_accepted_date_order_render'] ?? true;
		$order_id_render         = $settings['stm_lms_new_order_accepted_order_order_render'] ?? true;
		$items_list_render       = $settings['stm_lms_new_order_accepted_items_order_render'] ?? true;
		$button_view_render      = $settings['stm_lms_new_order_accepted_button_order_render'] ?? true;
	}
}

$status_color = 'gray';
if ( 'pending' === $order['status'] ) {
	$status_color = 'gray';
} else if ( 'completed' === $order['status'] ) {
	$status_color = 'green';
} else if ( 'cancelled' === $order['status'] ) {
	$status_color = 'red';
}

$payment_method = $order['payment_code'];
if ( $payment_method === 'wire_transfer' ) {
	$payment_method = 'Wire transfer';
}

?>
<!-- Wrapper Table -->
<table align="center" width="100%" cellpadding="0" cellspacing="0" border="0" style=" max-width: 620px;margin: 0 auto;text-align: center;margin-bottom: 30px !important;padding: 0;font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; font-style: normal;font-weight: 500;font-size: 15px;line-height: 26px;color: #808C98;">
	<tr>
		<td align="center">
			<!-- Email Content Table -->
			<table cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
				<!-- Header -->
				<tr>
					<td style="text-align: center; padding: 20px; font-size: 16px; color: #333333;">
						<?php
						if ( $date_render ) {
							?>
							<p style="margin: 0; font-weight: 700; font-size: 12px;text-transform: uppercase; font-style: normal; color: #4D5E6F;"><?php echo esc_html( $order['date_formatted'] ); ?></p>
							<?php
						}
						if ( isset( $title ) && $title !== '' && $title_render ) {
							?>
							<h2 style="margin: 10px 0; font-size: 24px; color: #001931; font-weight: 700"><?php echo $title; ?></h2>
							<?php
						}
						if ( $order_id_render ) { ?>
						<table cellpadding="0" cellspacing="0" border="0" style="padding-top: 25px;margin: 0 auto; line-height: 1; width: auto;">
							<tr>
								<!-- Order Text -->
								<td style="padding-right: 5px; vertical-align: middle;">
									<span style="font-size: 14px; color: #333;"> <?php echo esc_html__( 'Order ID:', 'masterstudy-lms-learning-management-system' );
										echo esc_html( $order['id'] ); ?></span>
								</td>
								<!-- Status Badge -->
								<td style="padding-left: 5px; vertical-align: middle;">
									<span style="padding: 3px 5px; color: white; font-weight: bold; font-size: 10px; border-radius: 4px; text-transform: uppercase; background: <?php echo esc_attr( $status_color ); ?>; display: inline-block;"><?php echo esc_html( $order['status'] ); ?></span>
								</td>
							</tr>
						</table>
						<?php
						}

						if ( isset( $message ) && $message !== '' ) {
							?>
							<p style="margin-top: 30px; color: #666666; font-size: 15px; font-weight: 400"><?php echo $message; ?></p>
							<?php
						}
						?>
					</td>
				</tr>

				<!-- Order Items -->
				<?php
				if ( $items_list_render ) {
				?>
				<tr>
					<td style="padding: 20px;">
						<!-- Item Table -->
						<?php
							echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
						?>
						<?php foreach ($cart_items_render as $item): ?>
				<tr>
					<td width="20%" style="padding: 10px;">
						<!-- Display Item Image -->
						<img src="<?php echo esc_url($item['image_url']); ?>" alt="Product Image" style="width: 110px; height: auto; border-radius: 4px;">
					</td>
					<td width="80%" style="padding: 10px; vertical-align: top;">
						<!-- Display Item Title -->
						<p style="margin: 0; font-weight: bold; color: #001931; line-height: 2.0;">
							<?php echo esc_html($item['title']); ?>
							<?php if ($item['bundle_courses_count'] > 0):?>
								<!-- Add "BUNDLE" span if bundle_courses_count > 0 -->
								<span style="background-color: #808C98; padding: 3px 5px; font-size: 10px; border-radius: 4px; color: white;"><?php echo esc_html__( 'BUNDLE', 'masterstudy-lms-learning-management-system' ); ?></span>
							<?php endif; ?>
							<?php if (!empty($item['enterprise_name'])):?>
								<!-- Add "GROUP" span if enterprise_name is set -->
								<span style="background-color: #808C98; padding: 3px 5px; font-size: 10px; border-radius: 4px; color: white;"><?php echo esc_html__( 'ENTERPRISE', 'masterstudy-lms-learning-management-system' ); ?></span>
							<?php endif; ?>
						</p>
						<!-- Display Terms -->
						<p style="margin: 0; font-size: 14px; color: #666666;">
							<?php
							if ( ! empty( $item['enterprise_name'] ) ) {
								echo esc_html__( 'for group', 'masterstudy-lms-learning-management-system' ) . ' ' . esc_html( $item['enterprise_name'] );
							} else if ( isset( $item['bundle_courses_count'] ) && $item['bundle_courses_count'] > 0 ) {
								echo esc_html( $item['bundle_courses_count'] . ' ' . esc_html__( 'courses in bundle', 'masterstudy-lms-learning-management-system' ) );
							}
							else {
								echo esc_html( implode( ', ', $item['terms'] ) );
							}
							?>
						</p>
						<!-- Display Price -->
						<p style="margin: 0; font-size: 14px; color: #333333; font-weight: 700">
							<span style="font-weight: 400">Price:</span>  <?php echo esc_html($item['price_formatted']); ?>
						</p>
					</td>
				</tr>
				<!-- Add Divider Row -->
				<tr>
					<td colspan="3" style="padding: 0; border-bottom: 1px solid #ddd;"></td>
				</tr>
				<?php endforeach; ?>
					<?php
						echo '</table>';
					?>
					</td>
				</tr>
			<?php }?>

				<!-- Customer Information -->
				<?php
				if ( isset( $customer_section ) && $customer_section && $customer_section_render ) {
					?>
					<tr>
						<td style="padding: 20px;">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-spacing: 10px; background-color: #EEF1F7;border-radius: 4px;">
								<tr>
									<td width="40px" style="padding: 10px 0 10px 10px;">
										<img src="<?php echo esc_html( $order['user']['avatar_url'] ); ?>" alt="Product Image" style="width: 40px; height: auto; border-radius: 40px; margin-bottom: -12px;">
									</td>
									<td style="padding: 10px; padding-top: 5px;">
										<p style="margin: 0; font-weight: bold; color: #333333;"><?php echo esc_html( $order['user']['login'] ); ?></p>
										<p style="margin: 0; font-size: 14px; color: #195EC8;     line-height: 1; "><?php echo esc_html( $order['user']['email'] ); ?></p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<?php
				}
				?>

				<?php
				if ( $items_list_render ) {
					?>
					<!-- Order Summary -->
					<tr>
						<td style="padding: 10px;">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-spacing: 10px; border-collapse: separate;">
								<tr>
									<td width="33%" style="padding: 15px 20px; text-align: center; font-size: 14px; color: #333333; background-color: #EEF1F7; border-radius: 4px;">
										<p style="margin: 0; text-align: left;"><?php echo esc_html__( 'Order Items:', 'masterstudy-lms-learning-management-system' ); ?></p>
										<p style="margin: 0; font-weight: bold; text-align: left; color: #001931"><?php echo esc_html( $itemCount ); ?></p>
									</td>
									<td width="33%" style="padding: 15px 20px; text-align: center; font-size: 14px; color: #333333; background-color: #EEF1F7; border-radius: 4px;">
										<p style="margin: 0; text-align: left;"><?php echo esc_html__( 'Payment Method:', 'masterstudy-lms-learning-management-system' ); ?></p>
										<p style="text-transform: capitalize; margin: 0; font-weight: bold; text-align: left; color: #001931"><?php echo esc_html( $payment_method ); ?></p>
									</td>
									<td width="33%" style="padding: 15px 20px; text-align: center; font-size: 14px; color: #333333; background-color: #EEF1F7; border-radius: 4px;">
										<p style="margin: 0;  text-align: left;"><?php echo esc_html__( 'Total:', 'masterstudy-lms-learning-management-system' ); ?></p>
										<p style="margin: 0; font-weight: bold; text-align: left; color: #001931"><?php echo esc_html( $totalPrice ); ?></p>
									</td>
								</tr>
							</table>

						</td>
					</tr>
				<?php
				}
				?>

				<!-- View Order Button -->
				<?php
				if ( $button_view_render ) {
				?>
				<tr>
					<td style="text-align: center; padding: 20px;">
						<a href="<?php echo $view_order_url;?>" target="_blank" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 16px;"><?php echo esc_html__( 'View Order', 'masterstudy-lms-learning-management-system' ); ?></a>
					</td>
				</tr>
				<?php }?>
			</table>
		</td>
	</tr>
</table>
