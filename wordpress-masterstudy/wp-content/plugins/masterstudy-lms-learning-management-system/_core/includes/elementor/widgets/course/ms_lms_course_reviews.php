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

class MsLmsCourseReviews extends Widget_Base {

	public function get_name() {
		return 'ms_lms_course_reviews';
	}

	public function get_title() {
		return esc_html__( 'Reviews', 'masterstudy-lms-learning-management-system' );
	}

	public function get_icon() {
		return 'stmlms-course-review lms-course-icon';
	}

	public function get_categories() {
		return array( 'stm_lms_course' );
	}

	public function get_style_depends() {
		return array(
			'masterstudy-single-course-components',
			'masterstudy-progress',
			'masterstudy-wp-editor',
		);
	}

	public function get_script_depends() {
		return array( 'masterstudy-course-reviews-editor', 'masterstudy-wp-editor' );
	}

	protected function register_controls() {
		$courses = \STM_LMS_Courses::get_all_courses_for_options();
		$context = masterstudy_lms_get_elementor_page_context( get_the_ID() );

		$this->start_controls_section(
			'section',
			array(
				'label' => esc_html__( 'Content', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
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
		$this->add_control(
			'preset',
			array(
				'label'              => esc_html__( 'Preset', 'masterstudy-lms-learning-management-system' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'default',
				'frontend_available' => true,
				'options'            => array(
					'default' => esc_html__( 'Standard', 'masterstudy-lms-learning-management-system' ),
					'grid'    => esc_html__( 'Reviews Grid', 'masterstudy-lms-learning-management-system' ),
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'rating_section',
			array(
				'label'      => esc_html__( 'Rating', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'rating_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__count',
			)
		);
		$this->add_control(
			'rating_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__count' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'rating_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__count' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'stars_section',
			array(
				'label'      => esc_html__( 'Stars', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_control(
			'stars_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__detailed .masterstudy-single-course-reviews__star::before' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'stars_filled_color',
			array(
				'label'     => esc_html__( 'Filled Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__detailed .masterstudy-single-course-reviews__star_filled::before' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'stars_size',
			array(
				'label'      => esc_html__( 'Size', 'masterstudy-lms-learning-management-system' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__detailed .masterstudy-single-course-reviews__star, {{WRAPPER}} .masterstudy-single-course-reviews__detailed .masterstudy-single-course-reviews__star_filled' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'stars_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__detailed .masterstudy-single-course-reviews__star, {{WRAPPER}} .masterstudy-single-course-reviews__detailed .masterstudy-single-course-reviews__star_filled' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'reviews_section',
			array(
				'label'      => esc_html__( 'Number of Reviews', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'reviews_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__quantity',
			)
		);
		$this->add_control(
			'reviews_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__quantity' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'reviews_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__quantity' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'progress_section',
			array(
				'label'      => esc_html__( 'Rating Breakdown Bar', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_control(
			'progress_slider_color',
			array(
				'label'     => esc_html__( 'Filled Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-progress__bar-filled' => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'progress_bar_color',
			array(
				'label'     => esc_html__( 'Empty Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-progress__bar-empty' => 'border-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'mark_label_section',
			array(
				'label'      => esc_html__( 'Rating Scale Labels', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_control(
			'mark_label_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__stats-item-mark' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'mark_label_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__stats-item-mark',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'mark_quantity_section',
			array(
				'label'      => esc_html__( 'Number of Ratings', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_control(
			'mark_quantity_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__stats-item-count' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'mark_quantity_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__stats-item-count',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'add_review_section',
			array(
				'label'      => esc_html__( 'Write Review Button', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'add_review_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__add-button',
			)
		);
		$this->add_control(
			'add_review_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__add-button' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'add_review_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__add-button:hover' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'add_review_icon_width',
			array(
				'label'      => esc_html__( 'Width', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__add-button-icon' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'add_review_icon_height',
			array(
				'label'      => esc_html__( 'Height', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__add-button-icon' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'add_review_icon_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__add-button-icon',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'add_review_icon_border',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__add-button-icon',
			)
		);
		$this->add_control(
			'add_review_icon_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__add-button-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'add_review_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'masterstudy-lms-learning-management-system' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__add-button-icon::before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'add_review_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__add-button-icon::before' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'add_review_icon_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__add-button-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'review_stars_section',
			array(
				'label' => esc_html__( 'Review Stars', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'review_stars_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__list .masterstudy-single-course-reviews__star::before' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'review_stars_filled_color',
			array(
				'label'     => esc_html__( 'Filled Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__list .masterstudy-single-course-reviews__star_filled::before' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'review_stars_size',
			array(
				'label'      => esc_html__( 'Size', 'masterstudy-lms-learning-management-system' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__list .masterstudy-single-course-reviews__star, {{WRAPPER}} .masterstudy-single-course-reviews__star_filled' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'review_stars_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__list .masterstudy-single-course-reviews__star, {{WRAPPER}} .masterstudy-single-course-reviews__list .masterstudy-single-course-reviews__star_filled' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'review_text_section',
			array(
				'label' => esc_html__( 'Review Text', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'review_text_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__item-content p',
			)
		);
		$this->add_control(
			'review_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__item-content p' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'review_text_separator_color',
			array(
				'label'     => esc_html__( 'Divider Line Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__item, {{WRAPPER}} .masterstudy-single-course-reviews__main' => 'border-color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'review_author_section',
			array(
				'label' => esc_html__( 'Review Author', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'review_author_typography',
				'selector' => '{{WRAPPER}} a.masterstudy-single-course-reviews__item-user span',
			)
		);
		$this->add_control(
			'review_author_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.masterstudy-single-course-reviews__item-user span' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'review_author_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.masterstudy-single-course-reviews__item-user:hover span' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'review_author_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} a.masterstudy-single-course-reviews__item-user' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'review_date_section',
			array(
				'label' => esc_html__( 'Review Date', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'review_date_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__item-date',
			)
		);
		$this->add_control(
			'review_date_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__item-date' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'review_status_section',
			array(
				'label' => esc_html__( 'Review Status', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'review_status_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__item-status',
			)
		);
		$this->add_control(
			'review_status_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__item-status' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'review_status_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__item-status',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'review_status_border',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__item-status',
			)
		);
		$this->add_control(
			'review_status_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__item-status' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'modal_section',
			array(
				'label'      => esc_html__( 'Editor', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'modal_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__form',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'modal_border',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__form',
			)
		);
		$this->add_control(
			'modal_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'modal_shadow',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__form',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'modal_title_section',
			array(
				'label'      => esc_html__( 'Editor Name', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_control(
			'modal_title_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'modal_title_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__form-title',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'modal_close_section',
			array(
				'label'      => esc_html__( 'Editor Close Button', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_control(
			'modal_close_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-close::after' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-close::after' => 'font-size: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-close' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-close' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'modal_close_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__form-close',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'modal_close_border',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__form-close',
			)
		);
		$this->add_control(
			'modal_close_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'submit_button_section',
			array(
				'label'      => esc_html__( 'Submit Button', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'submit_button_typography',
				'selector' => '{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-submit"] span.masterstudy-button__title',
			)
		);
		$this->start_controls_tabs(
			'submit_button_tab'
		);
		$this->start_controls_tab(
			'submit_button_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'submit_button_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-submit"] span.masterstudy-button__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'submit_button_normal_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-submit"]',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'submit_button_normal_border',
				'selector' => '{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-submit"]',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'submit_button_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'submit_button_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-submit"]:hover span.masterstudy-button__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'submit_button_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-submit"]:hover',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'submit_button_border_hover',
				'selector' => '{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-submit"]:hover',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'editor_stars_section',
			array(
				'label'      => esc_html__( 'Editor Stars', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_control(
			'editor_stars_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-rating .masterstudy-single-course-reviews__star::before' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'editor_stars_filled_color',
			array(
				'label'     => esc_html__( 'Filled Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-rating .masterstudy-single-course-reviews__star_clicked::before' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'editor_stars_size',
			array(
				'label'      => esc_html__( 'Size', 'masterstudy-lms-learning-management-system' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-rating .masterstudy-single-course-reviews__star, {{WRAPPER}} .masterstudy-single-course-reviews__form-rating .masterstudy-single-course-reviews__star_clicked' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'editor_stars_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-rating .masterstudy-single-course-reviews__star, {{WRAPPER}} .masterstudy-single-course-reviews__form-rating .masterstudy-single-course-reviews__star_filled' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'editor_stars_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__form-rating',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'editor_stars_border',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__form-rating',
			)
		);
		$this->add_control(
			'editor_stars_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-rating' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'container_section',
			array(
				'label' => esc_html__( 'Review Box', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'container_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__item',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'border',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__item',
			)
		);
		$this->add_control(
			'container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .masterstudy-single-course-reviews__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'load_button_section',
			array(
				'label' => esc_html__( 'Load More Button', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'load_button_typography',
				'selector' => '{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-more"] span.masterstudy-button__title',
			)
		);
		$this->add_control(
			'load_button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-more"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'load_button_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-more"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->start_controls_tabs(
			'load_button_tab'
		);
		$this->start_controls_tab(
			'load_button_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'load_button_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-more"] span.masterstudy-button__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'load_button_normal_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-more"]',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'load_button_normal_border',
				'selector' => '{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-more"]',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'load_button_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'load_button_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-more"]:hover span.masterstudy-button__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'load_button_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-more"]:hover',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'load_button_border_hover',
				'selector' => '{{WRAPPER}} a.masterstudy-button[data-id="masterstudy-single-course-reviews-more"]:hover',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'please_login_section',
			array(
				'label'      => esc_html__( 'Login', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'please_login_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__login',
			)
		);
		$this->add_control(
			'please_login_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__login' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'please_login_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__login' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'please_login_link_section',
			array(
				'label'      => esc_html__( 'Login Button', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'please_login_link_typography',
				'selector' => '{{WRAPPER}} a.masterstudy-single-course-reviews__login-link',
			)
		);
		$this->add_control(
			'please_login_link_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.masterstudy-single-course-reviews__login-link' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'please_login_link_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.masterstudy-single-course-reviews__login-link:hover' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'please_login_link_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} a.masterstudy-single-course-reviews__login-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'review_warning_section',
			array(
				'label'      => esc_html__( 'Warning Message', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'review_warning_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__form-message:not(.masterstudy-single-course-reviews__form-message_success)',
			)
		);
		$this->add_control(
			'review_warning_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-message:not(.masterstudy-single-course-reviews__form-message_success)' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'review_warning_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__form-message:not(.masterstudy-single-course-reviews__form-message_success)',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'review_warning_border',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__form-message:not(.masterstudy-single-course-reviews__form-message_success)',
			)
		);
		$this->add_control(
			'review_warning_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-message:not(.masterstudy-single-course-reviews__form-message_success)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'review_warning_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-message:not(.masterstudy-single-course-reviews__form-message_success)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'review_success_section',
			array(
				'label'      => esc_html__( 'Success Message', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'default',
						),
					),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'review_success_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__form-message_success',
			)
		);
		$this->add_control(
			'review_success_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-message_success' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'review_success_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__form-message_success',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'review_success_border',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-reviews__form-message_success',
			)
		);
		$this->add_control(
			'review_success_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-message_success' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'review_success_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-reviews__form-message_success' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		global $masterstudy_single_page_course_id;

		$settings    = $this->get_settings_for_display();
		$course_id   = ! empty( $masterstudy_single_page_course_id ) ? $masterstudy_single_page_course_id : $settings['course'] ?? null;
		$course_data = masterstudy_get_elementor_course_data( intval( $course_id ) );

		if ( empty( $course_data ) || ! isset( $course_data['course'] ) ) {
			return;
		}

		if ( Plugin::$instance->editor->is_edit_mode() ) {
			masterstudy_get_elementor_content_banner( 'reviews' );
		}

		wp_enqueue_script( 'masterstudy-single-course-components', STM_LMS_URL . 'assets/js/components/course/main.js', array( 'jquery', 'jquery.cookie' ), MS_LMS_VERSION, true );
		wp_enqueue_script( 'masterstudy-wp-editor', STM_LMS_URL . 'assets/js/components/wp-editor.js', array( 'jquery' ), MS_LMS_VERSION, true );

		\STM_LMS_Templates::show_lms_template(
			'components/course/reviews',
			array(
				'course'  => $course_data['course'],
				'user_id' => $course_data['current_user_id'],
				'style'   => $settings['preset'],
			)
		);
	}
}
