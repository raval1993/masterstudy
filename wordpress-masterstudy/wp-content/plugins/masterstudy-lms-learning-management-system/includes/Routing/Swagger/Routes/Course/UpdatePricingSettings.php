<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Course;

use MasterStudy\Lms\Enums\PricingMode;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class UpdatePricingSettings extends Route implements RequestInterface, ResponseInterface {

	public function request(): array {
		return array(
			'free_do_not_provide_certificate'        => array(
				'type'        => 'boolean',
				'description' => 'Flag to indicate if free course does not provide certificate',
				'required'    => false,
			),
			'free_paid_certificate'                  => array(
				'type'        => 'boolean',
				'description' => 'Flag to indicate if free course provides paid certificate',
				'required'    => false,
			),
			'free_certificate_price'                 => array(
				'type'        => 'number',
				'format'      => 'float',
				'description' => 'Price for free certificate',
				'required'    => false,
			),
			'free_price_info'                        => array(
				'type'        => 'string',
				'description' => 'Price info for free course',
				'required'    => false,
			),
			'single_sale'                            => array(
				'type'        => 'boolean',
				'description' => 'One-time purchase flag',
				'required'    => true,
			),
			'single_sale_price_info'                 => array(
				'type'        => 'string',
				'description' => 'Price info for one-time purchase',
				'required'    => false,
			),
			'single_sale_do_not_provide_certificate' => array(
				'type'        => 'boolean',
				'description' => 'Flag to indicate if one-time purchase does not provide certificate',
				'required'    => false,
			),
			'single_sale_paid_certificate'           => array(
				'type'        => 'boolean',
				'description' => 'Flag to indicate if one-time purchase provides paid certificate',
				'required'    => false,
			),
			'single_sale_certificate_price'          => array(
				'type'        => 'number',
				'format'      => 'float',
				'description' => 'Price for one-time purchase certificate',
				'required'    => false,
			),
			'price'                                  => array(
				'type'        => 'number',
				'format'      => 'float',
				'description' => 'Required for one-time purchase',
			),
			'sale_price'                             => array(
				'type'     => 'number',
				'format'   => 'float',
				'required' => false,
				'nullable' => true,
			),
			'sale_price_dates_start'                 => array(
				'type'        => 'integer',
				'description' => 'Timestamp for sale start date. Required with sale_price_dates_end',
				'nullable'    => true,
			),
			'sale_price_dates_end'                   => array(
				'type'        => 'integer',
				'description' => 'Timestamp for sale end date. Required with sale_price_dates_start',
				'nullable'    => true,
			),
			'points'                                 => array(
				'type'        => 'boolean',
				'description' => 'Flag to indicate if points is enabled',
				'required'    => false,
			),
			'points_price'                           => array(
				'type'        => 'number',
				'format'      => 'float',
				'description' => 'Price for points',
				'required'    => false,
			),
			'points_price_info'                      => array(
				'type'        => 'string',
				'description' => 'Price info for points',
				'required'    => false,
			),
			'points_do_not_provide_certificate'      => array(
				'type'        => 'boolean',
				'description' => 'Flag to indicate if points does not provide certificate',
				'required'    => false,
			),
			'points_paid_certificate'                => array(
				'type'        => 'boolean',
				'description' => 'Flag to indicate if points provides paid certificate',
				'required'    => false,
			),
			'points_certificate_price'               => array(
				'type'        => 'number',
				'format'      => 'float',
				'description' => 'Price for points certificate',
				'required'    => false,
			),
			'subscriptions'                          => array(
				'type'        => 'boolean',
				'description' => 'Flag to indicate if subscriptions is enabled',
				'required'    => false,
			),
			'subscriptions_price_info'               => array(
				'type'        => 'string',
				'description' => 'Price info for subscriptions',
				'required'    => false,
			),
			'enterprise'                             => array(
				'type'        => 'boolean',
				'description' => 'Flag to indicate if enterprise is enabled',
				'required'    => false,
			),
			'enterprise_price'                       => array(
				'type'        => 'number',
				'format'      => 'float',
				'description' => 'Price for enterprise',
				'required'    => false,
			),
			'enterprise_price_info'                  => array(
				'type'        => 'string',
				'description' => 'Price info for enterprise',
				'required'    => false,
			),
			'enterprise_do_not_provide_certificate'  => array(
				'type'        => 'boolean',
				'description' => 'Flag to indicate if enterprise does not provide certificate',
				'required'    => false,
			),
			'enterprise_paid_certificate'            => array(
				'type'        => 'boolean',
				'description' => 'Flag to indicate if enterprise provides paid certificate',
				'required'    => false,
			),
			'enterprise_certificate_price'           => array(
				'type'        => 'number',
				'format'      => 'float',
				'description' => 'Price for enterprise certificate',
				'required'    => false,
			),
			'not_membership'                         => array(
				'type'     => 'boolean',
				'required' => true,
			),
			'membership_price_info'                  => array(
				'type'        => 'string',
				'description' => 'Price info for not membership',
				'required'    => false,
			),
			'membership_do_not_provide_certificate'  => array(
				'type'        => 'boolean',
				'description' => 'Flag to indicate if not membership does not provide certificate',
				'required'    => false,
			),
			'membership_paid_certificate'            => array(
				'type'        => 'boolean',
				'description' => 'Flag to indicate if not membership provides paid certificate',
				'required'    => false,
			),
			'membership_certificate_price'           => array(
				'type'        => 'number',
				'format'      => 'float',
				'description' => 'Price for not membership certificate',
				'required'    => false,
			),
			'affiliate_course'                       => array(
				'type'        => 'boolean',
				'description' => 'Flag to indicate if affiliate course is enabled',
				'required'    => false,
			),
			'affiliate_course_text'                  => array(
				'type'        => 'string',
				'description' => 'Text for affiliate course',
				'required'    => false,
			),
			'affiliate_course_link'                  => array(
				'type'        => 'string',
				'description' => 'Link for affiliate course',
				'required'    => false,
			),
			'pricing_mode'                           => array(
				'type'        => 'string',
				'description' => 'Pricing mode',
				'required'    => true,
				'enum'        => array( PricingMode::FREE, PricingMode::PAID, PricingMode::AFFILIATE ),
			),
		);
	}

	public function response(): array {
		return array();
	}

	public function get_summary(): string {
		return 'Update course pricing settings';
	}

	public function get_description(): string {
		return 'Updates course pricing settings';
	}
}
