<?php
class STM_Course_Order_Item_Product extends WC_Order_Item_Product {
	public function set_product_id( $value ) {
		if ( has_filter( 'masterstudy_woo_post_types' ) ) {
			$post_types = apply_filters( 'masterstudy_woo_post_types', array( 'product' ) );
		} else {
			$post_types = array(
				MasterStudy\Lms\Plugin\PostType::COURSE,
				'product',
			);

			if ( is_ms_lms_addon_enabled( 'enterprise_courses' ) ) {
				$post_types[] = 'stm-ent-groups';
			}

			if ( is_ms_lms_addon_enabled( 'course_bundle' ) ) {
				$post_types[] = 'stm-course-bundles';
			}
		}

		if ( $value > 0 && ! in_array( get_post_type( absint( $value ) ), $post_types, true ) ) {
			$this->error( 'order_item_product_invalid_product_id', __( 'Invalid product ID', 'masterstudy-lms-learning-management-system' ) );
		}

		$this->set_prop( 'product_id', absint( $value ) );
	}
}
