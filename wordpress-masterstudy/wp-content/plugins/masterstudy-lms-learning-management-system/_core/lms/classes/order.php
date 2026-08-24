<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\SubscriptionPlanType;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;
use MasterStudy\Lms\Pro\RestApi\Repositories\CouponRepository;

STM_LMS_Order::init();

class STM_LMS_Order {
	public static function init() {
		/* Redirect if after PayPal method */
		add_action(
			'template_redirect',
			function () {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$is_paypal = ! empty( $_GET['paypal_order_id'] );

				if ( $is_paypal ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$order_id = intval( $_GET['paypal_order_id'] );
					$location = add_query_arg( 'stm_lms_paypal_order', $order_id, STM_LMS_User::user_page_url() . '#' . $order_id );
					wp_safe_redirect( $location );
				}
			}
		);

		add_action( 'wp_ajax_stm_lms_get_order_info', 'STM_LMS_Order::ajax_get_order_info' );

		add_action( 'wp_ajax_stm_lms_save_order', 'STM_LMS_Order::ajax_save_order' );

		add_action( 'wp_ajax_stm_lms_get_user_orders', 'STM_LMS_Order::user_orders' );

		add_action( 'save_post', 'STM_LMS_Order::save_order', 20 );

		add_filter( 'manage_stm-orders_posts_columns', 'STM_LMS_Order::column_names' );

		add_action( 'manage_stm-orders_posts_custom_column', 'STM_LMS_Order::column_fields', 10, 2 );

		add_action( 'wp_ajax_get_pagination', 'STM_LMS_Order::get_pagination' );
		add_action( 'wp_ajax_nopriv_get_pagination', 'STM_LMS_Order::get_pagination' );

		add_action( 'woocommerce_loaded', array( self::class, 'loaded' ) );
	}

	public static function loaded() {
		if ( STM_LMS_Cart::woocommerce_checkout_enabled() ) {
			require_once STM_LMS_PATH . '/lms/classes/woocommerce/class-wc-product-cpt.php';
			require_once STM_LMS_PATH . '/lms/classes/woocommerce/class-woocommerce-order-item.php';

			add_filter( 'woocommerce_get_order_item_classname', array( self::class, 'get_order_item_classname' ), 20, 2 );

			add_filter( 'woocommerce_data_stores', array( self::class, 'set_course_data_store' ), 10, 1 );

			add_filter( 'woocommerce_hidden_order_itemmeta', array( self::class, 'hidden_order_itemmeta' ), 20, 1 );
		}
	}

	public static function get_order_item_classname( $classname, $item_type ) {
		if ( in_array( $item_type, array( 'line_item', 'product' ), true ) ) {
			$classname = 'STM_Course_Order_Item_Product';
		}

		return $classname;
	}

	/**
	 * The woocommerce_data_stores filter sets the course post type in the product data store.
	 *
	 * @return array
	 */
	public static function set_course_data_store( $stores ) {
		if ( STM_LMS_Cart::woocommerce_checkout_enabled() ) {
			$stores['product'] = 'STM_Course_Data_Store_CPT';
		}

		return $stores;
	}

	public static function hidden_order_itemmeta( $items_meta ) {
		$items_meta[] = '_enterprise_id';
		$items_meta[] = '_bundle_id';
		$items_meta[] = '_masterstudy_lms-course';

		return $items_meta;
	}

	public static function ajax_get_order_info() {
		check_ajax_referer( 'get_order_info', 'nonce' );

		$order_id = absint( wp_unslash( $_GET['order_id'] ?? 0 ) );

		if ( ! self::user_can_view_order( $order_id ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'You are not allowed to view this order.', 'masterstudy-lms-learning-management-system' ),
				),
				403
			);
		}

		$order = self::get_order_info( $order_id );

		wp_send_json( $order );
	}

	public static function user_can_view_order( $order_id, $order = null ) {
		$order_id = absint( $order_id );
		if ( empty( $order_id ) ) {
			return false;
		}

		if ( null === $order && STM_LMS_Cart::woocommerce_checkout_enabled() && function_exists( 'wc_get_order' ) ) {
			$order = wc_get_order( $order_id );
		}

		$woocommerce_order = $order instanceof WC_Order;
		if ( ! $woocommerce_order && PostType::ORDER !== get_post_type( $order_id ) ) {
			return false;
		}

		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		$current_user_id = get_current_user_id();
		$order_owner_id  = $woocommerce_order ? $order->get_user_id() : get_post_meta( $order_id, 'user_id', true );

		return 0 < $current_user_id && absint( $order_owner_id ) === $current_user_id;
	}

	public static function instructor_can_access_order( $order_id, $user_id = 0 ) {
		$order_id = absint( $order_id );
		$user_id  = absint( $user_id ? $user_id : get_current_user_id() );

		if ( empty( $order_id ) || empty( $user_id ) || ! STM_LMS_Instructor::is_instructor( $user_id ) ) {
			return false;
		}

		global $wpdb;

		if ( STM_LMS_Cart::woocommerce_checkout_enabled() && function_exists( 'wc_get_order' ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$course_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT p.ID
					FROM {$wpdb->posts} p
					INNER JOIN {$wpdb->prefix}wc_order_product_lookup product_lookup ON product_lookup.product_id = p.ID
					WHERE product_lookup.order_id = %d
					AND p.post_type IN ( %s, %s )
					AND p.post_author = %d
					LIMIT 1",
					$order_id,
					PostType::COURSE,
					'stm-course-bundles',
					$user_id
				)
			);
		} else {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$course_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT p.ID
					FROM {$wpdb->prefix}stm_lms_order_items order_item
					INNER JOIN {$wpdb->posts} p ON order_item.object_id = p.ID
					WHERE order_item.order_id = %d
					AND p.post_author = %d
					LIMIT 1",
					$order_id,
					$user_id
				)
			);
		}

		return ! empty( $course_id );
	}

	public static function get_order_info( $order_id = '' ) {
		$checkout_enabled  = STM_LMS_Cart::woocommerce_checkout_enabled();
		$order             = $checkout_enabled ? wc_get_order( $order_id ) : null;
		$woocommerce_order = $order instanceof WC_Order;

		if ( ! self::user_can_view_order( $order_id, $order ) ) {
			if ( ! $woocommerce_order ) {
				STM_LMS_Templates::show_lms_template( 'stm-lms-login' );

				return array();
			}
			die;
		}

		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );

		if ( $woocommerce_order ) {
			$order_meta = apply_filters( 'stm_lms_order_details', array(), $order_id );
		} else {
			$order_meta = STM_LMS_Helpers::parse_meta_field( $order_id );

			// Parse meta fields is not getting hidden post meta, so we need to get them manually
			$order_meta['_order_total']    = get_post_meta( $order_id, '_order_total', true );
			$order_meta['_order_subtotal'] = get_post_meta( $order_id, '_order_subtotal', true );
			$order_meta['_order_taxes']    = get_post_meta( $order_id, '_order_taxes', true );
		}

		$cart_items = array();
		$total      = 0;

		$can_load_subscription_plans = STM_LMS_Helpers::is_pro_plus()
			&& is_ms_lms_addon_enabled( 'subscriptions' )
			&& class_exists( 'MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository' )
			&& function_exists( 'stm_lms_subscription_plans_table_name' );
		$can_load_subscriptions      = STM_LMS_Helpers::is_pro_plus()
			&& is_ms_lms_addon_enabled( 'subscriptions' )
			&& class_exists( 'MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository' )
			&& function_exists( 'stm_lms_subscriptions_table_name' );

		if ( $can_load_subscription_plans ) {
			if ( isset( $order_meta['subscription_id'] ) && intval( $order_meta['subscription_id'] ) ) {
				$order_meta['subscription_order_count'] = ( new SubscriptionPlanRepository() )->get_subscription_orders_with_queue( $order_id, intval( $order_meta['subscription_id'] ) );
			}
		}

		if ( isset( $order_meta['items'] ) && is_array( $order_meta['items'] ) ) {
			foreach ( $order_meta['items'] as $order_item ) {
				$item_id         = $order_item['item_id'];
				$terms           = stm_lms_get_terms_array( $item_id, 'stm_lms_course_taxonomy', 'name' );
				$is_subscription = $order_item['is_subscription'] ?? false;

				$cart_item = array(
					'item_id'                    => $item_id,
					'thumbnail_id'               => get_post_thumbnail_id( $item_id ),
					'title'                      => ! empty( $order_item['title'] ) ? $order_item['title'] : get_the_title( $item_id ),
					'link'                       => get_the_permalink( $item_id ),
					'image'                      => get_the_post_thumbnail( $item_id, 'img-300-225' ),
					'image_url'                  => get_the_post_thumbnail_url( $item_id ),
					'image_full'                 => get_the_post_thumbnail( $item_id, 'full' ),
					'placeholder'                => STM_LMS_URL . 'assets/img/image_not_found.png',
					'price'                      => $order_item['price'],
					'terms'                      => ! empty( $terms ) ? $terms : wp_list_pluck( get_the_terms( $item_id, 'product_cat' ), 'name' ),
					'price_formatted'            => STM_LMS_Helpers::display_price( $order_item['price'] ),
					'price_with_taxes'           => STM_LMS_Helpers::display_price_with_taxes( $order_item['price'], null, true ),
					'price_with_taxes_formatted' => STM_LMS_Helpers::display_price_with_taxes( $order_item['price'] ),
				);

				if ( ! $is_subscription ) {
					$cart_item['link']       = get_the_permalink( $item_id );
					$cart_item['image']      = get_the_post_thumbnail( $item_id, 'img-300-225' );
					$cart_item['image_url']  = get_the_post_thumbnail_url( $item_id, 'img-300-225' );
					$cart_item['image_full'] = get_the_post_thumbnail( $item_id, 'full' );
				} elseif ( $can_load_subscription_plans ) {
					$subscription_plan              = ( new SubscriptionPlanRepository() )->get( $item_id );
					$cart_item['title']             = $subscription_plan['name'] ?? esc_html__( 'N/A', 'masterstudy-lms-learning-management-system' );
					$cart_item['subscription_type'] = SubscriptionPlanRepository::get_plan_type( $subscription_plan['type'] ?? '' );

					if ( SubscriptionPlanType::COURSE === $subscription_plan['type'] ) {
						$course_item            = $subscription_plan['items'][0];
						$cart_item['image_url'] = get_the_post_thumbnail_url( $course_item['object_id'] );
					}
				}

				$total += $order_item['price'];

				$cart_item['enterprise_id'] = ! empty( $order_item['enterprise_id'] ) ? $order_item['enterprise_id'] : null;
				$cart_item['bundle_id']     = ! empty( $order_item['bundle_id'] ) ? $order_item['bundle_id'] : null;
				$cart_item['enterprise']    = ! empty( $order_item['enterprise'] ) ? $order_item['enterprise'] : null;
				$cart_item['bundle']        = ! empty( $order_item['bundle'] ) ? $order_item['bundle'] : null;

				if ( ! empty( $order_item['downloads'] ) ) {
					foreach ( $order_item['downloads'] as $download ) {
						$cart_item['downloads'][] = array(
							'name'                => $download['name'] ?? '',
							'url'                 => $download['url'] ?? '',
							'downloads_remaining' => $download['downloads_remaining'] ? $download['downloads_remaining'] : '∞',
							'access_expires'      => $download['access_expires'] ?? __( 'Never', 'masterstudy-lms-learning-management-system' ),
						);
					}
				}

				$bundle          = ! empty( $cart_item['bundle_id'] ) ? $cart_item['bundle_id'] : $cart_item['bundle'];
				$bundle_count    = self::get_bundle_courses_count( $bundle );
				$enterprise      = ! empty( $cart_item['enterprise_id'] ) ? $cart_item['enterprise_id'] : $cart_item['enterprise'];
				$enterprise_name = '';

				if ( ! empty( $enterprise ) ) {
					$enterprise_name = get_the_title( (int) $enterprise );
				}

				$cart_item['bundle_courses_count'] = $bundle_count;
				$cart_item['enterprise_name']      = $enterprise_name;

				$cart_items[] = $cart_item;
			}
		}

		$i18n = self::translates();

		$cart_total  = $total;
		$coupon_data = self::get_coupon_data( $order_id, $cart_total );

		$timezone = get_option( 'gmt_offset' );
		$diff     = ! empty( $timezone ) ? $timezone * 60 * 60 : 0;
		$diff     = apply_filters( 'stm_lms_gmt_offset', $diff );
		$total    = isset( $order_meta['_order_total'] ) ? (float) $order_meta['_order_total'] : (float) $total;
		$taxes    = isset( $order_meta['_order_taxes'] ) ? (float) $order_meta['_order_taxes'] : 0;
		$subtotal = isset( $order_meta['_order_subtotal'] ) ? (float) $order_meta['_order_subtotal'] : (float) $total;

		$result                  = $order_meta['personal_data'] ?? array();
		$is_result_empty         = empty( $result );
		$should_check_for_coupon = empty( $coupon_data ) && isset( $order_meta['subscription_order_count'] ) && $order_meta['subscription_order_count'] > 1;

		if ( $is_result_empty || $should_check_for_coupon ) {
			$subs_id = get_post_meta( $order_id, 'subscription_id', true );
			if ( null !== $subs_id && '' !== $subs_id && $can_load_subscriptions ) {
				$get_subscription = ( new SubscriptionRepository() )->get( intval( $subs_id ) );
				$first_order_id   = $get_subscription['first_order_id'] ?? 0;

				if ( $is_result_empty ) {
					$result = get_post_meta( $first_order_id, 'personal_data', true ) ?? array();
				}

				if ( $should_check_for_coupon ) {
					$coupon      = self::get_coupon_data( $first_order_id, $cart_total );
					$coupon_data = array(
						'first_order_coupon' => empty( $coupon ) ? null : $coupon,
					);
				}
			}
		}

		return array_merge(
			$order_meta,
			$i18n,
			$coupon_data,
			array(
				'id'                 => $order_id,
				'date'               => $order_meta['date'] ?? '',
				'date_formatted'     => isset( $order_meta['date'] )
					? date_i18n( $date_format . ' ' . $time_format, $order_meta['date'] + $diff )
					: '',
				'cart_items'         => $cart_items,
				'total'              => $total,
				'subtotal'           => $subtotal,
				'taxes'              => $taxes,
				'total_formatted'    => STM_LMS_Helpers::display_price( $total ),
				'total_with_taxes'   => STM_LMS_Helpers::display_price_with_taxes( $total ),
				'subtotal_formatted' => STM_LMS_Helpers::display_price( $subtotal ),
				'taxes_formatted'    => STM_LMS_Helpers::display_price( $taxes ),
				'personal_data'      => $result,
				'user'               => isset( $order_meta['user_id'] )
					? STM_LMS_User::get_current_user( $order_meta['user_id'] )
					: null,
				'locale'             => get_locale(),
			)
		);
	}

	//Get pagination for orders
	public static function get_pagination() {
		// This public endpoint only renders pagination markup from integer values and does not read or change stored data.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['total_pages'] ) || ! isset( $_POST['current_page'] ) ) {
			wp_send_json_error( 'Invalid data', 400 );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$total_pages = absint( wp_unslash( $_POST['total_pages'] ) );
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$current_page = absint( wp_unslash( $_POST['current_page'] ) );

		ob_start();

		STM_LMS_Templates::show_lms_template(
			'components/pagination',
			array(
				'max_visible_pages' => 3,
				'total_pages'       => $total_pages,
				'current_page'      => $current_page,
				'dark_mode'         => false,
				'is_queryable'      => false,
				'done_indicator'    => false,
				'is_hidden'         => false,
			)
		);
		$pagination_html = ob_get_clean();

		wp_send_json_success(
			array(
				'pagination' => $pagination_html,
			)
		);
	}

	//Get bundle for orders
	public static function get_bundle_courses_count( $bundle ) {
		$bundle_ids = get_post_meta( (int) $bundle, 'stm_lms_bundle_ids', true );
		return ! empty( $bundle_ids ) ? ( is_array( $bundle_ids ) ? count( $bundle_ids ) : count( explode( ',', $bundle_ids ) ) ) : 0;
	}

	public static function get_instructor_order_info( $order_id = '' ) {
		$current_user_id = get_current_user_id();

		if ( empty( $order_id ) || ! is_user_logged_in()
			|| ( ! current_user_can( 'manage_options' ) && ! STM_LMS_Instructor::is_instructor() ) ) {
			die;
		}

		global $wpdb;

		$cart_items = array();
		$total      = 0;

		if ( STM_LMS_Cart::woocommerce_checkout_enabled() && function_exists( 'wc_get_order' ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$courses = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT
                        p.ID AS course_id,
                        product_lookup.product_net_revenue as price,
						COALESCE(product_lookup.tax_amount, 0) AS price_tax,
                        enterprise.meta_value as enterprise_id,
                        bundle.meta_value as bundle_id
                        FROM {$wpdb->posts} p
                        INNER JOIN {$wpdb->prefix}wc_order_product_lookup product_lookup ON product_lookup.product_id = p.ID AND product_lookup.order_id = %d
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta enterprise ON enterprise.order_item_id = product_lookup.order_item_id AND enterprise.meta_key = '_enterprise_id'
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta bundle ON bundle.order_item_id = product_lookup.order_item_id AND bundle.meta_key = '_bundle_id'
                        WHERE p.post_type IN ( %s, %s ) AND p.post_author = %d",
					$order_id,
					PostType::COURSE,
					'stm-course-bundles',
					$current_user_id
				)
			);
		} else {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$courses = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT oi.object_id AS course_id, oi.price, pm.meta_value as items
					FROM {$wpdb->prefix}stm_lms_order_items oi
					INNER JOIN {$wpdb->posts} p ON oi.object_id = p.ID
					INNER JOIN {$wpdb->postmeta} pm ON pm.post_id = oi.order_id AND pm.meta_key = 'items'
					WHERE oi.order_id = %d
					AND p.post_author = %d",
					$order_id,
					$current_user_id
				)
			);
		}

		if ( empty( $courses ) && ! current_user_can( 'manage_options' ) ) {
			die;
		}

		foreach ( $courses as $course ) {
			$terms = stm_lms_get_terms_array( $course->course_id, 'stm_lms_course_taxonomy', 'name' );

			if ( ! empty( $course->items ) ) {
				$items         = maybe_unserialize( $course->items );
				$enterprise_id = wp_list_filter(
					$items,
					array(
						'item_id'    => $course->course_id,
						'enterprise' => true,
					)
				);

				$course->enterprise_id = ! empty( $enterprise_id ) ? reset( $enterprise_id )['enterprise'] : null;

				$bundle_id = wp_list_filter(
					$items,
					array(
						'item_id' => $course->course_id,
						'bundle'  => true,
					)
				);

				$course->bundle_id = ! empty( $bundle_id ) ? reset( $bundle_id )['bundle'] : null;
			}

			$price_net   = isset( $course->price ) ? (float) $course->price : 0.0;
			$price_tax   = isset( $course->price_tax ) ? (float) $course->price_tax : 0.0;
			$price_gross = $price_net + $price_tax;

			$cart_items[] = array(
				'thumbnail_id'         => get_post_thumbnail_id( $course->course_id ),
				'title'                => get_the_title( $course->course_id ),
				'link'                 => get_the_permalink( $course->course_id ),
				'image'                => get_the_post_thumbnail( $course->course_id, 'img-300-225' ),
				'image_full'           => get_the_post_thumbnail( $course->course_id, 'full' ),
				'placeholder'          => STM_LMS_URL . 'assets/img/image_not_found.png',
				'price'                => $course->price,
				'terms'                => ! empty( $terms ) ? $terms : wp_list_pluck( get_the_terms( $course->course_id, 'product_cat' ), 'name' ),
				'price_formatted'      => STM_LMS_Helpers::display_price_with_taxes( $price_gross ),
				'enterprise_id'        => ! empty( $course->enterprise_id ) ? $course->enterprise_id : null,
				'bundle_id'            => ! empty( $course->bundle_id ) ? $course->bundle_id : null,
				'bundle_courses_count' => self::get_bundle_courses_count( $course->bundle_id ),
			);
		}

		$order_meta = apply_filters( 'stm_lms_order_details', array(), $order_id );
		if ( empty( $order_meta ) ) {
			$order_meta = STM_LMS_Helpers::parse_meta_field( $order_id );
		}

		if ( STM_LMS_Helpers::is_pro_plus() ) {
			$coupon_id    = get_post_meta( $order_id, 'coupon_id', true );
			$coupon_value = get_post_meta( $order_id, 'coupon_value', true );
			$coupon_type  = get_post_meta( $order_id, 'coupon_type', true );

			if ( ! empty( $coupon_value ) ) {
				$coupon_value = '-' . ( 'amount' === $coupon_type ? STM_LMS_Helpers::display_price( (float) $coupon_value ) : $coupon_value . '%' );
			}
		}

		$i18n     = self::translates();
		$timezone = get_option( 'gmt_offset' );
		$diff     = ! empty( $timezone ) ? $timezone * 60 * 60 : 0;
		$diff     = apply_filters( 'stm_lms_gmt_offset', $diff );
		$taxes    = ! empty( $order_meta['_order_taxes'] ) ? (float) $order_meta['_order_taxes'] : 0;
		$subtotal = ! empty( $order_meta['_order_subtotal'] ) ? (float) $order_meta['_order_subtotal'] : 0;
		$total    = ! empty( $order_meta['_order_total'] ) ? (float) $order_meta['_order_total'] : 0;

		return array_merge(
			$order_meta,
			$i18n,
			array(
				'id'                 => $order_id,
				'date'               => $order_meta['date'] ?? '',
				'date_formatted'     => isset( $order_meta['date'] )
					? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $order_meta['date'] + $diff )
					: '',
				'cart_items'         => $cart_items,
				'total'              => $total,
				'subtotal'           => $subtotal,
				'taxes'              => $taxes,
				'total_formatted'    => STM_LMS_Helpers::display_price( $total ),
				'subtotal_formatted' => STM_LMS_Helpers::display_price( $subtotal ),
				'taxes_formatted'    => STM_LMS_Helpers::display_price( $taxes ),
				'user'               => isset( $order_meta['user_id'] )
					? STM_LMS_User::get_current_user( $order_meta['user_id'] )
					: null,
				'course_info'        => ! empty( $order_meta['course_info'] ) ? $order_meta['course_info'] : null,
				'coupon_value'       => $coupon_value ?? null,
				'coupon_type'        => $coupon_type ?? null,
				'coupon_id'          => $coupon_id ?? null,
			)
		);
	}

	/**
	 * @param $data [user_id, cart_items, payment_code, _order_total, _order_currency]
	 * @param bool $return
	 *
	 * @return null|int|WP_Error
	 */
	public static function create_order( $data, $return = false ) {
		if ( empty( $data['user_id'] ) ) {
			die;
		}

		$order_info = array(
			'user_id'         => $data['user_id'],
			'items'           => $data['cart_items'],
			'date'            => time(),
			'status'          => 'pending',
			'payment_code'    => $data['payment_code'],
			'order_key'       => uniqid( $data['user_id'] . time() ),
			'order_note'      => '',
			'_order_total'    => $data['_order_total'],
			'_order_taxes'    => $data['_order_taxes'],
			'_order_subtotal' => $data['_order_subtotal'],
			'_order_currency' => $data['_order_currency'],
			'coupon_value'    => $data['coupon_value'] ?? null,
			'coupon_type'     => $data['coupon_type'] ?? null,
			'coupon_id'       => $data['coupon_id'] ?? null,
			'is_subscription' => $data['is_subscription'] ?? false,
		);

		$order_post = array(
			'post_type'   => PostType::ORDER,
			'post_title'  => wp_strip_all_tags( $order_info['order_key'] ),
			'post_status' => 'publish',
		);

		$order_id = wp_insert_post( $order_post );

		foreach ( $order_info as $meta_key => $meta_value ) {
			update_post_meta( $order_id, $meta_key, $meta_value );
		}

		if ( $order_info['is_subscription'] ) {
			update_post_meta( $order_id, 'plan', $data['plan'] );
		}

		if ( ! empty( $data['course_info'] ) && is_array( $data['course_info'] ) ) {
			update_post_meta( $order_id, 'course_info', $data['course_info'] );
		}

		if ( ! empty( $data['personal_data'] ) && is_array( $data['personal_data'] ) ) {
			update_post_meta( $order_id, 'personal_data', $data['personal_data'] );
		}

		if ( $return ) {
			return $order_id;
		}
	}

	public static function translates() {
		return array(
			'i18n' => array(
				'order_key'        => esc_html__( 'Order key', 'masterstudy-lms-learning-management-system' ),
				'date'             => esc_html__( 'Date', 'masterstudy-lms-learning-management-system' ),
				'status'           => esc_html__( 'Status', 'masterstudy-lms-learning-management-system' ),
				'pending'          => esc_html__( 'Pending', 'masterstudy-lms-learning-management-system' ),
				'processing'       => esc_html__( 'Processing', 'masterstudy-lms-learning-management-system' ),
				'failed'           => esc_html__( 'Failed', 'masterstudy-lms-learning-management-system' ),
				'on-hold'          => esc_html__( 'On hold', 'masterstudy-lms-learning-management-system' ),
				'refunded'         => esc_html__( 'Refunded', 'masterstudy-lms-learning-management-system' ),
				'completed'        => esc_html__( 'Completed', 'masterstudy-lms-learning-management-system' ),
				'cancelled'        => esc_html__( 'Cancelled', 'masterstudy-lms-learning-management-system' ),
				'user'             => esc_html__( 'User', 'masterstudy-lms-learning-management-system' ),
				'order_items'      => esc_html__( 'Order items', 'masterstudy-lms-learning-management-system' ),
				'course_name'      => esc_html__( 'Course name', 'masterstudy-lms-learning-management-system' ),
				'course_price'     => esc_html__( 'Course price', 'masterstudy-lms-learning-management-system' ),
				'total'            => esc_html__( 'Total', 'masterstudy-lms-learning-management-system' ),
				'subtotal'         => esc_html__( 'Subtotal', 'masterstudy-lms-learning-management-system' ),
				'taxes'            => esc_html__( 'Taxes', 'masterstudy-lms-learning-management-system' ),
				'bundle'           => esc_html__( 'Courses in bundle', 'masterstudy-lms-learning-management-system' ),
				'enterprise'       => esc_html__( 'for group', 'masterstudy-lms-learning-management-system' ),
				'payment_plan'     => esc_html__( 'Payment Plan:', 'masterstudy-lms-learning-management-system' ),
				'daily_payments'   => esc_html__( 'daily payments', 'masterstudy-lms-learning-management-system' ),
				'weekly_payments'  => esc_html__( 'weekly payments', 'masterstudy-lms-learning-management-system' ),
				'monthly_payments' => esc_html__( 'monthly payments', 'masterstudy-lms-learning-management-system' ),
				'yearly_payments'  => esc_html__( 'yearly payments', 'masterstudy-lms-learning-management-system' ),
				'days'             => esc_html__( 'day(s)', 'masterstudy-lms-learning-management-system' ),
			),
		);
	}

	public static function ajax_save_order() {
		check_ajax_referer( 'save_order', 'nonce' );

		$order_id = absint( wp_unslash( $_POST['order_id'] ?? 0 ) );
		if ( empty( $order_id ) || PostType::ORDER !== get_post_type( $order_id ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Invalid order.', 'masterstudy-lms-learning-management-system' ),
				),
				400
			);
		}

		if ( ! current_user_can( 'manage_options' ) && ! self::instructor_can_access_order( $order_id ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'You are not allowed to update this order.', 'masterstudy-lms-learning-management-system' ),
				),
				403
			);
		}

		$order_note = wp_kses_post( wp_unslash( $_POST['order_note'] ?? '' ) );
		$status     = sanitize_text_field( get_post_meta( $order_id, 'status', true ) );

		self::save_order( $order_id, $status, $order_note );

		wp_send_json( array( 'success' => true ) );
	}

	public static function save_order( $post_id, $order_status = null, $order_note = null ) {
		if ( ! is_user_logged_in() || 'stm-orders' !== get_post_type( $post_id ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$status = sanitize_text_field( wp_unslash( $order_status ?? $_POST['order_status'] ?? '' ) );

		$user_id         = get_post_meta( $post_id, 'user_id', true );
		$previous_status = get_post_meta( $post_id, 'status', true );

		/*If status changed*/
		if ( $previous_status !== $status ) {
			if ( 'completed' === $status && ! empty( $user_id ) ) {
				update_post_meta( $post_id, 'status', $status );
				self::accept_order( $user_id, $post_id );
			} elseif ( 'cancelled' === $status && ! empty( $user_id ) ) {
				update_post_meta( $post_id, 'status', $status );
				self::remove_order( $user_id, $post_id );
			} else {
				update_post_meta( $post_id, 'status', $status );
			}
		}

		if ( null !== $order_note ) {
			update_post_meta( $post_id, 'order_note', $order_note );
		}
	}

	public static function accept_order( $user_id, $order_id = '' ) {
		$accept_order = apply_filters( 'stm_lms_accept_order', true );
		$cart_items   = stm_lms_get_cart_items( $user_id, apply_filters( 'stm_lms_cart_items_fields', array( 'item_id', 'price' ) ) );

		if ( ! empty( $order_id ) ) {
			$cart_items = get_post_meta( $order_id, 'items', true );
		}

		if ( $accept_order ) {
			foreach ( $cart_items as $cart_item ) {
				if ( 1 === (int) $cart_item['is_subscription'] ) {
					continue;
				}
				STM_LMS_Course::add_user_course( $cart_item['item_id'], $user_id, 0, 0 );
				STM_LMS_Course::add_student( $cart_item['item_id'] );
			}

			/*Delete Cart*/
			stm_lms_get_delete_cart_items( $user_id );
		}

		do_action( 'stm_lms_order_accepted', $user_id, $cart_items );
	}

	public static function remove_order( $user_id, $order_id ) {
		$cart_items = get_post_meta( $order_id, 'items', true );

		foreach ( $cart_items as $cart_item ) {
			stm_lms_get_delete_user_course( $user_id, $cart_item['item_id'] );
			STM_LMS_Course::remove_student( $cart_item['item_id'] );
			do_action( 'stm_lms_order_remove', $cart_item['item_id'], $cart_item, $user_id );
		}

		/*Delete Cart*/
		stm_lms_get_delete_cart_items( $user_id );
	}

	public static function user_orders() {
		check_ajax_referer( 'user_orders', 'nonce' );

		$user = STM_LMS_User::get_current_user();
		if ( empty( $user['id'] ) ) {
			die;
		}

		$user_id = $user['id'];
		$posts   = array();
		$pp      = get_option( 'posts_per_page' );
		$offset  = intval( $_GET['offset'] ?? 0 ) * $pp;

		$user_orders = apply_filters( 'stm_lms_user_orders', array(), $user_id, $pp, $offset );

		if ( empty( $user_orders ) ) {
			$args = array(
				'post_type'      => 'stm-orders',
				'posts_per_page' => $pp,
				'post_status'    => 'publish',
				'offset'         => $offset,
				'meta_query'     => array(
					array(
						'key'     => 'user_id',
						'compare' => '=',
						'value'   => $user_id,
					),
				),
			);

			$q     = new WP_Query( $args );
			$total = $q->found_posts;

			if ( $q->have_posts() ) {
				while ( $q->have_posts() ) {
					$q->the_post();
					$posts[] = self::get_order_info( get_the_ID() );
				}

				wp_reset_postdata();
			}
		} else {
			extract( $user_orders );
		}

		wp_send_json(
			array(
				'pages'        => ceil( $total / $pp ),
				'current_page' => $offset + 1,
				'total_posts'  => $total,
				'posts'        => $posts,
				'total'        => $total <= $offset + $pp,
			)
		);
	}

	public static function column_names( $columns ) {
		unset( $columns['cb'] );
		unset( $columns['title'] );

		$lms_id = array(
			'cb'           => '<input type="checkbox" />',
			'lms_id'       => esc_html__( 'Order', 'masterstudy-lms-learning-management-system' ),
			'order_key'    => esc_html__( 'Order Key', 'masterstudy-lms-learning-management-system' ),
			'order_status' => esc_html__( 'Order Status', 'masterstudy-lms-learning-management-system' ),
		);

		return array_merge( $lms_id, $columns );
	}

	public static function column_fields( $column, $post_id ) {
		$edit_link  = get_edit_post_link( $post_id );
		$title      = get_the_title( $post_id );
		$order_meta = STM_LMS_Helpers::parse_meta_field( $post_id );
		$user       = STM_LMS_User::get_current_user( $order_meta['user_id'] );

		switch ( $column ) {
			case 'lms_id':
				$user_login = $user['login'];
				echo wp_kses_post( "<a class='row-title' href='{$edit_link}'>#{$post_id} {$user_login}</a>" );
				break;
			case 'order_key':
				echo wp_kses_post( $title );
				break;
			case 'order_status':
				$status = isset( $order_meta['status'] ) ? $order_meta['status'] : '';
				echo wp_kses_post( "<span class='stm_lms_status stm_lms_status_{$status}'>{$status}</span>" );
				break;
		}
	}

	public static function has_purchased_courses( $user_id, $course_id ) {
		$is_bought      = false;
		$is_woocommerce = STM_LMS_Cart::woocommerce_checkout_enabled();

		if ( $is_woocommerce && class_exists( 'STM_LMS_Woocommerce' ) ) {
			$is_bought = STM_LMS_Woocommerce::has_course_been_purchased( $user_id, $course_id );
		} else {
			$query = new WP_Query(
				array(
					'post_type'   => 'stm-orders',
					'post_status' => 'publish',
					'meta_query'  => array(
						'relation' => 'AND',
						array(
							'key'     => 'user_id',
							'compare' => '=',
							'value'   => $user_id,
						),
						array(
							'key'     => 'status',
							'compare' => '=',
							'value'   => 'completed',
						),
						array(
							'key'     => 'items',
							'compare' => 'LIKE',
							'value'   => $course_id,
						),
					),
				)
			);

			if ( $query->found_posts > 0 ) {
				$is_bought = true;
			}
		}

		return $is_bought;
	}

	public static function is_purchased_by_enterprise( $course, $user_id ) {
		$in_enterprise = false;

		if ( isset( $course['enterprise_id'] ) && class_exists( 'STM_LMS_Enterprise_Courses' ) ) {
			$group_users = STM_LMS_Enterprise_Courses::get_group_users( $course['enterprise_id'] );
			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			$in_enterprise = ! empty( $group_users ) && is_array( $group_users ) && in_array( $user_id, $group_users );
		}

		return $in_enterprise;
	}

	public static function get_status_name( $status ) {
		$translates = self::translates();
		return $translates['i18n'][ $status ] ?? $status;
	}

	public static function get_payment_method_name( $method ) {
		switch ( $method ) {
			case 'cash':
				return esc_html__( 'Cash', 'masterstudy-lms-learning-management-system' );
			case 'wire_transfer':
				return esc_html__( 'Wire transfer', 'masterstudy-lms-learning-management-system' );
			case 'mollie':
				return esc_html__( 'Mollie', 'masterstudy-lms-learning-management-system' );
			case 'paystack':
				return esc_html__( 'Paystack', 'masterstudy-lms-learning-management-system' );
			default:
				return $method;
		}
	}

	private static function get_coupon_data( int $order_id, int $total ): array {
		if ( STM_LMS_Helpers::is_coupons_enabled() ) {
			$coupon_id    = get_post_meta( $order_id, 'coupon_id', true );
			$coupon_value = get_post_meta( $order_id, 'coupon_value', true );
			$coupon_type  = get_post_meta( $order_id, 'coupon_type', true );

			if ( empty( $coupon_id ) || empty( $coupon_value ) ) {
				return array();
			}

			$original_coupon_value = $coupon_value;

			if ( 'amount' === $coupon_type ) {
				$coupon_value = min( (float) $coupon_value, $total );
			}

			$coupon_value                = '-' . ( 'amount' === $coupon_type ? STM_LMS_Helpers::display_price( (float) $coupon_value ) : $coupon_value . '%' );
			$coupon_discount             = STM_LMS_Helpers::calculate_coupon_discount( $total, (float) $original_coupon_value, $coupon_type );
			$coupon_item_price_formatted = STM_LMS_Helpers::display_price_with_taxes( max( 0, $total - $coupon_discount ) );

			$coupon = ( new CouponRepository() )->get( (int) $coupon_id );

			return array(
				'coupon_data'                 => $coupon,
				'coupon_item_discount'        => STM_LMS_Helpers::display_price_with_taxes( $coupon_discount, null, true ),
				'coupon_value'                => $coupon_value,
				'coupon_item_price_formatted' => $coupon_item_price_formatted,
				'original_coupon_value'       => $coupon_value,
				'coupon_type'                 => $coupon_type,
			);
		}

		return array();
	}
}
