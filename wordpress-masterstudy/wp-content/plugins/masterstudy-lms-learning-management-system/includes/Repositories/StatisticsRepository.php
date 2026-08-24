<?php

namespace MasterStudy\Lms\Repositories;

use stmLms\Classes\Models\StmLmsPayout;
use stmLms\Classes\Models\StmStatistics;

final class StatisticsRepository {
	public function get_orders( array $request ): array {
		$page     = max( 1, absint( $request['page'] ?? 1 ) );
		$per_page = max( 1, absint( $request['per_page'] ?? 10 ) );
		$offset   = ( $page - 1 ) * $per_page;
		$params   = $this->map_request_to_legacy_params( $request );
		$result   = StmStatistics::get_user_orders( $offset, $per_page, $params );
		$total    = absint( $result['total'] ?? 0 );

		return array(
			'items'        => is_array( $result['items'] ?? null ) ? $result['items'] : array(),
			'total'        => $total,
			'pages'        => $per_page > 0 ? (int) ceil( $total / $per_page ) : 1,
			'current_page' => $page,
		);
	}

	public function get_summary( array $request ): array {
		$params         = $this->map_request_to_legacy_params( $request );
		$probe          = StmStatistics::get_user_orders( 0, 1, $params );
		$total_orders   = absint( $probe['total'] ?? 0 );
		$total_amount   = 0.0;
		$author_fee     = (float) StmStatistics::get_author_fee();
		$author_percent = $author_fee / 100;

		if ( $total_orders > 0 ) {
			$all_orders  = StmStatistics::get_user_orders( 0, $total_orders, $params );
			$total_amount = $this->calculate_total_amount(
				is_array( $all_orders['items'] ?? null ) ? $all_orders['items'] : array()
			);
		}

		return array(
			'total_amount'        => round( $total_amount, 2 ),
			'admin_commission'    => round( $total_amount - ( $total_amount * $author_percent ), 2 ),
			'instructor_earnings' => round( $total_amount * $author_percent, 2 ),
		);
	}

	public function create_payout(): array {
		$result = StmLmsPayout::pay_now();

		if ( ! is_array( $result ) ) {
			return array(
				'success' => false,
				'message' => esc_html__( 'Unexpected payout response.', 'masterstudy-lms-learning-management-system' ),
			);
		}

		if ( empty( $result ) ) {
			return array(
				'success'       => false,
				'message'       => esc_html__( 'No payouts were created.', 'masterstudy-lms-learning-management-system' ),
				'created_count' => 0,
			);
		}

		if ( isset( $result['success'] ) ) {
			$success = ! empty( $result['success'] );
			$message = sanitize_text_field( (string) ( $result['message'] ?? '' ) );

			if ( '' === $message ) {
				$message = $success
					? esc_html__( 'Payouts were processed successfully.', 'masterstudy-lms-learning-management-system' )
					: esc_html__( 'Unable to create payout.', 'masterstudy-lms-learning-management-system' );
			}

			return array(
				'success'       => $success,
				'message'       => $message,
				'created_count' => absint( $result['created_count'] ?? 0 ),
			);
		}

		if ( $this->is_sequential_array( $result ) ) {
			$created_count = count( $result );

			return array(
				'success'       => $created_count > 0,
				'message'       => sprintf(
					/* translators: %d: number of payouts created */
					_n(
						'%d payout was created.',
						'%d payouts were created.',
						$created_count,
						'masterstudy-lms-learning-management-system'
					),
					$created_count
				),
				'created_count' => $created_count,
			);
		}

		return array(
			'success' => false,
			'message' => esc_html__( 'Unexpected payout response.', 'masterstudy-lms-learning-management-system' ),
		);
	}

	private function is_sequential_array( array $data ): bool {
		return array_keys( $data ) === range( 0, count( $data ) - 1 );
	}

	private function map_request_to_legacy_params( array $request ): array {
		$params = array();

		if ( ! empty( $request['id'] ) ) {
			$params['id'] = absint( $request['id'] );
		}

		if ( ! empty( $request['user'] ) ) {
			$params['user'] = absint( $request['user'] );
		}

		if ( ! empty( $request['author'] ) ) {
			$params['post_author'] = absint( $request['author'] );
		}

		if ( ! empty( $request['date_range'] ) ) {
			$date_range = explode( ',', (string) $request['date_range'] );
			$date_from  = sanitize_text_field( trim( $date_range[0] ?? '' ) );
			$date_to    = sanitize_text_field( trim( $date_range[1] ?? '' ) );

			if ( '' !== $date_from && '' !== $date_to ) {
				$params['created_date_from'] = $date_from;
				$params['created_date_to']   = $date_to;
			}
		}

		if ( ! empty( $request['sort'] ) ) {
			$sort      = \STM_LMS_Helpers::get_sort_params_by_string( (string) $request['sort'] );
			$sort_key  = $sort['key'] ?? '';
			$direction = strtoupper( (string) ( $sort['direction'] ?? 'DESC' ) );
			$order_map = array(
				'id'           => 'ID',
				'created_date' => 'post_date',
			);

			if ( isset( $order_map[ $sort_key ] ) ) {
				$params['orderby'] = $order_map[ $sort_key ];
				$params['order']   = 'ASC' === $direction ? 'ASC' : 'DESC';
			}
		}

		return $params;
	}

	private function calculate_total_amount( array $orders ): float {
		$total = 0.0;

		foreach ( $orders as $order ) {
			$order_id = absint( $order->ID ?? 0 );

			if ( 0 === $order_id ) {
				continue;
			}

			$status = get_post_meta( $order_id, 'status', true );

			if ( ! empty( $status ) && 'completed' === $status ) {
				$order_total = get_post_meta( $order_id, '_order_total', true );

				if ( '' !== (string) $order_total ) {
					$total += (float) $order_total;
				}
			}

			if ( class_exists( 'WooCommerce' ) ) {
				$wc_order = wc_get_order( $order_id );

				if ( $wc_order && 'completed' === $wc_order->get_status() ) {
					$order_total = 0.0;

					foreach ( $wc_order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) ) as $order_item ) {
						$order_total += (float) $order_item->get_total() * (float) $order_item->get_quantity();
					}

					$total += $order_total;
				}
			}
		}

		return $total;
	}
}
