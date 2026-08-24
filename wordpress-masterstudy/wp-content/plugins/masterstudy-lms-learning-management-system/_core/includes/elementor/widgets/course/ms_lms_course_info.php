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

class MsLmsCourseInfo extends Widget_Base {

	public function get_name() {
		return 'ms_lms_course_info';
	}

	public function get_title() {
		return esc_html__( 'Info', 'masterstudy-lms-learning-management-system' );
	}

	public function get_icon() {
		return 'stmlms-course-info lms-course-icon';
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
				'default'            => 'basic_info',
				'frontend_available' => true,
				'options'            => array(
					'basic_info'        => esc_html__( 'Basic info', 'masterstudy-lms-learning-management-system' ),
					'requirements_info' => esc_html__( 'Course requirements', 'masterstudy-lms-learning-management-system' ),
					'intended_audience' => esc_html__( 'Intended audience', 'masterstudy-lms-learning-management-system' ),
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'styles_section',
			array(
				'label' => esc_html__( 'Title', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-info__title',
			)
		);
		$this->add_control(
			'color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-info__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'border',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-info__title',
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

		$info = array(
			'basic_info'        => $course_data['course']->basic_info,
			'requirements_info' => $course_data['course']->requirements,
			'intended_audience' => $course_data['course']->intended_audience,
		);

		if ( Plugin::$instance->editor->is_edit_mode() ) {
			foreach ( $info as $type => $value ) {
				if ( empty( $value ) && $type === $settings['preset'] ) {
					masterstudy_get_elementor_content_banner( $type );
				}
			}
		}

		$titles = array(
			'basic_info'        => esc_html__( 'Basic info', 'masterstudy-lms-learning-management-system' ),
			'requirements_info' => esc_html__( 'Course requirements', 'masterstudy-lms-learning-management-system' ),
			'intended_audience' => esc_html__( 'Intended audience', 'masterstudy-lms-learning-management-system' ),
		);

		\STM_LMS_Templates::show_lms_template(
			'components/course/info',
			array(
				'course_id' => $course_data['course']->id,
				'content'   => $info[ $settings['preset'] ],
				'title'     => $titles[ $settings['preset'] ],
			),
		);
	}
}
