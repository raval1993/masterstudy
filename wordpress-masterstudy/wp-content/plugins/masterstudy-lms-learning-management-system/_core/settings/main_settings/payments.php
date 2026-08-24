<?php

function stm_lms_settings_payments_section() {
	return array(
		'name'   => esc_html__( 'Payment Methods', 'masterstudy-lms-learning-management-system' ),
		'label'  => esc_html__( 'Payment Methods Settings', 'masterstudy-lms-learning-management-system' ),
		'icon'   => 'stmlms-money-check-alt',
		'fields' => array(
			'payment_methods' => array(
				'type'  => 'payments',
				'label' => esc_html__( 'Payment Methods', 'masterstudy-lms-learning-management-system' ),
			),
		),
	);
}
