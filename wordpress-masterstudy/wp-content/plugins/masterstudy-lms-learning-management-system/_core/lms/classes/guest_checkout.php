<?php

use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository;
use MasterStudy\Lms\Pro\addons\CourseBundle\Utility\CourseBundleCheckout;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;
use MasterStudy\Lms\Plugin\Addons;

new STM_LMS_Guest_Checkout();

class STM_LMS_Guest_Checkout {
	public function __construct() {
		add_filter( 'stm_lms_buy_button_auth', array( $this, 'guest_checkout' ), 10, 2 );

		add_action( 'wp_ajax_nopriv_stm_lms_add_to_cart_guest', array( $this, 'guest_checkout_process' ) );
		add_action( 'wp_ajax_nopriv_stm_lms_fast_login', array( $this, 'fast_login' ) );
		add_action( 'wp_ajax_nopriv_stm_lms_fast_register', array( $this, 'fast_register' ) );

		add_action( 'init', array( $this, 'handle_guest_activation_link' ) );
	}

	private static function build_activation_checkout_url( $token ) {
		$base = STM_LMS_Cart::woocommerce_checkout_enabled() ? wc_get_checkout_url() : STM_LMS_Cart::checkout_url();

		return add_query_arg(
			array(
				'stm_lms_guest_activate' => 1,
				'token'                  => rawurlencode( (string) $token ),
			),
			$base
		);
	}

	private static function get_guest_cart_item_ids_from_cookie() {
		if ( empty( $_COOKIE['stm_lms_notauth_cart'] ) ) {
			return array();
		}

		$decoded = json_decode( wp_unslash( $_COOKIE['stm_lms_notauth_cart'] ), true );

		if ( ! is_array( $decoded ) ) {
			return array();
		}

		return array_values(
			array_filter(
				array_map( 'absint', $decoded )
			)
		);
	}
	private static function restore_cart_items_for_user( $user_id, $item_ids ) {
		if ( empty( $user_id ) || empty( $item_ids ) || ! is_array( $item_ids ) ) {
			return;
		}

		if ( STM_LMS_Cart::woocommerce_checkout_enabled() && function_exists( 'WC' ) && WC()->cart ) {
			foreach ( $item_ids as $item_id ) {
				$item_id = absint( $item_id );
				if ( ! $item_id ) {
					continue;
				}

				STM_LMS_Woocommerce::add_to_cart( $item_id );
			}

			return;
		}

		foreach ( $item_ids as $item_id ) {
			$item_id = absint( $item_id );
			if ( ! $item_id ) {
				continue;
			}

			if (
				'stm-course-bundles' === get_post_type( $item_id )
				&& class_exists( '\MasterStudy\Lms\Pro\addons\CourseBundle\Utility\CourseBundleCheckout' )
			) {
				CourseBundleCheckout::add_to_cart( $item_id, $user_id );
			} else {
				STM_LMS_Cart::masterstudy_add_to_cart( $item_id, $user_id );
			}
		}
	}

	/**
	 * Handle activation link click: activate -> login -> restore cart -> redirect to checkout.
	 */
	public function handle_guest_activation_link() {
		if ( empty( $_GET['stm_lms_guest_activate'] ) || empty( $_GET['token'] ) ) {
			return;
		}

		$token = sanitize_text_field( wp_unslash( $_GET['token'] ) );
		if ( empty( $token ) ) {
			return;
		}

		$transient_key = 'stm_lms_guest_activate_' . $token;
		$payload       = get_transient( $transient_key );

		if ( empty( $payload ) || ! is_array( $payload ) ) {
			return;
		}

		$user_id  = ! empty( $payload['user_id'] ) ? absint( $payload['user_id'] ) : 0;
		$item_ids = ! empty( $payload['item_ids'] ) && is_array( $payload['item_ids'] ) ? $payload['item_ids'] : array();

		if ( ! $user_id || ! get_user_by( 'id', $user_id ) ) {
			delete_transient( $transient_key );
			return;
		}

		delete_user_meta( $user_id, 'stm_lms_guest_pending' );
		delete_transient( $transient_key );
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id, true, is_ssl() );
		self::restore_cart_items_for_user( $user_id, $item_ids );
		$redirect_url = STM_LMS_Cart::woocommerce_checkout_enabled() ? wc_get_checkout_url() : STM_LMS_Cart::checkout_url();
		wp_safe_redirect( $redirect_url );
		exit;
	}

	public static function guest_enabled() {
		return STM_LMS_Options::get_option( 'guest_checkout', false );
	}

	public function guest_checkout( $atts, $course_id ) {
		if ( ! self::guest_enabled() ) {
			return $atts;
		}
		if ( is_user_logged_in() ) {
			return $atts;
		}

		return array(
			'data-guest="' . $course_id . '"',
		);
	}

	public function guest_checkout_process() {
		check_ajax_referer( 'stm_lms_add_to_cart_guest', 'nonce' );

		if ( is_user_logged_in() || ! self::guest_enabled() ) {
			wp_send_json(
				array(
					'added'    => false,
					'message'  => esc_html__( 'Guest checkout is not available.', 'masterstudy-lms-learning-management-system' ),
					'redirect' => false,
				)
			);

			return;
		}

		$is_woocommerce = STM_LMS_Cart::woocommerce_checkout_enabled();
		$item_id        = isset( $_REQUEST['item_id'] ) ? absint( wp_unslash( $_REQUEST['item_id'] ) ) : 0;

		if ( ! $item_id && isset( $_REQUEST['plan_id'] ) ) {
			$item_id = absint( wp_unslash( $_REQUEST['plan_id'] ) );
		}

		if ( ! $item_id ) {
			wp_send_json(
				array(
					'added'    => false,
					'message'  => esc_html__( 'Invalid item.', 'masterstudy-lms-learning-management-system' ),
					'redirect' => false,
				)
			);

			return;
		}

		$response = array(
			'text'     => esc_html__( 'Go to Cart', 'masterstudy-lms-learning-management-system' ),
			'redirect' => STM_LMS_Options::get_option( 'redirect_after_purchase', false ),
		);

		if ( ! $is_woocommerce ) {
			$response['added']    = true;
			$response['cart_url'] = esc_url( STM_LMS_Cart::checkout_url() );

			wp_send_json( $response );
			return;
		}

		$added = STM_LMS_Woocommerce::add_to_cart( $item_id );

		$response['added']    = (bool) $added;
		$response['cart_url'] = esc_url( wc_get_cart_url() );

		wp_send_json( $response );
	}

	public static function get_cart_items() {
		$items = array();
		if ( isset( $_COOKIE['stm_lms_notauth_cart'] ) ) {
			$items = self::check_cart_items( $_COOKIE['stm_lms_notauth_cart'] );
		}

		return $items;
	}

	public static function check_cart_items( $items ) {
		$cart_items = array();

		if ( empty( $items ) ) {
			return $cart_items;
		}

		$items = json_decode( $items, true );

		foreach ( $items as $item_id ) {
			if ( ! is_int( $item_id ) && get_post_type( $item_id ) !== 'stm-courses' ) {
				continue;
			}

			$cart_item = array(
				'item_id' => $item_id,
			);

			$plan = is_ms_lms_addon_enabled( Addons::SUBSCRIPTIONS ) ? ( new SubscriptionPlanRepository() )->get( $item_id ) : null;

			if ( 'stm-course-bundles' === get_post_type( $item_id )
				&& class_exists( '\MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository' ) ) {
				$cart_item['price'] = CourseBundleRepository::get_bundle_price( $item_id );
			} elseif ( $plan ) {
				$cart_item['price']           = SubscriptionPlanRepository::get_actual_price( $plan );
				$cart_item['is_subscription'] = 1;
				$cart_item['is_trial']        = ! empty( $plan['trial_period'] );
			} else {
				$cart_item['price'] = STM_LMS_Course::get_course_price( $item_id );
			}

			$bundle_ids = get_post_meta( $item_id, 'stm_lms_bundle_ids', true );
			if ( ! empty( $bundle_ids ) ) {
				$cart_item['bundle'] = $item_id;
			}

			$cart_items[] = $cart_item;
		}

		return $cart_items;
	}

	public function fast_register() {
		check_ajax_referer( 'stm_lms_fast_register', 'nonce' );
		$premoderation = STM_LMS_Options::get_option( 'user_premoderation', false );

		$response = array(
			'status' => 'error',
		);

		$request_body = file_get_contents( 'php://input' );
		$data         = json_decode( $request_body, true );

		$email    = ! empty( $data['email'] ) ? sanitize_email( $data['email'] ) : '';
		$password = ! empty( $data['password'] ) ? (string) $data['password'] : '';

		if ( empty( $email ) ) {
			$response['errors'][] = array(
				'id'    => 'empty_email',
				'field' => 'email',
				'text'  => esc_html__( 'Field is required', 'masterstudy-lms-learning-management-system' ),
			);
		} elseif ( ! is_email( $email ) ) {
			$response['errors'][] = array(
				'id'    => 'wrong_email',
				'field' => 'email',
				'text'  => esc_html__( 'Enter valid email', 'masterstudy-lms-learning-management-system' ),
			);
		} elseif ( email_exists( $email ) ) {
			$response['errors'][] = array(
				'id'    => 'exists_email',
				'field' => 'email',
				'text'  => esc_html__( 'User with this email address already exists', 'masterstudy-lms-learning-management-system' ),
			);
		}

		$weak_password = STM_LMS_Options::get_option( 'registration_weak_password', false );

		if ( ! $weak_password && ! empty( $password ) ) {
			if ( strlen( $password ) < 8 ) {
				$response['errors'][] = array(
					'id'    => 'characters',
					'field' => 'password',
					'text'  => esc_html__( 'Password must have at least 8 characters', 'masterstudy-lms-learning-management-system' ),
				);
			}
			if ( ! preg_match( '#[a-z]+#', $password ) ) {
				$response['errors'][] = array(
					'id'    => 'lowercase',
					'field' => 'password',
					'text'  => esc_html__( 'Password must include at least one lowercase letter!', 'masterstudy-lms-learning-management-system' ),
				);
			}
			if ( ! preg_match( '#[0-9]+#', $password ) ) {
				$response['errors'][] = array(
					'id'    => 'number',
					'field' => 'password',
					'text'  => esc_html__( 'Password must include at least one number!', 'masterstudy-lms-learning-management-system' ),
				);
			}
			if ( ! preg_match( '#[A-Z]+#', $password ) ) {
				$response['errors'][] = array(
					'id'    => 'capital',
					'field' => 'password',
					'text'  => esc_html__( 'Password must include at least one capital letter!', 'masterstudy-lms-learning-management-system' ),
				);
			}
		}

		if ( ! empty( $response['errors'] ) ) {
			return wp_send_json( $response );
		}

		$username = sanitize_user( current( explode( '@', $email ) ), true );
		if ( empty( $username ) ) {
			$username = 'user';
		}

		// Ensure unique username.
		$base_username = $username;
		$i             = 1;
		while ( username_exists( $username ) ) {
			$username = $base_username . $i;
			$i++;
		}

		$user_id = wp_create_user( $username, $password, $email );

		if ( is_wp_error( $user_id ) ) {
			$response['errors'][] = array(
				'id'    => 'user_error',
				'field' => 'email',
				'text'  => $user_id->get_error_message(),
			);
			return wp_send_json( $response );
		}

		$user_id = absint( $user_id );
		if ( ! $premoderation ) {
			$user = wp_signon(
				array(
					'user_login'    => $email,
					'user_password' => $password,
				),
				is_ssl()
			);

			if ( is_wp_error( $user ) ) {
				$response['errors'][] = array(
					'id'    => 'login_error',
					'field' => 'email',
					'text'  => esc_html__( 'Unable to sign in after registration.', 'masterstudy-lms-learning-management-system' ),
				);

				wp_send_json( $response );
				return;
			}

			// Add items from guest cookie into the user cart.
			$response['items']  = self::add_cart( $user->ID );
			$response['status'] = 'success';

			wp_send_json( $response );
			return;
		}

		update_user_meta( $user_id, 'stm_lms_guest_pending', 1 );

		$token         = bin2hex( random_bytes( 16 ) );
		$transient_key = 'stm_lms_guest_activate_' . $token;

		$item_ids = self::get_guest_cart_item_ids_from_cookie();

		set_transient(
			$transient_key,
			array(
				'user_id'  => $user_id,
				'item_ids' => $item_ids,
			),
			3 * DAY_IN_SECONDS
		);

		$checkout_url = self::build_activation_checkout_url( $token );
		$blog_name    = get_bloginfo( 'name' );

		$subject = esc_html__( 'Activate your account', 'masterstudy-lms-learning-management-system' );

		$template = wp_kses_post(
			'Hi {{user_login}}, <br>
			Welcome to {{blog_name}} <br>
			To activate your account and continue to checkout, please click the link below: <br>
			Checkout Link: {{checkout_url}} <br>'
		);

		$email_data = array(
			'user_login'   => STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
			'blog_name'    => $blog_name,
			'checkout_url' => \MS_LMS_Email_Template_Helpers::link( $checkout_url ),
			'site_url'     => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
			'date'         => current_time( 'mysql' ),
		);

		$message = \MS_LMS_Email_Template_Helpers::render( $template, $email_data );
		$subject = \MS_LMS_Email_Template_Helpers::render( $subject, $email_data );

		STM_LMS_Helpers::send_email(
			$email,
			$subject,
			$message,
			'stm_lms_guest_checkout_activation',
			$email_data
		);

		$response['status']  = 'success';
		$response['message'] = esc_html__( 'Please check your email to activate your account and continue to checkout.', 'masterstudy-lms-learning-management-system' );

		return wp_send_json( $response );
	}

	public function fast_login() {
		check_ajax_referer( 'stm_lms_fast_login', 'nonce' );

		$response = array(
			'status' => 'error',
		);

		$request_body = file_get_contents( 'php://input' );
		$data         = json_decode( $request_body, true );

		if ( empty( $data['user_login'] ) ) {
			$response['errors'][] = array(
				'id'    => 'empty_email',
				'field' => 'email',
				'text'  => esc_html__( 'Field is required', 'masterstudy-lms-learning-management-system' ),
			);
		}

		if ( empty( $data['user_password'] ) ) {
			$response['errors'][] = array(
				'id'    => 'empty_pass',
				'field' => 'password',
				'text'  => esc_html__( 'Field is required', 'masterstudy-lms-learning-management-system' ),
			);
		}

		if ( ! empty( $response['errors'] ) ) {
			return wp_send_json( $response );
		}

		$get_user_by   = is_email( remove_accents( $data['user_login'] ) ) ? 'email' : 'login';
		$is_registered = get_user_by( $get_user_by, remove_accents( $data['user_login'] ) );
		if ( ! $is_registered ) {
			$response['errors'][] = array(
				'id'    => 'wrong_email',
				'field' => 'email',
				'text'  => esc_html__( 'Wrong email', 'masterstudy-lms-learning-management-system' ),
			);

			return wp_send_json( $response );
		}

		if ( ! wp_check_password( $data['user_password'], $is_registered->user_pass, $is_registered->ID ) ) {
			$response['errors'][] = array(
				'id'    => 'wrong_password',
				'field' => 'password',
				'text'  => esc_html__( 'Wrong password', 'masterstudy-lms-learning-management-system' ),
			);
			return wp_send_json( $response );
		}

		if ( masterstudy_lms_user_session_limit_reached( $is_registered ) ) {
			return wp_send_json( STM_LMS_User_Sessions::build_limit_response( $is_registered->ID ) );
		}

		$user = wp_signon( $data, is_ssl() );

		if ( is_wp_error( $user ) ) {
			$response['errors'][] = array(
				'id'    => 'wrong_password',
				'field' => 'password',
				'text'  => esc_html__( 'Wrong password', 'masterstudy-lms-learning-management-system' ),
			);
			return wp_send_json( $response );
		} else {
			$response['items']  = self::add_cart( $user->ID );
			$response['status'] = 'success';
		}

		wp_send_json( $response );
	}

	public static function add_cart( $user_id ) {
		$response = array();
		$items    = self::get_cart_items();

		foreach ( $items as $item ) {
			if ( 'stm-course-bundles' === get_post_type( $item['item_id'] )
				&& class_exists( 'CourseBundleRepository' ) ) {
				$response[] = CourseBundleCheckout::add_to_cart( $item['item_id'], $user_id );
			} else {
				$response[] = STM_LMS_Cart::masterstudy_add_to_cart( $item['item_id'], $user_id );
			}
		}

		return $response;
	}

}
