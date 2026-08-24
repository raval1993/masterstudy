<?php

use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MsLmsMailchimp extends Widget_Base {

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
		wp_register_style( 'lms-mailchimp', STM_LMS_URL . 'assets/css/elementor-widgets/mailchimp.css', array(), STM_LMS_VERSION, false );
	}

	public function get_name() {
		return 'ms_lms_mailchimp';
	}

	public function get_title() {
		return esc_html__( 'Mailchimp', 'masterstudy-lms-learning-management-system' );
	}

	public function get_style_depends() {
		return array( 'lms-mailchimp' );
	}

	public function get_icon() {
		return 'stmlms-mailchimp lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms' );
	}

	/** Register General Controls */
	protected function register_controls() {
		$this->register_style_controls_label_styles();
		$this->register_style_controls_field_styles();
		$this->register_style_controls_button_styles();
		$this->register_style_controls_message_styles();
	}

	/** Register Typography Controls */
	protected function register_style_controls_label_styles() {

		$this->start_controls_section(
			'section_label',
			array(
				'label' => esc_html__( 'Label', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'section_label_typography',
				'selector'       => '{{WRAPPER}} .mc4wp-form-fields label',
				'fields_options' => array(
					'typography' => array(
						'label' => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
					),
				),
			)
		);
		$this->add_control(
			'section_label_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mc4wp-form-fields label' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'section_label_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .mc4wp-form-fields label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function register_style_controls_field_styles() {

		$this->start_controls_section(
			'section_field',
			array(
				'label' => esc_html__( 'Field', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'section_field_typography',
				'selector'       => '{{WRAPPER}} .mc4wp-form-fields input[type=email]',
				'fields_options' => array(
					'typography' => array(
						'label' => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
					),
				),
			)
		);
		$this->add_control(
			'section_field_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mc4wp-form-fields input[type=email]' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'section_field_background',
			array(
				'label'     => esc_html__( 'Background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mc4wp-form-fields input[type=email]' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'section_field_placeholder_color',
			array(
				'label'     => esc_html__( 'Placeholder Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mc4wp-form-fields input[type=email]' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'section_field_width',
			array(
				'label'      => esc_html__( 'Width', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 100,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .mc4wp-form-fields input[type=email]' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'section_field_border',
				'selector' => '{{WRAPPER}} .mc4wp-form-fields input[type=email]',
			)
		);
		$this->add_responsive_control(
			'section_field_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .mc4wp-form-fields input[type=email]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'section_field_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .mc4wp-form-fields input[type=email]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'section_field_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .mc4wp-form-fields input[type=email]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function register_style_controls_button_styles() {

		$this->start_controls_section(
			'section_button',
			array(
				'label' => esc_html__( 'Button', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'section_button_typography',
				'selector'       => '{{WRAPPER}} .mc4wp-form-fields input[type=submit]',
				'fields_options' => array(
					'typography' => array(
						'label' => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
					),
				),
			)
		);

		$this->start_controls_tabs(
			'section_button_tab'
		);
		$this->start_controls_tab(
			'section_button_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'section_button_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mc4wp-form-fields input[type=submit]' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'section_button_background',
			array(
				'label'     => esc_html__( 'Background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mc4wp-form-fields input[type=submit]' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'section_button_border',
				'selector' => '{{WRAPPER}} .mc4wp-form-fields input[type=submit]',
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'section_button_shadow',
				'selector' => '{{WRAPPER}} .mc4wp-form-fields input[type=submit]',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'section_button_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'section_button_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mc4wp-form-fields input[type=submit]:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'section_button_background_hover',
			array(
				'label'     => esc_html__( 'Background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mc4wp-form-fields input[type=submit]:hover' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'section_button_border_hover',
				'selector' => '{{WRAPPER}} .mc4wp-form-fields input[type=submit]:hover',
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'section_button_shadow_hover',
				'selector' => '{{WRAPPER}} .mc4wp-form-fields input[type=submit]:hover',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'section_button_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .mc4wp-form-fields input[type=submit]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'section_button_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .mc4wp-form-fields input[type=submit]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'section_button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .mc4wp-form-fields input[type=submit]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function register_style_controls_message_styles() {

		$this->start_controls_section(
			'section_message',
			array(
				'label' => esc_html__( 'Message', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'section_message_typography',
				'selector'       => '{{WRAPPER}} .mc4wp-response .mc4wp-alert p',
				'fields_options' => array(
					'typography' => array(
						'label' => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
					),
				),
			)
		);
		$this->start_controls_tabs(
			'section_message_tab'
		);
		$this->start_controls_tab(
			'section_message_success',
			array(
				'label' => esc_html__( 'Success', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'section_message_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mc4wp-response .mc4wp-alert.mc4wp-success p, {{WRAPPER}} .mc4wp-response .mc4wp-alert.mc4wp-success p a' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'section_message_background',
			array(
				'label'     => esc_html__( 'Background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mc4wp-response .mc4wp-alert.mc4wp-success p' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'section_message_border',
				'selector' => '{{WRAPPER}} .mc4wp-response .mc4wp-alert.mc4wp-success p',
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'section_message_shadow',
				'selector' => '{{WRAPPER}} .mc4wp-response .mc4wp-alert.mc4wp-success p',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'section_message_alert_tab',
			array(
				'label' => esc_html__( 'Alert', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'section_message_alert_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mc4wp-response .mc4wp-alert.mc4wp-error p, {{WRAPPER}} .mc4wp-response .mc4wp-alert.mc4wp-error p a' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'section_message_alert_background',
			array(
				'label'     => esc_html__( 'Background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .mc4wp-response .mc4wp-alert.mc4wp-error p' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'section_message_alert_border',
				'selector' => '{{WRAPPER}} .mc4wp-response .mc4wp-alert.mc4wp-error p',
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'section_message_alert_shadow',
				'selector' => '{{WRAPPER}} .mc4wp-response .mc4wp-alert.mc4wp-error p',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'section_message_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .mc4wp-response .mc4wp-alert p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'section_message_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .mc4wp-response .mc4wp-alert p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	/** Render the widget output on the frontend */
	protected function render() {
		$settings = $this->get_settings_for_display();

		extract( $settings );

		global $wpdb;

		$form_id = $wpdb->get_var(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'mc4wp-form' LIMIT 1"
		);

		if ( $form_id ) {
			echo do_shortcode( '[mc4wp_form id=' . $form_id . ']' );
		}
	}
}
