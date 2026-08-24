<?php
namespace StmLmsElementor\Widgets\Course;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MsLmsCourseExcerpt extends Widget_Base {

	public function get_name() {
		return 'ms_lms_course_excerpt';
	}

	public function get_title() {
		return esc_html__( 'Excerpt', 'masterstudy-lms-learning-management-system' );
	}

	public function get_icon() {
		return 'stmlms-course-short-text lms-course-icon';
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
					'default' => esc_html__( 'Full Text', 'masterstudy-lms-learning-management-system' ),
					'short'   => esc_html__( 'Shortened Text', 'masterstudy-lms-learning-management-system' ),
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'styles_section',
			array(
				'label' => esc_html__( 'Text', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'       => 'typography',
				'selector'   => '{{WRAPPER}} .masterstudy-single-course-excerpt',
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
			'color',
			array(
				'label'      => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-excerpt' => 'color: {{VALUE}}',
				),
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
		$this->add_responsive_control(
			'align',
			array(
				'label'      => esc_html__( 'Text alignment', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
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
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-excerpt' => 'text-align: {{VALUE}};',
				),
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
				'name'       => 'visible_typography',
				'selector'   => '{{WRAPPER}} .masterstudy-single-course-excerpt__visible,
				{{WRAPPER}} .masterstudy-single-course-excerpt__continue,
				{{WRAPPER}} .masterstudy-single-course-excerpt__hidden',
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'short',
						),
					),
				),
			)
		);
		$this->add_control(
			'visible_color',
			array(
				'label'      => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-excerpt__visible,
					{{WRAPPER}} .masterstudy-single-course-excerpt__continue,
					{{WRAPPER}} .masterstudy-single-course-excerpt__hidden' => 'color: {{VALUE}}',
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'short',
						),
					),
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'styles_button_section',
			array(
				'label'      => esc_html__( 'Show more', 'masterstudy-lms-learning-management-system' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'short',
						),
					),
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} span.masterstudy-single-course-excerpt__more',
			)
		);
		$this->add_control(
			'button_color',
			array(
				'label'      => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} span.masterstudy-single-course-excerpt__more' => 'color: {{VALUE}};',
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'short',
						),
					),
				),
			)
		);
		$this->add_control(
			'button_hover_color',
			array(
				'label'      => esc_html__( 'Hover Color', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} span.masterstudy-single-course-excerpt__more:hover' => 'color: {{VALUE}};',
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'preset',
							'operator' => '===',
							'value'    => 'short',
						),
					),
				),
			)
		);
		$this->end_controls_section();
	}

	public function get_script_depends() {
		return array( 'masterstudy-course-components-editor' );
	}

	protected function render() {
		global $masterstudy_single_page_course_id;

		$settings    = $this->get_settings_for_display();
		$course_id   = ! empty( $masterstudy_single_page_course_id ) ? $masterstudy_single_page_course_id : $settings['course'] ?? null;
		$course_data = masterstudy_get_elementor_course_data( intval( $course_id ) );

		if ( empty( $course_data ) || ! isset( $course_data['course'] ) ) {
			return;
		}

		if ( ! empty( $course_data['course']->excerpt ) || ! empty( $course_data['course']->udemy_headline ) ) {

			wp_enqueue_script( 'masterstudy-single-course-components', STM_LMS_URL . 'assets/js/components/course/main.js', array( 'jquery', 'jquery.cookie' ), MS_LMS_VERSION, true );

			\STM_LMS_Templates::show_lms_template(
				'components/course/excerpt',
				array(
					'course' => $course_data['course'],
					'long'   => 'short' === $settings['preset'] ? false : true,
				)
			);
		} elseif ( Plugin::$instance->editor->is_edit_mode() ) {
			masterstudy_get_elementor_content_banner( 'excerpt' );
		}
	}
}
