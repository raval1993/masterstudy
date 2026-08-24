<?php

namespace MasterStudy\Lms\Routing\Swagger\Fields;

use MasterStudy\Lms\Routing\Swagger\Field;

class ListOrder extends Field {
	/**
	 * Object Properties
	 */
	public static array $properties = array(
		'id'      => array(
			'type' => 'integer',
		),
		'user'    => array(
			'type'        => 'string',
			'description' => 'User display name',
		),
		'user_id' => array(
			'type'        => 'integer',
			'description' => 'User ID',
		),
		'amount'  => array(
			'type'        => 'string',
			'description' => 'Formatted total sum of order items',
		),
		'method'  => array(
			'type'        => 'string',
			'description' => 'Payment method',
		),
		'date'    => array(
			'type'        => 'string',
			'description' => 'Timestamp of order',
		),
		'status'  => array(
			'type'        => 'string',
			'description' => 'Order Status',
			'enum'        => array( 'completed', 'pending', 'cancelled' ),
		),
	);
}
