<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Order;

use MasterStudy\Lms\Routing\Swagger\Fields\Order;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetOrder extends Route implements ResponseInterface {
	public function response(): array {
		return Order::as_response();
	}

	public function get_summary(): string {
		return 'Get Order by ID';
	}

	public function get_description(): string {
		return 'Returns order based on ID.';
	}
}
