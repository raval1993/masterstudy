<?php

namespace MasterStudy\Lms\Http\Serializers;

use STM_LMS_Helpers;

final class OrderSerializer extends AbstractSerializer {

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function toArray( $data ): array {
		return array(
			'id'                          => $data['id'],
			'status'                      => $data['status'],
			'order_note'                  => $data['order_note'] ?? '',
			'cart_items'                  => $data['cart_items'],
			'user'                        => array(
				'login' => $data['user']['login'],
				'email' => $data['user']['email'],
				'id'    => $data['user']['id'],
			),
			'total'                       => $data['total'],
			'subtotal'                    => $data['subtotal'],
			'coupon_value'                => $data['coupon_value'],
			'coupon_data'                 => $data['coupon_data'],
			'coupon_type'                 => $data['coupon_type'],
			'original_coupon_value'       => $data['original_coupon_value'],
			'coupon_item_price_formatted' => $data['coupon_item_price_formatted'],
			'taxes'                       => $data['taxes'],
			'date'                        => $data['date'],
			'payment_code'                => $data['payment_code'],
			'coupon'                      => $data['coupon'],
			'order_key'                   => $data['order_key'],
			'personal_data'               => $data['personal_data'],
		);
	}
}
