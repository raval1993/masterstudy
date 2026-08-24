<?php
namespace StmLmsElementor\Widgets\Course;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MsLmsCourseDetails extends Widget_Base {

	public function get_name() {
		return 'ms_lms_course_details';
	}

	public function get_title() {
		return esc_html__( 'Details', 'masterstudy-lms-learning-management-system' );
	}

	public function get_icon() {
		return 'stmlms-course-details lms-course-icon';
	}

	public function get_categories() {
		return array( 'stm_lms_course' );
	}

	public function get_style_depends() {
		return array(
			'masterstudy-single-course-components',
		);
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
					'default'     => esc_html__( 'Standard', 'masterstudy-lms-learning-management-system' ),
					'row'         => esc_html__( 'Horizontal', 'masterstudy-lms-learning-management-system' ),
					'divider_row' => esc_html__( 'Horizontal Divided', 'masterstudy-lms-learning-management-system' ),
					'grid'        => esc_html__( 'Grid', 'masterstudy-lms-learning-management-system' ),
					'grid_row'    => esc_html__( 'Grid Compact', 'masterstudy-lms-learning-management-system' ),
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'title_section',
			array(
				'label'      => esc_html__( 'Title', 'masterstudy-lms-learning-management-system' ),
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
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-details__title',
			)
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-details__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'detail_section',
			array(
				'label' => esc_html__( 'Detail Label', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'detail_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-details__name, {{WRAPPER}} .masterstudy-single-course-details__separator',
			)
		);
		$this->add_control(
			'detail_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-details__name, {{WRAPPER}} .masterstudy-single-course-details__separator' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'detail_icon_section',
			array(
				'label' => esc_html__( 'Icon', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'detail_icon_size',
			array(
				'label'      => esc_html__( 'Size', 'masterstudy-lms-learning-management-system' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-details__icon::before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'detail_icon_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-details__icon::before' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'detail_icon_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-details__icon-wrapper',
			)
		);
		$this->add_control(
			'detail_icon_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-details__icon-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'detail_icon_border',
				'label'    => esc_html__( 'Border', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-details__icon-wrapper',
			)
		);
		$this->add_control(
			'detail_icon_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-details__icon-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-details__icon-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'detail_value_section',
			array(
				'label' => esc_html__( 'Detail Value', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'detail_value_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-details__quantity',
			)
		);
		$this->add_control(
			'detail_value_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-details__quantity' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'detail_container_section',
			array(
				'label' => esc_html__( 'Container', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'detail_container_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-details__item',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'detail_divider_section',
			array(
				'label'      => esc_html__( 'Divider', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'divider_row',
						),
					),
				),
			)
		);
		$this->add_control(
			'detail_divider_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-details_divider_row .masterstudy-single-course-details__item' => 'border-color: {{VALUE}}',
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

		\STM_LMS_Templates::show_lms_template(
			'components/course/details',
			array(
				'course' => $course_data['course'],
				'style'  => $settings['preset'] ?? 'default',
			)
		);
	}
}
