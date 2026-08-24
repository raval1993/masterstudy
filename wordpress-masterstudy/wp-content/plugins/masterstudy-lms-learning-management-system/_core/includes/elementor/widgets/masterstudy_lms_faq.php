<?php
/**
 * MasterStudy LMS FAQ Widget for Elementor
 *
 * @package MasterStudy LMS
 */

namespace StmLmsElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Masterstudy_Lms_Faq extends Widget_Base {
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
		wp_register_style(
			'masterstudy_lms_faq',
			STM_LMS_URL . 'assets/css/elementor-widgets/masterstudy-lms-faq.css',
			array( 'masterstudy-fonts' ),
			STM_LMS_VERSION,
			false
		);
		wp_register_script(
			'masterstudy_lms_faq_script',
			STM_LMS_URL . 'assets/js/elementor-widgets/masterstudy-lms-faq.js',
			array( 'jquery' ),
			STM_LMS_VERSION,
			true
		);
	}

	public function get_name(): string {
		return 'masterstudy_lms_faq';
	}

	public function get_title(): string {
		return esc_html__( 'FAQ', 'masterstudy-lms-learning-management-system' );
	}

	public function get_icon(): string {
		return 'eicon-help-o lms-icon';
	}

	public function get_categories(): array {
		return array( 'stm_lms' );
	}

	public function get_style_depends(): array {
		return array( 'masterstudy-fonts', 'masterstudy_lms_faq' );
	}

	public function get_script_depends(): array {
		return array( 'masterstudy_lms_faq_script' );
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	private function register_content_controls(): void {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'masterstudy-lms-learning-management-system' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'FAQ', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$this->add_control(
			'faq_icon_collapsed',
			array(
				'label' => esc_html__( 'Collapsed icon', 'masterstudy-lms-learning-management-system' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'faq_icon_opened',
			array(
				'label' => esc_html__( 'Opened icon', 'masterstudy-lms-learning-management-system' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'question',
			array(
				'label'       => esc_html__( 'Question', 'masterstudy-lms-learning-management-system' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Do I need prior experience?', 'masterstudy-lms-learning-management-system' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'answer',
			array(
				'label'       => esc_html__( 'Answer', 'masterstudy-lms-learning-management-system' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'No prior experience is required. The course is designed with beginners in mind and starts from the very basics.', 'masterstudy-lms-learning-management-system' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'faq_items',
			array(
				'label'       => esc_html__( 'FAQ Items', 'masterstudy-lms-learning-management-system' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'question' => esc_html__( 'Do I need prior experience?', 'masterstudy-lms-learning-management-system' ),
						'answer'   => esc_html__( 'No prior experience is required. The course is designed with beginners in mind and starts from the very basics.', 'masterstudy-lms-learning-management-system' ),
					),
					array(
						'question' => esc_html__( 'Can I study at my own pace?', 'masterstudy-lms-learning-management-system' ),
						'answer'   => esc_html__( 'Yes, you can access the course materials anytime and learn at your own pace.', 'masterstudy-lms-learning-management-system' ),
					),
					array(
						'question' => esc_html__( 'Will I get a certificate?', 'masterstudy-lms-learning-management-system' ),
						'answer'   => esc_html__( 'Upon successful completion of the course, you will receive a certificate.', 'masterstudy-lms-learning-management-system' ),
					),
					array(
						'question' => esc_html__( 'Can I access lessons on mobile?', 'masterstudy-lms-learning-management-system' ),
						'answer'   => esc_html__( 'Yes, our platform is fully responsive and works on all devices.', 'masterstudy-lms-learning-management-system' ),
					),
				),
				'title_field' => '{{{ question }}}',
			)
		);

		$this->end_controls_section();
	}

	private function register_style_controls(): void {
		$title_selector       = '{{WRAPPER}} .masterstudy-lms-faq__title';
		$item_selector        = '{{WRAPPER}} .masterstudy-lms-faq__item';
		$item_selector_opened = '{{WRAPPER}} .masterstudy-lms-faq__item.masterstudy-lms-faq__item_opened';
		$question_sel         = '{{WRAPPER}} .masterstudy-lms-faq__question';
		$answer_sel           = '{{WRAPPER}} .masterstudy-lms-faq__answer';

		$this->start_controls_section(
			'title_style_section',
			array(
				'label' => esc_html__( 'Title', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => $title_selector,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( $title_selector => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'title_background',
			array(
				'label'     => esc_html__( 'Background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#D9F750',
				'selectors' => array( $title_selector => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array( $title_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array( $title_selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'item_style_section',
			array(
				'label' => esc_html__( 'Item', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'item_background',
			array(
				'label'     => esc_html__( 'Background (collapsed)', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F8F8F8',
				'selectors' => array( $item_selector => '--masterstudy-faq-item-bg: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'item_background_active',
			array(
				'label'     => esc_html__( 'Background (expanded)', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#262626',
				'selectors' => array( $item_selector => '--masterstudy-faq-item-bg-active: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'item_border_color',
			array(
				'label'     => esc_html__( 'Border color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#262626',
				'selectors' => array( $item_selector => '--masterstudy-faq-border: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'item_border_radius',
			array(
				'label'      => esc_html__( 'Border radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array( $item_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array( $item_selector => '--masterstudy-faq-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_box_shadow',
				'label'    => esc_html__( 'Box shadow (collapsed)', 'masterstudy-lms-learning-management-system' ),
				'selector' => $item_selector,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_box_shadow_opened',
				'label'    => esc_html__( 'Box shadow (expanded)', 'masterstudy-lms-learning-management-system' ),
				'selector' => $item_selector_opened,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'question_style_section',
			array(
				'label' => esc_html__( 'Question', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'question_typography',
				'selector' => $question_sel,
			)
		);

		$this->add_control(
			'question_color',
			array(
				'label'     => esc_html__( 'Color (collapsed)', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#262626',
				'selectors' => array( $item_selector => '--masterstudy-faq-question-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'question_color_active',
			array(
				'label'     => esc_html__( 'Color (expanded)', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array( $item_selector => '--masterstudy-faq-question-color-active: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon color (collapsed)', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7B6AF4',
				'selectors' => array( $item_selector => '--masterstudy-faq-icon-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'icon_color_active',
			array(
				'label'     => esc_html__( 'Icon color (expanded)', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#D9F750',
				'selectors' => array( $item_selector => '--masterstudy-faq-icon-color-active: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'answer_style_section',
			array(
				'label' => esc_html__( 'Answer', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'answer_typography',
				'selector' => $answer_sel,
			)
		);

		$this->add_control(
			'answer_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.7)',
				'selectors' => array( $answer_sel => 'color: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$items    = isset( $settings['faq_items'] ) && is_array( $settings['faq_items'] ) ? $settings['faq_items'] : array();

		\STM_LMS_Templates::show_lms_template(
			'elementor-widgets/masterstudy-lms-faq',
			array(
				'title'          => isset( $settings['title'] ) ? $settings['title'] : esc_html__( 'FAQ', 'masterstudy-lms-learning-management-system' ),
				'items'          => $items,
				'icon_collapsed' => isset( $settings['faq_icon_collapsed'] ) ? $settings['faq_icon_collapsed'] : array(),
				'icon_opened'    => isset( $settings['faq_icon_opened'] ) ? $settings['faq_icon_opened'] : array(),
			)
		);
	}
}
