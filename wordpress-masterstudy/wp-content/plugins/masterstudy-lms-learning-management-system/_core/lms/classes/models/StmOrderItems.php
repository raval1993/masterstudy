<?php

namespace stmLms\Classes\Models;

use stmLms\Classes\Vendor\StmBaseModel;

class StmOrderItems extends StmBaseModel {

	protected $fillable = array(
		'id',
		'order_id',
		'object_id',
		'payout_id',
		'quantity',
		'price',
		'transaction',
	);

	public $id;
	public $order_id;
	public $object_id;
	public $payout_id;
	public $quantity;
	public $price;
	public $transaction;
	public $total_price;
	public $count;
	public $name;
	public $date_created;

	public static function init() {
		add_action( 'order_created', array( self::class, 'order_created' ), 10, 4 );
		add_action(
			'woocommerce_checkout_update_order_meta',
			array( self::class, 'lms_woocommerce_checkout_update_order_meta' ),
			200,
			1
		);
		add_action(
			'woocommerce_store_api_checkout_update_order_meta',
			array( self::class, 'stm_before_create_order_api' ),
			200,
			1
		);
	}

	public static function get_primary_key() {
		return 'id';
	}

	public static function get_table() {
		global $wpdb;

		return $wpdb->prefix . 'stm_lms_order_items';
	}

	public function get() {
		global $wpdb;

		// Escape the value
		$object_id = $this->object_id;
		$order_id  = $this->order_id;

		// Get the table name
		$table = static::get_table();

		// Get the item
		return $wpdb->get_row(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT * FROM {$table} io WHERE io.object_id = {$object_id} AND io.order_id = {$order_id}",
			ARRAY_A
		);
	}

	public static function get_searchable_fields() {
		return array(
			'id',
			'order_id',
			'object_id',
			'payout_id',
			'quantity',
			'price',
			'transaction',
		);
	}

	/**
	 * @param $data
	 *
	 * @return StmOrderItems
	 */
	public static function load( $data ) {
		$model = new StmOrderItems();

		foreach ( $data as $key => $val ) {
			$model->$key = $val;
		}

		return $model;
	}

	/**
	 * @param $user_id
	 * @param $cart_items
	 * @param $payment_code
	 * @param $order_id
	 */
	public static function order_created( $user_id, $cart_items, $payment_code, $order_id ) {
		if ( ! is_array( $cart_items ) || empty( $cart_items ) || empty( $order_id ) ) {
			return;
		}

		foreach ( $cart_items as $item ) {
			$order_items              = new StmOrderItems();
			$order_items->order_id    = $order_id;
			$order_items->object_id   = $item['item_id'];
			$order_items->price       = $item['price'];
			$order_items->quantity    = 1;
			$order_items->transaction = 0;
			if ( empty( $order_items->get() ) ) {
				$order_items->save();
			}
		}
	}

	public static function stm_before_create_order_api( $order ) {
		$order_id = $order->get_id();
		self::lms_woocommerce_checkout_update_order_meta( $order_id );
	}

	/**
	 * @param $order_id
	 */
	public static function lms_woocommerce_checkout_update_order_meta( $order_id ) {
		$cart = WC()->cart->get_cart();

		foreach ( $cart as $cart_item ) {
			$order_items              = new StmOrderItems();
			$order_items->order_id    = $order_id;
			$order_items->object_id   = $cart_item['product_id'];
			$order_items->price       = ( isset( $cart_item['data'] ) ) ? $cart_item['data']->get_price() : 0;
			$order_items->quantity    = $cart_item['quantity'];
			$order_items->transaction = 0;
			if ( empty( $order_items->get() ) ) {
				$order_items->save();
			}
		}
	}

	/**
	 * @return array|null|\WP_Post
	 */
	public function get_items_posts() {
		return get_post( $this->object_id );
	}

	public function get_items_posts_order() {
		return get_post( $this->order_id );
	}

	/**
	 * @return string
	 */
	public function get_items_author() {
		$post = $this->get_items_posts();

		return ! empty( $post ) ? get_userdata( $post->post_author ) : false;
	}
}

