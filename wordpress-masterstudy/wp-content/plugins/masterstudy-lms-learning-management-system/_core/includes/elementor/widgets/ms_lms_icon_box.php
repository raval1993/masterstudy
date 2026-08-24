<?php
namespace StmLmsElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; /* Exit if accessed directly*/
}
/**
 * Elementor Hello World
 * Elementor widget for hello world.
 * @since 1.0.0
 */
class MsLmsIconBox extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'ms_lms_icon_box';
	}

	/**
	 * Retrieve the widget title.
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Icon Box', 'masterstudy-lms-learning-management-system' );
	}

	/**
	 * Retrieve the widget style dependencies.
	 * @since 1.0.0
	 * @access public
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends() {
		return array( 'masterstudy-icon-box' );
	}

	/**
	 * Retrieve the widget icon.
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'stmlms-iconbox lms-icon';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 * Used to determine where to display the widget in the editor.
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'stm_lms' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'masterstudy-lms-learning-management-system' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'preset',
			array(
				'label'   => esc_html__( 'Preset', 'masterstudy-lms-learning-management-system' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => esc_html__( 'Standard', 'masterstudy-lms-learning-management-system' ),
					'column'  => esc_html__( 'Vertical', 'masterstudy-lms-learning-management-system' ),
				),
			)
		);
		$this->add_control(
			'icon_box_heading',
			array(
				'label' => esc_html__( 'Heading', 'masterstudy-lms-learning-management-system' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'icon_box_text',
			array(
				'label' => esc_html__( 'Text', 'masterstudy-lms-learning-management-system' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$this->add_control(
			'icon_box_button_title',
			array(
				'label'       => esc_html__( 'Button Title', 'masterstudy-lms-learning-management-system' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$this->add_control(
			'icon_box_button_link',
			array(
				'label'       => esc_html__( 'Button Link', 'masterstudy-lms-learning-management-system' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'options'     => array( 'url', 'is_external', 'nofollow' ),
				'label_block' => true,
			)
		);
		$this->add_control(
			'icon',
			array(
				'label' => esc_html__( 'Icon', 'masterstudy-lms-learning-management-system' ),
				'type'  => \Elementor\Controls_Manager::ICONS,
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_icon_box_heading',
			array(
				'label' => __( 'Heading', 'masterstudy-lms-learning-management-system' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'icon_box_heading_typography',
				'label'    => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-icon-box__heading',
			)
		);
		$this->add_control(
			'icon_box_heading_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-icon-box__heading' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'icon_box_heading_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-icon-box__heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'icon_box_heading_align',
			array(
				'label'     => esc_html__( 'Alignment', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => esc_html__( 'Center', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-icon-box__heading' => 'text-align: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_icon_box_text',
			array(
				'label' => __( 'Text', 'masterstudy-lms-learning-management-system' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'icon_box_text_typography',
				'label'    => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-icon-box__text',
			)
		);
		$this->add_control(
			'icon_box_text_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-icon-box__text' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'icon_box_text_align',
			array(
				'label'     => esc_html__( 'Alignment', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => esc_html__( 'Center', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-icon-box__text' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-icon-box__text' => 'justify-content: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_icon_box_button',
			array(
				'label' => __( 'Button', 'masterstudy-lms-learning-management-system' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'icon_box_button_text_typography',
				'label'    => esc_html__( 'Text typography', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-icon-box__button',
			)
		);
		$this->add_responsive_control(
			'icon_box_button_align',
			array(
				'label'     => esc_html__( 'Alignment', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-icon-box__button' => 'align-self: {{VALUE}};',
				),
			)
		);
		$this->start_controls_tabs(
			'icon_box_button_tabs'
		);
		$this->start_controls_tab(
			'icon_box_button_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'icon_box_button_text_color',
			array(
				'label'     => esc_html__( 'Text color', 'masterstudy-lms-learning-management-system' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-icon-box__button' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'icon_box_button_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-icon-box__button',
			)
		);
		$this->add_control(
			'icon_box_button_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-icon-box__button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'icon_box_button_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-icon-box__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'icon_box_button_border',
				'label'    => esc_html__( 'Border', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-icon-box__button',
			)
		);
		$this->add_control(
			'icon_box_button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-icon-box__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'icon_box_button_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'icon_box_button_text_color_hover',
			array(
				'label'     => esc_html__( 'Text color', 'masterstudy-lms-learning-management-system' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-icon-box__button:hover' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'icon_box_button_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-icon-box__button:hover',
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'icon_box_button_border_hover',
				'label'    => esc_html__( 'Border', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-icon-box__button:hover',
			)
		);
		$this->add_control(
			'icon_box_button_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-icon-box__button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_icon_box_icon',
			array(
				'label' => __( 'Icon', 'masterstudy-lms-learning-management-system' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'icon_box_icon_width',
			array(
				'label'     => esc_html__( 'Width', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 20,
						'max'  => 500,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-icon-box__icon i'   => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .masterstudy-icon-box__icon svg' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);
		$this->add_control(
			'icon_box_icon_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-icon-box__icon i' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'icon_box_icon_background',
				'label'    => esc_html__( 'Background', 'masterstudy-lms-learning-management-system' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-icon-box__icon',
			)
		);
		$this->add_control(
			'icon_box_icon_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-icon-box__icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'icon_box_icon_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-icon-box__icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'icon_box_icon_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-icon-box__icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'icon_box_icon_align',
			array(
				'label'     => esc_html__( 'Alignment', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-icon-box__icon' => 'align-self: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_icon_box_container',
			array(
				'label' => __( 'Container', 'masterstudy-lms-learning-management-system' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'icon_box_container_background',
				'label'    => esc_html__( 'Background', 'masterstudy-lms-learning-management-system' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-icon-box',
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'icon_box_container_border',
				'label'    => esc_html__( 'Border', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-icon-box',
			)
		);
		$this->add_control(
			'icon_box_container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-icon-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'icon_box_container_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-icon-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'icon_box_container_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-icon-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'icon_box_container_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-icon-box',
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$atts = array(
			'preset'      => $settings['preset'] ?? 'default',
			'heading'     => $settings['icon_box_heading'],
			'button_text' => $settings['icon_box_button_title'],
			'text'        => $settings['icon_box_text'],
			'button_link' => $settings['icon_box_button_link'],
			'library'     => '',
			'icon'        => '',
		);

		if ( isset( $settings['icon']['library'] ) ) {
			$atts['library'] = $settings['icon']['library'];
		}

		if ( ! empty( $settings['icon']['value'] ) ) {
			if ( 'svg' === $atts['library'] ) {
				$atts['icon'] = Icons_Manager::render_uploaded_svg_icon( $settings['icon']['value'] );
			} else {
				$atts['icon'] = ' ' . $settings['icon']['value'];
			}
		}

		\STM_LMS_Templates::show_lms_template( 'elementor-widgets/icon-box', $atts );
	}
	/**
	 * Render the widget output in the editor.
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 * @since 1.0.0
	 * @access protected
	 */

	protected function content_template() {
	}
}
