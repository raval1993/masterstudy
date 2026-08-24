<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Enums\PricingMode;

final class PricingRepository {
	private const CASTS = array(
		'free_do_not_provide_certificate'        => 'bool',
		'free_paid_certificate'                  => 'bool',
		'free_price_info'                        => 'string',
		'free_certificate_price'                 => 'numeric',

		'single_sale'                            => 'bool',
		'price'                                  => 'numeric',
		'sale_price'                             => 'numeric',
		'sale_price_dates_start'                 => 'numeric',
		'sale_price_dates_end'                   => 'numeric',
		'single_sale_price_info'                 => 'string',
		'single_sale_do_not_provide_certificate' => 'bool',
		'single_sale_paid_certificate'           => 'bool',
		'single_sale_certificate_price'          => 'numeric',

		'enterprise'                             => 'bool',
		'enterprise_price'                       => 'numeric',
		'enterprise_price_info'                  => 'string',
		'enterprise_do_not_provide_certificate'  => 'bool',
		'enterprise_paid_certificate'            => 'bool',
		'enterprise_certificate_price'           => 'numeric',

		'not_membership'                         => 'bool',
		'membership_price_info'                  => 'string',
		'membership_do_not_provide_certificate'  => 'bool',
		'membership_paid_certificate'            => 'bool',
		'membership_certificate_price'           => 'numeric',

		'points'                                 => 'bool',
		'points_price'                           => 'numeric',
		'points_price_info'                      => 'string',
		'points_do_not_provide_certificate'      => 'bool',
		'points_paid_certificate'                => 'bool',
		'points_certificate_price'               => 'numeric',

		'subscriptions'                          => 'bool',
		'subscriptions_price_info'               => 'string',

		'affiliate_course'                       => 'bool',
		'affiliate_course_link'                  => 'string',
		'affiliate_course_text'                  => 'string',
		'affiliate_course_price'                 => 'numeric',

		'pricing_mode'                           => 'string',
	);

	private const DEFAULTS = array(
		'free_do_not_provide_certificate'        => false,
		'free_paid_certificate'                  => false,
		'free_certificate_price'                 => null,
		'free_price_info'                        => '',

		'single_sale'                            => true,
		'price'                                  => null,
		'sale_price'                             => null,
		'sale_price_dates_start'                 => null,
		'sale_price_dates_end'                   => null,
		'single_sale_price_info'                 => '',
		'single_sale_do_not_provide_certificate' => false,
		'single_sale_paid_certificate'           => false,
		'single_sale_certificate_price'          => null,

		'enterprise'                             => false,
		'enterprise_price'                       => null,
		'enterprise_price_info'                  => '',
		'enterprise_do_not_provide_certificate'  => false,
		'enterprise_paid_certificate'            => false,
		'enterprise_certificate_price'           => null,

		'not_membership'                         => true,
		'membership_price_info'                  => '',
		'membership_do_not_provide_certificate'  => false,
		'membership_paid_certificate'            => false,
		'membership_certificate_price'           => null,

		'points'                                 => false,
		'points_price'                           => null,
		'points_price_info'                      => '',
		'points_do_not_provide_certificate'      => false,
		'points_paid_certificate'                => false,
		'points_certificate_price'               => null,

		'subscriptions'                          => false,
		'subscriptions_price_info'               => '',

		'affiliate_course'                       => false,
		'affiliate_course_link'                  => '',
		'affiliate_course_text'                  => '',
		'affiliate_course_price'                 => null,

		'pricing_mode'                           => PricingMode::FREE,
	);

	public function get( int $course_id ): array {
		$meta = get_post_meta( $course_id );

		$pricing = self::DEFAULTS;

		foreach ( $pricing as $key => $default ) {
			$pricing[ $key ] = $this->cast( $key, $meta[ $key ][0] ?? $default );
		}

		return $pricing;
	}

	private function update_pricing_mode( int $course_id, array $pricing ): array {
		$old_pricing_mode     = get_post_meta( $course_id, 'pricing_mode', true );
		$current_pricing_mode = $pricing['pricing_mode'];

		if ( $old_pricing_mode === $current_pricing_mode ) {
			return $pricing;
		}

		$default_pricing = self::DEFAULTS;

		foreach ( $default_pricing as $key => $default ) {
			if ( ! isset( $pricing[ $key ] ) ) {
				$pricing[ $key ] = $default;
			}
		}

		return $pricing;
	}

	public function save( int $course_id, array $pricing ): void {
		$pricing = $this->update_pricing_mode( $course_id, $pricing );

		foreach ( $pricing as $key => $value ) {
			if ( isset( self::CASTS[ $key ] ) && 'bool' === self::CASTS[ $key ] ) {
				$value = $value ? 'on' : '';
			} else {
				$value = (string) $value;
			}

			update_post_meta( $course_id, $key, $value );
		}

		do_action( 'masterstudy_lms_course_price_updated', $course_id );
	}

	public static function get_price_info( int $post_id ): array {
		return array(
			'free_price_info'          => get_post_meta( $post_id, 'free_price_info', true ) ?? '',
			'single_sale_price_info'   => get_post_meta( $post_id, 'single_sale_price_info', true ) ?? '',
			'enterprise_price_info'    => get_post_meta( $post_id, 'enterprise_price_info', true ) ?? '',
			'points_price_info'        => get_post_meta( $post_id, 'points_price_info', true ) ?? '',
			'subscriptions_price_info' => get_post_meta( $post_id, 'subscriptions_price_info', true ) ?? '',
			'membership_price_info'    => get_post_meta( $post_id, 'not_membership_price_info', true ) ?? '',
		);
	}

	public static function is_certificate_enabled( int $post_id ): bool {
		$certificate_id = get_post_meta( $post_id, 'course_certificate', true );
		return is_ms_lms_addon_enabled( 'certificate_builder' ) && 'none' !== $certificate_id;
	}

	public static function get_certificates_info( int $post_id ): array {
		$is_certificate_enabled = self::is_certificate_enabled( $post_id );
		return array(
			'free'        => empty( get_post_meta( $post_id, 'free_do_not_provide_certificate', true ) ) && $is_certificate_enabled,
			'single_sale' => empty( get_post_meta( $post_id, 'single_sale_do_not_provide_certificate', true ) ) && $is_certificate_enabled,
			'enterprise'  => empty( get_post_meta( $post_id, 'enterprise_do_not_provide_certificate', true ) ) && $is_certificate_enabled,
			'pmpro'       => empty( get_post_meta( $post_id, 'membership_do_not_provide_certificate', true ) ) && $is_certificate_enabled,
			'points'      => empty( get_post_meta( $post_id, 'points_do_not_provide_certificate', true ) ) && $is_certificate_enabled,
		);
	}

	private function cast( $key, $value ) {
		if ( null === $value ) {
			return null;
		}

		$type = self::CASTS[ $key ] ?? '';
		switch ( $type ) {
			case 'bool':
				return 'on' === $value;
			case 'numeric':
				return '' !== $value ? (float) $value : null;
			case 'string':
				return $value;
			default:
				return '' !== $value ? $value : null;
		}
	}
}
