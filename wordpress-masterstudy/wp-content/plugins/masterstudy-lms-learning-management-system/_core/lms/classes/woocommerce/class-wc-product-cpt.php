<?php

class STM_Course_Data_Store_CPT extends WC_Product_Data_Store_CPT implements WC_Object_Data_Store_Interface, WC_Product_Data_Store_Interface {

	/**
	 * Method to read a product from the database.
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @throws Exception If invalid product.
	 */
	public function read( &$product ) {
		add_filter( 'woocommerce_is_purchasable', '__return_true' );

		$product->set_defaults();

		// Determine LMS post types
		$lms_types   = $this->get_lms_post_types();
		$post_types  = array_merge( $lms_types, array( 'product' ) );
		$post_object = get_post( $product->get_id() );

		if ( ! $post_object || ! in_array( $post_object->post_type, $post_types, true ) ) {
			throw new Exception( __( 'Invalid product.', 'masterstudy-lms-learning-management-system' ) );
		}

		// Set basic product properties
		$this->set_product_props( $product, $post_object );

		// Set price for course bundles or regular products
		$this->set_product_price( $product, $post_object );

		// Set product image and other properties
		$this->set_product_image( $product, $post_object );

		$product->set_virtual( true );
		$product->set_sold_individually( true );
		$product->set_downloadable( true );

		$this->read_attributes( $product );
		$this->read_downloads( $product );
		$this->read_visibility( $product );

		if ( ! in_array( $post_object->post_type, $lms_types, true ) ) {
			$this->read_product_data( $product );
		}

		// Google Listings and Ads integration
		$this->handle_google_listings_and_ads( $product, $post_object );

		// Facebook and Instagram Ads integration
		$this->handle_facebook_listings_and_ads( $product, $post_object );

		$product->set_object_read( true );
		do_action( 'woocommerce_product_read', $product->get_id() );
	}

	/**
	 * Get LMS post types based on filters and available add-ons.
	 *
	 * @return array
	 */
	private function get_lms_post_types(): array {
		if ( has_filter( 'masterstudy_woo_post_types' ) ) {
			return apply_filters( 'masterstudy_woo_post_types', array() );
		}

		$lms_types = array( \MasterStudy\Lms\Plugin\PostType::COURSE );

		if ( is_ms_lms_addon_enabled( 'enterprise_courses' ) ) {
			$lms_types[] = \MasterStudy\Lms\Plugin\PostType::COURSE_GROUPS;
		}

		if ( is_ms_lms_addon_enabled( 'course_bundle' ) ) {
			$lms_types[] = \MasterStudy\Lms\Plugin\PostType::COURSE_BUNDLES;
		}

		return $lms_types;
	}

	/**
	 * Set product properties based on the post object.
	 *
	 * @param WC_Product $product
	 * @param WP_Post $post_object
	 */
	private function set_product_props( WC_Product $product, WP_Post $post_object ) {
		$product->set_id( $post_object->ID );
		$product->set_props(
			array(
				'product_id'        => $post_object->ID,
				'name'              => $post_object->post_title,
				'slug'              => $post_object->post_name,
				'date_created'      => $this->parse_timestamp( $post_object->post_date_gmt ),
				'date_modified'     => $this->parse_timestamp( $post_object->post_modified_gmt ),
				'status'            => $post_object->post_status,
				'description'       => $post_object->post_content,
				'short_description' => $post_object->post_excerpt,
				'parent_id'         => $post_object->post_parent,
				'menu_order'        => $post_object->menu_order,
				'reviews_allowed'   => 'open' === $post_object->comment_status,
				'post_type'         => $post_object->post_type,
			)
		);
	}

	/**
	 * Parse timestamp from the string or return null.
	 *
	 * @param string $date_gmt
	 *
	 * @return int|null
	 */
	private function parse_timestamp( string $date_gmt ) {
		return $date_gmt ? wc_string_to_timestamp( $date_gmt ) : null;
	}

	/**
	 * Set price for the product, considering bundles and sale prices.
	 *
	 * @param WC_Product $product
	 * @param WP_Post $post_object
	 */
	private function set_product_price( WC_Product $product, WP_Post $post_object ) {
		if ( \MasterStudy\Lms\Plugin\PostType::COURSE_BUNDLES === $post_object->post_type ) {
			$price = \MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository::get_bundle_price( $post_object->ID );
			$product->set_regular_price( $price );
			$product->set_price( $price );
		} else {
			$this->set_regular_and_sale_price( $product, $post_object );
		}
	}

	/**
	 * Set regular and sale prices for regular products.
	 *
	 * @param WC_Product $product
	 * @param WP_Post $post_object
	 */
	private function set_regular_and_sale_price( WC_Product $product, WP_Post $post_object ) {
		$single_sale       = get_post_meta( $post_object->ID, 'single_sale', true );
		$price             = get_post_meta( $post_object->ID, 'price', true );
		$sale_price        = get_post_meta( $post_object->ID, 'sale_price', true );
		$sale_price_active = STM_LMS_Helpers::is_sale_price_active( $post_object->ID );

		if ( 'on' === $single_sale ) {
			if ( $sale_price_active && ! empty( $price ) && ! empty( $sale_price ) ) {
				$product->set_regular_price( $price );
				$product->set_sale_price( $sale_price );
				$product->set_price( $sale_price );
			} else {
				$product->set_regular_price( $price );
				$product->set_price( $price );
			}
		}
	}

	/**
	 * Set product image ID.
	 *
	 * @param WC_Product $product
	 * @param WP_Post $post_object
	 */
	private function set_product_image( WC_Product $product, WP_Post $post_object ) {
		$thumbnail_id = get_post_thumbnail_id( $post_object->ID );
		$product->set_image_id( $thumbnail_id ?? null );
	}

	/**
	 * Handle Google Listings and Ads integration.
	 *
	 * @param WC_Product $product
	 * @param WP_Post $post_object
	 */
	private function handle_google_listings_and_ads( WC_Product $product, WP_Post $post_object ) {
		if ( class_exists( '\Automattic\WooCommerce\GoogleListingsAndAds\PluginFactory' ) ) {
			wp_set_object_terms( $product->get_id(), 'simple', 'product_type', true );
			$product->add_meta_data( 'identifier_exists', 'no', true );

			if ( \MasterStudy\Lms\Plugin\PostType::COURSE_BUNDLES === $post_object->post_type ) {
				$product->add_meta_data( '_wc_gla_visibility', \Automattic\WooCommerce\GoogleListingsAndAds\Value\ChannelVisibility::SYNC_AND_SHOW, true );
			}

			if ( \MasterStudy\Lms\Plugin\PostType::COURSE === $post_object->post_type ) {
				$terms = get_the_terms( $post_object->ID, \MasterStudy\Lms\Plugin\Taxonomy::COURSE_CATEGORY );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					$product->add_meta_data( 'google_product_category', $terms[0]->name, true );
				}
			}

			$product->add_meta_data( 'brand', get_bloginfo( 'name' ), true );
		}
	}

	/**
	 * Handle Facebook and Instagram Ads integration.
	 *
	 * @param WC_Product $product
	 * @param WP_Post $post_object
	 */
	private function handle_facebook_listings_and_ads( WC_Product $product, WP_Post $post_object ) {
		$lms_types = $this->get_lms_post_types();

		if ( class_exists( 'WC_Facebook_Loader' ) && in_array( $post_object->post_type, $lms_types, true ) ) {
			wp_set_object_terms( $product->get_id(), 'simple', 'product_type', true );

			if ( \MasterStudy\Lms\Plugin\PostType::COURSE_BUNDLES === $post_object->post_type ) {
				$product->add_meta_data( \WooCommerce\Facebook\Products::VISIBILITY_META_KEY, \WooCommerce\Facebook\Admin::SYNC_MODE_SYNC_AND_SHOW, true );
			}

			// phpcs:disable WordPress.Security.NonceVerification.Missing
			$sync_mode = isset( $_POST['wc_facebook_sync_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['wc_facebook_sync_mode'] ) ) : '';

			$product->set_virtual( empty( $sync_mode ) );
		}
	}

	/**
	 * Get the product type based on product ID.
	 *
	 * @param int $product_id
	 *
	 * @return bool|string
	 */
	public function get_product_type( $product_id ) {
		$cache_key    = WC_Cache_Helper::get_cache_prefix( 'product_' . $product_id ) . '_type_' . $product_id;
		$product_type = wp_cache_get( $cache_key, 'products' );

		if ( $product_type ) {
			return $product_type;
		}

		$post_type  = get_post_type( $product_id );
		$lms_types  = $this->get_lms_post_types();
		$post_types = array_merge( $lms_types, array( 'product' ) );

		$product_type = $this->resolve_product_type( $post_type, $product_id, $post_types );

		wp_cache_set( $cache_key, $product_type, 'products' );

		return $product_type;
	}

	/**
	 * Resolve the product type.
	 *
	 * @param string $post_type
	 * @param int $product_id
	 * @param array $post_types
	 *
	 * @return string|bool
	 */
	private function resolve_product_type( string $post_type, int $product_id, array $post_types ) {
		if ( 'product_variation' === $post_type ) {
			return 'variation';
		}

		if ( in_array( $post_type, $post_types, true ) ) {
			$terms = get_the_terms( $product_id, 'product_type' );
			return ! empty( $terms ) && ! is_wp_error( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
		}

		return false;
	}
}

