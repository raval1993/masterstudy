<?php

namespace MasterStudy\Lms\Ecommerce;

abstract class AbstractPayment {
	protected $data = array();

	abstract public function setup( array $config ): void;

	abstract public function check(): bool;

	public function set_data( $data ) {
		$this->data = $data;
	}

	public function get_data() {
		return $this->data;
	}

	public function get_error() {
		return $this->data['error'] ?? null;
	}

	public function process_subscription( $data ) {
		try {
			$this->set_data( $data );

			$this->subscribe();
		} catch ( \Throwable $th ) {
			throw $th;
		}
	}
}
