<?php

namespace MasterStudy\Lms\Ecommerce;

class Ecommerce {

	/**
	 * Get payment gateways
	 *
	 * @param string|null $gateway
	 *
	 * @return array|null
	 *
	 * Return example:
	 * array(
	 *     'paypal' => Paypal::class,
	 *     'stripe' => Stripe::class,
	 * )
	 */
	public static function get_payment_gateway_class( $gateway = null ) {
		$gateways = apply_filters(
			'masterstudy_lms_payment_gateways',
			array()
		);

		return is_null( $gateway ) ? $gateways : $gateways[ $gateway ] ?? null;
	}

	/**
	 * Get payment webhook class
	 *
	 * @param string $gateway
	 *
	 * @return string|null
	 */
	public static function get_payment_webhook_class( $gateway = null ) {
		$gateways = apply_filters(
			'masterstudy_lms_payment_webhook_classes',
			array()
		);

		return is_null( $gateway ) ? $gateways : $gateways[ $gateway ] ?? null;
	}

	/**
	 * Get payment gateway object
	 *
	 * @param string $gateway
	 *
	 * @return AbstractPayment|null
	 */
	public static function get_payment_gateway_object( $gateway ) {
		$gateway_class = self::get_payment_gateway_class( $gateway );

		if ( ! $gateway_class ) {
			return null;
		}

		$gateway_object = new $gateway_class();

		// Check if the gateway is configured correctly
		if ( ! $gateway_object->check() ) {
			return null;
		}

		return $gateway_object;
	}
}
