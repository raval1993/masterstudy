<?php
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

$course_statuses = STM_LMS_Helpers::get_course_statuses();

$this->start_controls_section(
	'style_card_status_section',
	array(
		'label' => esc_html__( 'Card: Status', 'masterstudy-lms-learning-management-system' ),
		'tab'   => Controls_Manager::TAB_STYLE,
	)
);

foreach ( $course_statuses as $status ) {
	if ( empty( $status['id'] ) ) {
		continue;
	}

	$raw_id      = (string) $status['id'];
	$status_id   = sanitize_key( $raw_id );
	$label       = ! empty( $status['label'] ) ? (string) $status['label'] : ucfirst( $status_id );
	$default_bg  = isset( $status['bg_color'] ) ? (string) $status['bg_color'] : '';
	$default_txt = isset( $status['text_color'] ) ? (string) $status['text_color'] : '';
	$closed_tab  = ! empty( $status['closed_tab'] );

	$is_featured   = ( 'featured' === $status_id );
	$base_class    = $is_featured ? '.ms_lms_courses_card_item_featured' : ( '.ms_lms_courses_card_item_status.' . $status_id );
	$span_selector = '{{WRAPPER}} ' . $base_class . ' span';
	$base_selector = '{{WRAPPER}} ' . $base_class;
	$before_sel    = '{{WRAPPER}} ' . $base_class . '::before';
	$after_sel     = '{{WRAPPER}} ' . $base_class . '::after';

	$this->add_control(
		"style_card_status_{$status_id}_heading",
		array(
			/* translators: %s: string Label */
			'label'     => $label,
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		)
	);

	$this->add_group_control(
		Group_Control_Typography::get_type(),
		array(
			'name'           => "style_card_status_{$status_id}_typography",
			'selector'       => $span_selector,
			'fields_options' => array(
				'typography' => array(
					/* translators: %s: string Label */
					'label' => sprintf( esc_html__( '%s Typography', 'masterstudy-lms-learning-management-system' ), $label ),
				),
			),
		)
	);

	$this->add_control(
		"style_card_status_{$status_id}_color",
		array(
			/* translators: %s: string Label */
			'label'     => sprintf( esc_html__( '%s Color', 'masterstudy-lms-learning-management-system' ), $label ),
			'type'      => Controls_Manager::COLOR,
			'default'   => $default_txt,
			'selectors' => array(
				$span_selector => 'color: {{VALUE}}',
			),
		)
	);

	$this->add_control(
		"style_card_status_{$status_id}_background_style_rectangle",
		array(
			/* translators: %s: string Label */
			'label'      => sprintf( esc_html__( '%s Background', 'masterstudy-lms-learning-management-system' ), $label ),
			'type'       => Controls_Manager::COLOR,
			'default'    => $default_bg,
			'selectors'  => array(
				$base_selector => 'background: {{VALUE}}',
			),
			'conditions' => $this->add_card_status_conditions( 'status_style_1' ),
		)
	);

	$this->add_control(
		"style_card_status_{$status_id}_background_right_style_flag",
		array(
			/* translators: %s: string Label */
			'label'      => sprintf( esc_html__( '%s Background', 'masterstudy-lms-learning-management-system' ), $label ),
			'type'       => Controls_Manager::COLOR,
			'default'    => $default_bg,
			'selectors'  => array(
				$base_selector => 'background: {{VALUE}}',
				$before_sel    => 'border-color: transparent {{VALUE}} transparent transparent',
				$after_sel     => 'border-color: transparent transparent {{VALUE}} transparent',
			),
			'conditions' => $this->add_card_status_conditions(
				'status_style_2',
				array(
					'name'     => 'status_position',
					'operator' => '===',
					'value'    => 'right',
				)
			),
		)
	);

	$this->add_control(
		"style_card_status_{$status_id}_background_left_style_flag",
		array(
			/* translators: %s: string Label */
			'label'      => sprintf( esc_html__( '%s Background', 'masterstudy-lms-learning-management-system' ), $label ),
			'type'       => Controls_Manager::COLOR,
			'default'    => $default_bg,
			'selectors'  => array(
				$base_selector => 'background: {{VALUE}}',
				$before_sel    => 'border-color: {{VALUE}} transparent transparent transparent',
				$after_sel     => 'border-color: transparent transparent transparent {{VALUE}}',
			),
			'conditions' => $this->add_card_status_conditions(
				'status_style_2',
				array(
					'name'     => 'status_position',
					'operator' => '===',
					'value'    => 'left',
				)
			),
		)
	);

	$this->add_control(
		"style_card_status_{$status_id}_background_left_style_arrow",
		array(
			/* translators: %s: string Label */
			'label'      => sprintf( esc_html__( '%s Background', 'masterstudy-lms-learning-management-system' ), $label ),
			'type'       => Controls_Manager::COLOR,
			'default'    => $default_bg,
			'selectors'  => array(
				$base_selector => 'background: {{VALUE}}',
				$before_sel    => 'border-left-color: {{VALUE}}',
			),
			'conditions' => $this->add_card_status_conditions(
				'status_style_3',
				array(
					'name'     => 'status_position',
					'operator' => '===',
					'value'    => 'left',
				)
			),
		)
	);

	$this->add_control(
		"style_card_status_{$status_id}_background_right_style_arrow",
		array(
			/* translators: %s: string Label */
			'label'      => sprintf( esc_html__( '%s Background', 'masterstudy-lms-learning-management-system' ), $label ),
			'type'       => Controls_Manager::COLOR,
			'default'    => $default_bg,
			'selectors'  => array(
				$base_selector => 'background: {{VALUE}}',
				$before_sel    => 'border-right-color: {{VALUE}}',
			),
			'conditions' => $this->add_card_status_conditions(
				'status_style_3',
				array(
					'name'     => 'status_position',
					'operator' => '===',
					'value'    => 'right',
				)
			),
		)
	);

	$this->add_control(
		"style_card_status_{$status_id}_divider",
		array(
			'type' => Controls_Manager::DIVIDER,
		)
	);
}

$this->end_controls_section();
