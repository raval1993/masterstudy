<?php
use MasterStudy\Lms\Plugin\Addons;
$is_subscription_enabled = is_ms_lms_addon_enabled( Addons::SUBSCRIPTIONS );
$is_plus_enabled         = defined( 'STM_LMS_PLUS_ENABLED' ) && STM_LMS_PLUS_ENABLED;
$go_pro_url              = admin_url( 'admin.php?page=stm-lms-go-pro' );
?>

<script type="text/javascript">
	<?php
	ob_start();
	require STM_LMS_PATH . '/settings/payments/components/payments.php';
	$template = preg_replace( "/\r|\n/", '', addslashes( ob_get_clean() ) );
	?>
	const IS_SUBS_ENABLED = <?php echo $is_subscription_enabled ? 'true' : 'false'; ?>;
	const IS_PLUS_ENABLED = <?php echo $is_plus_enabled ? 'true' : 'false'; ?>;

	Vue.component('stm-payments', {
		props: ['saved_payments'],
		data: function () {
			return {
				payment_values : {},
				payments: {
					stripe: {
						enabled: '',
						displayShow: false,
						name: '<?php esc_html_e( 'Stripe', 'masterstudy-lms-learning-management-system' ); ?>',
						img: 'stripe.svg',
						payment_description: '',
						fields: {
							stripe_mode: {
								type: 'select',
								source: 'stripe_modes',
								value : 'test',
								placeholder: '<?php esc_html_e( 'Select Stripe mode', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Payment mode', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php echo wp_kses_post( __( 'Set <strong>pk_test_, sk_test_</strong> keys for Test mode or <strong>pk_live_, sk_live_</strong> for Live mode.', 'masterstudy-lms-learning-management-system' ) ); ?>',
							},
							stripe_public_api_key: {
								type: 'text',
								placeholder: '<?php esc_html_e( 'Enter publishable key', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Publishable key', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php echo wp_kses_post( __( 'Enter your Stripe publishable key. Find it in your Stripe dashboard under <strong>Developers → API Keys.</strong>', 'masterstudy-lms-learning-management-system' ) ); ?>',
							},
							secret_key: {
								type: 'text',
								placeholder: '<?php esc_html_e( 'Enter secret key', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Secret key', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php echo wp_kses_post( __( 'Enter your secret key from the <strong>API Keys</strong> section in your Stripe dashboard for secure payments.', 'masterstudy-lms-learning-management-system' ) ); ?>',
							},
							webhook_key: {
								type: 'text',
								placeholder: '<?php esc_html_e( 'Enter webhook signature key', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Webhook signature key', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Enter the webhook signature key from your Stripe dashboard.', 'masterstudy-lms-learning-management-system' ); ?>',
							},
							webhook_url: {
								type: 'text',
								info_title: '<?php esc_html_e( 'Webhook URL', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Put this URL to the Webhook URL in your Stripe account.', 'masterstudy-lms-learning-management-system' ); ?>',
								value: '<?php echo esc_url( rest_url( '/masterstudy-lms/v2/ecommerce-webhook/stripe/' ) ); ?>',
								readonly: true,
							},
							description: {
								type: 'textarea',
								placeholder: '<?php esc_html_e( 'Enter payment method description', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Checkout description', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Shown to users during checkout. Add a short message (e.g. “Pay securely with Stripe”).', 'masterstudy-lms-learning-management-system' ); ?>',
							},
						},
					},
					paypal: {
						enabled: '',
						displayShow: false,
						name: "<?php esc_html_e( 'PayPal', 'masterstudy-lms-learning-management-system' ); ?>",
						img: 'paypal.svg',
						payment_description: '',
						fields: {
							paypal_mode: {
								type: 'select',
								source: 'paypal_modes',
								value : 'sandbox',
								placeholder: '<?php esc_html_e( 'Select PayPal mode', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Payment mode', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Choose Live to accept real payments or Sandbox to test.', 'masterstudy-lms-learning-management-system' ); ?>',
							},
							paypal_email: {
								type: 'text',
								placeholder: '<?php esc_html_e( 'Enter PayPal email', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'PayPal business email', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Enter the PayPal email address where you want to receive payments.', 'masterstudy-lms-learning-management-system' ); ?>',
							},
							client_id: {
								type: 'text',
								placeholder: '<?php esc_html_e( 'Enter PayPal client ID', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Client ID', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Enter the PayPal client ID for Recurring Payments.', 'masterstudy-lms-learning-management-system' ); ?>',
							},
							client_secret: {
								type: 'text',
								placeholder: '<?php esc_html_e( 'Enter PayPal client secret', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Client secret', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Enter the PayPal client secret for Recurring Payments.', 'masterstudy-lms-learning-management-system' ); ?>',
							},
							webhook_id: {
								type: 'text',
								placeholder: '<?php esc_html_e( 'Enter PayPal webhook ID', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Webhook ID', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Enter the PayPal webhook ID from your PayPal account.', 'masterstudy-lms-learning-management-system' ); ?>',
							},
							webhook_url: {
								type: 'text',
								info_title: '<?php esc_html_e( 'Webhook URL', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Put this URL to the Webhook URL in your PayPal account.', 'masterstudy-lms-learning-management-system' ); ?>',
								value: '<?php echo esc_url( rest_url( '/masterstudy-lms/v2/ecommerce-webhook/paypal/' ) ); ?>',
								readonly: true,
							},
							description: {
								type: 'textarea',
								placeholder: '<?php esc_html_e( 'Enter payment method description', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Checkout description', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Shown to users during checkout. Add a short message (e.g. “Pay securely with Stripe”).', 'masterstudy-lms-learning-management-system' ); ?>',
							},
						},
					},
					mollie: {
						enabled: '',
						displayShow: false,
						name: "<?php esc_html_e( 'Mollie', 'masterstudy-lms-learning-management-system' ); ?>",
						img: 'mollie.svg',
						pro: true,
						pro_url: '<?php echo esc_url( $go_pro_url ); ?>',
						payment_description: '',
						fields: {
							masterstudy_mollie_api_key: {
								type: 'text',
								placeholder: '<?php esc_html_e( 'Enter Mollie API key', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'API key', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php echo wp_kses_post( __( 'Use your Mollie API key (for example, <strong>test_...</strong> for testing or <strong>live_...</strong> for real payments).', 'masterstudy-lms-learning-management-system' ) ); ?>',
							},
							webhook_url: {
								type: 'text',
								info_title: '<?php esc_html_e( 'Webhook URL', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Put this URL to the Webhook URL in your Mollie dashboard.', 'masterstudy-lms-learning-management-system' ); ?>',
								value: '<?php echo esc_url( rest_url( '/masterstudy-lms/v2/payments-webhook/mollie/' ) ); ?>',
								readonly: true,
							},
							description: {
								type: 'textarea',
								placeholder: '<?php esc_html_e( 'Enter payment method description', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Checkout description', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Shown to users during checkout. Add a short message (e.g. “Pay securely with Mollie”).', 'masterstudy-lms-learning-management-system' ); ?>',
							},
						},
					},
					paystack: {
						enabled: '',
						displayShow: false,
						name: "<?php esc_html_e( 'Paystack', 'masterstudy-lms-learning-management-system' ); ?>",
						img: 'paystack.svg',
						pro: true,
						pro_url: '<?php echo esc_url( $go_pro_url ); ?>',
						payment_description: '',
						fields: {
							masterstudy_paystack_secret_key: {
								type: 'text',
								placeholder: '<?php esc_html_e( 'Enter Paystack secret key', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Secret key', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php echo wp_kses_post( __( 'Use your Paystack secret key (for example, <strong>sk_test_...</strong> or <strong>sk_live_...</strong>).', 'masterstudy-lms-learning-management-system' ) ); ?>',
							},
							webhook_url: {
								type: 'text',
								info_title: '<?php esc_html_e( 'Webhook URL', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Put this URL in your Paystack dashboard webhook settings.', 'masterstudy-lms-learning-management-system' ); ?>',
								value: '<?php echo esc_url( rest_url( '/masterstudy-lms/v2/payments-webhook/paystack/' ) ); ?>',
								readonly: true,
							},
							description: {
								type: 'textarea',
								placeholder: '<?php esc_html_e( 'Enter payment method description', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Checkout description', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Shown to users during checkout. Add a short message (e.g. “Pay securely with Paystack”).', 'masterstudy-lms-learning-management-system' ); ?>',
							},
						},
					},
					wire_transfer: {
						enabled: '',
						displayShow: false,
						name: "<?php esc_html_e( 'Wire Transfer', 'masterstudy-lms-learning-management-system' ); ?>",
						img: 'wire-transfer.svg',
						fields: {
							account_number: {
								type: 'text',
								placeholder: '<?php esc_html_e( 'Enter account number', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Account number', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Provide your bank account number for payments.', 'masterstudy-lms-learning-management-system' ); ?>',
							},
							holder_name: {
								type: 'text',
								placeholder: '<?php esc_html_e( 'Enter holder name', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Account holder name', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Enter the full name of the account owner.', 'masterstudy-lms-learning-management-system' ); ?>',
							},
							bank_name: {
								type: 'text',
								placeholder: '<?php esc_html_e( 'Enter bank name', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Bank name', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Enter your bank’s official name.', 'masterstudy-lms-learning-management-system' ); ?>',
							},
							swift: {
								type: 'text',
								placeholder: '<?php esc_html_e( 'Enter SWIFT/BIC code', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'SWIFT/BIC code', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Enter the SWIFT or BIC code for international transfers.', 'masterstudy-lms-learning-management-system' ); ?>',
							},
							description: {
								type: 'textarea',
								placeholder: '<?php esc_html_e( 'Enter payment method description', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Checkout description', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'This will appear as the wire transfer option at checkout.', 'masterstudy-lms-learning-management-system' ); ?>',
							},
						},
					},
					cash: {
						enabled: '',
						displayShow: false,
						name: "<?php esc_html_e( 'Offline Payment', 'masterstudy-lms-learning-management-system' ); ?>",
						img: 'offline.svg',
						fields: {
							description: {
								type: 'textarea',
								placeholder: '<?php esc_html_e( 'Enter payment method description', 'masterstudy-lms-learning-management-system' ); ?>',
								info_title: '<?php esc_html_e( 'Offline payment processing', 'masterstudy-lms-learning-management-system' ); ?>',
								info_description: '<?php esc_html_e( 'Accept payments offline. Orders will be created and stored in the system for your manual approval.', 'masterstudy-lms-learning-management-system' ); ?>',
								pro: true
							},
						},
					},
				},
				sources: {
					codes: {
						'<?php esc_html_e( 'Select Currency code', 'masterstudy-lms-learning-management-system' ); ?>' : '',
						'<?php esc_html_e( 'Australian dollar', 'masterstudy-lms-learning-management-system' ); ?>' : 'AUD',
						'<?php esc_html_e( 'Brazilian real', 'masterstudy-lms-learning-management-system' ); ?>' : 'BRL',
						'<?php esc_html_e( 'Canadian dollar', 'masterstudy-lms-learning-management-system' ); ?>' : 'CAD',
						'<?php esc_html_e( 'Czech koruna', 'masterstudy-lms-learning-management-system' ); ?>' : 'CZK',
						'<?php esc_html_e( 'Danish krone', 'masterstudy-lms-learning-management-system' ); ?>' : 'DKK',
						'<?php esc_html_e( 'Euro', 'masterstudy-lms-learning-management-system' ); ?>' : 'EUR',
						'<?php esc_html_e( 'Hong Kong dollar', 'masterstudy-lms-learning-management-system' ); ?>' : 'HKD',
						'<?php esc_html_e( 'Hungarian forint 1', 'masterstudy-lms-learning-management-system' ); ?>' : 'HUF',
						'<?php esc_html_e( 'Indian rupee', 'masterstudy-lms-learning-management-system' ); ?>' : 'INR',
						'<?php esc_html_e( 'Israeli new shekel', 'masterstudy-lms-learning-management-system' ); ?>' : 'ILS',
						'<?php esc_html_e( 'Japanese yen 1', 'masterstudy-lms-learning-management-system' ); ?>' : 'JPY',
						'<?php esc_html_e( 'Malaysian ringgit 2	', 'masterstudy-lms-learning-management-system' ); ?>' : 'MYR',
						'<?php esc_html_e( 'Mexican peso', 'masterstudy-lms-learning-management-system' ); ?>' : 'MXN',
						'<?php esc_html_e( 'New Taiwan dollar 1', 'masterstudy-lms-learning-management-system' ); ?>' : 'TWD',
						'<?php esc_html_e( 'New Zealand dollar', 'masterstudy-lms-learning-management-system' ); ?>' : 'NZD',
						'<?php esc_html_e( 'Norwegian krone', 'masterstudy-lms-learning-management-system' ); ?>' : 'NOK',
						'<?php esc_html_e( 'Philippine peso', 'masterstudy-lms-learning-management-system' ); ?>' : 'PHP',
						'<?php esc_html_e( 'Polish złoty', 'masterstudy-lms-learning-management-system' ); ?>' : 'PLN',
						'<?php esc_html_e( 'Pound sterling', 'masterstudy-lms-learning-management-system' ); ?>' : 'GBP',
						'<?php esc_html_e( 'Russian ruble', 'masterstudy-lms-learning-management-system' ); ?>' : 'RUB',
						'<?php esc_html_e( 'Singapore dollar', 'masterstudy-lms-learning-management-system' ); ?>' : 'SGD',
						'<?php esc_html_e( 'Swedish krona', 'masterstudy-lms-learning-management-system' ); ?>' : 'SEK',
						'<?php esc_html_e( 'Swiss franc', 'masterstudy-lms-learning-management-system' ); ?>' : 'CHF',
						'<?php esc_html_e( 'Thai baht', 'masterstudy-lms-learning-management-system' ); ?>' : 'THB',
						'<?php esc_html_e( 'United States dollar', 'masterstudy-lms-learning-management-system' ); ?>' : 'USD',
					},
					paypal_modes : {
						'<?php esc_html_e( 'Sandbox', 'masterstudy-lms-learning-management-system' ); ?>' : 'sandbox',
						'<?php esc_html_e( 'Live', 'masterstudy-lms-learning-management-system' ); ?>' : 'live',
					},
					stripe_modes : {
						'<?php esc_html_e( 'Test', 'masterstudy-lms-learning-management-system' ); ?>' : 'test',
						'<?php esc_html_e( 'Live', 'masterstudy-lms-learning-management-system' ); ?>' : 'live',
					}
				},
				activeTooltip: '',
			}
		},
		template: '<?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ echo stm_wpcfto_filtered_output( $template ); ?>',
		created() {
			if (!IS_SUBS_ENABLED) {
				if (this.payments?.stripe?.fields) {
					delete this.payments.stripe.fields.webhook_key;
					delete this.payments.stripe.fields.webhook_url;
				}
				if (this.payments?.paypal?.fields) {
					delete this.payments.paypal.fields.client_id;
					delete this.payments.paypal.fields.client_secret;
					delete this.payments.paypal.fields.webhook_id;
					delete this.payments.paypal.fields.webhook_url;
				}
			}
		},
		mounted: function () {
			if (this.saved_payments) this.setPaymentValues();
		},
		methods: {
			setPaymentValues() {
				var vm = this;
				for(var payment_method in vm.payments) {
					if (!vm.payments.hasOwnProperty(payment_method)) continue;

					const saved_payment = (vm.saved_payments && vm.saved_payments[payment_method]) ? vm.saved_payments[payment_method] : {};
					const saved_fields  = (saved_payment && saved_payment.fields) ? saved_payment.fields : {};
					const saved_enabled = (saved_payment && typeof saved_payment.enabled !== 'undefined') ? saved_payment.enabled : '';

					vm.payments[payment_method]['enabled'] = vm.isProLocked(vm.payments[payment_method]) ? '' : saved_enabled;

					for(var field_name in vm.payments[payment_method]['fields']) {
						const saved_value   = saved_fields[field_name];
						const default_value = vm.payments[payment_method]['fields'][field_name]['value'];
						const field_value   = (typeof saved_value === 'undefined' || '' === saved_value || null === saved_value) ? default_value : saved_value;

						vm.$set(vm.payments[payment_method]['fields'][field_name], 'value', field_value);
					}
				}
			},
			getPaymentValues() {
				var vm = this;
				for (var payment_method in vm.payments) {
					if (!vm.payments.hasOwnProperty(payment_method)) continue;
					const is_locked = vm.isProLocked(vm.payments[payment_method]);
					vm.payment_values[payment_method] = {
						'enabled' : is_locked ? '' : vm.payments[payment_method]['enabled'],
					};

					if (typeof vm.payment_values[payment_method]['fields'] === 'undefined') vm.payment_values[payment_method]['fields'] = {};

					for (var field_name in vm.payments[payment_method]['fields']) {
						if (! vm.payments[payment_method]['fields'].hasOwnProperty(field_name)) continue;
						var value = (typeof vm.payments[payment_method]['fields'][field_name]['value'] === 'undefined') ? '' : vm.payments[payment_method]['fields'][field_name]['value'];

						vm.payment_values[payment_method]['fields'][field_name] = value;
					}
				}

				this.$emit('update-payments', vm.payment_values);
			},
			togglePayment(paymentKey, event) {
				this.payments[paymentKey].displayShow = !this.payments[paymentKey].displayShow;
			},
			isProLocked(paymentInfo) {
				return !!(paymentInfo && paymentInfo.pro && !IS_PLUS_ENABLED);
			},
			handleInputClick(field, field_id) {
				if (field && field.readonly) {
					const inputField = document.getElementById(field_id)
					if(inputField) {
						inputField.select()
						document.execCommand('copy')
						this.activeTooltip = field_id
						setTimeout(() => {
							this.activeTooltip = ''
						}, 2000)
					}
				}
			},
		},
		watch: {
			payments: {
				handler: function () {
					this.getPaymentValues();
				},
				deep: true
			},
		}
	})
</script>
