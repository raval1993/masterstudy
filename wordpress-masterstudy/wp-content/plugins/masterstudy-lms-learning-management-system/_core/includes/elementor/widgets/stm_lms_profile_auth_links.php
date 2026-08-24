<?php

use Elementor\Group_Control_Border;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;

class StmLmsProfileAuthLinks extends Widget_Base {
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
		wp_register_style( 'stm-lms-user', STM_LMS_URL . 'assets/css/parts/user.css', array(), STM_LMS_VERSION, false );
	}

	public function get_name() {
		return 'stm_lms_pro_site_authorization_links';
	}

	public function get_title() {
		return esc_html__( 'Site Authorization links', 'masterstudy-lms-learning-management-system' );
	}


	public function get_icon() {
		return 'stmlms-authlinks lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms' );
	}

	public function get_style_depends() {
		return array( 'profile-auth-links-style', 'stm-lms-user' );
	}


	/** Register General Controls */

	protected function register_controls() {
		$this->content_tab_profile_icon();
		$this->content_tab_auth_links();
		$this->content_tab_auth_dropdown();
	}

	protected function content_tab_profile_icon() {
		$label = STM_LMS_Options::get_option( 'restrict_registration', false ) ? esc_html__( 'Login', 'masterstudy-lms-learning-management-system' ) : esc_html__( 'Account', 'masterstudy-lms-learning-management-system' );

		$this->start_controls_section(
			'profile_general_section',
			array(
				'label' => $label,
			)
		);

		$this->add_control(
			'profile_lms_icon',
			array(
				'name'      => 'profile_lms_icon_selected',
				'label'     => esc_html__( 'Icon', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::ICONS,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'auth_links_btn_text',
			array(
				'label'   => esc_html__( 'Button Text', 'masterstudy-lms-learning-management-system' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => esc_html__( 'Login/Sign Up', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$this->add_control(
			'auth_links_btn_link',
			array(
				'label'       => esc_html__( 'Button Link', 'masterstudy-lms-learning-management-system' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array(
					'active' => true,
				),
				'placeholder' => site_url() . '/user-account',
				'default'     => array(
					'url' => site_url() . '/user-account',
				),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'profile_general_section_logged',
			array(
				'label' => esc_html__( 'Login', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$this->add_control(
			'profile_general_icon',
			array(
				'name'      => 'profile_general_icon_selected',
				'label'     => esc_html__( 'Icon', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::ICONS,
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	protected function content_tab_auth_links() {
		$label = STM_LMS_Options::get_option( 'restrict_registration', false ) ? esc_html__( 'Login', 'masterstudy-lms-learning-management-system' ) : esc_html__( 'Account', 'masterstudy-lms-learning-management-system' );

		$this->start_controls_section(
			'auth_style_section_sing_in',
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'profile_icon_section_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .ms-lms-authorization' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'profile_icon_section_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .ms-lms-authorization' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'auth_links__btn_typography',
				'label'    => __( 'Typography', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} span.ms-lms-authorization-title',
			)
		);

		$this->start_controls_tabs(
			'general_auth_links_tabs'
		);

		$this->start_controls_tab(
			'general_event_btn_tab_normal',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$this->add_control(
			'general_auth_links_color',
			array(
				'label'     => esc_html__( ' Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} span.ms-lms-authorization-title' => 'color: {{VALUE}}',
				),
				'separator' => 'after',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'general_auth_links_tab_focus',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$this->add_control(
			'general_auth_links_color_focus',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}:hover span.ms-lms-authorization-title' => 'color: {{VALUE}}',
				),
				'separator' => 'after',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'profile_lms_icon_section_heading',
			array(
				'label' => esc_html__( 'Icon Options', 'masterstudy-lms-learning-management-system' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'profile_lms_icon_section_width',
			array(
				'label'          => esc_html__( 'Width', 'masterstudy-lms-learning-management-system' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'unit' => 'px',
					'size' => 42,
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'size_units'     => array( '%', 'px', 'vw' ),
				'range'          => array(
					'%'  => array(
						'min' => 1,
						'max' => 100,
					),
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
					'vw' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'      => array(
					'{{WRAPPER}}  span.ms-lms-authorization-icon' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'profile_lms_icon_section_height',
			array(
				'label'          => esc_html__( 'Height', 'masterstudy-lms-learning-management-system' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'unit' => 'px',
					'size' => 42,
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'size_units'     => array( '%', 'px', 'vw' ),
				'range'          => array(
					'%'  => array(
						'min' => 1,
						'max' => 100,
					),
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
					'vw' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'      => array(
					'{{WRAPPER}} span.ms-lms-authorization-icon' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'profile_icon_section_border',
				'selector'  => '{{WRAPPER}} span.ms-lms-authorization-icon',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} span.ms-lms-authorization-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'profile_lms_icon_section_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} span.ms-lms-authorization-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'profile_icon_section_typography',
				'label'    => __( 'Typography', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} span.ms-lms-authorization-icon',
			)
		);

		$this->start_controls_tabs(
			'profile_icon_login_tabs'
		);

		$this->start_controls_tab(
			'profile_icon_login_tab_normal',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$this->add_responsive_control(
			'profile_icon_section_bg_color',
			array(
				'label'     => esc_html__( 'Background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#227AFF',
				'selectors' => array(
					'{{WRAPPER}} span.ms-lms-authorization-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'profile_icon_section_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => array(
					'{{WRAPPER}} span.ms-lms-authorization-icon i' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'profile_icon_focus_login',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$this->add_responsive_control(
			'profile_icon_section_bg_focus_color',
			array(
				'label'     => esc_html__( 'Background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#227AFF',
				'selectors' => array(
					'{{WRAPPER}}:hover span.ms-lms-authorization-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'profile_icon_section_color_focus',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => array(
					'{{WRAPPER}}:hover span.ms-lms-authorization-icon i' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'auth_style_section',
			array(
				'label' => esc_html__( 'Login', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'general_auth_links_logged_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .dropdown button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'general_auth_links_logged_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .stm_lms_account_dropdown' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'profile_lms_icon_section_width_logged_icon',
			array(
				'label'          => esc_html__( 'Icon Size', 'masterstudy-lms-learning-management-system' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'unit' => 'px',
					'size' => 14,
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'size_units'     => array( '%', 'px', 'vw' ),
				'range'          => array(
					'%'  => array(
						'min' => 1,
						'max' => 100,
					),
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
					'vw' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'      => array(
					'{{WRAPPER}}  .stm_lms_account_dropdown .dropdown button span' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'auth_links__btn_typography_logged_profile',
				'label'    => __( 'Typography', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}}  .stm_lms_account_dropdown .dropdown button .login_name',
			)
		);

		$this->start_controls_tabs(
			'general_auth_links_logged_tabs'
		);

		$this->start_controls_tab(
			'general_event_btn_logged_tab_normal',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$this->add_control(
			'general_auth_links_color_logged_text',
			array(
				'label'     => esc_html__( 'Text Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#273044',
				'selectors' => array(
					'{{WRAPPER}} .stm_lms_account_dropdown .dropdown button span, {{WRAPPER}} .stm_lms_account_dropdown .dropdown button i' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'general_auth_links_color_logged',
			array(
				'label'     => esc_html__( 'Background Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#EEF1F7',
				'selectors' => array(
					'{{WRAPPER}} .stm_lms_account_dropdown .dropdown button' => 'background-color: {{VALUE}} !important',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'general_auth_links_tab_focus_logged',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$this->add_control(
			'general_auth_links_color_focus_logged_text',
			array(
				'label'     => esc_html__( 'Text Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#227AFF',
				'selectors' => array(
					'{{WRAPPER}} .stm_lms_account_dropdown .dropdown button:hover span, {{WRAPPER}} .stm_lms_account_dropdown .dropdown button:hover i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .stm_lms_account_dropdown .dropdown.open button span, {{WRAPPER}} .stm_lms_account_dropdown .dropdown.open button i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .stm_lms_account_dropdown .dropdown.open .caret.rotate' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'general_auth_links_color_focus_logged',
			array(
				'label'     => esc_html__( 'Background Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#227aff1a',
				'selectors' => array(
					'{{WRAPPER}} .stm_lms_account_dropdown .dropdown button:hover' => 'background-color: {{VALUE}} !important',
					'{{WRAPPER}} .stm_lms_account_dropdown .open button' => 'background-color: {{VALUE}} !important',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function content_tab_auth_dropdown() {

		$this->start_controls_section(
			'auth_dropdown_style_section',
			array(
				'label' => esc_html__( 'Quick Menu', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'auth_dropdown_style_title',
			array(
				'label'     => esc_html__( 'Title Options', 'masterstudy-lms-learning-management-system' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'auth_dropdown_style_title',
				'label'    => __( 'Typography', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-dropdown-menu__wrap h3',
			)
		);

		$this->add_responsive_control(
			'auth_dropdown_content_title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__wrap h3' => 'margin: {{TOP}}{{UNIT}} {{right}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'auth_dropdown_content_title',
			array(
				'label'     => esc_html__( 'Block Options', 'masterstudy-lms-learning-management-system' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'auth_dropdown_content_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__learning-column' => 'padding: {{TOP}}{{UNIT}} 20{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-dropdown-menu__main-column'     => 'padding: {{TOP}}{{UNIT}} {{right}}{{UNIT}} {{BOTTOM}}{{UNIT}} 20{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'auth_dropdown_content_logout_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__logout a' => 'margin: {{TOP}}{{UNIT}} {{right}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'auth_dropdown_style_shadow',
				'selector' => '{{WRAPPER}} .masterstudy-dropdown-menu',
			)
		);

		$this->add_responsive_control(
			'section_layout_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'auth_dropdown_content_item_title',
			array(
				'label'     => esc_html__( 'Item Options', 'masterstudy-lms-learning-management-system' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'auth_dropdown_content_item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__list li a' => 'padding: {{TOP}}{{UNIT}} {{right}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'auth_dropdown_style_typography',
				'label'    => __( 'Typography', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-dropdown-menu',
			)
		);

		$this->add_responsive_control(
			'auth_dropdown_style_background',
			array(
				'label'     => esc_html__( 'Background Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__learning-column' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->start_controls_tabs(
			'auth_dropdown_style_tabs'
		);
		$this->start_controls_tab(
			'auth_dropdown_style_tab_normal',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$this->add_responsive_control(
			'auth_dropdown_style_text',
			array(
				'label'     => esc_html__( 'Text Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__learning-column h3' => 'color: {{VALUE}}',
					'{{WRAPPER}} .masterstudy-dropdown-menu__learning-column .masterstudy-dropdown-menu__list li a .dropdown_menu_item__title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .masterstudy-dropdown-menu__learning-column .masterstudy-dropdown-menu__logout a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .masterstudy-dropdown-menu__learning-column .masterstudy-dropdown-menu__logout a span' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'auth_dropdown_style_focus',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_responsive_control(
			'auth_dropdown_style_text_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#227AFF',
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__learning-column .masterstudy-dropdown-menu__list li a:hover .dropdown_menu_item__title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .masterstudy-dropdown-menu__learning-column .masterstudy-dropdown-menu__logout a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .masterstudy-dropdown-menu__learning-column .masterstudy-dropdown-menu__logout a:hover span' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'auth_dropdown_style_item_bg_hover',
			array(
				'label'     => esc_html__( 'Item background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__learning-column .masterstudy-dropdown-menu__list li a:hover' => 'background-color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'auth_dropdown_style_line',
			array(
				'label'     => esc_html__( 'Separator line', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__learning-column .masterstudy-dropdown-menu__logout' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'auth_dropdown_style_instructor',
			array(
				'label' => esc_html__( 'Instructor area', 'masterstudy-lms-learning-management-system' ),
				'type'  => 'raw_html',
			)
		);

		$this->add_responsive_control(
			'auth_dropdown_style_instructor_background',
			array(
				'label'     => esc_html__( 'Background Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__main-column' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->start_controls_tabs(
			'auth_dropdown_style_instructor_tabs'
		);
		$this->start_controls_tab(
			'auth_dropdown_style_instructor_tab_normal',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$this->add_responsive_control(
			'auth_dropdown_style_instructor_text',
			array(
				'label'     => esc_html__( 'Text Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__main-column h3' => 'color: {{VALUE}}',
					'{{WRAPPER}} .masterstudy-dropdown-menu__main-column .masterstudy-dropdown-menu__list li a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .masterstudy-dropdown-menu__main-column .masterstudy-dropdown-menu__logout a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .masterstudy-dropdown-menu__main-column .masterstudy-dropdown-menu__logout a span' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'auth_dropdown_style_instructor_focus',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_responsive_control(
			'auth_dropdown_style_instructor_text_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__main-column .masterstudy-dropdown-menu__list li a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .masterstudy-dropdown-menu__main-column .masterstudy-dropdown-menu__logout a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .masterstudy-dropdown-menu__main-column .masterstudy-dropdown-menu__logout a:hover span' => 'color: {{VALUE}}',
					'{{WRAPPER}} .masterstudy-dropdown-menu__main-column .masterstudy-dropdown-menu__list li a:hover span' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'auth_dropdown_style_instructor_item_bg_hover',
			array(
				'label'     => esc_html__( 'Item background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__main-column .masterstudy-dropdown-menu__list li a:hover' => 'background-color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'auth_dropdown_style_counter',
			array(
				'label'     => esc_html__( 'Counter', 'masterstudy-lms-learning-management-system' ),
				'type'      => 'raw_html',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'auth_dropdown_style_counter_background',
			array(
				'label'     => esc_html__( 'Background Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__list li a abbr' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'auth_dropdown_style_counter_text',
			array(
				'label'     => esc_html__( 'Text Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-dropdown-menu__list li a abbr' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		//** STM_LMS_Templates */
		wp_enqueue_style( 'profile-auth-links-style' );

		$settings = $this->get_settings_for_display();
		if ( ! is_user_logged_in() ) {
			?>
			<a href="<?php echo esc_url( $settings['auth_links_btn_link']['url'] ); ?>" class="ms-lms-authorization">
				<span class="ms-lms-authorization-icon">
					<i class="<?php echo esc_attr( ! empty( $settings['profile_lms_icon']['value'] ) ? $settings['profile_lms_icon']['value'] : 'stmlms-user-2' ); ?>" aria-hidden="true"></i>
				</span>
				<a href="<?php echo esc_url( $settings['auth_links_btn_link']['url'] ); ?>">
					<span class="ms-lms-authorization-title">
						<?php echo esc_html( $settings['auth_links_btn_text'] ); ?>
					</span>
				</a>
			</a>

			<?php

		} else {
			$icon = $settings['profile_general_icon']['value'];

			\STM_LMS_Templates::show_lms_template( 'global/account-dropdown', array( 'elementor_icon' => $icon ) );
			\STM_LMS_Templates::show_lms_template( 'global/settings-button' );
		}
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
