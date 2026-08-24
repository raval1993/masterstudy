<?php // phpcs:ignoreFile

class STM_LMS_PayPal {

	private $url;
	public $currency_code;
	public $email;
	public $return_url;

	function __construct( $amount = 10, $invoice = 0, $item_name = '', $item_number = '', $user_email = '', $return_url = '' )
	{

		$payments              = STM_LMS_Options::get_option( 'payment_methods' );
		$transactions_currency = STM_LMS_Options::get_option( 'transactions_currency' );

		if ( ! empty( $payments['paypal'] ) and ! empty( $payments['paypal']['enabled'] ) and ! empty( $payments['paypal']['fields'] ) ) {
			$paypal        = $payments['paypal'];
			$paypal_fields = $paypal['fields'];

			$this->url           = ( $paypal_fields['paypal_mode'] == 'live' ) ? 'www.paypal.com' : 'www.sandbox.paypal.com';
			$this->currency_code = $transactions_currency;
			$this->email         = $paypal_email = $paypal_fields['paypal_email'];
			$this->return_url    = empty( $return_url ) ? add_query_arg( 'paypal_order_id', $invoice, home_url() ) : $return_url;
			$this->invoice       = $invoice;
			$this->amount        = $amount;
			$this->item_name     = $item_name;
			$this->item_number   = $item_number;
			$this->user_email    = $user_email;

			$this->return_url = apply_filters( 'stm_paypal_return_url', $this->return_url );
		}
	}

	function generate_payment_url() {
		$get_params = array(
			'cmd'           => '_xclick',
			'business'      => $this->email,
			'no_shipping'   => 1,
			'no_note'       => 1,
			'currency_code' => $this->currency_code,
			'bn'            => 'PP%2dBuyNowBF',
			'charset'       => 'UTF%2d8',
			'item_name'     => $this->item_name,
			'item_number'   => $this->item_number,
			'invoice'       => $this->invoice,
			'return'        => $this->return_url,
			'email'         => $this->user_email,
			'rm'            => 2,
			'amount'        => $this->amount,
			'notify_url'    => add_query_arg( 'stm_lms_check_ipn', 1, home_url() )
		);

		$url = 'https://' . $this->url . '/cgi-bin/webscr?' . http_build_query( $get_params );

		return $url;
	}

	function check_payment( $data = array() ) {
		$order_id = isset( $data['invoice'] ) && is_scalar( $data['invoice'] ) ? absint( $data['invoice'] ) : 0;

		if ( ! $this->is_valid_payment( $order_id, $data ) ) {
			return false;
		}

		$req = 'cmd=_notify-validate';
		foreach ($data as $key => $value) {
			if ( ! is_scalar( $value ) ) {
				return false;
			}

			$key   = urlencode( (string) $key );
			$value = urlencode( (string) $value );
			$req  .= "&$key=$value";
		}

		$response = wp_remote_post(
			'https://' . $this->url . '/cgi-bin/webscr',
			array(
				'body'        => $req,
				'headers'     => array(
					'Connection'   => 'Close',
					'Content-Type' => 'application/x-www-form-urlencoded',
				),
				'httpversion' => '1.1',
				'sslverify'   => true,
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$res = wp_remote_retrieve_body( $response );

		if ( 'VERIFIED' !== trim( $res ) || 'pending' !== get_post_meta( $order_id, 'status', true ) ) {
			return false;
		}

		$user_id = absint( get_post_meta( $order_id, 'user_id', true ) );
		if ( empty( $user_id ) ) {
			return false;
		}

		update_post_meta( $order_id, 'masterstudy_paypal_txn_id', sanitize_text_field( $data['txn_id'] ) );
		update_post_meta( $order_id, 'status', 'completed' );
		STM_LMS_Order::accept_order( $user_id, $order_id );

		return true;
	}

	private function is_valid_payment( $order_id, $data ) {
		if ( empty( $order_id )
			|| 'stm-orders' !== get_post_type( $order_id )
			|| 'paypal' !== get_post_meta( $order_id, 'payment_code', true )
			|| 'pending' !== get_post_meta( $order_id, 'status', true )
		) {
			return false;
		}

		$required_fields = array( 'payment_status', 'mc_gross', 'mc_currency', 'txn_id' );
		foreach ( $required_fields as $field ) {
			if ( ! isset( $data[ $field ] ) || ! is_scalar( $data[ $field ] ) || '' === trim( (string) $data[ $field ] ) ) {
				return false;
			}
		}

		$expected_amount   = get_post_meta( $order_id, '_order_total', true );
		$expected_currency = get_post_meta( $order_id, 'masterstudy_paypal_currency', true );
		$expected_receiver = get_post_meta( $order_id, 'masterstudy_paypal_receiver', true );

		if ( empty( $expected_currency ) ) {
			$expected_currency = $this->currency_code;
		}

		if ( empty( $expected_receiver ) ) {
			$expected_receiver = $this->email;
		}

		if ( 'completed' !== strtolower( trim( (string) $data['payment_status'] ) )
			|| ! is_numeric( $expected_amount )
			|| ! is_numeric( $data['mc_gross'] )
			|| abs( (float) $expected_amount - (float) $data['mc_gross'] ) > 0.00001
			|| 0 !== strcasecmp( trim( (string) $expected_currency ), trim( (string) $data['mc_currency'] ) )
			|| ! $this->receiver_matches( $expected_receiver, $data )
		) {
			return false;
		}

		return true;
	}

	private function receiver_matches( $expected_receiver, $data ) {
		$expected_receiver = strtolower( trim( (string) $expected_receiver ) );
		if ( empty( $expected_receiver ) ) {
			return false;
		}

		foreach ( array( 'receiver_email', 'business' ) as $field ) {
			if ( isset( $data[ $field ] )
				&& is_scalar( $data[ $field ] )
				&& hash_equals( $expected_receiver, strtolower( trim( (string) $data[ $field ] ) ) )
			) {
				return true;
			}
		}

		return false;
	}
}

if ( ! empty( $_GET['stm_lms_check_ipn'] ) ) {
	$paypal = new STM_LMS_PayPal();
	$paypal->check_payment( wp_unslash( $_POST ) );
	header( 'HTTP/1.1 200 OK' );
	exit;
}
