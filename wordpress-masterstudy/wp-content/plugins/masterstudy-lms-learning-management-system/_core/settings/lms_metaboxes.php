<?php

add_filter(
	'stm_wpcfto_boxes',
	function ( $boxes ) {
		$data_boxes = array(
			'stm_reviews'    => array(
				'post_type' => array( 'stm-reviews' ),
				'label'     => esc_html__( 'Review info', 'masterstudy-lms-learning-management-system' ),
			),
			'stm_order_info' => array(
				'post_type'      => array( 'stm-orders' ),
				'label'          => esc_html__( 'Order info', 'masterstudy-lms-learning-management-system' ),
				'skip_post_type' => 1,
			),
		);

		return array_merge( $data_boxes, $boxes );
	}
);

add_filter(
	'stm_wpcfto_fields',
	function ( $fields ) {
		$courses = ( class_exists( 'WPCFTO_Settings' ) ) ? WPCFTO_Settings::stm_get_post_type_array( 'stm-courses' ) : array();

		$data_fields = array(
			'stm_reviews'    => array(
				'section_data' => array(
					'name'   => esc_html__( 'Review info', 'masterstudy-lms-learning-management-system' ),
					'fields' => array(
						'review_course' => array(
							'type'    => 'select',
							'label'   => esc_html__( 'Course Reviewed', 'masterstudy-lms-learning-management-system' ),
							'options' => $courses,
						),
						'review_user'   => array(
							'type'      => 'autocomplete',
							'post_type' => array( 'post' ),
							'label'     => esc_html__( 'User Reviewed', 'masterstudy-lms-learning-management-system' ),
							'limit'     => 1,
						),
						'review_mark'   => array(
							'type'    => 'select',
							'label'   => esc_html__( 'User Review mark', 'masterstudy-lms-learning-management-system' ),
							'options' => array(
								'5' => '5',
								'4' => '4',
								'3' => '3',
								'2' => '2',
								'1' => '1',
							),
						),
					),
				),
			),
			'stm_order_info' => array(
				'order_info' => array(
					'name'   => esc_html__( 'Order', 'masterstudy-lms-learning-management-system' ),
					'fields' => array(
						'order' => array(
							'type' => 'order',
						),
					),
				),
			),
		);

		return array_merge( $data_fields, $fields );
	}
);
