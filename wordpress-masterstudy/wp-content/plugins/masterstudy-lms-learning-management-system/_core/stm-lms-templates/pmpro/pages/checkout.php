<?php
/**
 * Template: Checkout
 * Version: 3.5
 *
 * See documentation for how to override the PMPro templates.
 *
 * @link https://www.paidmembershipspro.com/documentation/templates/
 *
 * @version 3.5
 *
 * @author Paid Memberships Pro
 */

global $gateway, $pmpro_review, $skip_account_fields, $pmpro_paypal_token, $wpdb, $current_user, $pmpro_msg, $pmpro_msgt, $pmpro_requirebilling, $pmpro_level, $pmpro_levels, $tospage, $pmpro_show_discount_code, $pmpro_error_fields;
global $discount_code, $username, $password, $password2, $bfirstname, $blastname, $baddress1, $baddress2, $bcity, $bstate, $bzipcode, $bcountry, $bphone, $bemail, $bconfirmemail, $CardType, $AccountNumber, $ExpirationMonth, $ExpirationYear;

/**
 * Filter to set if PMPro uses email or text as the type for email field inputs.
 *
 * @since 1.8.4.5
 *
 * @param bool $use_email_type , true to use email type, false to use text type
 */

wp_enqueue_script( 'masterstudy-pmpro-checkout' );

$pmpro_email_field_type  = apply_filters( 'pmpro_email_field_type', true );
$courses_included        = get_option( "stm_lms_course_number_{$pmpro_level->id}" );
$featured_quotas         = get_option( "stm_lms_featured_courses_number_{$pmpro_level->id}" );
$level_period            = pmpro_translate_billing_period( $pmpro_level->cycle_period );
$level_price             = pmpro_formatPrice( $pmpro_level->initial_payment );
$level_period_count      = ( 1 < $pmpro_level->cycle_number ) ? $level_period . 's' : $level_period;
$level_description       = apply_filters( 'pmpro_level_description', $pmpro_level->description, $pmpro_level );
$level_price_description = pmpro_getLevelCost( $pmpro_level );
if ( ! empty( $_GET['app'] ) ) {
	stm_pa( getallheaders() );
}

if ( class_exists( 'STM_LMS_Templates' ) ) {
	STM_LMS_Templates::show_lms_template(
		'pmpro/checkout-warning',
		array(
			'level_id' => $pmpro_level->id,
		)
	);
}
?>
<h1 class="pmpro_checkout_title"><?php esc_html_e( 'Checkout', 'masterstudy-lms-learning-management-system' ); ?></h1>
<div id="pmpro_level-<?php echo esc_attr( $pmpro_level->id ); ?>" class="pmpro_form_container">
	<form id="pmpro_form" class="pmpro_form"
		action="<?php echo ( ! empty( $_REQUEST['review'] ) ) ? esc_attr( pmpro_url( 'checkout', '?level=' . $pmpro_level->id ) ) : ''; ?>"
		method="post">
		<input type="hidden" id="pmpro_level" name="pmpro_level" value="<?php echo esc_attr( $pmpro_level->id ); ?>" />
		<input type="hidden" id="level" name="level" value="<?php echo esc_attr( $pmpro_level->id ); ?>"/>
		<input type="hidden" id="checkjavascript" name="checkjavascript" value="1"/>
		<div class="pmpro_fields_container">
				<?php
				if ( $pmpro_msg ) {
					?>
					<div id="pmpro_message"
						class="pmpro_message <?php echo esc_attr( $pmpro_msgt ); ?>"><i class="stmlms-info"></i><?php echo wp_kses_post( $pmpro_msg ); ?></div>
					<?php
				} else {
					?>
					<div id="pmpro_message" class="pmpro_message" style="display: none;"></div>
					<?php
				}
				?>

				<?php if ( $pmpro_review ) { ?>
					<p><?php esc_html_e( 'Almost done. Review the membership information and pricing below then <strong>click the "Complete Payment" button</strong> to finish your order.', 'masterstudy-lms-learning-management-system' ); ?></p>
				<?php } ?>

				<?php
				do_action( 'pmpro_checkout_after_pricing_fields' );
				?>

				<?php if ( ! $skip_account_fields && ! $pmpro_review ) { ?>
					<table id="pmpro_user_fields" class="pmpro_checkout" width="100%" cellpadding="0" cellspacing="0"
						border="0">
						<thead>
						<tr>
							<th>
								<h3 class="pmpro_thead-name"><?php esc_html_e( 'Account Information', 'masterstudy-lms-learning-management-system' ); ?></h3>
								<span class="pmpro_thead-msg"><?php esc_html_e( 'Have an account?', 'masterstudy-lms-learning-management-system' ); ?>
									<a href="<?php echo esc_url( add_query_arg( 'redirect_to', pmpro_url( 'checkout', '?level=' . $pmpro_level->id ), STM_LMS_User::login_page_url() ) ); ?>"><?php esc_html_e( 'Log in here', 'masterstudy-lms-learning-management-system' ); ?></a>
								</span>
							</th>
						</tr>
						</thead>
						<tbody>
						<tr class="lp-pmpro-account-info">
							<td class="lp-pmpro-td">
								<div class="lp-pmpro-desc">
									<div class="pmpro_label_wrapper">
										<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pmpro_img/pmpro_user.svg' ); ?>" alt="">
										<label for="username"><?php esc_html_e( 'Username', 'masterstudy-lms-learning-management-system' ); ?></label>
									</div>
									<input id="username" name="username" type="text"
										placeholder="<?php esc_attr_e( 'Enter username', 'masterstudy-lms-learning-management-system' ); ?>"
										class="input <?php echo esc_attr( pmpro_getClassForField( 'username' ) ); ?>" size="30"
										value="<?php echo esc_attr( $username ); ?>"/>
								</div>

								<?php
								do_action( 'pmpro_checkout_after_username' );
								?>

								<div class="lp-pmpro-desc">
									<div class="pmpro_label_wrapper">
										<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pmpro_img/pmpro_email.svg' ); ?>" alt="">
										<label for="bemail"><?php esc_html_e( 'E-mail Address', 'masterstudy-lms-learning-management-system' ); ?></label>
									</div>
									<input id="bemail" name="bemail" placeholder="example@gmail.com"
										type="<?php echo ( esc_attr( $pmpro_email_field_type ) ) ? 'email' : 'text'; ?>"
										class="input <?php echo esc_attr( pmpro_getClassForField( 'bemail' ) ); ?>" size="30"
										value="<?php echo esc_attr( $bemail ); ?>"/>
								</div>
								<?php
								$pmpro_checkout_confirm_email = apply_filters( 'pmpro_checkout_confirm_email', false );
								if ( $pmpro_checkout_confirm_email ) {
									?>
									<div class="lp-pmpro-desc">
										<label for="bconfirmemail"><?php esc_html_e( 'Confirm E-mail Address', 'masterstudy-lms-learning-management-system' ); ?></label>
										<input id="bconfirmemail" name="bconfirmemail"
											type="<?php echo( esc_attr( $pmpro_email_field_type ) ? 'email' : 'text' ); ?>"
											class="input <?php echo esc_attr( pmpro_getClassForField( 'bconfirmemail' ) ); ?>" size="30"
											value="<?php echo esc_attr( $bconfirmemail ); ?>"/>

									</div>
									<?php
								} else {
									?>
									<input type="hidden" name="bconfirmemail_copy" value="1"/>
									<?php
								}
								?>

								<?php
								do_action( 'pmpro_checkout_after_email' );
								?>

								<div class="lp-pmpro-desc">
									<div class="pmpro_label_wrapper">
										<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pmpro_img/pmpro_padlock.svg' ); ?>" alt="">
										<label for="password"><?php esc_html_e( 'Password', 'masterstudy-lms-learning-management-system' ); ?></label>
									</div>
									<div class="pmpro_pass_wrapper">
										<a href="#" class="pmpro_show_pass">
											<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pmpro_img/show_password.svg' ); ?>" alt="">
										</a>
										<input id="password" name="password" type="password"
											placeholder="<?php esc_html_e( 'Enter password', 'masterstudy-lms-learning-management-system' ); ?>"
											class="input <?php echo esc_attr( pmpro_getClassForField( 'password' ) ); ?>" size="30"
											value="<?php echo esc_attr( $password ); ?>"/>
									</div>
								</div>
								<?php
								$pmpro_checkout_confirm_password = apply_filters( 'pmpro_checkout_confirm_password', true );
								if ( $pmpro_checkout_confirm_password ) {
									?>
									<div class="lp-pmpro-desc">
										<div class="pmpro_label_wrapper">
											<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pmpro_img/pmpro_padlock.svg' ); ?>" alt="">
											<label for="password2"><?php esc_html_e( 'Confirm Password', 'masterstudy-lms-learning-management-system' ); ?></label>
										</div>
										<div class="pmpro_pass_wrapper">
											<a href="#" class="pmpro_show_pass">
												<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pmpro_img/show_password.svg' ); ?>" alt="">
											</a>
											<input id="password2" name="password2" type="password"
												placeholder="<?php esc_html_e( 'Repeat password', 'masterstudy-lms-learning-management-system' ); ?>"
												class="input <?php echo esc_attr( pmpro_getClassForField( 'password2' ) ); ?>" size="30"
												value="<?php echo esc_attr( $password2 ); ?>"/>
										</div>
									</div>
									<?php
								} else {
									?>
									<input type="hidden" name="password2_copy" value="1"/>
									<?php
								}
								?>

								<?php
								do_action( 'pmpro_checkout_after_password' );
								?>

								<div class="pmpro_hidden">
									<label for="fullname"><?php esc_html_e( 'Full Name', 'masterstudy-lms-learning-management-system' ); ?></label>
									<input id="fullname" name="fullname" type="text"
										class="input <?php echo esc_attr( pmpro_getClassForField( 'fullname' ) ); ?>" size="30" value=""/>
									<strong><?php esc_html_e( 'LEAVE THIS BLANK', 'masterstudy-lms-learning-management-system' ); ?></strong>
								</div>

								<div class="pmpro_captcha">
									<?php
									global $recaptcha, $recaptcha_publickey;
									if ( 2 === (int) $recaptcha || ( 1 === (int) $recaptcha && pmpro_isLevelFree( $pmpro_level ) ) ) {
										echo wp_kses_post( pmpro_recaptcha_get_html( $recaptcha_publickey, null, true ) );
									}
									?>
								</div>

								<?php
								do_action( 'pmpro_checkout_after_captcha' );
								?>

							</td>
						</tr>
						</tbody>
					</table>
					<?php
				} elseif ( $current_user->ID && ! $pmpro_review ) {
					if ( class_exists( 'STM_LMS_User' ) ) {
						$url  = STM_LMS_User::login_page_url();
						$text = esc_html__( 'log in', 'masterstudy-lms-learning-management-system' );
					} else {
						$url  = wp_logout_url();
						$text = esc_html__( 'log out now', 'masterstudy-lms-learning-management-system' );
					}
					?>
					<div id="pmpro_account_loggedin" class="message message-notice">
						<i class="stmlms-info"></i>
						<p><?php printf( wp_kses_post( __( 'You are logged in as <strong>%1$s</strong>. If you would like to use a different account for this membership, <a href="%2$s">%3$s.</a>', 'masterstudy-lms-learning-management-system' ) ), esc_html( $current_user->user_login ), esc_url( $url ), wp_kses_post( $text ) ); ?></p>
					</div>
				<?php } ?>

				<?php
				do_action( 'pmpro_checkout_after_user_fields' );
				?>

				<?php
				do_action( 'pmpro_checkout_boxes' );
				?>

				<?php if ( 'paypal' === pmpro_getGateway() && empty( $pmpro_review ) ) { ?>
					<table id="pmpro_payment_method" class="pmpro_checkout top1em" width="100%" cellpadding="0" cellspacing="0"
						border="0"
						<?php if ( ! $pmpro_requirebilling ) { ?>
							style="display: none;"
						<?php } ?>
					>
						<thead>
						<tr>
							<th><h3><?php esc_html_e( 'Choose Payment Method', 'masterstudy-lms-learning-management-system' ); ?></h3></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td>
								<div class="gateways_wrapper">
									<span class="gateway_paypal">
										<input type="radio" name="gateway" value="paypal"
											<?php
											if ( ! $gateway || 'paypal' === $gateway ) {
												?>
													checked="checked"<?php } ?> />
										<a href="javascript:void(0);"
										class="pmpro_radio"><?php esc_html_e( 'Check Out with a Credit Card Here', 'masterstudy-lms-learning-management-system' ); ?></a>
									</span>
										<span class="gateway_paypalexpress">
										<input type="radio" name="gateway" value="paypalexpress"
											<?php
											if ( 'paypalexpress' === $gateway ) {
												?>
													checked="checked"<?php } ?> />
										<a href="javascript:void(0);"
										class="pmpro_radio"><?php esc_html_e( 'Check Out with PayPal', 'masterstudy-lms-learning-management-system' ); ?></a>
									</span>
								</div>
							</td>
						</tr>
						</tbody>
					</table>
				<?php } ?>

				<?php
				$pmpro_include_billing_address_fields = apply_filters( 'pmpro_include_billing_address_fields', true );
				if ( $pmpro_include_billing_address_fields ) {
					?>
					<table id="pmpro_billing_address_fields" class="pmpro_checkout top1em" width="100%" cellpadding="0"
						cellspacing="0" border="0"
						<?php if ( ! $pmpro_requirebilling || apply_filters( 'pmpro_hide_billing_address_fields', false ) ) { ?>
							style="display: none;"
						<?php } ?>
					>
						<thead>
						<tr>
							<th><h3><?php esc_html_e( 'Billing Address', 'masterstudy-lms-learning-management-system' ); ?></h3></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td>
								<div class="stm_lms_checkout_fields">
									<div>
										<label for="bfirstname"><?php esc_html_e( 'First Name', 'masterstudy-lms-learning-management-system' ); ?></label>
										<input id="bfirstname" name="bfirstname" type="text"
											placeholder="<?php esc_html_e( 'Enter name', 'masterstudy-lms-learning-management-system' ); ?>"
											class="input <?php echo esc_attr( pmpro_getClassForField( 'bfirstname' ) ); ?>" size="30"
											value="<?php echo esc_attr( $bfirstname ); ?>"/>
									</div>
									<div>
										<label for="blastname"><?php esc_html_e( 'Last Name', 'masterstudy-lms-learning-management-system' ); ?></label>
										<input id="blastname" name="blastname" type="text"
											placeholder="<?php esc_html_e( 'Enter name', 'masterstudy-lms-learning-management-system' ); ?>"
											class="input <?php echo esc_attr( pmpro_getClassForField( 'blastname' ) ); ?>" size="30"
											value="<?php echo esc_attr( $blastname ); ?>"/>
									</div>
									<div>
										<label for="baddress1"><?php esc_html_e( 'Address 1', 'masterstudy-lms-learning-management-system' ); ?></label>
										<input id="baddress1" name="baddress1" type="text"
											placeholder="<?php esc_html_e( 'Enter address', 'masterstudy-lms-learning-management-system' ); ?>"
											class="input <?php echo esc_attr( pmpro_getClassForField( 'baddress1' ) ); ?>" size="30"
											value="<?php echo esc_attr( $baddress1 ); ?>"/>
									</div>
									<div>
										<label for="baddress2"><?php esc_html_e( 'Address 2', 'masterstudy-lms-learning-management-system' ); ?></label>
										<input id="baddress2" name="baddress2" type="text"
											placeholder="<?php esc_html_e( 'Enter address', 'masterstudy-lms-learning-management-system' ); ?>"
											class="input <?php echo esc_attr( pmpro_getClassForField( 'baddress2' ) ); ?>" size="30"
											value="<?php echo esc_attr( $baddress2 ); ?>"/>
									</div>

									<?php
									$longform_address = apply_filters( 'pmpro_longform_address', true );
									if ( $longform_address ) {
										?>
										<div>
											<label for="bcity"><?php esc_html_e( 'City', 'masterstudy-lms-learning-management-system' ); ?></label>
											<input id="bcity" name="bcity" type="text"
												placeholder="<?php esc_html_e( 'Enter city', 'masterstudy-lms-learning-management-system' ); ?>"
												class="input <?php echo esc_attr( pmpro_getClassForField( 'bcity' ) ); ?>" size="30"
												value="<?php echo esc_attr( $bcity ); ?>"/>
										</div>
										<div>
											<label for="bstate"><?php esc_html_e( 'State', 'masterstudy-lms-learning-management-system' ); ?></label>
											<input id="bstate" name="bstate" type="text"
												placeholder="<?php esc_html_e( 'Enter state', 'masterstudy-lms-learning-management-system' ); ?>"
												class="input <?php echo esc_attr( pmpro_getClassForField( 'bstate' ) ); ?>" size="30"
												value="<?php echo esc_attr( $bstate ); ?>"/>
										</div>
										<div>
											<label for="bzipcode"><?php esc_html_e( 'Postal Code', 'masterstudy-lms-learning-management-system' ); ?></label>
											<input id="bzipcode" name="bzipcode" type="text"
												placeholder=""
												class="input <?php echo esc_attr( pmpro_getClassForField( 'bzipcode' ) ); ?>" size="30"
												value="<?php echo esc_attr( $bzipcode ); ?>"/>
										</div>
										<?php
									} else {
										?>
										<div>
											<label for="bcity_state_zip"><?php esc_html_e( 'City, State Zip', 'masterstudy-lms-learning-management-system' ); ?></label>
											<input id="bcity" name="bcity" type="text"
												class="input <?php echo esc_attr( pmpro_getClassForField( 'bcity' ) ); ?>" size="14"
												value="<?php echo esc_attr( $bcity ); ?>"/>,
											<?php
											$state_dropdowns = apply_filters( 'pmpro_state_dropdowns', false );
											if ( true === $state_dropdowns || 'names' === $state_dropdowns ) {
												global $pmpro_states;
												?>
												<select name="bstate" class=" <?php echo esc_attr( pmpro_getClassForField( 'bstate' ) ); ?>">
													<option value="">--</option>
													<?php
													foreach ( $pmpro_states as $ab => $st ) {
														?>
														<option value="<?php echo esc_attr( $ab ); ?>"
																<?php
																if ( $ab === $bstate ) {
																	?>
																	selected="selected"<?php } ?>><?php echo esc_html( $st ); ?></option>
													<?php } ?>
												</select>
												<?php
											} elseif ( 'abbreviations' === $state_dropdowns ) {
												global $pmpro_states_abbreviations;
												?>
												<select name="bstate" class=" <?php echo esc_attr( pmpro_getClassForField( 'bstate' ) ); ?>">
													<option value="">--</option>
													<?php
													foreach ( $pmpro_states_abbreviations as $ab ) {
														?>
														<option value="<?php echo esc_attr( $ab ); ?>"
																<?php
																if ( $ab === $bstate ) {
																	?>
																	selected="selected"<?php } ?>><?php echo esc_html( $ab ); ?></option>
													<?php } ?>
												</select>
												<?php
											} else {
												?>
												<input id="bstate" name="bstate" type="text"
													class="input <?php echo esc_attr( pmpro_getClassForField( 'bstate' ) ); ?>" size="2"
													value="<?php echo esc_attr( $bstate ); ?>"/>
												<?php
											}
											?>
											<input id="bzipcode" name="bzipcode" type="text"
												class="input <?php echo esc_attr( pmpro_getClassForField( 'bzipcode' ) ); ?>" size="5"
												value="<?php echo esc_attr( $bzipcode ); ?>"/>
										</div>
										<?php
									}
									?>

									<?php
									$show_country = apply_filters( 'pmpro_international_addresses', true );
									if ( $show_country ) {
										?>
										<div>
											<label for="bcountry"><?php esc_html_e( 'Country', 'masterstudy-lms-learning-management-system' ); ?></label>
											<select name="bcountry" class=" <?php echo esc_attr( pmpro_getClassForField( 'bcountry' ) ); ?>">
												<?php
												global $pmpro_countries, $pmpro_default_country;
												if ( ! $bcountry ) {
													$bcountry = $pmpro_default_country;
												}
												foreach ( $pmpro_countries as $abbr => $country ) {
													?>
													<option value="<?php echo esc_attr( $abbr ); ?>"
															<?php
															if ( $abbr === $bcountry ) {
																?>
																selected="selected"<?php } ?>><?php echo esc_html( $country ); ?></option>
													<?php
												}
												?>
											</select>
										</div>
										<?php
									} else {
										?>
										<input type="hidden" name="bcountry" value="US"/>
										<?php
									}
									?>
									<div>
										<label for="bphone"><?php esc_html_e( 'Phone', 'masterstudy-lms-learning-management-system' ); ?></label>
										<input id="bphone" name="bphone" type="text"
											placeholder="(1) 212-535-4721"
											class="input <?php echo esc_attr( pmpro_getClassForField( 'bphone' ) ); ?>" size="30"
											value="<?php echo esc_attr( formatPhone( $bphone ) ); ?>"/>
									</div>
									<?php if ( $skip_account_fields ) { ?>
										<?php
										if ( $current_user->ID ) {
											if ( ! $bemail && $current_user->user_email ) {
												$bemail = $current_user->user_email;
											}
											if ( ! $bconfirmemail && $current_user->user_email ) {
												$bconfirmemail = $current_user->user_email;
											}
										}
										?>
										<div>
											<label for="bemail"><?php esc_html_e( 'E-mail Address', 'masterstudy-lms-learning-management-system' ); ?></label>
											<input id="bemail" name="bemail"
												type="<?php echo( esc_attr( $pmpro_email_field_type ) ? 'email' : 'text' ); ?>"
												class="input <?php echo esc_attr( pmpro_getClassForField( 'bemail' ) ); ?>" size="30"
												value="<?php echo esc_attr( $bemail ); ?>"/>
										</div>
										<?php
										$pmpro_checkout_confirm_email = apply_filters( 'pmpro_checkout_confirm_email', true );
										if ( $pmpro_checkout_confirm_email ) {
											?>
											<div>
												<label for="bconfirmemail"><?php esc_html_e( 'Confirm E-mail', 'masterstudy-lms-learning-management-system' ); ?></label>
												<input id="bconfirmemail" name="bconfirmemail"
													type="<?php echo( esc_attr( $pmpro_email_field_type ) ? 'email' : 'text' ); ?>"
													class="input <?php echo esc_attr( pmpro_getClassForField( 'bconfirmemail' ) ); ?>"
													size="30" value="<?php echo esc_attr( $bconfirmemail ); ?>"/>

											</div>
											<?php
										} else {
											?>
											<input type="hidden" name="bconfirmemail_copy" value="1"/>
											<?php
										}
										?>
									<?php } ?>

								</div>
							</td>
						</tr>
						</tbody>
					</table>
				<?php } ?>

				<?php do_action( 'pmpro_checkout_after_billing_fields' ); ?>

				<?php
				$pmpro_accepted_credit_cards        = pmpro_getOption( 'accepted_credit_cards' );
				$pmpro_accepted_credit_cards        = explode( ',', $pmpro_accepted_credit_cards );
				$pmpro_accepted_credit_cards_string = pmpro_implodeToEnglish( $pmpro_accepted_credit_cards );
				?>

				<?php
				$pmpro_include_payment_information_fields = apply_filters( 'pmpro_include_payment_information_fields', true );
				if ( $pmpro_include_payment_information_fields ) {
					?>
					<table id="pmpro_payment_information_fields" class="pmpro_checkout top1em" width="100%" cellpadding="0"
						cellspacing="0" border="0"
						<?php
						if ( ! $pmpro_requirebilling || apply_filters( 'pmpro_hide_payment_information_fields', false ) ) {
							?>
							style="display: none;"<?php } ?>>
						<thead>
						<tr>
							<th class="pmpro_payment_info">
								<h3><?php esc_html_e( 'Payment Information', 'masterstudy-lms-learning-management-system' ); ?></h3>
								<div class="pmpro_cards_container">
									<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pmpro_img/visa_card.svg' ); ?>" alt="">
									<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pmpro_img/mastercard.svg' ); ?>" alt="">
									<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pmpro_img/americanexpress_card.svg' ); ?>" alt="">
									<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pmpro_img/discover_card.svg' ); ?>" alt="">
								</div>
							</th>
						</tr>
						</thead>
						<tbody>
						<tr valign="top">
							<td>
								<div class="stm_lms_pmpro_payment_info">
									<?php
									$pmpro_include_cardtype_field = apply_filters( 'pmpro_include_cardtype_field', false );
									if ( $pmpro_include_cardtype_field ) {
										?>
										<div class="pmpro_payment-card-type">
											<label for="CardType"><?php esc_html_e( 'Card Type', 'masterstudy-lms-learning-management-system' ); ?></label>
											<select id="CardType" name="CardType" class=" <?php echo esc_attr( pmpro_getClassForField( 'CardType' ) ); ?>">
												<?php foreach ( $pmpro_accepted_credit_cards as $cc ) { ?>
													<option value="<?php echo esc_attr( $cc ); ?>"
															<?php
															if ( $CardType === $cc ) {
																?>
																selected="selected"<?php } ?>><?php echo esc_html( $cc ); ?></option>
												<?php } ?>
											</select>
										</div>
										<?php
									} else {
										?>
									<input type="hidden" id="CardType" name="CardType"
										value="<?php echo esc_attr( $CardType ); ?>"/>
										<script>
											<!--
											jQuery(document).ready(function () {
												jQuery('#AccountNumber').validateCreditCard(function (result) {
													var cardtypenames = {
														"amex": "American Express",
														"diners_club_carte_blanche": "Diners Club Carte Blanche",
														"diners_club_international": "Diners Club International",
														"discover": "Discover",
														"jcb": "JCB",
														"laser": "Laser",
														"maestro": "Maestro",
														"mastercard": "Mastercard",
														"visa": "Visa",
														"visa_electron": "Visa Electron"
													};

													if (result.card_type)
														jQuery('#CardType').val(cardtypenames[result.card_type.name]);
													else
														jQuery('#CardType').val('Unknown Card Type');
												});
											});
											-->
										</script>
										<?php
									}
									?>

									<div class="pmpro_payment-account-number">
										<label for="AccountNumber"><?php esc_html_e( 'Card Number', 'masterstudy-lms-learning-management-system' ); ?></label>
										<input id="AccountNumber" name="AccountNumber"
											placeholder="0000 0000 0000 0000"
											class="input <?php echo esc_attr( pmpro_getClassForField( 'AccountNumber' ) ); ?>" type="text"
											size="25" value="<?php echo esc_attr( $AccountNumber ); ?>"
											data-encrypted-name="number"
											autocomplete="off"/>
									</div>

									<div class="pmpro_payment-expiration">
										<label for="ExpirationMonth"><?php esc_html_e( 'Expiration Date', 'masterstudy-lms-learning-management-system' ); ?></label>
										<div class="pmpro-expiration-wrapper">
											<div class="pmpro_expiration_container">
												<select id="ExpirationMonth" name="ExpirationMonth" class=" <?php echo esc_attr( pmpro_getClassForField( 'ExpirationMonth' ) ); ?>">
													<option value="01"<?php selected( $ExpirationMonth, '01' ); ?>>01</option>
													<option value="02"<?php selected( $ExpirationMonth, '02' ); ?>>02</option>
													<option value="03"<?php selected( $ExpirationMonth, '03' ); ?>>03</option>
													<option value="04"<?php selected( $ExpirationMonth, '04' ); ?>>04</option>
													<option value="05"<?php selected( $ExpirationMonth, '05' ); ?>>05</option>
													<option value="06"<?php selected( $ExpirationMonth, '06' ); ?>>06</option>
													<option value="07"<?php selected( $ExpirationMonth, '07' ); ?>>07</option>
													<option value="08"<?php selected( $ExpirationMonth, '08' ); ?>>08</option>
													<option value="09"<?php selected( $ExpirationMonth, '09' ); ?>>09</option>
													<option value="10"<?php selected( $ExpirationMonth, '10' ); ?>>10</option>
													<option value="11"<?php selected( $ExpirationMonth, '11' ); ?>>11</option>
													<option value="12"<?php selected( $ExpirationMonth, '12' ); ?>>12</option>
												</select>
											</div>
											<span class="pmpro_expiration_divider">/</span>
											<div class="pmpro_expiration_container">
												<select id="ExpirationYear" name="ExpirationYear"
													class=" <?php echo esc_attr( pmpro_getClassForField( 'ExpirationYear' ) ); ?>">
													<?php
													$current_year = (int) date_i18n( 'Y' );
													$end_year     = $current_year + 10;
													for ( $i = $current_year; $i < $end_year; $i++ ) {
														?>
														<option value="<?php echo esc_attr( $i ); ?>"
															<?php
															if ( (int) $ExpirationYear === $i ) {
																?>
																selected="selected"<?php } ?>><?php echo esc_html( $i ); ?></option>
														<?php
													}
													?>
												</select>
											</div>
										</div>
									</div>

									<?php
									$pmpro_show_cvv = apply_filters( 'pmpro_show_cvv', true );
									if ( $pmpro_show_cvv ) {
										?>
										<div class="pmpro_payment-cvv">
											<div class="pmpro-payment_label_wrapper">
												<label for="CVV"><?php esc_html_e( 'CVV', 'masterstudy-lms-learning-management-system' ); ?></label>
												<small><a href="javascript:void(0);"
														onclick="javascript:window.open('<?php echo esc_url( pmpro_https_filter( PMPRO_URL ) ); ?>/pages/popup-cvv.html','cvv','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=600, height=475');"><?php esc_html_e( "What's this?", 'masterstudy-lms-learning-management-system' ); ?></a>
												</small>
											</div>
											<input class="input" id="CVV" name="CVV" type="text" size="4"
												value="<?php ( ! empty( $_REQUEST['CVV'] ) ) ? esc_attr( $_REQUEST['CVV'] ) : ''; ?>"
												class="<?php echo esc_attr( pmpro_getClassForField( 'CVV' ) ); ?>"/>
										</div>
									<?php } ?>

									<?php if ( $pmpro_show_discount_code ) { ?>
										<div class="pmpro_payment-discount-code">
											<label for="pmpro_discount_code"><?php esc_html_e( 'Discount Code', 'masterstudy-lms-learning-management-system' ); ?></label>
											<input class="input <?php echo esc_attr( pmpro_get_element_class( 'input pmpro_alter_price', 'discount_code' ) ); ?>"
												id="pmpro_discount_code" name="pmpro_discount_code" type="text" size="10"
												value="<?php echo esc_attr( $discount_code ); ?>"/>
											<input type="button" id="discount_code_button" name="discount_code_button"
												value="<?php esc_html_e( 'Apply', 'masterstudy-lms-learning-management-system' ); ?>"/>
												<p id="discount_code_message" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_message', 'discount_code_message' ) ); ?>" style="display: none;"></p>
										</div>
									<?php } ?>
									<?php
									$sslseal = pmpro_getOption( 'sslseal' );
									if ( $sslseal ) {
										?>
										<div class="pmpro_sslseal"><?php echo esc_attr( stripslashes( $sslseal ) ); ?></div>
										<?php
									}
									?>
								</div>
							</td>
						</tr>
						</tbody>
					</table>
				<?php } ?>

				<?php do_action( 'pmpro_checkout_after_payment_information_fields' ); ?>

				<?php
				if ( $tospage && ! $pmpro_review ) {
					?>
					<table id="pmpro_tos_fields" class="pmpro_checkout top1em" width="100%" cellpadding="0" cellspacing="0"
						border="0">
						<thead>
						<tr>
							<th><?php echo esc_html( $tospage->post_title ); ?></th>
						</tr>
						</thead>
						<tbody>
						<tr class="odd">
							<td>
								<div id="pmpro_license">
									<?php echo wp_kses_post( wpautop( do_shortcode( $tospage->post_content ) ) ); ?>
								</div>
								<input type="checkbox" name="tos" value="1" id="tos"/>
								<label class="pmpro_normal pmpro_clickable"
									for="tos"><?php printf( esc_html__( 'I agree to the %s', 'masterstudy-lms-learning-management-system' ), esc_html( $tospage->post_title ) ); ?></label>
							</td>
						</tr>
						</tbody>
					</table>
					<?php
				}
				?>

				<?php do_action( 'pmpro_checkout_after_tos_fields' ); ?>

				<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_captcha', 'pmpro_captcha' );  // phpcs:ignore ?>">
					<?php
					global $recaptcha, $recaptcha_publickey;
					if ( 2 === (int) $recaptcha || ( 1 === (int) $recaptcha && pmpro_isLevelFree( $pmpro_level ) ) ) {
						echo pmpro_recaptcha_get_html( $recaptcha_publickey, null, true );  // phpcs:ignore
					}
					?>
				</div> <!-- end pmpro_captcha -->

				<?php
				do_action( 'pmpro_checkout_after_captcha' );
				?>
				<?php do_action( 'pmpro_checkout_before_submit_button' ); ?>

				<div class="pmpro_submit">
					<?php if ( $pmpro_review ) { ?>

						<span id="pmpro_submit_span">
						<input type="hidden" name="confirm" value="1"/>
						<input type="hidden" name="token" value="<?php echo esc_attr( $pmpro_paypal_token ); ?>"/>
						<input type="hidden" name="gateway" value="<?php echo esc_attr( $gateway ); ?>"/>
						<input id="pmpro_btn-submit" type="submit" class="pmpro_btn pmpro_btn-submit-checkout"
							value="<?php esc_html_e( 'Complete Payment', 'masterstudy-lms-learning-management-system' ); ?> &raquo;"/>
					</span>

					<?php } else { ?>

						<?php
						$pmpro_checkout_default_submit_button = apply_filters( 'pmpro_checkout_default_submit_button', true );
						if ( $pmpro_checkout_default_submit_button ) {
							?>
							<span id="pmpro_submit_span">
							<input type="hidden" name="submit-checkout" value="1"/>
							<input id="pmpro_btn-submit" type="submit" class="btn btn-default pmpro_btn-submit-checkout" value="
							<?php
							if ( $pmpro_requirebilling ) {
								esc_html_e( 'Submit and Checkout', 'masterstudy-lms-learning-management-system' );
							} else {
								esc_html_e( 'Submit and Confirm', 'masterstudy-lms-learning-management-system' );
							}
							?>
							"/>
						</span>
							<?php
						}
						?>

					<?php } ?>

					<span id="pmpro_processing_message" style="visibility: hidden;">
					<?php
					$processing_message = apply_filters( 'pmpro_processing_message', __( 'Processing...', 'masterstudy-lms-learning-management-system' ) );
					echo wp_kses_post( $processing_message );
					?>
				</span>
				</div>
		</div>
		<div class="pmpro_pricing_fields_container">
				<table id="pmpro_pricing_fields" class="pmpro_checkout" width="100%" cellpadding="0" cellspacing="0" border="0">
					<thead>
					<tr>
						<th>
							<h3 class="pmpro_thead-name"><?php esc_html_e( 'Membership Plan', 'masterstudy-lms-learning-management-system' ); ?></h3>
						</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td class="lp-pmpro-td">
							<div class="lp-pmpro-name">
								<h4><?php echo esc_html( $pmpro_level->name ); ?></h4>
							<?php
							if ( pmpro_isLevelRecurring( $pmpro_level ) ) {
								if ( 1 < $pmpro_level->cycle_number ) {
									?>
									<span><?php echo sprintf( wp_kses_post( __( '/ per %1$d %2$s', 'masterstudy-lms-learning-management-system' ) ), esc_html( $pmpro_level->cycle_number ), esc_html( $level_period_count ) ); ?></span>
								<?php } else { ?>
									<span><?php echo sprintf( wp_kses_post( __( '/ per %s', 'masterstudy-lms-learning-management-system' ) ), esc_html( $level_period_count ) ); ?></span>
									<?php
								}
							}
							if ( count( $pmpro_levels ) > 1 ) {
								?>
								<a class="pmpro_change_level" href="<?php echo esc_url( pmpro_url( 'levels' ) ); ?>">
									<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pmpro_img/change_plan.svg' ); ?>" alt="">
								</a>
							<?php } ?>
							</div>
							<div class="lp-pmpro-content">
								<?php if ( $discount_code && pmpro_checkDiscountCode( $discount_code ) ) { ?>
									<p class="pmpro_level_discount_applied"><?php echo wp_kses_post( printf( __( 'The <strong>%s</strong> code has been applied to your order.', 'masterstudy-lms-learning-management-system' ), esc_html( $discount_code ) ) ); ?></p>
								<?php } ?>
							<div class="lp-pmpro-plan-included">
								<?php if ( ! empty( $courses_included ) ) { ?>
									<div class="lp-pmpro-plan-included--wrapper">
										<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pmpro_img/pmpro_checkmark.svg' ); ?>" alt="">
										<label><?php printf( esc_html__( 'Courses included: %s', 'masterstudy-lms-learning-management-system' ), esc_html( $courses_included ) ); ?></label>
									</div>
								<?php } ?>
								<?php if ( ! empty( $featured_quotas ) ) { ?>
									<div class="lp-pmpro-plan-included--wrapper">
										<img src="<?php echo esc_url( STM_LMS_URL . 'assets/img/pmpro_img/pmpro_checkmark.svg' ); ?>" alt="">
										<label><?php printf( esc_html__( 'Featured courses quote included: %d', 'masterstudy-lms-learning-management-system' ), esc_html( $featured_quotas ) ); ?></label>
									</div>
								<?php } ?>
							</div>
							<?php if ( ! empty( $level_description ) ) { ?>
								<div class="lp-pmpro-desc lp-pmpro-user-description">
									<?php echo wp_kses_post( $pmpro_level->description ); ?>
								</div>
							<?php } ?>
								<div id="pmpro_level_cost">
									<div class="lp-pmpro-desc">
										<label><?php esc_html_e( 'Price:', 'masterstudy-lms-learning-management-system' ); ?></label>
										<div class="pmpro-level-price">
											<?php
												echo wp_kses_post( $level_price );
											if ( pmpro_isLevelRecurring( $pmpro_level ) ) {
												if ( 1 < $pmpro_level->cycle_number ) {
													echo sprintf( wp_kses_post( __( ' / per %1$d %2$s*', 'masterstudy-lms-learning-management-system' ) ), esc_html( $pmpro_level->cycle_number ), esc_html( $level_period_count ) );
												} else {
													echo sprintf( wp_kses_post( __( ' / per %s*', 'masterstudy-lms-learning-management-system' ) ), esc_html( $level_period_count ) );
												}
											}
											?>
										</div>
									</div>
								</div>

							<?php do_action( 'pmpro_checkout_after_level_cost' ); ?>

							<?php if ( pmpro_isLevelRecurring( $pmpro_level ) ) { ?>
								<div id="level_price_description" class="lp-pmpro-desc">
									<p><?php echo '* ' . wp_kses_post( $level_price_description ); ?></p>
									<?php if ( $pmpro_level->expiration_period ) { ?>
										<p>
											<?php esc_html_e( 'Expires after:', 'masterstudy-lms-learning-management-system' ); ?>
											<?php echo sprintf( wp_kses_post( '%1$d %2$s', 'masterstudy-lms-learning-management-system' ), esc_html( $pmpro_level->expiration_number ), wp_kses_post( pmpro_translate_billing_period( $pmpro_level->expiration_period, $pmpro_level->expiration_number ) ) ); ?>
										</p>
									<?php } ?>
								</div>
								<?php
							}

							if ( $pmpro_show_discount_code ) {
								?>
								<div class="pmpro_payment-discount-code <?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_actions' ) ); ?>">
									<?php
									if ( $discount_code && ! $pmpro_review ) {
										?>
										<span id="other_discount_code_p"><button type="button" id="other_discount_code_toggle"><?php esc_html_e( 'Click here to change your discount code', 'masterstudy-lms-learning-management-system' ); ?></button></span>
										<?php
									} elseif ( ! $pmpro_review ) {
										?>
										<span id="other_discount_code_p"><?php esc_html_e( 'Do you have a discount code?', 'masterstudy-lms-learning-management-system' ); ?> <button type="button" id="other_discount_code_toggle"><?php esc_html_e( 'Click here to enter your discount code', 'masterstudy-lms-learning-management-system' ); ?></button></span>
										<?php
									} elseif ( $pmpro_review && $discount_code ) {
										?>
										<span><strong><?php esc_html_e( 'Discount Code', 'masterstudy-lms-learning-management-system' ); ?>:</strong> <?php echo esc_html( $discount_code ); ?></span>
										<?php
									}
									?>
									<div id="other_discount_code_fields" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_field pmpro_form_field-text' ) ); ?>" style="display: none;">
										<label for="pmpro_other_discount_code" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_label' ) ); ?>"><?php esc_html_e( 'Discount Code', 'masterstudy-lms-learning-management-system' ); ?></label>
										<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_fields-inline' ) ); ?>">
											<input id="pmpro_other_discount_code" name="pmpro_other_discount_code" type="text" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_input pmpro_form_input-text pmpro_alter_price', 'other_discount_code' ) ); ?>" value="<?php echo esc_attr( $discount_code ); ?>" />
											<input aria-label="<?php esc_html_e( 'Apply discount code', 'masterstudy-lms-learning-management-system' ); ?>" type="button" name="other_discount_code_button" id="other_discount_code_button" value="<?php esc_attr_e( 'Apply', 'masterstudy-lms-learning-management-system' ); ?>" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_btn pmpro_btn-submit-discount-code', 'other_discount_code_button' ) ); ?>" />
										</div>
									</div>
								</div> <!-- end pmpro_card_actions -->
								<?php
							}
							?>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
		</div>
		<?php wp_nonce_field( 'pmpro_checkout_nonce', 'pmpro_checkout_nonce' ); ?>
	</form>

	<?php do_action( 'pmpro_checkout_after_form' ); ?>

</div> <!-- end pmpro_level-ID -->

<script>
	<!--
	// Find ALL <form> tags on your page
	jQuery('form').submit(function () {
		// On submit disable its submit button
		jQuery('input[type=submit]', this).attr('disabled', 'disabled');
		jQuery('input[type=image]', this).attr('disabled', 'disabled');
		jQuery('#pmpro_processing_message').css('visibility', 'visible');
	});

	//iOS Safari fix (see: http://stackoverflow.com/questions/20210093/stop-safari-on-ios7-prompting-to-save-card-data)
	var userAgent = window.navigator.userAgent;
	if (userAgent.match(/iPad/i) || userAgent.match(/iPhone/i)) {
		jQuery('input[type=submit]').on('click', function () {
			try {
				jQuery("input[type=password]").attr("type", "hidden");
			} catch (ex) {
				try {
					jQuery("input[type=password]").prop("type", "hidden");
				} catch (ex) {
				}
			}
		});
	}

	//add required to required fields
	jQuery('.pmpro_required').after('<span class="pmpro_asterisk"> <abbr title="Required Field">*</abbr></span>');

	//unhighlight error fields when the user edits them
	jQuery('.pmpro_error').on("change keyup input", function () {
		jQuery(this).removeClass('pmpro_error');
	});

	jQuery(document).ready(function($) {
		$('#discount_code_button').on('click', function() {
			applyDiscountCode();
		});

		$('#pmpro_discount_code').on('keydown', function(e) {
			if (e.keyCode === 13) {
				e.preventDefault();
				applyDiscountCode();
			}
		});
	});

	function applyDiscountCode() {
		var code = jQuery('#pmpro_discount_code').val();
		var level_id = jQuery('#pmpro_level').val();
		if (!level_id) {
			level_id = jQuery('#level').val();
		}

		if (code) {
			jQuery('.pmpro_discount_code_msg').hide();
			jQuery('#discount_code_message').hide().html('');
			jQuery('#discount_code_button').attr('disabled', 'disabled');

			jQuery.ajax({
				url: pmpro.ajaxurl,
				type: 'GET',
				timeout: pmpro.ajax_timeout,
				dataType: 'html',
				data: {
					action: 'applydiscountcode',
					code: code,
					pmpro_level: level_id,
					msgfield: 'pmpro_message'
				},
				error: function() {
					jQuery('#discount_code_button').removeAttr('disabled');
				},
				success: function(responseHTML) {
					if (responseHTML === 'error') {
						return;
					} else {
						jQuery('#discount_code_message').html(responseHTML).show();
						jQuery('#pmpro_message').html(responseHTML);
					}
					jQuery('#discount_code_button').removeAttr('disabled');
				}
			});
		}
	}

	//hide apply button if a discount code was passed in
	<?php if ( ! empty( $_REQUEST['discount_code'] ) ) { ?>
		jQuery('#discount_code_button').hide();
		jQuery('#discount_code').on('change keyup', function () {
			jQuery('#discount_code_button').show();
		});
	<?php } ?>
	-->
</script>
<script>
	<!--
	//add javascriptok hidden field to checkout
	jQuery("input[name=submit-checkout]").after('<input type="hidden" name="javascriptok" value="1" />');
	-->
</script>
<script>
function show_password(event) {
		event.preventDefault();
		let input_pass = this.nextElementSibling;
		if (input_pass.type === "password") {
			input_pass.type = "text";
		} else {
			input_pass.type = "password";
		}
	}
	let show_pass_buttons = document.querySelectorAll(".pmpro_show_pass");
	for (let i = 0; i < show_pass_buttons.length; i++) {
		show_pass_buttons[i].addEventListener('click', show_password);
	}
</script>
