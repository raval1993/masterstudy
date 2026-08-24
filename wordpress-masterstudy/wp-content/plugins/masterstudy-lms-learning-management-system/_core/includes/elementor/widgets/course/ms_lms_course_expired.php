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

class MsLmsCourseExpired extends Widget_Base {

	public function get_name() {
		return 'ms_lms_course_expired';
	}

	public function get_title() {
		return esc_html__( 'Expiration', 'masterstudy-lms-learning-management-system' );
	}

	public function get_icon() {
		$icon = ! \STM_LMS_Helpers::is_pro() ? 'lms-locked-course-icon' : 'lms-course-icon';

		return "stmlms-course-calendar $icon";
	}

	public function get_categories() {
		return array( 'stm_lms_course' );
	}

	public function get_style_depends() {
		return array(
			'masterstudy-single-course-components',
		);
	}

	protected function get_upsale_data(): array {
		$is_pro_plus = \STM_LMS_Helpers::is_pro_plus();
		$is_pro      = \STM_LMS_Helpers::is_pro();
		$only_pro    = $is_pro && ! $is_pro_plus;
		$link        = $only_pro ? 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=elementorwidget' : admin_url( 'admin.php?page=stm-lms-go-pro&source=elementorwidget' );
		$link_text   = $only_pro ? esc_html__( 'Upgrade to PRO PLUS', 'masterstudy-lms-learning-management-system' ) : esc_html__( 'Upgrade to PRO', 'masterstudy-lms-learning-management-system' );
		$icon_url    = $only_pro ? STM_LMS_URL . 'assets/img/pro-features/pro_plus.svg' : STM_LMS_URL . 'assets/img/pro-features/unlock-pro-logo.svg';
		$title       = $only_pro ? esc_html__( 'Get Access to Exclusive Widgets with MasterStudy PRO PLUS', 'masterstudy-lms-learning-management-system' ) : esc_html__( 'Get Access to Exclusive Widgets with MasterStudy PRO', 'masterstudy-lms-learning-management-system' );

		return array(
			'condition'    => ! $is_pro,
			'image'        => esc_url( $icon_url ),
			'title'        => $title,
			'upgrade_url'  => $link,
			'upgrade_text' => $link_text,
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

		if ( ! \STM_LMS_Helpers::is_pro() ) {
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
			'text_section',
			array(
				'label' => esc_html__( 'Label', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-expired',
			)
		);
		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-expired' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'value_section',
			array(
				'label' => esc_html__( 'Value', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'value_typography',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-expired strong',
			)
		);
		$this->add_control(
			'value_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-single-course-expired strong' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'container_section',
			array(
				'label' => esc_html__( 'Expiration Box', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'container_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-single-course-expired',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border',
				'selector' => '{{WRAPPER}} .masterstudy-single-course-expired',
			)
		);
		$this->add_control(
			'container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-single-course-expired' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$is_edit_mode = Plugin::$instance->editor->is_edit_mode();

		if ( ! \STM_LMS_Helpers::is_pro() ) {
			if ( $is_edit_mode ) {
				masterstudy_get_elementor_unlock_banner();
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

		wp_enqueue_script( 'masterstudy-single-course-components', STM_LMS_URL . 'assets/js/components/course/main.js', array( 'jquery', 'jquery.cookie' ), MS_LMS_VERSION, true );

		\STM_LMS_Templates::show_lms_template(
			'components/course/expired',
			array(
				'course'         => $course_data['course'],
				'user_id'        => $course_data['current_user_id'],
				'is_coming_soon' => $course_data['is_coming_soon'],
			)
		);
	}
}
