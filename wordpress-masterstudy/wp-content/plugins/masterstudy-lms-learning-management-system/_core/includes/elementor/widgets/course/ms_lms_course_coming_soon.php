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

class MsLmsCourseComingSoon extends Widget_Base {

	public function get_name() {
		return 'ms_lms_course_coming_soon';
	}

	public function get_title() {
		return esc_html__( 'Coming Soon', 'masterstudy-lms-learning-management-system' );
	}

	public function get_icon() {
		$icon = ! \STM_LMS_Helpers::is_pro_plus() ? 'lms-locked-course-icon' : 'lms-course-icon';

		return "stmlms-course-countdown $icon";
	}

	public function get_categories() {
		return array( 'stm_lms_course' );
	}

	public function get_style_depends() {
		return array(
			'masterstudy-single-course-components',
		);
	}

	public function get_script_depends() {
		return array(
			'masterstudy-course-coming-soon-editor',
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
		$courses     = \STM_LMS_Courses::get_all_courses_for_options();
		$is_pro_plus = \STM_LMS_Helpers::is_pro_plus();
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
		$this->add_control(
			'show_title',
			array(
				'label'        => esc_html__( 'Title', 'masterstudy-lms-learning-management-system' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'masterstudy-lms-learning-management-system' ),
				'label_off'    => esc_html__( 'Hide', 'masterstudy-lms-learning-management-system' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
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
				'selector' => '{{WRAPPER}} .masterstudy-lms-coming-soon-container',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border',
				'selector' => '{{WRAPPER}} .masterstudy-lms-coming-soon-container',
			)
		);
		$this->add_control(
			'container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-lms-coming-soon-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .masterstudy-lms-coming-soon-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'container_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-lms-coming-soon-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .coming-soon-heading',
			)
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .coming-soon-heading' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .coming-soon-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'timer_container_section',
			array(
				'label' => esc_html__( 'Timer Containers', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'timer_container_width',
			array(
				'label'      => esc_html__( 'Width', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-countdown .countSeconds, {{WRAPPER}} .masterstudy-countdown .countMinutes, {{WRAPPER}} .masterstudy-countdown .countHours, {{WRAPPER}} .masterstudy-countdown .countDays' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'timer_container_height',
			array(
				'label'      => esc_html__( 'Height', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-countdown .countSeconds, {{WRAPPER}} .masterstudy-countdown .countMinutes, {{WRAPPER}} .masterstudy-countdown .countHours, {{WRAPPER}} .masterstudy-countdown .countDays' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'timer_container_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-countdown .countSeconds, {{WRAPPER}} .masterstudy-countdown .countMinutes, {{WRAPPER}} .masterstudy-countdown .countHours, {{WRAPPER}} .masterstudy-countdown .countDays' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'timer_container_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-countdown .countSeconds, {{WRAPPER}} .masterstudy-countdown .countMinutes, {{WRAPPER}} .masterstudy-countdown .countHours, {{WRAPPER}} .masterstudy-countdown .countDays',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'timer_container_border',
				'selector' => '{{WRAPPER}} .masterstudy-countdown .countSeconds, {{WRAPPER}} .masterstudy-countdown .countMinutes, {{WRAPPER}} .masterstudy-countdown .countHours, {{WRAPPER}} .masterstudy-countdown .countDays',
			)
		);
		$this->add_control(
			'timer_container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-countdown .countSeconds, {{WRAPPER}} .masterstudy-countdown .countMinutes, {{WRAPPER}} .masterstudy-countdown .countHours, {{WRAPPER}} .masterstudy-countdown .countDays' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'number_section',
			array(
				'label' => esc_html__( 'Numbers', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'number_typography',
				'selector' => '{{WRAPPER}} .masterstudy-countdown .digit',
			)
		);
		$this->add_control(
			'number_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-countdown .digit' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'number_between_width',
			array(
				'label'      => esc_html__( 'Between spacing', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-countdown .countSeconds .position, {{WRAPPER}} .masterstudy-countdown .countMinutes .position, {{WRAPPER}} .masterstudy-countdown .countHours .position, {{WRAPPER}} .masterstudy-countdown .countDays .position' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'timer_text_section',
			array(
				'label' => esc_html__( 'Timer text', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'timer_text_typography',
				'selector' => '{{WRAPPER}} .countdown_label',
			)
		);
		$this->add_control(
			'timer_text_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .countdown_label' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'notify_button_section',
			array(
				'label' => esc_html__( 'Notification Button', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'notify_button_width',
			array(
				'label'      => esc_html__( 'Width', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .coming-soon-notify-alert, {{WRAPPER}} .coming-soon-notify-alert.notify-me' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'notify_button_height',
			array(
				'label'      => esc_html__( 'Height', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .coming-soon-notify-alert, {{WRAPPER}} .coming-soon-notify-alert.notify-me' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'notify_button_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .coming-soon-notify-alert, {{WRAPPER}} .coming-soon-notify-alert.notify-me',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'notify_button_border',
				'selector' => '{{WRAPPER}} .coming-soon-notify-alert, {{WRAPPER}} .coming-soon-notify-alert.notify-me',
			)
		);
		$this->add_control(
			'notify_button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .coming-soon-notify-alert, {{WRAPPER}} .coming-soon-notify-alert.notify-me' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'notify_button_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .coming-soon-notify-alert img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'modal_section',
			array(
				'label' => esc_html__( 'Notification Modal', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'modal_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-coming-soon-modal__wrapper',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'modal_border',
				'selector' => '{{WRAPPER}} .masterstudy-coming-soon-modal__wrapper',
			)
		);
		$this->add_control(
			'modal_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'modal_shadow',
				'selector' => '{{WRAPPER}} .masterstudy-coming-soon-modal__wrapper',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'modal_title_section',
			array(
				'label' => esc_html__( 'Notification Modal Title', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'modal_title_typography',
				'selector' => '{{WRAPPER}} .masterstudy-coming-soon-modal__title',
			)
		);
		$this->add_control(
			'modal_title_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'modal_title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'modal_description_section',
			array(
				'label' => esc_html__( 'Notification Modal Description', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'modal_description_typography',
				'selector' => '{{WRAPPER}} .masterstudy-coming-soon-modal__description',
			)
		);
		$this->add_control(
			'modal_description_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__description' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_responsive_control(
			'modal_description_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'modal_image_section',
			array(
				'label' => esc_html__( 'Notification Modal Image', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'modal_image_width',
			array(
				'label'      => esc_html__( 'Width', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__image img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'modal_image_height',
			array(
				'label'      => esc_html__( 'Height', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__image img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'modal_image_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'modal_close_section',
			array(
				'label' => esc_html__( 'Notification Modal Close Button', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'modal_close_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__close::after' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .masterstudy-coming-soon-modal__close::after' => 'font-size: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .masterstudy-coming-soon-modal__close' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .masterstudy-coming-soon-modal__close' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'modal_close_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-coming-soon-modal__close',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'modal_close_border',
				'selector' => '{{WRAPPER}} .masterstudy-coming-soon-modal__close',
			)
		);
		$this->add_control(
			'modal_close_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'modal_notify_button_section',
			array(
				'label' => esc_html__( 'Notification Modal Button', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'modal_notify_button_typography',
				'selector' => '{{WRAPPER}} .masterstudy-coming-soon-modal__cta a.masterstudy-button .masterstudy-button__title',
			)
		);
		$this->add_responsive_control(
			'modal_notify_button_height',
			array(
				'label'      => esc_html__( 'Height', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__cta a.masterstudy-button' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'modal_notify_button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__cta a.masterstudy-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'modal_notify_button_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__cta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->start_controls_tabs(
			'modal_notify_button_tab'
		);
		$this->start_controls_tab(
			'modal_notify_button_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'modal_notify_button_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__cta a.masterstudy-button .masterstudy-button__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'modal_notify_button_normal_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-coming-soon-modal__cta a.masterstudy-button',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'modal_notify_button_normal_border',
				'selector' => '{{WRAPPER}} .masterstudy-coming-soon-modal__cta a.masterstudy-button',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'modal_notify_button_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'modal_notify_button_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-coming-soon-modal__cta a.masterstudy-button:hover .masterstudy-button__title' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'modal_notify_button_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-coming-soon-modal__cta a.masterstudy-button:hover',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'modal_notify_button_border_hover',
				'selector' => '{{WRAPPER}} .masterstudy-coming-soon-modal__cta a.masterstudy-button:hover',
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

		if ( ! is_ms_lms_addon_enabled( 'coming_soon' ) ) {
			if ( $is_edit_mode ) {
				masterstudy_get_elementor_unlock_banner( 'coming_soon' );
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
			'global/coming_soon',
			array(
				'course_id'  => $course_data['course']->id,
				'mode'       => 'course',
				'show_title' => $settings['show_title'] ?? true,
			),
		);
	}
}
