<?php

/** @var \MasterStudy\Lms\Plugin $plugin */

use MasterStudy\Lms\Repositories\CurriculumRepository;
use MasterStudy\Lms\Repositories\CurriculumMaterialRepository;
use MasterStudy\Lms\Repositories\CurriculumSectionRepository;
use MasterStudy\Lms\Plugin\PostType;

add_action( 'init', array( $plugin, 'init' ) );
add_action( 'rest_api_init', array( $plugin, 'register_api' ) );

add_action(
	'plugins_loaded',
	function () use ( $plugin ) {
		if ( ! is_textdomain_loaded( 'masterstudy-lms-learning-management-system' ) ) {
			load_plugin_textdomain(
				'masterstudy-lms-learning-management-system',
				false,
				'masterstudy-lms-learning-management-system/languages'
			);
		}

		$plugin->register_addons( apply_filters( 'masterstudy_lms_plugin_addons', array() ) );

		do_action( 'masterstudy_lms_plugin_loaded', $plugin );
	}
);

add_action(
	'delete_post',
	function ( int $post_id, \WP_Post $post ) {
		if ( PostType::COURSE === $post->post_type ) {
			( new CurriculumSectionRepository() )->delete_course_sections( $post_id );
			return;
		}

		if (
			in_array(
				$post->post_type,
				array( PostType::LESSON, PostType::QUIZ, PostType::ASSIGNMENT, PostType::GOOGLE_MEET ),
				true
			)
		) {
			( new CurriculumMaterialRepository() )->delete_by_post( $post_id );
		}
	},
	10,
	2
);

add_action(
	'dp_duplicate_post',
	function ( $post_id, $post ) {
		if ( PostType::COURSE === $post->post_type ) {
			( new CurriculumRepository() )->duplicate_curriculum( $post->ID, $post_id );
		}
	},
	10,
	2
);

function masterstudy_lms_duplicate_wpml_curriculum( $master_post_id, $post_id, $language_code ) {
	if ( PostType::COURSE === get_post_type( $post_id ) ) {
		$sections = ( new CurriculumSectionRepository() )->get_course_section_ids( $post_id );

		if ( empty( $sections ) ) {
			( new CurriculumRepository() )->duplicate_curriculum( $master_post_id, $post_id, $language_code );
		}
	}
}

add_action(
	'wpml_after_save_post',
	function ( $post_id, $trid, $language_code ) {
		if ( 'publish' === get_post_status( $post_id ) ) {
			masterstudy_lms_duplicate_wpml_curriculum( $trid, $post_id, $language_code );
		}
	},
	10,
	3
);

add_action(
	'icl_make_duplicate',
	function ( $master_post_id, $target_lang, $post_array, $target_post_id ) {
		masterstudy_lms_duplicate_wpml_curriculum( $master_post_id, $target_post_id, $target_lang );
	},
	10,
	4
);

add_action(
	'icl_pro_translation_completed',
	function ( $post_id, $fields, $job ) {
		if ( ! empty( $job->original_doc_id ) ) {
			masterstudy_lms_duplicate_wpml_curriculum( $job->original_doc_id, $post_id, $job->language_code ?? '' );
		}
	},
	10,
	3
);

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 */
function masterstudy_lms_gutenberg_blocks_init() {

	$blocks = array(
		'cta',
		'icon',
		'button',
		'testimonials',
		'iconbox',
		'adaptive-box',
		'advanced-text',
		'courses/archive/container',
		'courses/archive/columns',
		'courses/filter/container',
		'courses/filter/category',
		'courses/filter/category-block',
		'courses/filter/status',
		'courses/filter/level',
		'courses/filter/level-block',
		'courses/filter/rating',
		'courses/filter/price',
		'courses/grid',
		'courses/courses-tab-category',
		'courses/courses-tab-options',
		'courses/courses-load-more',
		'course-categories/container',
		'courses/preset',
		'courses/presets/classic',
		'courses/presets/full-size-image',
		'courses/presets/price-button',
		'courses/presets/price-accent',
		'course-search/container',
		'featured-teacher/about',
		'featured-teacher/container',
		'featured-teacher/button',
		'course-carousel',
		'instructors/grid',
		'instructors/preset',
		'instructors/presets/classic',
		'instructors-carousel',
	);

	if ( is_ms_lms_addon_enabled( 'coming_soon' ) ) {
		$blocks[] = 'courses/filter/availability-block';
		$blocks[] = 'courses/filter/availability';
	}

	if ( is_ms_lms_addon_enabled( 'course_bundle' ) ) {
		$blocks[] = 'courses/bundles/container';
		$blocks[] = 'courses/bundles/cards';
	}

	foreach ( $blocks as $block ) {
		register_block_type( MS_LMS_PATH . '/assets/gutenberg/blocks/' . $block );
	}
}
add_action( 'init', 'masterstudy_lms_gutenberg_blocks_init' );

/**
 * Hook for enrolling a user in courses after completing a WooCommerce checkout as a guest.
 *
 * This hook triggers when an order status changes to "completed".
 * It ensures that users who purchased a course via WooCommerce Checkout as a guest
 * and were automatically registered are properly enrolled in their courses.
 *
 * 🔹 Workflow:
 * 1. Retrieve the user ID from the order.
 * 2. Loop through the order items and get the `product_id`.
 * 3. Check if the product is linked to a course and retrieve its `stm_lms_product_id`.
 * 4. If the product is a bundle, fetch the course list from `stm_lms_bundle_ids`.
 * 5. Enroll the user in all courses from the bundle using `STM_LMS_Course::add_user_course()`.
 *
 * ⚠️ Important: This only works for orders with the status "completed".
 *
 * @param int $order_id The order ID.
 */
add_action(
	'woocommerce_order_status_completed',
	function ( $order_id ) {
		$order   = wc_get_order( $order_id );
		$user_id = $order->get_user_id();

		if ( ! $user_id ) {
			return;
		}

		foreach ( $order->get_items() as $item ) {
			$product_id = $item->get_product_id();
			$course_id  = get_post_meta( $product_id, 'stm_lms_product_id', true );

			if ( ! $course_id ) {
				continue;
			}

			$course_ids = array( $course_id );
			$bundle_ids = get_post_meta( $course_id, 'stm_lms_bundle_ids', true );

			if ( is_array( $bundle_ids ) && ! empty( $bundle_ids ) ) {
				$course_ids = array_merge( $course_ids, $bundle_ids );
			}

			$course_ids = array_unique( $course_ids );

			foreach ( $course_ids as $course ) {
				STM_LMS_Course::add_user_course( $course, $user_id, 0, 0 );
				STM_LMS_Course::add_student( $course );
			}
		}
	},
	10,
	1
);
