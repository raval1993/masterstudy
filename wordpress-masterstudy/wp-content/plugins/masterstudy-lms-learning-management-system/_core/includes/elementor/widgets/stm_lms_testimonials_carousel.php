<?php

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class StmLmsProTestimonials extends Widget_Base {

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
		wp_register_style( 'swiper-bundle', STM_LMS_URL . 'assets/vendors/swiper-bundle.min.css', array(), STM_LMS_VERSION, false );
		wp_register_style( 'lms-testimonials-carousel', STM_LMS_URL . 'assets/css/elementor-widgets/testimonials-carousel.css', array(), STM_LMS_VERSION, false );
	}

	public function get_name() {
		return 'stm_lms_pro_testimonials';
	}

	public function get_title() {
		return esc_html__( 'Testimonials', 'masterstudy-lms-learning-management-system' );
	}

	public function get_style_depends() {
		return array( 'lms-testimonials-carousel', 'swiper-bundle' );
	}

	public function get_icon() {
		return 'stmlms-testimonials lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms' );
	}

	/** Register General Controls */
	protected function register_controls() {
		$this->register_general_content_controls();
		$this->register_item_typo_content_controls();
		$this->register_heading_typo_content_controls();
		$this->register_heading_review_content_controls();
		$this->register_description_content_controls();
		$this->register_author_content_controls();
		$this->register_style_controls_nav_arrows();
		$this->register_style_controls_nav_pagination();
		$this->register_item_typo_avatar_controls();
	}

	protected function register_general_content_controls() {
		$this->start_controls_section(
			'section_general_fields',
			array(
				'label' => esc_html__( 'General', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'testimonials_title',
			array(
				'label' => esc_html__( 'Title', 'masterstudy-lms-learning-management-system' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'testimonials_style',
			array(
				'label'   => esc_html__( 'Style', 'masterstudy-lms-learning-management-system' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style_1',
				'options' => array(
					'style_1' => esc_html__( 'Centered', 'masterstudy-lms-learning-management-system' ),
					'style_6' => esc_html__( 'Centered 2', 'masterstudy-lms-learning-management-system' ),
					'style_2' => esc_html__( 'Outlined', 'masterstudy-lms-learning-management-system' ),
					'style_3' => esc_html__( 'Classic', 'masterstudy-lms-learning-management-system' ),
					'style_4' => esc_html__( 'Compact', 'masterstudy-lms-learning-management-system' ),
					'style_5' => esc_html__( 'Classic 2', 'masterstudy-lms-learning-management-system' ),
				),
			)
		);
		$this->add_control(
			'carousel_heading',
			array(
				'label'     => esc_html__( 'Carousel', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_responsive_control(
			'per_view',
			array(
				'label'              => esc_html__( 'Per view', 'masterstudy-lms-learning-management-system' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '1',
				'options'            => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'devices'            => array( 'desktop', 'tablet', 'mobile' ),
				'desktop_default'    => '1',
				'tablet_default'     => '1',
				'mobile_default'     => '1',
				'frontend_available' => true,
				'condition'          => array(
					'testimonials_style' => array( 'style_1', 'style_6', 'style_3', 'style_4', 'style_5' ),
				),
			)
		);
		$this->add_control(
			'autoplay',
			array(
				'label'              => esc_html__( 'Autoplay', 'masterstudy-lms-learning-management-system' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'On', 'masterstudy-lms-learning-management-system' ),
				'label_off'          => esc_html__( 'Off', 'masterstudy-lms-learning-management-system' ),
				'return_value'       => 'true',
				'default'            => false,
				'frontend_available' => true,
			)
		);
		$this->add_control(
			'loop',
			array(
				'label'              => esc_html__( 'Loop', 'masterstudy-lms-learning-management-system' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'On', 'masterstudy-lms-learning-management-system' ),
				'label_off'          => esc_html__( 'Off', 'masterstudy-lms-learning-management-system' ),
				'return_value'       => 'true',
				'default'            => 'true',
				'frontend_available' => true,
			)
		);
		$this->add_responsive_control(
			'arrows',
			array(
				'label'              => esc_html__( 'Arrows', 'masterstudy-lms-learning-management-system' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'On', 'masterstudy-lms-learning-management-system' ),
				'label_off'          => esc_html__( 'Off', 'masterstudy-lms-learning-management-system' ),
				'return_value'       => 'yes',
				'default'            => false,
				'frontend_available' => true,
			)
		);
		$this->add_control(
			'pagination',
			array(
				'label'              => esc_html__( 'Pagination', 'masterstudy-lms-learning-management-system' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'On', 'masterstudy-lms-learning-management-system' ),
				'label_off'          => esc_html__( 'Off', 'masterstudy-lms-learning-management-system' ),
				'return_value'       => 'yes',
				'default'            => false,
				'frontend_available' => true,
				'condition'          => array(
					'testimonials_style' => array( 'style_3' ),
				),
			)
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			array(
				'label' => esc_html__( 'User Logo', 'masterstudy-lms-learning-management-system' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$repeater->add_control(
			'author_name',
			array(
				'label' => esc_html__( 'Author name', 'masterstudy-lms-learning-management-system' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'author_position',
			array(
				'label'   => esc_html__( 'Author position', 'masterstudy-lms-learning-management-system' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Customer', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$repeater->add_control(
			'review_rating',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => esc_html__( 'Rating', 'masterstudy-lms-learning-management-system' ),
				'default' => 5,
				'options' => array(
					5 => '5',
					4 => '4',
					3 => '3',
					2 => '2',
					1 => '1',
					0 => '0',
				),
			)
		);
		$repeater->add_control(
			'content',
			array(
				'label'      => esc_html__( 'Content', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::WYSIWYG,
				'show_label' => false,
			)
		);
		$this->add_control(
			'testimonials_heading',
			array(
				'label'     => esc_html__( 'Testimonials', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		$this->add_control(
			'testimonials',
			array(
				'type'        => Controls_Manager::REPEATER,
				'label'       => '',
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ author_name }}}',
			)
		);
		$this->end_controls_section();
	}

	/** Register Typography Controls */
	protected function register_item_typo_content_controls() {
		$this->start_controls_section(
			'section_item_typography',
			array(
				'label'     => esc_html__( 'Item', 'masterstudy-lms-learning-management-system' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'testimonials_style' => array( 'style_2', 'style_3' ),
				),
			)
		);
		$this->add_control(
			'section_item_first_frame_color',
			array(
				'label'     => esc_html__( 'Frame One Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .stm-testimonials-carousel-shapes:before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'testimonials_style' => 'style_2',
				),
			)
		);
		$this->add_control(
			'section_item_second_frame_color',
			array(
				'label'     => esc_html__( 'Frame Two Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .stm-testimonials-carousel-shapes:after' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'testimonials_style' => 'style_2',
				),
			)
		);
		$this->add_control(
			'section_item_first_quote_color',
			array(
				'label'     => esc_html__( 'Quote One Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .stm-testimonials-carousel-shapes .ms-lms-testimonial-data::before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'testimonials_style' => 'style_2',
				),
			)
		);
		$this->add_control(
			'section_item_second_quote_color',
			array(
				'label'     => esc_html__( 'Quote Two Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .stm-testimonials-carousel-shapes .ms-lms-testimonial-data::after' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'testimonials_style' => 'style_2',
				),
			)
		);
		$this->start_controls_tabs(
			'items_style_tabs'
		);
		$this->start_controls_tab(
			'item_box_shadow_normal',
			array(
				'label'     => esc_html__( 'Normal', 'companion-elementor' ),
				'condition' => array(
					'testimonials_style' => 'style_3',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'item_box_border',
				'selector'  => '{{WRAPPER}} .ms-lms-testimonial-data-item',
				'condition' => array(
					'testimonials_style' => 'style_3',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'item_box_shadow',
				'selector'  => '{{WRAPPER}} .ms-lms-testimonial-data-item',
				'condition' => array(
					'testimonials_style' => 'style_3',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'item_box_shadow_hover',
			array(
				'label'     => esc_html__( 'Hover', 'companion-elementor' ),
				'condition' => array(
					'testimonials_style' => 'style_3',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'item_box_border_hover',
				'selector'  => '{{WRAPPER}} .ms-lms-testimonial-data-item:hover',
				'condition' => array(
					'testimonials_style' => 'style_3',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'item_box_shadow_hover',
				'selector'  => '{{WRAPPER}} .ms-lms-testimonial-data-item:hover',
				'condition' => array(
					'testimonials_style' => 'style_3',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function register_heading_typo_content_controls() {
		$this->start_controls_section(
			'section_heading_typography',
			array(
				'label' => esc_html__( 'Heading', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
				'name'     => 'testimonials_title_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .ms-lms-testimonials-header p, {{WRAPPER}} .ms-lms-testimonials-header .testimonials_main_title_6',
			)
		);
		$this->add_responsive_control(
			'testimonials_title_alignment',
			array(
				'label'                => esc_html__( 'Alignment', 'masterstudy-lms-learning-management-system' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'              => 'center',
				'selectors'            => array(
					'{{WRAPPER}} .ms-lms-testimonials-header p, {{WRAPPER}} .ms-lms-testimonials-header .testimonials_main_title_6' => 'width: 100%; text-align: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'text_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .ms-lms-testimonials-header p, {{WRAPPER}} .ms-lms-testimonials-header .testimonials_main_title_6' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->start_controls_tabs(
			'style_tabs'
		);
		$this->start_controls_tab(
			'heading_text_normal',
			array(
				'label' => esc_html__( 'Normal', 'companion-elementor' ),
			)
		);
		$this->add_control(
			'testimonials_title_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ms-lms-testimonials-header p, {{WRAPPER}} .ms-lms-testimonials-header .testimonials_main_title_6' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'testimonials_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ms-lms-testimonials-header .ms-lms-testimonials-icon .ms-lms-testimonials-icon__fillable' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .ms-lms-testimonials-header .testimonials_style_6_quote i' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'testimonials_style' => array( 'style_1', 'style_6' ),
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'header_text_hover',
			array(
				'label' => esc_html__( 'Hover', 'companion-elementor' ),
			)
		);
		$this->add_control(
			'testimonials_title_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ms-lms-testimonials-header p:hover, {{WRAPPER}} .ms-lms-testimonials-header .testimonials_main_title_6:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function register_heading_review_content_controls() {
		$this->start_controls_section(
			'section_heading_star',
			array(
				'label'     => esc_html__( 'Reviews', 'masterstudy-lms-learning-management-system' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'testimonials_style' => array( 'style_1', 'style_2', 'style_3', 'style_4', 'style_5' ),
				),
			)
		);
		$this->add_control(
			'testimonials_star_icon_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Star Icon', 'masterstudy-lms-learning-management-system' ),
				'default'   => '#ffc321',
				'selectors' => array(
					'{{WRAPPER}} .elementor-testimonials-carousel .ms-lms-testimonial-data .ms-lms-testimonial-review-rating i' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'reviews_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}  .ms-lms-testimonial-review-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function register_description_content_controls() {
		$this->start_controls_section(
			'section_description',
			array(
				'label' => esc_html__( 'Description', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'testimonials_content_typography',
				'label'    => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .elementor-testimonials-carousel .ms-lms-testimonial-data .content',
				'exclude'  => array(
					'font_style',
					'text_decoration',
				),
			)
		);
		$this->add_responsive_control(
			'content_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-testimonials-carousel .ms-lms-testimonial-data .content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->start_controls_tabs(
			'style_tabs_content'
		);
		$this->start_controls_tab(
			'content_text_normal',
			array(
				'label' => esc_html__( 'Normal', 'companion-elementor' ),
			)
		);
		$this->add_control(
			'testimonials_content_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-testimonials-carousel .ms-lms-testimonial-data .content' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'content_text_hover',
			array(
				'label' => esc_html__( 'Hover', 'companion-elementor' ),
			)
		);
		$this->add_control(
			'testimonials_content_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-testimonials-carousel .ms-lms-testimonial-data .content:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function register_author_content_controls() {
		$this->start_controls_section(
			'section_author',
			array(
				'label' => esc_html__( 'Author', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'testimonials_author_typography',
				'label'    => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .elementor-testimonials-carousel .ms-lms-testimonial-data .author-name',
			)
		);
		$this->add_responsive_control(
			'author_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-testimonials-carousel .ms-lms-testimonial-data .author-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'author_position_color',
			array(
				'label'     => esc_html__( 'Position Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .stm-testimonials-carousel-wrapper-style_4 .author-position' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'testimonials_style' => 'style_4',
				),
			)
		);
		$this->start_controls_tabs(
			'style_tabs_author'
		);
		$this->start_controls_tab(
			'author_text_normal',
			array(
				'label' => esc_html__( 'Normal', 'companion-elementor' ),
			)
		);
		$this->add_control(
			'testimonials_author_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'default'   => '#232628',
				'selectors' => array(
					'{{WRAPPER}} .elementor-testimonials-carousel .ms-lms-testimonial-data .author-name' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'author_text_hover',
			array(
				'label' => esc_html__( 'Hover', 'companion-elementor' ),
			)
		);
		$this->add_control(
			'testimonials_author_color_hover',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'default'   => '#232628',
				'selectors' => array(
					'{{WRAPPER}} .elementor-testimonials-carousel .ms-lms-testimonial-data .author-name:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function register_style_controls_nav_arrows() {

		$this->start_controls_section(
			'section_navigation_arrows',
			array(
				'label'     => esc_html__( 'Nav Arrows', 'masterstudy-lms-learning-management-system' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'arrows'             => 'yes',
					'testimonials_style' => array( 'style_1', 'style_2', 'style_3', 'style_4', 'style_5' ),
				),
			)
		);
		$this->start_controls_tabs(
			'navigation_arrows_tab'
		);
		$this->start_controls_tab(
			'navigation_arrows_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'navigation_arrows_typography',
				'selector' => '{{WRAPPER}} .swiper-button-prev:after, {{WRAPPER}} .swiper-button-next:after',
			)
		);
		$this->add_control(
			'navigation_prev_icon',
			array(
				'label'     => esc_html__( 'Prev Icon', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::ICONS,
				'skin'      => 'inline',
				'condition' => array(
					'arrows'             => 'yes',
					'testimonials_style' => 'style_4',
				),
			)
		);
		$this->add_control(
			'navigation_next_icon',
			array(
				'label'     => esc_html__( 'Next Icon', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::ICONS,
				'skin'      => 'inline',
				'condition' => array(
					'arrows'             => 'yes',
					'testimonials_style' => 'style_4',
				),
			)
		);
		$this->add_control(
			'navigation_arrows_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .swiper-button-prev:after' => 'color: {{VALUE}}',
					'{{WRAPPER}} .swiper-button-next:after' => 'color: {{VALUE}}',
					'{{WRAPPER}} .swiper-button-prev i'   => 'color: {{VALUE}}',
					'{{WRAPPER}} .swiper-button-next i'   => 'color: {{VALUE}}',
					'{{WRAPPER}} .swiper-button-prev svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .swiper-button-next svg' => 'fill: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'navigation_arrows_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'navigation_arrows_border',
				'selector' => '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next',
			)
		);
		$this->add_control(
			'navigation_arrows_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'navigation_arrows_shadow',
				'selector' => '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next',
			)
		);
		$this->add_responsive_control(
			'navigation_arrows_width',
			array(
				'label'      => esc_html__( 'Width', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-button-prev' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-button-next' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'navigation_arrows_height',
			array(
				'label'      => esc_html__( 'Height', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-button-prev' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-button-next' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'navigation_arrows_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'navigation_arrows_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .swiper-button-prev:hover:after' => 'color: {{VALUE}}',
					'{{WRAPPER}} .swiper-button-next:hover:after' => 'color: {{VALUE}}',
					'{{WRAPPER}} .swiper-button-prev:hover i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .swiper-button-next:hover i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .swiper-button-prev:hover svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .swiper-button-next:hover svg' => 'fill: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'navigation_arrows_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .swiper-button-prev:hover, {{WRAPPER}} .swiper-button-next:hover',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'navigation_arrows_border_hover',
				'selector' => '{{WRAPPER}} .swiper-button-prev:hover, {{WRAPPER}} .swiper-button-next:hover',
			)
		);
		$this->add_control(
			'navigation_arrows_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-button-prev:hover, {{WRAPPER}} .swiper-button-next:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'navigation_arrows_shadow_hover',
				'selector' => '{{WRAPPER}} .swiper-button-prev:hover, {{WRAPPER}} .swiper-button-next:hover',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'navigation_arrows_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function register_style_controls_nav_pagination() {

		$this->start_controls_section(
			'section_navigation_pagination',
			array(
				'label'     => esc_html__( 'Pagination', 'masterstudy-lms-learning-management-system' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pagination'         => 'yes',
					'testimonials_style' => array( 'style_1', 'style_3', 'style_6' ),
				),
			)
		);
		$this->start_controls_tabs(
			'navigation_pagination_tab'
		);
		$this->start_controls_tab(
			'navigation_pagination_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'navigation_pagination_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .ms-lms-elementor-testimonials-swiper-pagination .swiper-pagination-bullet',
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'navigation_pagination_active_background',
				'types'          => array( 'classic', 'gradient' ),
				'selector'       => '{{WRAPPER}} .ms-lms-elementor-testimonials-swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active',
				'fields_options' => array(
					'background' => array(
						'label' => esc_html__( 'Active Background Type', 'masterstudy-lms-learning-management-system' ),
					),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'navigation_pagination_border',
				'selector' => '{{WRAPPER}} .ms-lms-elementor-testimonials-swiper-pagination .swiper-pagination-bullet',
			)
		);
		$this->add_control(
			'navigation_pagination_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .ms-lms-elementor-testimonials-swiper-pagination .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'navigation_pagination_shadow',
				'selector' => '{{WRAPPER}} .ms-lms-elementor-testimonials-swiper-pagination .swiper-pagination-bullet',
			)
		);
		$this->add_responsive_control(
			'navigation_pagination_width',
			array(
				'label'      => esc_html__( 'Width', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ms-lms-elementor-testimonials-swiper-pagination .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'navigation_pagination_height',
			array(
				'label'      => esc_html__( 'Height', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ms-lms-elementor-testimonials-swiper-pagination .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'navigation_pagination_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'navigation_pagination_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .ms-lms-elementor-testimonials-swiper-pagination .swiper-pagination-bullet:hover',
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'navigation_pagination_active_background_hover',
				'types'          => array( 'classic', 'gradient' ),
				'selector'       => '{{WRAPPER}} .ms-lms-elementor-testimonials-swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active:hover',
				'fields_options' => array(
					'background' => array(
						'label' => esc_html__( 'Active Background Type', 'masterstudy-lms-learning-management-system' ),
					),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'navigation_pagination_border_hover',
				'selector' => '{{WRAPPER}} .ms-lms-elementor-testimonials-swiper-pagination .swiper-pagination-bullet:hover',
			)
		);
		$this->add_control(
			'navigation_pagination_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .ms-lms-elementor-testimonials-swiper-pagination .swiper-pagination-bullet:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'navigation_pagination_shadow_hover',
				'selector' => '{{WRAPPER}} .ms-lms-elementor-testimonials-swiper-pagination .swiper-pagination-bullet:hover',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'navigation_pagination_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .ms-lms-elementor-testimonials-swiper-pagination .swiper-pagination-bullet' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function register_item_typo_avatar_controls() {
		$this->start_controls_section(
			'section_avatar',
			array(
				'label'     => esc_html__( 'Avatar', 'masterstudy-lms-learning-management-system' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'testimonials_style' => array( 'style_2' ),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'section_avatar_border',
				'selector'  => '{{WRAPPER}} .stm-testimonials-carousel-shapes .ms-lms-testimonial-data .ms-lms-testimonial-media',
				'condition' => array(
					'testimonials_style' => 'style_2',
				),
			)
		);
		$this->add_control(
			'section_avatar_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .stm-testimonials-carousel-shapes .ms-lms-testimonial-data .ms-lms-testimonial-media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'section_avatar_shadow',
				'selector'  => '{{WRAPPER}} .stm-testimonials-carousel-shapes .ms-lms-testimonial-data .ms-lms-testimonial-media',
				'condition' => array(
					'testimonials_style' => 'style_2',
				),
			)
		);
		$this->end_controls_section();
	}

	/** Render the widget output on the frontend */
	protected function render() {
		if ( ! Plugin::$instance->editor->is_edit_mode() ) {
			wp_enqueue_script( 'swiper-bundle', STM_LMS_URL . 'assets/vendors/swiper-bundle.min.js', array(), STM_LMS_VERSION, true );
			wp_enqueue_script( 'lms-testimonials-carousel', STM_LMS_URL . 'assets/js/elementor-widgets/testimonials_carousel.js', array(), STM_LMS_VERSION, true );
		}

		$settings              = $this->get_settings_for_display();
		$settings['unique_id'] = 'stm_testimonials_carousel-' . $this->get_id();
		if ( empty( $settings['testimonials'] ) ) {
			?>
			<h2><?php echo esc_html__( 'LMS Testimonials Widget', 'masterstudy-lms-learning-management-system' ); ?></h2>
			<p><?php echo esc_html__( 'Add some reviewers to display the content of the widget.', 'masterstudy-lms-learning-management-system' ); ?></p>
			<?php
		}
		extract( $settings );
		if ( ! empty( $testimonials ) ) {
			$allowed_styles     = array( 'style_1', 'style_2', 'style_3', 'style_4', 'style_5', 'style_6' );
			$testimonials_style = ( ! empty( $testimonials_style ) && in_array( $testimonials_style, $allowed_styles, true ) )
				? $testimonials_style
				: 'style_1';

			$style_file = MS_LMS_PATH . '/_core/includes/elementor/widgets/testimonials/styles/' . $testimonials_style . '.php';

			if ( ! file_exists( $style_file ) ) {
				$style_file = MS_LMS_PATH . '/_core/includes/elementor/widgets/testimonials/styles/style_1.php';
			}

			if ( file_exists( $style_file ) ) {
				require_once $style_file;
			}
		}
	}

	protected function get_html_data( $testimonials_data, $title ) {
		$html  = '<div class="ms-lms-testimonials-wrapper simple_carousel_wrapper">';
		$html .= '<div class="ms-lms-testimonials-header"><i class="ms-lms-testimonials-icon"></i>';
		$html .= '<p>' . esc_html( $title ) . '</p>';
		$html .= '</div>';
		$html .= '<div class="ms-lms-starter-theme-testimonials">';
		foreach ( $testimonials_data as $testimonial ) {
			$html .= '<div class="stm_testimonials_single" >
						<div class="stars" ><i class="stmlms-star-3" ></i ></div>
						<div class="testimonials_title h3" >'
					. sanitize_text_field( $testimonial['title'] ) .
					'</div>
						<div class="testimonials_excerpt" >'
					. wp_kses_post( $testimonial['excerpt'] ) .
					'</div>
					</div>';
		}
		$html .= '</div>';
		$html .= '<div class="navs">';
		$html .= '<ul id="carousel-custom-dots">';
		foreach ( $testimonials_data as $testimonial ) {
			$html .= '<li class="testinomials_dots_image"><img src="' . esc_url( $testimonial['image'] ) . '" /></li>';
		}
		$html .= '</ul></div></div>';

		return $html;
	}

	protected function content_template() {
	}

}
