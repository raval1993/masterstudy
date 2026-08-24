<?php

namespace MasterStudy\Lms\Routing\Swagger\Fields;

use MasterStudy\Lms\Routing\Swagger\Field;

class Order extends Field {
	/**
	 * Object Properties
	 */
	public static array $properties = array(
		'id'           => array(
			'type' => 'integer',
		),
		'user'         => array(
			'type'        => 'object',
			'description' => 'User object',
			'properties'  => array(
				'login' => array(
					'type' => 'string',
				),
				'email' => array(
					'type' => 'string',
				),
				'id'    => array(
					'type' => 'integer',
				),
			),
		),
		'total'        => array(
			'type'        => 'string',
			'description' => 'Formatted total sum of order items',
		),
		'payment_code' => array(
			'type'        => 'string',
			'description' => 'Payment method',
		),
		'order_note'   => array(
			'type' => 'string',
		),
		'date'         => array(
			'type'        => 'string',
			'description' => 'Timestamp of order',
		),
		'status'       => array(
			'type'        => 'string',
			'description' => 'Order Status',
			'enum'        => array( 'completed', 'pending', 'cancelled' ),
		),
		'cart_items'   => array(
			'type'        => 'array',
			'description' => 'Courses and course bundles',
		),
	);
}
