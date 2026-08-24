<?php

namespace MasterStudy\Lms\Http\Serializers;

use stmLms\Classes\Models\StmOrderItems;

final class StatisticsOrderSerializer extends AbstractSerializer {

	/**
	 * @param object $order
	 */
	public function toArray( $order ): array {
		$order_id   = absint( $order->ID ?? 0 );
		$post_type  = (string) ( $order->post_type ?? get_post_type( $order_id ) );
		$order_meta = get_post_meta( $order_id );
		$user       = null;
		$type       = __( 'Lms', 'masterstudy-lms-learning-management-system' );
		$price      = $this->get_lms_price( $order_meta );

		if ( 'shop_order' === $post_type || 'shop_order_placehold' === $post_type ) {
			$type  = 'WooCommerce';
			$user  = get_userdata( absint( $order->post_author ?? 0 ) );
			$price = $this->get_woocommerce_price( $order_id );
		} else {
			$user = get_userdata( absint( $order_meta['user_id'][0] ?? 0 ) );
		}

		return array(
			'id'           => $order_id,
			'type'         => $type,
			'user'         => $this->format_user_label( $user ),
			'price'        => $price,
			'items'        => $this->serialize_items( $order_id ),
			'created_date' => array(
				'date' => date_i18n( get_option( 'date_format' ), strtotime( (string) ( $order->post_date ?? '' ) ) ),
				'time' => date_i18n( get_option( 'time_format' ), strtotime( (string) ( $order->post_date ?? '' ) ) ),
			),
		);
	}

	private function get_lms_price( array $order_meta ): string {
		$total    = isset( $order_meta['_order_total'][0] ) ? (string) $order_meta['_order_total'][0] : '0';
		$currency = isset( $order_meta['_order_currency'][0] ) ? (string) $order_meta['_order_currency'][0] : '';

		return trim( $total . ' ' . $currency );
	}

	private function get_woocommerce_price( int $order_id ): string {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return esc_html__( 'Activate WooCommerce to see the price', 'masterstudy-lms-learning-management-system' );
		}

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return '0';
		}

		$total = 0.0;

		foreach ( $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) ) as $order_item ) {
			$total += (float) $order_item->get_total() * (float) $order_item->get_quantity();
		}

		$currency_symbol = html_entity_decode( get_woocommerce_currency_symbol( $order->get_currency() ) );
		$formatted_total = number_format_i18n( $total, wc_get_price_decimals() );

		return trim( $formatted_total . ' ' . $currency_symbol );
	}

	private function format_user_label( $user ): string {
		if ( ! $user instanceof \WP_User ) {
			return esc_html__( 'Not found', 'masterstudy-lms-learning-management-system' );
		}

		$parts = array_filter(
			array(
				sanitize_text_field( (string) $user->user_firstname ),
				sanitize_text_field( (string) $user->user_lastname ),
			)
		);
		$name  = ! empty( $parts ) ? implode( ' ', $parts ) : sanitize_text_field( (string) $user->display_name );

		return sprintf(
			'(%1$d) %2$s (%3$s)',
			absint( $user->ID ),
			$name,
			sanitize_email( (string) $user->user_email )
		);
	}

	private function serialize_items( int $order_id ): array {
		$items = StmOrderItems::query()->where( 'order_id', $order_id )->find();

		if ( empty( $items ) ) {
			return $this->serialize_woocommerce_items( $order_id );
		}

		return array_values(
			array_filter(
				array_map(
					array( $this, 'serialize_order_item' ),
					$items
				)
			)
		);
	}

	private function serialize_order_item( $item ): ?array {
		$item_post = $item->get_items_posts();

		if ( ! $item_post ) {
			return null;
		}

		$author = $item->get_items_author();

		return array(
			'course'   => html_entity_decode( wp_strip_all_tags( (string) $item_post->post_title ) ),
			'quantity' => (int) $item->quantity,
			'price'    => (string) $item->price,
			'author'   => $this->format_author_label( $author ),
			'payout'   => ! empty( $item->transaction ),
		);
	}

	private function serialize_woocommerce_items( int $order_id ): array {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return array();
		}

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return array();
		}

		$items = array();

		foreach ( $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) ) as $order_item ) {
			$product_id = absint( $order_item->get_product_id() );
			$product    = $product_id ? get_post( $product_id ) : null;
			$author     = $product ? get_userdata( absint( $product->post_author ) ) : false;
			$quantity   = (int) $order_item->get_quantity();
			$price      = $quantity > 0 ? ( (float) $order_item->get_total() / $quantity ) : (float) $order_item->get_total();

			$items[] = array(
				'course'   => $product ? html_entity_decode( wp_strip_all_tags( (string) $product->post_title ) ) : esc_html__( '(deleted item)', 'masterstudy-lms-learning-management-system' ),
				'quantity' => $quantity,
				'price'    => (string) $price,
				'author'   => $this->format_author_label( $author ),
				'payout'   => false,
			);
		}

		return $items;
	}

	private function format_author_label( $author ): string {
		if ( ! $author instanceof \WP_User ) {
			return '';
		}

		return sprintf(
			'%1$d %2$s %3$s',
			absint( $author->ID ),
			sanitize_email( (string) $author->user_email ),
			sanitize_text_field( (string) $author->user_firstname )
		);
	}
}
