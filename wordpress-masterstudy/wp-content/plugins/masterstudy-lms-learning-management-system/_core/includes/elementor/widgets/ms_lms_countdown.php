<?php
namespace StmLmsElementor\Widgets;

use DateTime;
use DateTimeZone;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Plugin;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MsLmsCountdown extends Widget_Base {

	public function get_name() {
		return 'ms_lms_countdown';
	}

	public function get_title() {
		return esc_html__( 'Countdown', 'masterstudy-lms-learning-management-system' );
	}

	public function get_icon() {
		return 'stmlms-countdown-widget lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section',
			array(
				'label' => esc_html__( 'Content', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'preset',
			array(
				'label'   => esc_html__( 'Preset', 'masterstudy-lms-learning-management-system' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => esc_html__( 'Standard', 'masterstudy-lms-learning-management-system' ),
					'gray'    => esc_html__( 'Gray Card', 'masterstudy-lms-learning-management-system' ),
				),
			)
		);
		$this->add_control(
			'datepicker',
			array(
				'label'          => __( 'Date', 'masterstudy-lms-learning-management-system' ),
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => array(
					'enableTime' => false,
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
	}

	public function get_style_depends() {
		return array( 'masterstudy-countdown' );
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$site_tz = wp_timezone();
		try {
			$dt    = new DateTime( $settings['datepicker'], $site_tz );
			$ts_ms = $dt->setTimezone( new DateTimeZone( 'UTC' ) )->getTimestamp() * 1000;
		} catch ( Exception $e ) {
			$ts_ms = 0;
		}

		\STM_LMS_Templates::show_lms_template(
			'components/countdown',
			array(
				'style'      => $settings['preset'] ?? 'default',
				'start_time' => $ts_ms,
				'id'         => wp_rand( 0, 999999 ),
			)
		);
	}
}
