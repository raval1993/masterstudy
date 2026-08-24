<?php
namespace StmLmsElementor\Widgets\Course;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MsLmsCourseGrades extends Widget_Base {

	public function get_name() {
		return 'ms_lms_course_grades';
	}

	public function get_title() {
		return esc_html__( 'Grades', 'masterstudy-lms-learning-management-system' );
	}

	public function get_icon() {
		$icon = ! \STM_LMS_Helpers::is_pro_plus() ? 'lms-locked-course-icon' : 'lms-course-icon';

		return "stmlms-course-grade-a $icon";
	}

	public function get_categories() {
		return array( 'stm_lms_course' );
	}

	public function get_style_depends() {
		return array(
			'masterstudy-single-course-components',
			'masterstudy-single-course-grade',
			'masterstudy-grade-details',
		);
	}

	public function get_script_depends() {
		return array(
			'masterstudy-course-grades-editor',
		);
	}

	protected function get_upsale_data(): array {
		$is_pro_plus = \STM_LMS_Helpers::is_pro_plus();
		$only_pro    = \STM_LMS_Helpers::is_pro() && ! $is_pro_plus;
		$link        = $only_pro ? 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=elementorwidget' : admin_url( 'admin.php?page=stm-lms-go-pro&source=elementorwidget' );
		$link_text   = $only_pro ? esc_html__( 'Upgrade to PRO PLUS', 'masterstudy-lms-learning-management-system' ) : esc_html__( 'Upgrade to PRO', 'masterstudy-lms-learning-management-system' );
		$icon_url    = $only_pro ? STM_LMS_URL . 'assets/img/pro-features/pro_plus.svg' : STM_LMS_URL . 'assets/img/pro-features/unlock-pro-logo.svg';
		$title       = $only_pro ? esc_html__( 'Get Access to Exclusive Widgets with MasterStudy PRO PLUS', 'masterstudy-lms-learning-management-system' ) : esc_html__( 'Get Access to Exclusive Widgets with MasterStudy PRO', 'masterstudy-lms-learning-management-system' );

		return array(
			'condition'    => ! $is_pro_plus,
			'image'        => esc_url( $icon_url ),
			'title'        => $title,
			'upgrade_url'  => $link,
			'upgrade_text' => $link_text,
		);
	}

	protected function register_controls() {
		$is_pro_plus = \STM_LMS_Helpers::is_pro_plus();
		$courses     = \STM_LMS_Courses::get_all_courses_for_options();
		$context     = masterstudy_lms_get_elementor_page_context( get_the_ID() );

		$this->start_controls_section(
			'section',
			array(
				'label' => esc_html__( 'Content', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		if ( ! $is_pro_plus ) {
			return;
		}

		$this->add_control(
			'course',
			array(
				'label'              => esc_html__( 'Course', 'masterstudy-lms-learning-management-system' ),
				'type'               => Controls_Manager::SELECT2,
				'label_block'        => true,
				'multiple'           => false,
				'options'            => $courses,
				'frontend_available' => true,
				'default'            => ! empty( $context['course_for_page'] ) && isset( $courses[ $context['course_for_page'] ] )
					? $context['course_for_page']
					: ( ! empty( $courses ) ? key( $courses ) : '' ),
			)
		);
		if ( $context['is_course_template'] ) {
			$this->add_control(
				'course_note',
				array(
					'type' => \Elementor\Controls_Manager::RAW_HTML,
					'raw'  => \STM_LMS_Templates::load_lms_template( 'elementor-widgets/course-note' ),
				)
			);
		}
		$this->end_controls_section();
		$this->start_controls_section(
			'container_section',
			array(
				'label' => esc_html__( 'Container', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'container_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades',
			)
		);
		$this->add_control(
			'container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'container_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'title_section',
			array(
				'label' => esc_html__( 'Title', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__title',
			)
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'badge_section',
			array(
				'label' => esc_html__( 'Badge', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__badge',
			)
		);
		$this->add_responsive_control(
			'badge_width',
			array(
				'label'      => esc_html__( 'Width', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__badge' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'badge_height',
			array(
				'label'      => esc_html__( 'Height', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__badge' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'badge_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'points_range_labels_section',
			array(
				'label' => esc_html__( 'Points & Range Labels', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'points_range_labels_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__points-label, {{WRAPPER}} .masterstudy-single-course-grades__points-label',
			)
		);
		$this->add_control(
			'points_range_labels_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__points-label, {{WRAPPER}} .masterstudy-single-course-grades__points-label' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'points_range_values_section',
			array(
				'label' => esc_html__( 'Points & Range Values', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'points_range_values_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__points-value, {{WRAPPER}} .masterstudy-single-course-grades__points-value',
			)
		);
		$this->add_control(
			'points_range_values_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__points-value, {{WRAPPER}} .masterstudy-single-course-grades__points-value' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'description_section',
			array(
				'label' => esc_html__( 'Description', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__description',
			)
		);
		$this->add_control(
			'description_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__description' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'description_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'details_button_section',
			array(
				'label' => esc_html__( 'Details Button', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'details_button_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__details-button a.masterstudy-button .masterstudy-button__title',
			)
		);
		$this->add_responsive_control(
			'details_button_height',
			array(
				'label'      => esc_html__( 'Height', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__details-button a.masterstudy-button' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'details_button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__details-button a.masterstudy-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'details_button_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__details-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->start_controls_tabs(
			'details_button_tab'
		);
		$this->start_controls_tab(
			'details_button_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'details_button_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__details-button a.masterstudy-button .masterstudy-button__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'details_button_normal_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__details-button a.masterstudy-button',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'details_button_normal_border',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__details-button a.masterstudy-button',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'details_button_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'details_button_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__details-button a.masterstudy-button:hover .masterstudy-button__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'details_button_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__details-button a.masterstudy-button:hover',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'details_button_border_hover',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__details-button a.masterstudy-button:hover',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'modal_section',
			array(
				'label' => esc_html__( 'Details Modal', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'modal_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-grade-details__block, {{WRAPPER}} .masterstudy-grade-details__loader, {{WRAPPER}} .masterstudy-grade-details__loader-body::after',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'modal_border',
				'selector' => '{{WRAPPER}} .masterstudy-grade-details__block',
			)
		);
		$this->add_control(
			'modal_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-grade-details__block' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'modal_shadow',
				'selector' => '{{WRAPPER}} .masterstudy-grade-details__block',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'modal_title_section',
			array(
				'label' => esc_html__( 'Details Modal Title', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'modal_title_typography',
				'selector' => '{{WRAPPER}} .masterstudy-grade-details__title',
			)
		);
		$this->add_control(
			'modal_title_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-grade-details__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'modal_close_section',
			array(
				'label' => esc_html__( 'Details Modal Close Button', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'modal_close_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-grade-details__close::after' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'modal_close_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'masterstudy-lms-learning-management-system' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-grade-details__close::after' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'modal_close_width',
			array(
				'label'      => esc_html__( 'Width', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-grade-details__close' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'modal_close_height',
			array(
				'label'      => esc_html__( 'Height', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-grade-details__close' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'modal_close_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-grade-details__close',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'modal_close_border',
				'selector' => '{{WRAPPER}} .masterstudy-grade-details__close',
			)
		);
		$this->add_control(
			'modal_close_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-grade-details__close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'exam_icon_section',
			array(
				'label' => esc_html__( 'Modal Exam Icon', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'exam_icon_size',
			array(
				'label'      => esc_html__( 'Size', 'masterstudy-lms-learning-management-system' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-grade-details__exams-item-icon::before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'exam_icon_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-grade-details__exams-item-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'exam_title_section',
			array(
				'label' => esc_html__( 'Modal Exam Title', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'exam_title_typography',
				'selector' => '{{WRAPPER}} .masterstudy-grade-details__exams-item-title',
			)
		);
		$this->add_control(
			'exam_title_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-grade-details__exams-item-title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'exam_title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-grade-details__exams-item-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'exam_attempts_section',
			array(
				'label' => esc_html__( 'Modal Exam Attempts', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'exam_attempts_typography',
				'selector' => '{{WRAPPER}} .masterstudy-grade-details__exams-item-attempt',
			)
		);
		$this->add_control(
			'exam_attempts_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-grade-details__exams-item-attempt' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'exam_attempts_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-grade-details__exams-item-attempt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'grade_value_section',
			array(
				'label' => esc_html__( 'Modal Grade Value', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'grade_value_typography',
				'selector' => '{{WRAPPER}} .masterstudy-grade-details__exams-item-grade-value',
			)
		);
		$this->add_control(
			'grade_value_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-grade-details__exams-item-grade-value' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'grade_value_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-grade-details__exams-item-grade-value' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'grade_percent_section',
			array(
				'label' => esc_html__( 'Modal Exam Percent', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'grade_percent_typography',
				'selector' => '{{WRAPPER}} .masterstudy-grade-details__exams-item-percent',
			)
		);
		$this->add_control(
			'grade_percent_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-grade-details__exams-item-percent' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'grade_percent_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-grade-details__exams-item-percent' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'separator_section',
			array(
				'label' => esc_html__( 'Modal Exam Separator', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'separator_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-grade-details__exams-item' => 'border-color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'message_container_section',
			array(
				'label' => esc_html__( 'Message Container', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'message_container_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__message',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'message_container_border',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__message',
			)
		);
		$this->add_control(
			'message_container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'message_container_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'message_container_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__message' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'message_text_section',
			array(
				'label' => esc_html__( 'Message Text', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'message_text_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__message-text',
			)
		);
		$this->add_control(
			'message_text_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__message-text' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'generate_button_section',
			array(
				'label' => esc_html__( 'Message Button', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'generate_button_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__message-button a.masterstudy-button .masterstudy-button__title',
			)
		);
		$this->add_responsive_control(
			'enerate_button_height',
			array(
				'label'      => esc_html__( 'Height', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__message-button a.masterstudy-button' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'generate_button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__message-button a.masterstudy-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'generate_button_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__message-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->start_controls_tabs(
			'generate_button_tab'
		);
		$this->start_controls_tab(
			'generate_button_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'generate_button_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__message-button a.masterstudy-button .masterstudy-button__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'generate_button_normal_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__message-button a.masterstudy-button',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'generate_button_normal_border',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__message-button a.masterstudy-button',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'generate_button_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'generate_button_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-grades__message-button a.masterstudy-button:hover .masterstudy-button__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'generate_button_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__message-button a.masterstudy-button:hover',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'generate_button_border_hover',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-grades__message-button a.masterstudy-button:hover',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function render() {
		$is_edit_mode = Plugin::$instance->editor->is_edit_mode();

		if ( ! \STM_LMS_Helpers::is_pro_plus() ) {
			if ( $is_edit_mode ) {
				masterstudy_get_elementor_unlock_banner();
			}
			return;
		}

		if ( ! is_ms_lms_addon_enabled( 'grades' ) ) {
			if ( $is_edit_mode ) {
				masterstudy_get_elementor_unlock_banner( 'grades' );
			}
			return;
		}

		global $masterstudy_single_page_course_id;

		$settings    = $this->get_settings_for_display();
		$course_id   = ! empty( $masterstudy_single_page_course_id ) ? $masterstudy_single_page_course_id : $settings['course'] ?? null;
		$course_data = masterstudy_get_elementor_course_data( intval( $course_id ) );

		if ( empty( $course_data ) || ! isset( $course_data['course'] ) ) {
			return;
		}

		\STM_LMS_Templates::show_lms_template(
			'components/course/grades',
			array(
				'course'     => $course_data['course'],
				'user_id'    => $course_data['current_user_id'],
				'in_sidebar' => false,
			)
		);
	}
}
