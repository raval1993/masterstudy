<?php

namespace MasterStudy\Lms\Repositories;

final class OrderRepository {

	public function get_all_user_orders( array $request = array() ): array {
		$user     = get_current_user_id();
		$per_page = $request['per_page'] ?? 10;
		$page     = $request['current_page'] ?? 1;
		$offset   = ( $page - 1 ) * $per_page;

		global $wpdb;

		$base_query = "
			LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
			WHERE p.post_type = %s
			AND p.post_status = %s
			AND pm.meta_key = 'user_id'
			AND pm.meta_value = %d
		";

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->posts} p " . $base_query, // phpcs:ignore
				'stm-orders',
				'publish',
				$user
			)
		);

		$results = $wpdb->get_results(
			$wpdb->prepare( // phpcs:ignore
				'SELECT p.ID, p.post_date, p.post_status FROM ' . $wpdb->posts . ' p ' . $base_query . ' ORDER BY p.post_date DESC LIMIT %d OFFSET %d', // phpcs:ignore
				'stm-orders',
				'publish',
				$user,
				$per_page,
				$offset
			),
			ARRAY_A
		);

		$posts = array_map(
			function ( $post ) {
				return \STM_LMS_Order::get_order_info( $post['ID'] );
			},
			$results,
		);

		return array(
			'success'      => true,
			'orders'       => $posts,
			'pages'        => (int) ceil( $total / $per_page ),
			'current_page' => (int) $page,
			'total_orders' => (int) $total,
			'total'        => ( $total <= $offset + $per_page ),
		);
	}

	public function get_all_orders( array $request = array() ): array {
		global $wpdb;

		$per_page   = $request['per_page'] ?? 10;
		$page       = $request['page'] ?? 1;
		$offset     = ( $page - 1 ) * $per_page;
		$search     = isset( $request['search'] ) ? trim( $request['search'] ) : '';
		$status     = $request['status'] ?? '';
		$date_range = isset( $request['date_range'] ) ? trim( $request['date_range'] ) : '';
		$sort       = $request['sort'] ?? '';
		$coupon_id  = $request['coupon_id'] ?? '';

		$joins  = array();
		$wheres = array(
			$wpdb->prepare( 'p.post_type = %s', 'stm-orders' ),
			$wpdb->prepare( 'p.post_status = %s', 'publish' ),
		);

		if ( ! empty( $search ) ) {
			if ( is_numeric( $search ) ) {
				$wheres[] = $wpdb->prepare( 'p.ID = %d', intval( $search ) );
			} else {
				$joins[]  = "LEFT JOIN {$wpdb->users} u ON u.ID = p.post_author";
				$wheres[] = $wpdb->prepare( 'u.display_name LIKE %s', '%' . $wpdb->esc_like( $search ) . '%' );
			}
		}

		$status_join = '';
		if ( ! empty( $status ) ) {
			$status_join = "INNER JOIN {$wpdb->postmeta} pm_status ON pm_status.post_id = p.ID AND pm_status.meta_key = 'status'";
			$joins[]     = $status_join;
			$wheres[]    = $wpdb->prepare( 'pm_status.meta_value = %s', $status );
		}

		if ( ! empty( $coupon_id ) ) {
			$joins[]  = "INNER JOIN {$wpdb->postmeta} pm_coupon ON pm_coupon.post_id = p.ID AND pm_coupon.meta_key = 'coupon_id'";
			$wheres[] = $wpdb->prepare( 'pm_coupon.meta_value = %s', $coupon_id );
		}

		if ( ! empty( $date_range ) ) {
			$dates = explode( ',', $date_range );
			$from  = ! empty( $dates[0] ) ? trim( $dates[0] ) : '';
			$end   = ! empty( $dates[1] ) ? trim( $dates[1] ) : '';

			if ( $from && $end ) {
				$wheres[] = $wpdb->prepare( 'p.post_date BETWEEN %s AND %s', $from . ' 00:00:00', $end . ' 23:59:59' );
			} elseif ( $from ) {
				$wheres[] = $wpdb->prepare( 'p.post_date >= %s', $from . ' 00:00:00' );
			} elseif ( $end ) {
				$wheres[] = $wpdb->prepare( 'p.post_date <= %s', $end . ' 23:59:59' );
			}
		}

		$order_by = 'ORDER BY p.post_date DESC';

		if ( ! empty( $sort ) ) {
			$sort_params = \STM_LMS_Helpers::get_sort_params_by_string( $sort );
			$direction   = $sort_params['direction'];
			$sort_key    = $sort_params['key'];

			switch ( $sort_key ) {
				case 'id':
					$order_by = "ORDER BY p.ID $direction";
					break;
				case 'status':
					$already_joined = '' !== $status_join;
					if ( ! $already_joined ) {
						$joins[]  = "LEFT JOIN {$wpdb->postmeta} pm_status_sort
                                  ON pm_status_sort.post_id = p.ID
                                 AND pm_status_sort.meta_key = 'status'";
						$order_by = "ORDER BY pm_status_sort.meta_value $direction";
					} else {
						$order_by = "ORDER BY pm_status.meta_value $direction";
					}
					break;
				default:
					$order_by = "ORDER BY p.post_date $direction";
			}
		}

		$join_clause  = $joins ? implode( "\n", $joins ) : '';
		$where_clause = $wheres ? 'WHERE ' . implode( ' AND ', $wheres ) : '';

		$total_query = "SELECT COUNT(*) FROM {$wpdb->posts} p $join_clause $where_clause";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$total = $wpdb->get_var( $total_query );

		$main_query = "SELECT p.ID, p.post_date, p.post_status
        FROM {$wpdb->posts} p
        $join_clause
        $where_clause
        $order_by
        LIMIT %d OFFSET %d";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( $wpdb->prepare( $main_query, $per_page, $offset ), ARRAY_A );

		// TODO: Needs optimization (Make one sql query)
		$posts = array_map(
			function ( $post ) {
				return \STM_LMS_Order::get_order_info( $post['ID'] );
			},
			$results
		);

		return array(
			'orders'       => $posts,
			'pages'        => (int) ceil( $total / $per_page ),
			'current_page' => (int) $page,
			'total_orders' => (int) $total,
		);
	}

	private static function delete_order( $user_id, $order_id ) {
		if ( get_post_type( $order_id ) !== 'stm-orders' ) {
			return false;
		}

		if ( ! current_user_can( 'delete_post', $order_id ) ) {
			return false;
		}

		\STM_LMS_Order::remove_order( $user_id, $order_id );
		return wp_delete_post( $order_id, true );
	}

	public function bulk_remove_orders( array $request = array() ) {
		$orders = $request['orders'];

		foreach ( $orders as $order ) {
			$success = self::delete_order( $order['user_id'], $order['id'] );

			if ( false === $success ) {
				return array(
					'id' => $order['id'],
				);
			}
		}

		return true;
	}

	public function bulk_update_orders( array $request = array() ) {
		$orders = $request['orders'];

		foreach ( $orders as $order ) {
			$success = self::update_order( $order['id'], $order );

			if ( false === $success ) {
				return array(
					'id' => $order['id'],
				);
			}
		}

		return true;
	}

	public function update_order( int $order_id, array $request ): bool {
		$order_status = $request['status'];
		$order_note   = $request['note'];

		\STM_LMS_Order::save_order( $order_id, $order_status, $order_note );

		return true;
	}
}
