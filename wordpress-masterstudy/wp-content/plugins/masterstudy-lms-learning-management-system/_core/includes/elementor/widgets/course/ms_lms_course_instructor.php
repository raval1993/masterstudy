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

class MsLmsCourseInstructor extends Widget_Base {

	public function get_name() {
		return 'ms_lms_course_instructor';
	}

	public function get_title() {
		return esc_html__( 'Instructor', 'masterstudy-lms-learning-management-system' );
	}

	public function get_icon() {
		return 'stmlms-course-teacher lms-course-icon';
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
					'default'       => esc_html__( 'Standard', 'masterstudy-lms-learning-management-system' ),
					'without_title' => esc_html__( 'Compact', 'masterstudy-lms-learning-management-system' ),
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'title_section',
			array(
				'label' => esc_html__( 'Instructor Label', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-instructor__title',
			)
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-instructor__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'instructor_section',
			array(
				'label' => esc_html__( 'Instructor Name', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'instructor_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-instructor__name, {{WRAPPER}} .masterstudy-single-course-instructor__co-instructor',
			)
		);
		$this->add_control(
			'instructor_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-instructor__name, {{WRAPPER}} .masterstudy-single-course-instructor__co-instructor' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'instructor_color_hover',
			array(
				'label'     => esc_html__( 'Hover Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-instructor__name:hover, {{WRAPPER}} .masterstudy-single-course-instructor__co-instructor:hover' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'instructor_avatar_section',
			array(
				'label' => esc_html__( 'Instructor Photo', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'width',
			array(
				'label'      => esc_html__( 'Width', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-instructor__avatar img' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'height',
			array(
				'label'      => esc_html__( 'Height', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-instructor__avatar img' => 'min-height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .masterstudy-single-course-instructor__avatar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			'components/course/instructor',
			array(
				'instructor'    => $course_data['instructor'],
				'course'        => $course_data['course'],
				'without_title' => 'without_title' === $settings['preset'] ? true : false,
			)
		);
	}
}
