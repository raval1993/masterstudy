<?php

use MasterStudy\Lms\Plugin\Addons;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;

add_action( 'vc_after_init', 'masterstudy_membership_vc', 100 );

function masterstudy_membership_vc() {
	if ( ! is_ms_lms_addon_enabled( Addons::SUBSCRIPTIONS ) ) {
		return;
	}

	$memberships_select = array();
	$memberships_plans  = ( new SubscriptionPlanRepository() )->get_enabled_plans();

	foreach ( $memberships_plans as $membership_plan ) {
		$memberships_select[ $membership_plan['name'] ] = $membership_plan['name'];
	}

	vc_map(
		array(
			'name'           => esc_html__( 'Membership plans', 'masterstudy-lms-learning-management-system' ),
			'base'           => 'masterstudy_membership',
			'icon'           => 'masterstudy_membership',
			'description'    => esc_html__( 'Membership Plans', 'masterstudy-lms-learning-management-system' ),
			'html_template'  => STM_LMS_Templates::vc_locate_template( 'vc_templates/masterstudy_membership' ),
			'php_class_name' => 'WPBakeryShortCode_Masterstudy_Membership',
			'category'       => array(
				esc_html__( 'Content', 'masterstudy-lms-learning-management-system' ),
			),
			'params'         => array(
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button position', 'masterstudy-lms-learning-management-system' ),
					'param_name' => 'button_position',
					'value'      => array(
						__( 'Before plan items', 'masterstudy-lms-learning-management-system' ) => 'before_membership_items',
						__( 'After plan items', 'masterstudy-lms-learning-management-system' )  => 'after_membership_items',
					),
					'std'        => 'before_membership_items',
				),
				array(
					'type'       => 'param_group',
					'value'      => '',
					'param_name' => 'plan_label',
					'heading'    => esc_html__( 'Plan label', 'masterstudy-lms-learning-management-system' ),
					'params'     => array(
						array(
							'type'       => 'textfield',
							'value'      => '',
							'heading'    => esc_html__( 'Title', 'masterstudy-lms-learning-management-system' ),
							'param_name' => 'plan_title',
						),
						array(
							'type'       => 'dropdown',
							'heading'    => esc_html__( 'For plan', 'masterstudy-lms-learning-management-system' ),
							'param_name' => 'plan_label_relation',
							'value'      => $memberships_select,
							'std'        => '',
						),
					),
				),
				array(
					'type'       => 'css_editor',
					'heading'    => esc_html__( 'Plan container', 'masterstudy-lms-learning-management-system' ),
					'param_name' => 'css_plan_container',
					'group'      => esc_html__( 'Design options', 'masterstudy-lms-learning-management-system' ),
				),
			),
		)
	);
}

if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_Masterstudy_Membership extends WPBakeryShortCode {
	}
}
