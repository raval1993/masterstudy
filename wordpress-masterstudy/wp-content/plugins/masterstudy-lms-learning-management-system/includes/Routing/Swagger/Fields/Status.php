<?php

namespace MasterStudy\Lms\Routing\Swagger\Fields;

use MasterStudy\Lms\Routing\Swagger\Field;

class Status extends Field {

	/**
	 * Object Properties
	 */
	public static array $properties = array(
		'id'   => array(
			'type' => 'string',
		),
		'name' => array(
			'type' => 'string',
		),
	);
}
