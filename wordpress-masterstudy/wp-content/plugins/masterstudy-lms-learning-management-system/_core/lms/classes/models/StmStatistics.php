<?php

namespace stmLms\Classes\Models;

use STM_LMS_Helpers;
use STM_LMS_Options;
use stmLms\Classes\Models\Admin\StmStatisticsListTable;

class StmStatistics {
	public $object;

	public function admin_menu() {
		add_action( 'admin_menu', array( $this, 'add_order_list' ), 100001 );
	}

	public function add_order_list() {
		if ( function_exists( 'masterstudy_lms_resolve_admin_submenu_items' ) ) {
			$registered_items = masterstudy_lms_resolve_admin_submenu_items( true );

			if ( isset( $registered_items['stm_lms_statistics'] ) ) {
				return;
			}
		}

		add_submenu_page(
			'stm-lms-settings',
			__( 'Statistics', 'masterstudy-lms-learning-management-system' ),
			__( '⤷ Statistics', 'masterstudy-lms-learning-management-system' ),
			'manage_options',
			'stm_lms_statistics',
			'masterstudy_lms_render_admin_react_page'
		);
	}

	public function render_statistics() {
		\STM_LMS_Templates::show_lms_template( 'components/admin-react-app/main' );
	}

	public function stm_lms_statistics_screen_option() {
		$option = 'per_page';
		$args   = array(
			'label'   => 'Statistics',
			'default' => 10,
			'option'  => 'stm_lms_statistics_per_page',
		);

		add_screen_option( $option, $args );

		$this->object = new StmStatisticsListTable();
	}

	/**
	 * @return mixed
	 */
	public static function get_author_fee() {
		$author_fee = STM_LMS_Options::get_option( 'author_fee', false );

		return $author_fee ? $author_fee : 10;
	}

	private static function normalize_order_direction( $order ) {
		$order = strtoupper( trim( sanitize_text_field( (string) $order ) ) );

		return in_array( $order, array( 'ASC', 'DESC' ), true ) ? $order : 'ASC';
	}

	private static function normalize_orderby( $orderby, $allowed_columns, $default_column ) {
		$orderby = sanitize_key( (string) $orderby );

		return $allowed_columns[ $orderby ] ?? $default_column;
	}

	private static function normalize_scalar_text_filter( $value ) {
		if ( is_array( $value ) || is_object( $value ) ) {
			return '';
		}

		return sanitize_text_field( (string) $value );
	}

	/**
	 * @param $offset
	 * @param $limit
	 * @param array $params
	 *
	 * @return array
	 */
	public static function get_user_orders( $offset, $limit, $params = array() ) {
		global $wpdb;

		$prefix      = $wpdb->prefix;
		$user_orders = array();
		$query       = StmOrder::query()
			->select( ' _order.*, meta.* ' )
			->asTable( '_order' )
			->join( ' left join `' . $prefix . 'stm_lms_order_items` as lms_order_items on ( lms_order_items.`order_id` = _order.ID ) left join `' . $prefix . 'posts` as course on  (course.ID = lms_order_items.`object_id`) ' )
			->where_in( '_order.post_type', array( 'stm-orders', 'shop_order' ) );

		if ( ! empty( $params['id'] ) ) {
			$query->where( '_order.ID', $params['id'] );
		}

		if ( ! empty( trim( $params['created_date_from'] ?? '' ) ) && ! empty( trim( $params['created_date_to'] ?? '' ) ) ) {
			$query->where_raw(
				$wpdb->prepare(
					' DATE(_order.post_date) >= %s AND DATE(_order.post_date) <= %s ',
					gmdate( 'Y-m-d', strtotime( $params['created_date_from'] ) ),
					gmdate( 'Y-m-d', strtotime( $params['created_date_to'] ) )
				)
			);
		}

		if ( ! empty( $params['total_price'] ) ) {
			$total_price = self::normalize_scalar_text_filter( $params['total_price'] );

			if ( '' !== $total_price ) {
				$query->where_raw(
					$wpdb->prepare(
						' ( meta.meta_key = "_order_total" AND meta.meta_value = %s ) ',
						$total_price
					)
				);
			}
		}

		if ( ! empty( $params['status'] ) ) {
			$status = self::normalize_scalar_text_filter( $params['status'] );

			if ( '' !== $status ) {
				$query->where_raw(
					$wpdb->prepare(
						' (
							( meta.meta_key = "status" AND meta.meta_value = %s ) OR
							( _order.post_status = %s )
						) ',
						$status,
						$status
					)
				);
			}
		}

		if ( ! empty( $params['user'] ) ) {
			$user_id = intval( $params['user'] );
			if ( ! empty( $user_id ) ) {
				$query->where_raw(
					$wpdb->prepare(
						' (
							(meta.meta_key = "user_id" AND meta.meta_value in (%d)) OR
							(meta.meta_key = "_customer_user" AND meta.meta_value in (%d))
						) ',
						$user_id,
						$user_id
					)
				);
			}
		}

		if ( ! empty( $params['post_author'] ) ) {
			$query->where( 'course.`post_author`', (int) $params['post_author'] );
		}

		$allowed_orderby = array(
			'id'        => 'ID',
			'post_date' => 'post_date',
		);
		$orderby         = self::normalize_orderby( $params['orderby'] ?? '', $allowed_orderby, 'ID' );
		$order           = self::normalize_order_direction( $params['order'] ?? 'DESC' );

		$query->sort_by( $orderby )->order( $order );

		$query_total = clone $query;

		$user_orders['total'] = $query_total->select( ' COUNT(DISTINCT _order.ID) as count ' )->findOne()->count ?? 0;
		$query->join( ' left join ' . $prefix . 'postmeta as meta on (meta.post_id = _order.ID)' )
			->group_by( '_order.ID' )
			->limit( $limit )
			->offset( $offset );

		$user_orders['items'] = $query->find();

		return $user_orders;
	}

	/**
	 * @param $offset
	 * @param $limit
	 * @param array $params
	 *
	 * @return array
	 */
	public static function get_user_order_items( $offset, $limit, $params = array() ) {
		global $wpdb;
		$prefix      = $wpdb->prefix;
		$user_orders = array();
		$query       = StmOrderItems::query()
			->select(
				' lms_order_items.*,
			course.post_title as name,
			_order.`post_date` as date_created,
			MAX(CASE WHEN meta.meta_key = "status" THEN meta.meta_value END) as status,
    		MAX(CASE WHEN meta.meta_key = "payment_code" THEN meta.meta_value END) as payment_code'
			)
			->asTable( 'lms_order_items' )
			->join( ' left join `' . $prefix . 'posts` as _order on ( lms_order_items.`order_id` = _order.ID ) left join `' . $prefix . 'posts` as course on  (course.ID = lms_order_items.`object_id`) left join ' . $prefix . 'postmeta as meta on (meta.post_id = _order.ID) ' )
			->where_in( '_order.post_type', array( 'stm-orders', 'shop_order' ) );

		if ( ! empty( $params['id'] ) ) {
			$query->where( '_order.ID', intval( $params['id'] ) );
		}

		if ( ! empty( trim( $params['date_from'] ?? '' ) ) && ! empty( trim( $params['date_to'] ?? '' ) ) ) {
			$query->where_raw(
				$wpdb->prepare(
					' DATE(_order.post_date) >= %s AND DATE(_order.post_date) <= %s ',
					gmdate( 'Y-m-d', strtotime( $params['date_from'] ) ),
					gmdate( 'Y-m-d', strtotime( $params['date_to'] ) )
				)
			);
		}

		if ( ! empty( $params['total_price'] ) ) {
			$total_price = self::normalize_scalar_text_filter( $params['total_price'] );

			if ( '' !== $total_price ) {
				$query->where_raw(
					$wpdb->prepare(
						' ( meta.meta_key = "_order_total" AND meta.meta_value = %s ) ',
						$total_price
					)
				);
			}
		}

		if ( ! empty( $params['status'] ) ) {
			$status = self::normalize_scalar_text_filter( $params['status'] );

			if ( '' !== $status ) {
				$query->where_raw(
					$wpdb->prepare(
						' (
							( meta.meta_key = "status" AND meta.meta_value = %s ) OR
							( _order.post_status = %s )
						) ',
						$status,
						$status
					)
				);
			}
		}

		if ( ! empty( $params['user'] ) ) {
			$user_id = intval( $params['user'] );
			if ( ! empty( $user_id ) ) {
				$query->where_raw(
					$wpdb->prepare(
						' (
							(meta.meta_key = "user_id" AND meta.meta_value in (%d)) OR
							(meta.meta_key = "_customer_user" AND meta.meta_value in (%d))
						) ',
						$user_id,
						$user_id
					)
				);
			}
		}

		if ( ! empty( $params['course_id'] ) ) {
			$query->where( 'course.ID', intval( $params['course_id'] ) );
		}

		if ( ! empty( $params['author_id'] ) ) {
			$query->where( 'course.`post_author`', intval( $params['author_id'] ) );
		}

		if ( ! empty( $params['completed'] ) ) {
			$query->join( ' left join ' . $prefix . "postmeta as meta_status on ( meta_status.post_id = _order.ID AND _order.`post_type` = 'stm-orders' AND  meta_status.`meta_key` = 'status' AND meta_status.`meta_value` = 'completed') " )
				->join( ' left join ' . $prefix . "posts as order_status on ( lms_order_items.`order_id` = order_status.ID AND order_status.`post_status` = 'wc-completed') " )
				->where_raw( ' (  meta_status.post_id = _order.ID OR order_status.ID = _order.ID )  ' );
		}

		$allowed_orderby = array(
			'id'           => 'id',
			'date_created' => 'date_created',
			'name'         => 'name',
			'status'       => 'status',
			'payment_code' => 'payment_code',
		);
		$orderby         = self::normalize_orderby( $params['orderby'] ?? '', $allowed_orderby, 'id' );
		$order           = self::normalize_order_direction( $params['order'] ?? 'DESC' );

		$query->sort_by( $orderby )->order( $order );

		$query_total          = clone $query;
		$user_orders['total'] = $query_total->select( ' COUNT(DISTINCT lms_order_items.id) as count ' )->findOne()->count ?? 0;

		$query_total_price = clone $query;
		$query_total_price->select( ' lms_order_items.id, ( lms_order_items.`price` * lms_order_items.`quantity` ) as total_price ' )
			->group_by( 'lms_order_items.id' );
		$total_price_rows = $query_total_price->find();
		$total_price      = 0;

		if ( ! empty( $total_price_rows ) ) {
			foreach ( $total_price_rows as $total_price_row ) {
				$total_price += (float) ( $total_price_row->total_price ?? 0 );
			}
		}

		$user_orders['total_price']     = ( $total_price ) ? $total_price : 0;
		$user_orders['formatted_price'] = STM_LMS_Helpers::display_price( $user_orders['total_price'] );
		$query->group_by( 'lms_order_items.id' )
			->limit( $limit )
			->offset( $offset );

		$user_orders['items'] = $query->find();

		return $user_orders;
	}

	public static function get_user_orders_api() {
		check_ajax_referer( 'wp_rest', 'nonce' );

		$offset = 0;
		$limit  = 10;

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$params = wp_unslash( $_POST );

		if ( ! empty( $params['offset'] ) ) {
			$offset = intval( $params['offset'] );
		}

		if ( ! empty( $params['limit'] ) ) {
			$limit = intval( $params['limit'] );
		}

		$params['completed'] = true;

		if ( ! empty( $params['author_id'] ) ) {
			$params['author_id'] = (int) $params['author_id'];

			return self::get_user_order_items( $offset, $limit, $params );
		}

		return array();
	}

	/**
	 * @param $date_start
	 * @param $date_end
	 * @param $user_id
	 * @param null $course_id
	 * @param string $group_by
	 *
	 * @return array
	 */
	public static function get_course_statisticas( $date_start, $date_end, $user_id, $course_id = null, $group_by = 'month' ) {
		global $wpdb;

		$group_by         = 'day' === $group_by ? 'day' : 'month';
		$date_group_field = 'day' === $group_by ? 'DATE(_order.post_date)' : "DATE_FORMAT(_order.post_date, '%m-%Y')";

		$data    = array();
		$courses = StmLmsCourse::query()
			->select( ' course.ID, course.`post_title`, _order.`post_date` as date, SUM(order_items.`price` * order_items.`quantity`) as amount' )
			->asTable( 'course' )
			->join( ' left join `' . $wpdb->prefix . 'stm_lms_order_items` as order_items on order_items.`object_id` = course.ID ' )
			->join( ' left join `' . $wpdb->prefix . 'posts` _order on _order.ID = order_items.`order_id` ' )
			->join( ' left join ' . $wpdb->prefix . "postmeta as meta_status on ( meta_status.post_id = _order.ID AND _order.`post_type` = 'stm-orders' AND  meta_status.`meta_key` = 'status' AND meta_status.`meta_value` = 'completed') " )
			->where( 'course.post_author', $user_id )
			->where_raw( " ( course.post_type = 'stm-courses' OR course.post_type = 'stm-course-bundles' OR course.post_type = 'stm-orders' ) " )
			->where_raw( " (_order.`post_status` = 'wc-completed' OR meta_status.post_id = _order.ID) " )
			->where_raw(
				$wpdb->prepare(
					' (DATE(_order.`post_date`) BETWEEN %s AND %s) ',
					$date_start,
					$date_end
				)
			)
			->group_by( " course.ID, {$date_group_field} " );

		if ( null !== $course_id ) {
			$courses->where( 'course.ID', $course_id )->findOne();
		}

		foreach ( $courses->find() as $course ) {
			$data[] = array(
				'id'              => $course->ID,
				'title'           => $course->post_title,
				'amount'          => $course->amount,
				'date'            => $course->date,
				'backgroundColor' => rand_color( 0.50 ),
			);
		}

		return $data;
	}

	/**
	 * @param $user_id
	 * @param null $course_id
	 */
	public static function get_course_sales_statisticas( $user_id, $course_id = null ) {
		global $wpdb;

		$data    = array();
		$courses = StmLmsCourse::query()
			->select( ' course.ID, course.`post_title`, SUM(order_items.`quantity`) as order_item_count ' )
			->asTable( 'course' )
			->join( ' left join `' . $wpdb->prefix . 'stm_lms_order_items` as order_items on order_items.`object_id` = course.ID ' )
			->join( ' left join `' . $wpdb->prefix . 'posts` _order on _order.ID = order_items.`order_id` ' )
			->join( ' left join ' . $wpdb->prefix . "postmeta as meta_status on ( meta_status.post_id = _order.ID AND _order.`post_type` = 'stm-orders' AND  meta_status.`meta_key` = 'status' AND meta_status.`meta_value` = 'completed') " )
			->where( 'course.post_author', $user_id )
			->where_raw( " ( course.post_type = 'stm-courses' OR course.post_type = 'stm-course-bundles' OR course.post_type = 'stm-orders' ) " )
			->where_raw( " (_order.`post_status` = 'wc-completed' OR meta_status.post_id = _order.ID) " )
			->group_by( ' course.ID ' );

		if ( null !== $course_id ) {
			$courses->where( 'course.ID', $course_id )->findOne();
		}

		foreach ( $courses->find() as $course ) {
			$data[] = array(
				'id'               => $course->ID,
				'title'            => $course->post_title,
				'backgroundColor'  => rand_color( 0.50 ),
				'order_item_count' => $course->order_item_count,
			);
		}

		return $data;
	}
}
