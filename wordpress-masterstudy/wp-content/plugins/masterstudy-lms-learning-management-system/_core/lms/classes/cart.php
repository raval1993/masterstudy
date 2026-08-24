<?php

use Google\Service\Reseller\SubscriptionPlan;
use MasterStudy\Lms\Ecommerce\Ecommerce;
use MasterStudy\Lms\Plugin\Addons;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;
use MasterStudy\Lms\Pro\RestApi\Repositories\CouponRepository;
use MasterStudy\Lms\Pro\RestApi\Http\Serializers\Coupons\CouponSerializer;

STM_LMS_Cart::init();

class STM_LMS_Cart {
	public static $user_cache = array();

	public static function init() {
		add_action( 'wp_ajax_stm_lms_add_to_cart', 'STM_LMS_Cart::add_to_cart' );
		add_action( 'wp_ajax_nopriv_stm_lms_add_to_cart', 'STM_LMS_Cart::add_to_cart' );

		add_action( 'wp_ajax_stm_lms_delete_from_cart', 'STM_LMS_Cart::delete_from_cart' );
		add_action( 'wp_ajax_nopriv_stm_lms_delete_from_cart', 'STM_LMS_Cart::delete_from_cart' );

		add_action( 'init', 'STM_LMS_Cart::masterstudy_add_order_received_endpoint' );
		add_action( 'template_redirect', 'STM_LMS_Cart::masterstudy_handle_order_received_endpoint' );

		add_action( 'wp_ajax_stm_lms_purchase', 'STM_LMS_Cart::purchase_courses' );
		add_action( 'wp_ajax_nopriv_stm_lms_purchase', 'STM_LMS_Cart::purchase_courses' );
		add_action( 'masterstudy_lms_course_price_updated', array( self::class, 'course_price_updated' ), 10, 2 );
	}

	public static function course_price_updated( $item_id ) {
		$course_meta = STM_LMS_Helpers::parse_meta_field( $item_id );
		$price       = self::get_course_price( $course_meta );
		stm_lms_update_user_cart( $item_id, $price );
	}

	public static function woocommerce_checkout_enabled() {
		return STM_LMS_Options::get_option( 'ecommerce_engine', 'native' ) === 'woocommerce' && class_exists( 'WooCommerce' );
	}

	public static function masterstudy_add_to_cart( $item_id, $user_id ) {
		$response = array();

		$single_sale = (bool) get_post_meta( $item_id, 'single_sale', true );
		$plan        = is_ms_lms_addon_enabled( Addons::SUBSCRIPTIONS ) ? ( new SubscriptionPlanRepository() )->get( $item_id ) : null;
		$is_plan     = ! empty( $plan );

		if ( ! $single_sale && ! $is_plan ) {
			return $response;
		}

		$is_woocommerce = self::woocommerce_checkout_enabled();
		$item_added     = count( stm_lms_get_item_in_cart( $user_id, $item_id, array( 'user_cart_id' ) ) );

		if ( ! $item_added ) {
			do_action( 'masterstudy_lms_before_add_to_cart', $item_id, $user_id );

			$quantity = 1;

			if ( $is_plan ) {
				$price                        = SubscriptionPlanRepository::get_actual_price( $plan );
				$cart_args                    = compact( 'user_id', 'item_id', 'quantity', 'price' );
				$cart_args['is_subscription'] = 1;
				stm_lms_add_user_cart( $cart_args );
			} else {
				$item_meta = STM_LMS_Helpers::parse_meta_field( $item_id );
				$price     = self::get_course_price( $item_meta );
				stm_lms_add_user_cart( compact( 'user_id', 'item_id', 'quantity', 'price' ) );
			}

			do_action( 'masterstudy_lms_after_add_to_cart', $item_id, $user_id );
		}

		if ( ! $is_woocommerce ) {
			$response['text']     = esc_html__( 'Go to Cart', 'masterstudy-lms-learning-management-system' );
			$response['cart_url'] = esc_url( self::checkout_url() );
		} else {
			$response['added']    = STM_LMS_Woocommerce::add_to_cart( $item_id );
			$response['text']     = esc_html__( 'Go to Cart', 'masterstudy-lms-learning-management-system' );
			$response['cart_url'] = esc_url( wc_get_cart_url() );
		}

		$response['redirect'] = (bool) STM_LMS_Options::get_option( 'redirect_after_purchase', false );

		return apply_filters( 'stm_lms_add_to_cart_r', $response, $item_id );
	}

	public static function add_to_cart() {
		check_ajax_referer( 'stm_lms_add_to_cart', 'nonce' );

		if ( ! is_user_logged_in() || empty( $_GET['item_id'] ) ) {
			die;
		}

		$item_id = intval( $_GET['item_id'] );
		$user    = STM_LMS_User::get_current_user();
		$user_id = $user['id'];

		$r = self::masterstudy_add_to_cart( $item_id, $user_id );

		wp_send_json( $r );
	}

	public static function delete_from_cart() {
		check_ajax_referer( 'stm_lms_delete_from_cart', 'nonce' );

		if ( ( ! is_user_logged_in() && empty( $_GET['guest'] ) ) || empty( $_GET['item_id'] ) ) {
			die;
		}

		if ( ! empty( $_GET['guest'] ) ) {
			wp_send_json( 'OK' );
		} else {
			$user = STM_LMS_User::get_current_user();

			if ( apply_filters( 'stm_lms_delete_from_cart_filter', true ) ) {
				stm_lms_get_delete_cart_item( $user['id'], intval( $_GET['item_id'] ) );
			}

			do_action( 'stm_lms_delete_from_cart', $user['id'] );

			wp_send_json( 'OK' );
		}
	}

	public static function get_course_price( $course_meta ) {
		$price = 0;
		if ( ! empty( $course_meta['price'] ) ) {
			$price = $course_meta['price'];
		}
		if ( ! empty( $course_meta['sale_price'] ) ) {
			$price = apply_filters( 'stm_lms_sale_price_meta', $course_meta['sale_price'], $course_meta, $price );
		}
		return apply_filters( 'stm_lms_get_course_price_in_meta', $price, $course_meta );
	}

	public static function checkout_url() {
		$settings = get_option( 'stm_lms_settings', array() );

		if ( empty( $settings['checkout_url'] ) ) {
			return home_url( '/' );
		}

		return get_the_permalink( $settings['checkout_url'] );
	}

	//Add endpoint for thank you page url
	public static function masterstudy_add_order_received_endpoint() {
		add_rewrite_endpoint( 'masterstudy-orders-received', EP_ROOT | EP_PAGES );
	}

	//Add template for thank you page
	public static function masterstudy_handle_order_received_endpoint() {
		global $wp;

		if ( ! isset( $wp->query_vars['masterstudy-orders-received'] ) ) {
			return;
		}

		$order_id = (int) $wp->query_vars['masterstudy-orders-received'];
		$order    = get_post( $order_id );

		if ( empty( $order ) || 'stm-orders' !== get_post_type( $order ) || 'trash' === $order->post_status ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			get_template_part( '404' );
			exit;
		}

		$provided_key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		if ( '' === $provided_key ) {
			status_header( 403 );
			exit;
		}

		$expected_key = get_post_meta( $order_id, 'order_key', true );
		if ( '' === $expected_key ) {
			$expected_key = (string) get_post_field( 'post_name', $order_id ); // legacy
		}

		if ( ! hash_equals( (string) $expected_key, (string) $provided_key ) ) {
			status_header( 403 );
			exit;
		}

		$current_user_id = get_current_user_id();
		$order_owner_id  = (int) get_post_meta( $order_id, 'user_id', true );

		$can_view = (
			( $current_user_id > 0 && $current_user_id === $order_owner_id ) ||
			current_user_can( 'manage_options' ) // or a custom cap like 'stm_lms_manage_orders'
		);

		if ( ! $can_view ) {
			status_header( 403 );
			exit;
		}

		include MS_LMS_PATH . '/_core/stm-lms-templates/checkout/thankyou.php';
		exit;
	}

	public static function purchase_courses() {
		check_ajax_referer( 'stm_lms_purchase', 'nonce' );

		$user = STM_LMS_User::get_current_user();
		if ( empty( $user['id'] ) ) {
			die;
		}

		$user_id = $user['id'];

		$payment_code  = ( ! empty( $_REQUEST['payment_code'] ) ) ? sanitize_text_field( $_REQUEST['payment_code'] ) : '';
		$personal_data = array();
		$coupon_id     = null;

		if ( isset( $_REQUEST['personal_data'] ) ) {
			$raw_personal_data = wp_unslash( $_REQUEST['personal_data'] );
			$decoded_data      = json_decode( $raw_personal_data, true );

			if ( is_array( $decoded_data ) ) {
				$personal_data = array_map( 'sanitize_text_field', $decoded_data );
			}
		}

		if ( STM_LMS_Helpers::is_pro_plus() && class_exists( CouponRepository::class ) && isset( $_REQUEST['coupon_id'] ) ) {
			$coupon_id   = intval( wp_unslash( $_REQUEST['coupon_id'] ) );
			$coupon_repo = new CouponRepository();
			$raw_coupon  = $coupon_repo->get( (int) $coupon_id );

			if ( $raw_coupon && is_array( $raw_coupon ) ) {
				$coupon_type  = isset( $raw_coupon['discount_type'] ) ? (string) $raw_coupon['discount_type'] : 'percent';
				$coupon_value = isset( $raw_coupon['discount'] ) ? (float) $raw_coupon['discount'] : 0.0;
			}
		}

		$r = array(
			'status' => 'success',
		);

		if ( empty( $payment_code ) ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => esc_html__( 'Please, select payment method', 'masterstudy-lms-learning-management-system' ),
				)
			);
			return;
		}

		if ( ! empty( $personal_data ) && is_array( $personal_data ) ) {
			$personal_data_fields = masterstudy_lms_personal_data_fields();
			$errors               = array();

			foreach ( $personal_data as $key => $value ) {
				if ( '' === trim( $value ) ) {
					$errors[] = array(
						'field'   => $key,
						'message' => sprintf(
							/* translators: %s: Field Label */
							esc_html__( '%s field is required', 'masterstudy-lms-learning-management-system' ),
							$personal_data_fields[ $key ] ?? ucfirst( $key )
						),
					);
				}
			}

			if ( ! empty( $errors ) ) {
				wp_send_json(
					array(
						'status' => 'personal_data_error',
						'errors' => $errors,
					)
				);
				return;
			}

			update_user_meta( $user_id, 'masterstudy_personal_data', $personal_data );
		}

		$cart_items      = stm_lms_get_cart_items( $user_id, apply_filters( 'stm_lms_cart_items_fields', array( 'item_id', 'price' ) ) );
		$cart_total      = self::get_cart_totals( $cart_items, $personal_data, $coupon_id );
		$symbol          = STM_LMS_Options::get_option( 'currency_symbol', 'none' );
		$checkout_url    = ! empty( STM_LMS_Options::get_option( 'checkout_url' ) ) ? get_permalink( STM_LMS_Options::get_option( 'checkout_url' ) ) : home_url();
		$is_subscription = false;

		if ( is_ms_lms_addon_enabled( Addons::SUBSCRIPTIONS ) ) {
			foreach ( $cart_items as $item ) {
				if ( $item['is_subscription'] ) {
					$is_subscription = true;
					break;
				}
			}
		}

		/*Create ORDER*/
		$order_data = array(
			'user_id'         => $user_id,
			'cart_items'      => $cart_items,
			'payment_code'    => $payment_code,
			'_order_total'    => $cart_total['total'],
			'_order_subtotal' => $cart_total['subtotal'],
			'_order_taxes'    => $cart_total['taxes'],
			'_order_currency' => $symbol,
			'personal_data'   => $personal_data,
			'coupon_id'       => $coupon_id ?? null,
			'coupon_value'    => $coupon_value ?? null,
			'coupon_type'     => $coupon_type ?? null,
			'is_subscription' => $is_subscription,
		);

		if ( $is_subscription ) {
			$order_data['plan'] = ( new SubscriptionPlanRepository() )->get( intval( $cart_items[0]['item_id'] ) );

			// Get course_id from plan items
			$course_id = '';
			if ( ! empty( $order_data['plan']['items'] ) && is_array( $order_data['plan']['items'] ) && ! empty( $order_data['plan']['items'][0]['object_id'] ) ) {
				$course_id = $order_data['plan']['items'][0]['object_id'];
			}

			if ( function_exists( 'masterstudy_lms_get_membership_url' ) ) {
				$order_data['plan']['memberships_url'] = masterstudy_lms_get_membership_url();
			}

			if ( ! empty( $course_id ) ) {
				$order_data['course_info'] = array(
					'course_title'     => esc_html( get_the_title( $course_id ) ),
					'course_url'       => esc_url( get_the_permalink( $course_id ) ),
					'course_thumbnail' => esc_url( get_the_post_thumbnail_url( $course_id, 'thumbnail' ) ),
				);
			}
		}

		$invoice   = STM_LMS_Order::create_order( $order_data, true );
		$order_id  = intval( $invoice );
		$order_key = get_post_meta( $order_id, 'order_key', true );
		if ( empty( $order_key ) ) {
			$order_key = get_post_field( 'post_name', $order_id );
		}

		// Auto-accept orders with zero total (100% discount)
		$total_float = (float) $cart_total['total'];

		if ( 0 === $total_float || 0.0 === $total_float ) {
			update_post_meta( $invoice, 'status', 'completed' );
			STM_LMS_Order::accept_order( $user_id, $invoice );
		}

		do_action( 'order_created', $user_id, $cart_items, $payment_code, $invoice );

		if ( $is_subscription ) {
			// Stripe subscription data
			$order_data['order_id']          = $order_id;
			$order_data['checkout_url']      = $checkout_url;
			$order_data['thankyou_url']      = $checkout_url . "masterstudy-orders-received/{$invoice}/?key={$order_key}";
			$order_data['payment_method_id'] = sanitize_text_field( $_REQUEST['payment_method_id'] ?? '' );
			$order_data['card_last4']        = sanitize_text_field( $_REQUEST['card_last4'] ?? '' );

			$payment_gateway = Ecommerce::get_payment_gateway_object( $payment_code );
			if ( $payment_gateway ) {
				$payment_data = self::prepare_payment_data( $order_data );

				$payment_gateway->process_subscription( $payment_data );

				if ( ! empty( $payment_gateway->get_error() ) ) {
					wp_send_json(
						array(
							'status'  => 'error',
							'message' => $payment_gateway->get_error(),
						)
					);
				}

				$subscription_data = $payment_gateway->get_data();

				if ( ! empty( $subscription_data['subscription_id'] ) ) {
					// Update order meta
					$order_status = in_array( $subscription_data['subscription_status'], array( 'active', 'trialing' ), true )
						? 'completed'
						: 'pending';

					update_post_meta( $invoice, 'status', $order_status );
					update_post_meta( $invoice, 'subscription_id', $subscription_data['subscription_id'] );
					update_post_meta( $invoice, 'gateway_invoice_id', $subscription_data['gateway_invoice_id'] );
					update_post_meta( $invoice, 'subscription_order_number', 1 );

					// If trial plan, set order total to 0
					if ( SubscriptionPlanRepository::is_plan_trial( $order_data['plan'] ) ) {
						update_post_meta( $invoice, '_order_total', 0 );
						update_post_meta( $invoice, '_order_subtotal', 0 );
						update_post_meta( $invoice, '_order_taxes', 0 );
					}

					// Accept order
					STM_LMS_Order::accept_order( $user_id, $invoice );

					self::update_coupon_usage( $coupon_id, $user_id );
					wp_send_json(
						array(
							'status'        => 'success',
							'message'       => esc_html__( 'Subscription created successfully. You are being redirected to the next step.', 'masterstudy-lms-learning-management-system' ),
							'url'           => $subscription_data['redirect_url'] ?? $order_data['thankyou_url'],
							'client_secret' => $subscription_data['client_secret'] ?? null,
						)
					);
				}

				wp_send_json(
					array(
						'status'  => 'error',
						'message' => esc_html__( 'Error creating subscription for customer.', 'masterstudy-lms-learning-management-system' ),
					)
				);

				wp_die();
			}

			wp_send_json(
				array(
					'status'  => 'error',
					'message' => esc_html__( 'Choosen payment method is not supported for subscription', 'masterstudy-lms-learning-management-system' ),
				)
			);

			wp_die();
		}

		self::update_coupon_usage( $coupon_id, $user_id );

		// Skip payment processing for zero total orders
		$is_zero_total = ( 0 === (float) $cart_total['total'] || 0.0 === (float) $cart_total['total'] );

		if ( $is_zero_total && $order_id > 0 && ! empty( $order_key ) ) {
			// For zero total orders, skip payment and redirect directly
			$r['message'] = esc_html__( 'Order created, redirecting', 'masterstudy-lms-learning-management-system' );
			$r['url']     = $checkout_url . "/masterstudy-orders-received/{$order_id}/?key={$order_key}";
		} elseif ( 'paypal' === $payment_code ) {
			/*If Paypal*/
			$paypal       = new STM_LMS_PayPal(
				$cart_total['total'],
				$invoice,
				$cart_total['item_name'],
				$invoice,
				$user['email']
			);
			update_post_meta( $invoice, 'masterstudy_paypal_currency', $paypal->currency_code );
			update_post_meta( $invoice, 'masterstudy_paypal_receiver', $paypal->email );
			$r['url']     = $paypal->generate_payment_url();
			$r['message'] = esc_html__( 'Order created, redirecting to PayPal', 'masterstudy-lms-learning-management-system' );
		} elseif ( 'stripe' === $payment_code ) {
			// Accept token from any HTTP method.
			$raw_token_id = isset( $_REQUEST['token_id'] ) ? wp_unslash( $_REQUEST['token_id'] ) : '';
			$token_id     = sanitize_text_field( $raw_token_id );

			if ( ! empty( $token_id ) ) {
				$url                   = 'https://api.stripe.com/v1/charges';
				$payment               = STM_LMS_Options::get_option( 'payment_methods' );
				$transactions_currency = STM_LMS_Options::get_option( 'transactions_currency' );

				if ( empty( $payment['stripe']['enabled'] ) || empty( $payment['stripe']['fields']['secret_key'] ) ) {
					wp_send_json(
						array(
							'status'  => 'error',
							'message' => esc_html__( 'Stripe is not configured.', 'masterstudy-lms-learning-management-system' ),
						)
					);
				}

				$sk_key  = $payment['stripe']['fields']['secret_key'];
				$headers = array( 'Authorization' => 'Bearer ' . $sk_key );

				$currency = ! empty( $transactions_currency ) ? $transactions_currency : 'usd';
				if ( 'jpy' === strtolower( $currency ) ) {
					$stripe_amount = intval( round( (float) $cart_total['total'] ) );
				} else {
					$increment     = (int) apply_filters( 'masterstudy_payment_increment', 100 );
					$stripe_amount = (float) $cart_total['total'] * $increment;
				}

				$args = array(
					'source'      => $token_id,
					'amount'      => $stripe_amount,
					'description' => sprintf(
					/* translators: 1: Course name, 2: Order key */
						esc_html__( '%1$s. Order key: %2$s', 'masterstudy-lms-learning-management-system' ),
						$cart_total['item_name'],
						get_the_title( $invoice )
					),
					'currency'    => $currency,
				);

				$req = wp_remote_post(
					$url,
					array(
						'headers' => $headers,
						'body'    => $args,
					)
				);
				$req = wp_remote_retrieve_body( $req );
				$req = json_decode( $req, true );

				$r['message'] = esc_html__( 'Order created. Payment not completed.', 'masterstudy-lms-learning-management-system' );
				if ( ! empty( $req['paid'] ) && ! empty( $req['amount'] ) && ( (float) $req['amount'] === (float) $stripe_amount ) ) {
					update_post_meta( $invoice, 'status', 'completed' );
					STM_LMS_Order::accept_order( $user_id, $invoice );
					$r['message'] = esc_html__( 'Order created. Payment completed.', 'masterstudy-lms-learning-management-system' );
				} else {
					if ( ! $is_zero_total ) { // Don't delete order if total is zero (already accepted)
						wp_delete_post( $invoice, true );
						$r['status']  = 'error';
						$r['message'] = esc_html__( 'Error occurred. Please try again.', 'masterstudy-lms-learning-management-system' );
						$r['url']     = false;
					}
				}
				if ( $order_id > 0 && ! empty( $order_key ) ) {
					$r['url'] = $checkout_url . "/masterstudy-orders-received/{$order_id}/?key={$order_key}";
				}
				$r['order'] = $req;
			} else {
				wp_send_json(
					array(
						'status'  => 'error',
						'message' => esc_html__( 'Please, select payment method', 'masterstudy-lms-learning-management-system' ),
					)
				);
			}
		} elseif ( $order_id > 0 && ! empty( $order_key ) ) {
				$r['message'] = esc_html__( 'Order created, redirecting', 'masterstudy-lms-learning-management-system' );
				$r['url']     = $checkout_url . "/masterstudy-orders-received/{$order_id}/?key={$order_key}";
		}

		do_action( 'stm_lms_purchase_action_done', $user_id );

		do_action( 'masterstudy_lms_order_completed', $user_id, $cart_items, $payment_code, $invoice );

		wp_send_json( apply_filters( 'stm_lms_purchase_done', $r ) );
		die;
	}

	public static function payment_methods() {
		return apply_filters(
			'stm_lms_payment_methods',
			array(
				'cash'           => esc_html__( 'Offline Payment', 'masterstudy-lms-learning-management-system' ),
				'wire_transfer'  => esc_html__( 'Wire transfer', 'masterstudy-lms-learning-management-system' ),
				'paypal'         => esc_html__( 'Paypal', 'masterstudy-lms-learning-management-system' ),
				'stripe'         => esc_html__( 'Stripe', 'masterstudy-lms-learning-management-system' ),
				'account_number' => esc_html__( 'Account Number', 'masterstudy-lms-learning-management-system' ),
				'holder_name'    => esc_html__( 'Holder name', 'masterstudy-lms-learning-management-system' ),
				'bank_name'      => esc_html__( 'Bank name', 'masterstudy-lms-learning-management-system' ),
				'swift'          => esc_html__( 'Swift', 'masterstudy-lms-learning-management-system' ),
				'description'    => esc_html__( 'Description', 'masterstudy-lms-learning-management-system' ),
				'currency'       => esc_html__( 'Currency', 'masterstudy-lms-learning-management-system' ),
			)
		);
	}

	public static function subscription_payment_methods() {
		return array(
			'stripe' => esc_html__( 'Stripe', 'masterstudy-lms-learning-management-system' ),
			'paypal' => esc_html__( 'Paypal', 'masterstudy-lms-learning-management-system' ),
		);
	}

	public static function prepare_payment_data( $order_data ) {
		$user_data = get_userdata( $order_data['user_id'] );

		return array_merge(
			array(
				'total_price'        => floatval( $order_data['_order_total'] ),
				'currency'           => STM_LMS_Options::get_option( 'currency_code', 'USD' ),
				'decimal_separator'  => STM_LMS_Options::get_option( 'currency_decimals', '.' ),
				'thousand_separator' => STM_LMS_Options::get_option( 'currency_thousands', ',' ),
				'customer'           => array(
					'name'  => $user_data->display_name,
					'email' => $user_data->user_email ?? '',
				),
			),
			$order_data
		);
	}

	public static function cart_has_subscription_item() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$current_user_id = get_current_user_id();

		if ( isset( self::$user_cache[ $current_user_id ] ) ) {
			return self::$user_cache[ $current_user_id ];
		}

		$cart_items = stm_lms_get_cart_items(
			$current_user_id,
			apply_filters( 'stm_lms_cart_items_fields', array( 'item_id', 'price', 'is_subscription' ) )
		);

		self::$user_cache[ $current_user_id ] = ! empty( $cart_items[0]['is_subscription'] );

		return self::$user_cache[ $current_user_id ];
	}

	public static function get_cart_totals( $cart_items, $personal_data = array(), $coupon_id = null ) {
		$taxes_display = STM_LMS_Helpers::taxes_display();
		$tax_enabled   = ! empty( $taxes_display['enabled'] );
		$tax_included  = $tax_enabled && ! empty( $taxes_display['included'] );

		$tax_rate = 0.0;
		if ( $tax_enabled && ! empty( $personal_data['country'] ) ) {
			$tax_rate = (float) STM_LMS_Helpers::get_tax_rate_for_personal_data( $personal_data );
		}

		$coupon_discount_type  = '';
		$coupon_discount_value = 0.0;
		$eligible_subtotal     = 0.0;

		if ( STM_LMS_Helpers::is_pro_plus() && class_exists( CouponRepository::class ) && $coupon_id ) {
			$coupon_repo = new CouponRepository();
			$raw_coupon  = $coupon_repo->get( (int) $coupon_id );

			if ( $raw_coupon && is_array( $raw_coupon ) ) {
				$coupon_discount_type  = isset( $raw_coupon['discount_type'] ) ? (string) $raw_coupon['discount_type'] : 'percent';
				$coupon_discount_value = isset( $raw_coupon['discount'] ) ? (float) $raw_coupon['discount'] : 0.0;

				$normalized_items = array();
				foreach ( $cart_items as $item ) {
					$normalized_items[] = array(
						'item_id'         => isset( $item['item_id'] ) ? (int) $item['item_id'] : 0,
						'price'           => isset( $item['price'] ) ? (float) $item['price'] : 0.0,
						'quantity'        => isset( $item['quantity'] ) ? (int) $item['quantity'] : 1,
						'bundle'          => isset( $item['bundle'] ) ? (int) $item['bundle'] : 0,
						'enterprise'      => isset( $item['enterprise'] ) ? (int) $item['enterprise'] : 0,
						'is_subscription' => ! empty( $item['is_subscription'] ),
					);
				}

				$stats = $coupon_repo->evaluate_cart_for_coupon( $raw_coupon, $normalized_items );

				if ( is_array( $stats ) && isset( $stats['eligible_subtotal'] ) ) {
					$eligible_subtotal = (float) $stats['eligible_subtotal'];
				}
			}
		}

		$item_names     = array();
		$gross_subtotal = 0.0;

		foreach ( $cart_items as $cart_item ) {
			$line_total      = isset( $cart_item['price'] ) ? (float) $cart_item['price'] : 0.0;
			$gross_subtotal += $line_total;

			if ( ! empty( $cart_item['item_id'] ) ) {
				$item_names[] = get_the_title( (int) $cart_item['item_id'] );
			}
		}

		$decimals = (int) STM_LMS_Options::get_option( 'decimals_num', 2 );
		if ( $decimals < 0 ) {
			$decimals = 2;
		}

		$round = static function ( $value ) use ( $decimals ) {
			return round( (float) $value, $decimals, PHP_ROUND_HALF_UP );
		};

		$gross_subtotal    = (float) $gross_subtotal;
		$eligible_subtotal = (float) $eligible_subtotal;

		$taxes = 0.0;
		$total = 0.0;

		$coupon_discount_amount = 0.0;

		if ( $coupon_discount_value > 0 && $eligible_subtotal > 0 && '' !== $coupon_discount_type ) {
			$type = strtolower( (string) $coupon_discount_type );

			if ( 'percent' === $type ) {
				$coupon_discount_amount = $eligible_subtotal * ( $coupon_discount_value / 100.0 );
			} elseif ( 'amount' === $type ) {
				$coupon_discount_amount = $coupon_discount_value;
			}

			if ( $coupon_discount_amount > $eligible_subtotal ) {
				$coupon_discount_amount = $eligible_subtotal;
			}
		}

		if ( $tax_enabled && $tax_included && $tax_rate > 0 ) {
			$gross_before = $gross_subtotal;

			$gross_after = max( 0.0, $gross_before - $coupon_discount_amount );

			$net_after = $gross_after;
			if ( $tax_rate > 0 ) {
				$net_after = $gross_after * 100.0 / ( 100.0 + $tax_rate );
			}

			$taxes = $gross_after - $net_after;
			$total = $gross_after;
		} else {
			$net_before = $gross_subtotal;

			$net_after = max( 0.0, $net_before - $coupon_discount_amount );

			if ( $tax_enabled && $tax_rate > 0 ) {
				$taxes = $net_after * $tax_rate / 100.0;
			} else {
				$taxes = 0.0;
			}

			$total = $net_after + $taxes;
		}

		$subtotal_for_display = $round( $gross_subtotal );

		return array(
			'subtotal'  => $subtotal_for_display,
			'taxes'     => $round( $taxes ),
			'total'     => $round( $total ),
			'item_name' => implode( ', ', $item_names ),
		);
	}

	private static function update_coupon_usage( ?int $coupon_id, int $user_id ): void {
		if ( STM_LMS_Helpers::is_pro_plus() && class_exists( CouponRepository::class ) && $coupon_id ) {
			$usage_map = get_user_meta( $user_id, 'masterstudy_coupon_usage', true );

			if ( ! is_array( $usage_map ) ) {
				$usage_map = array();
			}

			$current_count           = isset( $usage_map[ $coupon_id ] ) ? (int) $usage_map[ $coupon_id ] : 0;
			$usage_map[ $coupon_id ] = $current_count + 1;

			update_user_meta( $user_id, 'masterstudy_coupon_usage', $usage_map );

			( new CouponRepository() )->increment_used_count( (int) $coupon_id, 1 );

			if ( ! headers_sent() ) {
				setcookie(
					'masterstudy_cart_coupon',
					'',
					array(
						'expires'  => time() - DAY_IN_SECONDS,
						'path'     => '/',
						'secure'   => is_ssl(),
						'httponly' => false,
						'samesite' => 'Lax',
					)
				);
			}
		}
	}
}
