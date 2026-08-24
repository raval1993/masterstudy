<?php
namespace StmLmsElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * MasterStudy Mega Menu Widget
 */
class MsLmsMegaMenu extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 */
	public function get_name() {
		return 'ms_lms_mega_menu';
	}

	/**
	 * Retrieve the widget title.
	 */
	public function get_title() {
		return esc_html__( 'Mega Menu', 'masterstudy-lms-learning-management-system' );
	}

	/**
	 * Retrieve the widget icon.
	 */
	public function get_icon(): string {
		return 'stmlms-mega-menu-widget lms-icon';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 */
	public function get_categories() {
		return array( 'stm_lms' );
	}

	/**
	 * Retrieve the widget style dependencies.
	 */
	public function get_style_depends() {
		return array( 'masterstudy-mega-menu' );
	}

	/**
	 * Retrieve the widget script dependencies.
	 */
	public function get_script_depends() {
		return array( 'masterstudy-mega-menu-editor' );
	}

	/**
	 * Get available WordPress menus
	 */
	protected function get_menu_options() {
		$menus   = wp_get_nav_menus();
		$options = array( '' => esc_html__( 'Select Menu', 'masterstudy-lms-learning-management-system' ) );
		foreach ( $menus as $menu ) {
			$options[ $menu->term_id ] = $menu->name;
		}
		return $options;
	}

	/**
	 * Get nested menu items options for Elementor controls.
	 *
	 * Used for controls where we need to target a specific nested menu item ID.
	 * Top-level menu items are excluded because images are not supported for them.
	 *
	 * Note: If $menu_id is provided, only items from that menu are returned.
	 *
	 * @return array
	 */
	protected function get_menu_items_options( $menu_id = 0 ) {
		static $options_cache = array();
		$menu_id              = absint( $menu_id );
		$cache_key            = $menu_id ? $menu_id : 'all';

		if ( isset( $options_cache[ $cache_key ] ) ) {
			return $options_cache[ $cache_key ];
		}

		$options = array(
			'' => esc_html__( 'Select Menu Item', 'masterstudy-lms-learning-management-system' ),
		);

		$menus = array();
		if ( $menu_id ) {
			$menu = wp_get_nav_menu_object( $menu_id );
			if ( $menu && ! is_wp_error( $menu ) ) {
				$menus = array( $menu );
			}
		} else {
			$menus = wp_get_nav_menus();
		}

		$show_menu_prefix = count( $menus ) > 1;

		foreach ( $menus as $menu ) {
			$items = wp_get_nav_menu_items( $menu->term_id );
			if ( empty( $items ) ) {
				continue;
			}

			$prefix = $show_menu_prefix ? '[' . $menu->name . '] ' : '';

			foreach ( $items as $item ) {
				if ( 0 === (int) $item->menu_item_parent ) {
					continue;
				}

				$options[ (string) $item->ID ] = $prefix . $item->title;
			}
		}

		$options_cache[ $cache_key ] = $options;
		return $options_cache[ $cache_key ];
	}

	/**
	 * Get only top-level menu items options for Elementor controls.
	 *
	 * Used for controls where we target a top-level menu item.
	 * If $menu_id is provided, only items from that menu are returned.
	 *
	 * @return array
	 */
	protected function get_top_level_menu_items_options( $menu_id = 0 ) {
		static $options_cache = array();
		$menu_id              = absint( $menu_id );
		$cache_key            = $menu_id ? $menu_id : 'all';

		if ( isset( $options_cache[ $cache_key ] ) ) {
			return $options_cache[ $cache_key ];
		}

		$options = array(
			'' => esc_html__( 'Select Menu Item', 'masterstudy-lms-learning-management-system' ),
		);

		$menus = array();
		if ( $menu_id ) {
			$menu = wp_get_nav_menu_object( $menu_id );
			if ( $menu && ! is_wp_error( $menu ) ) {
				$menus = array( $menu );
			}
		} else {
			$menus = wp_get_nav_menus();
		}

		foreach ( $menus as $menu ) {
			$items = wp_get_nav_menu_items( $menu->term_id );
			if ( empty( $items ) ) {
				continue;
			}

			// Detect items that have children (widget only renders mega for top-level items with children).
			$has_children = array();
			foreach ( $items as $item ) {
				$parent_id = (int) $item->menu_item_parent;
				if ( 0 !== $parent_id ) {
					$has_children[ $parent_id ] = true;
				}
			}

			foreach ( $items as $item ) {
				if ( 0 !== (int) $item->menu_item_parent ) {
					continue;
				}

				// Only items with children can open mega dropdown.
				if ( ! isset( $has_children[ (int) $item->ID ] ) ) {
					continue;
				}

				$options[ (string) $item->ID ] = $item->title;
			}
		}

		$options_cache[ $cache_key ] = $options;
		return $options_cache[ $cache_key ];
	}

	/**
	 * Get all top-level menu items options for Elementor controls.
	 *
	 * Used for controls where we need any first-level menu item (with or without children).
	 * If $menu_id is provided, only items from that menu are returned.
	 *
	 * @return array
	 */
	protected function get_all_top_level_menu_items_options( $menu_id = 0 ) {
		static $options_cache = array();
		$menu_id              = absint( $menu_id );
		$cache_key            = $menu_id ? $menu_id : 'all';

		if ( isset( $options_cache[ $cache_key ] ) ) {
			return $options_cache[ $cache_key ];
		}

		$options = array(
			'' => esc_html__( 'Select Menu Item', 'masterstudy-lms-learning-management-system' ),
		);

		$menus = array();
		if ( $menu_id ) {
			$menu = wp_get_nav_menu_object( $menu_id );
			if ( $menu && ! is_wp_error( $menu ) ) {
				$menus = array( $menu );
			}
		} else {
			$menus = wp_get_nav_menus();
		}

		$show_menu_prefix = count( $menus ) > 1;

		foreach ( $menus as $menu ) {
			$items = wp_get_nav_menu_items( $menu->term_id );
			if ( empty( $items ) ) {
				continue;
			}

			$prefix = $show_menu_prefix ? '[' . $menu->name . '] ' : '';

			foreach ( $items as $item ) {
				if ( 0 !== (int) $item->menu_item_parent ) {
					continue;
				}
				$options[ (string) $item->ID ] = $prefix . $item->title;
			}
		}

		$options_cache[ $cache_key ] = $options;
		return $options_cache[ $cache_key ];
	}

	/**
	 * Register the widget controls.
	 */
	protected function register_controls() {
		// Content Tab - Menu Settings
		$this->start_controls_section(
			'section_menu_settings',
			array(
				'label' => esc_html__( 'Menu Settings', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'menu',
			array(
				'label'       => esc_html__( 'Menu', 'masterstudy-lms-learning-management-system' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $this->get_menu_options(),
				'default'     => '',
				'description' => esc_html__( 'Mega menu will automatically display for all top-level items with children.', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$desktop_nested_view_repeater = new Repeater();

		$desktop_nested_view_repeater->add_control(
			'menu_item_id',
			array(
				'label'       => esc_html__( 'Select navigation item', 'masterstudy-lms-learning-management-system' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->get_all_top_level_menu_items_options(),
				'default'     => '',
				'label_block' => true,
			)
		);

		$desktop_nested_view_repeater->add_control(
			'desktop_nested_view',
			array(
				'label'   => esc_html__( 'Dropdown Style', 'masterstudy-lms-learning-management-system' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'headings' => esc_html__( 'Mega Menu', 'masterstudy-lms-learning-management-system' ),
					'cascade'  => esc_html__( 'Dropdown', 'masterstudy-lms-learning-management-system' ),
				),
				'default' => 'cascade',
			)
		);

		$desktop_nested_view_repeater->add_control(
			'desktop_dropdown_side',
			array(
				'label'   => esc_html__( 'Dropdown Open Side', 'masterstudy-lms-learning-management-system' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'auto'  => esc_html__( 'Auto', 'masterstudy-lms-learning-management-system' ),
					'right' => esc_html__( 'Right', 'masterstudy-lms-learning-management-system' ),
					'left'  => esc_html__( 'Left', 'masterstudy-lms-learning-management-system' ),
				),
				'default' => 'auto',
			)
		);

		$desktop_nested_view_repeater->add_control(
			'desktop_center_content',
			array(
				'label'        => esc_html__( 'Center Content', 'masterstudy-lms-learning-management-system' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'masterstudy-lms-learning-management-system' ),
				'label_off'    => esc_html__( 'No', 'masterstudy-lms-learning-management-system' ),
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'desktop_nested_views',
			array(
				'label'         => esc_html__( 'Menu Item Dropdown Styles', 'masterstudy-lms-learning-management-system' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $desktop_nested_view_repeater->get_controls(),
				'default'       => array(),
				'prevent_empty' => false,
				'button_text'   => esc_html__( 'Select Menu Item', 'masterstudy-lms-learning-management-system' ),
				'condition'     => array(
					'menu!' => '',
				),
			)
		);

		$this->end_controls_section();

		// Content Tab - Menu Item Images
		$this->start_controls_section(
			'section_menu_item_images',
			array(
				'label'     => esc_html__( 'Menu Item Images', 'masterstudy-lms-learning-management-system' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'menu!' => '',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			array(
				'label'       => esc_html__( 'Image', 'masterstudy-lms-learning-management-system' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'image' ),
				'default'     => array(),
			)
		);

		$repeater->add_control(
			'menu_item_id',
			array(
				'label'       => esc_html__( 'Menu Item', 'masterstudy-lms-learning-management-system' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->get_menu_items_options(),
				'default'     => '',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'menu_item_image_position',
			array(
				'label'   => esc_html__( 'Image Layout', 'masterstudy-lms-learning-management-system' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'left' => esc_html__( 'Inline', 'masterstudy-lms-learning-management-system' ),
					'top'  => esc_html__( 'Large', 'masterstudy-lms-learning-management-system' ),
				),
				'default' => 'left',
			)
		);

		$this->add_control(
			'menu_item_images',
			array(
				'label'         => esc_html__( 'Images', 'masterstudy-lms-learning-management-system' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => array(),
				'prevent_empty' => false,
			)
		);

		$this->add_control(
			'menu_item_images_note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => '<div class="elementor-control-field-description">' .
					esc_html__( 'Only one image can be assigned per menu item. If the same menu item is selected multiple times, duplicates will be ignored (the first image will be used).', 'masterstudy-lms-learning-management-system' ) .
			'</div>',
			)
		);

		$large_image_size_repeater = new Repeater();

		$large_image_size_repeater->add_control(
			'menu_item_id',
			array(
				'label'       => esc_html__( 'Top Level Menu Item', 'masterstudy-lms-learning-management-system' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->get_all_top_level_menu_items_options(),
				'default'     => '',
				'label_block' => true,
			)
		);

		$large_image_size_repeater->add_control(
			'large_image_height',
			array(
				'label'       => esc_html__( 'Large Image Height (px)', 'masterstudy-lms-learning-management-system' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 40,
				'max'         => 500,
				'step'        => 1,
				'default'     => 260,
				'description' => esc_html__( 'Value is set in pixels.', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$this->add_control(
			'top_level_large_image_sizes',
			array(
				'label'         => esc_html__( 'Large Image Sizes By Top Level Item', 'masterstudy-lms-learning-management-system' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $large_image_size_repeater->get_controls(),
				'default'       => array(),
				'prevent_empty' => false,
				'button_text'   => esc_html__( 'Select Menu Item', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$this->add_control(
			'top_level_large_image_sizes_note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => '<div class="elementor-control-field-description">' .
					esc_html__( 'These overrides apply to large images inside the selected top-level dropdown only. All values are in pixels. If the same menu item is selected multiple times, the first size will be used.', 'masterstudy-lms-learning-management-system' ) .
				'</div>',
			)
		);

		$this->end_controls_section();
		// Content Tab - Full Width
		$this->start_controls_section(
			'section_full_width',
			array(
				'label'     => esc_html__( 'Full-Width Menu', 'masterstudy-lms-learning-management-system' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'menu!' => '',
				),
			)
		);

		$this->add_control(
			'full_width_triggers',
			array(
				'label'       => esc_html__( 'Full-Width Menu Items', 'masterstudy-lms-learning-management-system' ),
				'type'        => Controls_Manager::SELECT2,
				// Same reasoning as above: settings may be null during control registration.
				'options'     => $this->get_top_level_menu_items_options(),
				'multiple'    => true,
				'label_block' => true,
				'default'     => array(),
				'description' => esc_html__( 'Choose which top-level menu items use a full-width dropdown.', 'masterstudy-lms-learning-management-system' ),
			)
		);

		$this->end_controls_section();

		// Style Tab - Top Level Items (Triggers)
		$this->start_controls_section(
			'section_style_top_level_items',
			array(
				'label' => esc_html__( 'Top Level Items', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'top_item_typography',
				'label'    => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-mega-menu__trigger, {{WRAPPER}} .masterstudy-mega-menu__mobile-trigger',
			)
		);

		$this->add_control(
			'top_item_color',
			array(
				'label'     => esc_html__( 'Text Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__trigger' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'top_item_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#007bff',
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__trigger:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - Menu Items
		$this->start_controls_section(
			'section_style_items',
			array(
				'label' => esc_html__( 'Dropdown Items', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_typography',
				'label'    => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-mega-menu__item a, {{WRAPPER}} .masterstudy-mega-menu__mobile-list a',
			)
		);

		$this->add_control(
			'item_color',
			array(
				'label'     => esc_html__( 'Text Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__item a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__item--image-top .masterstudy-mega-menu__item-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#007bff',
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__item a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__item--image-top:hover .masterstudy-mega-menu__item-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_hover_bg',
			array(
				'label'     => esc_html__( 'Hover Background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__item:not(.masterstudy-mega-menu__item--image-top):hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__submenu .masterstudy-mega-menu__item:not(.masterstudy-mega-menu__item--image-top):hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-list li:not(.masterstudy-mega-menu__mobile-item--image-top):hover' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();

		// Style Tab - Section Headings
		$this->start_controls_section(
			'section_style_headings',
			array(
				'label' => esc_html__( 'Dropdown Headings', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'label'    => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-mega-menu__heading, {{WRAPPER}} .masterstudy-mega-menu__mobile-section-toggle, {{WRAPPER}} .masterstudy-mega-menu__mobile-section-link',
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#666666',
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__heading' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__heading--link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - Mobile Items
		$this->start_controls_section(
			'section_style_mobile_items',
			array(
				'label' => esc_html__( 'Mobile Items', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'mobile_item_color',
			array(
				'label'     => esc_html__( 'Text Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-trigger' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-direct-link' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-section-toggle' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-section-link' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-item-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mobile_item_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#007bff',
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-trigger:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-direct-link:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-section-toggle:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-section.is-open > .masterstudy-mega-menu__mobile-section-toggle' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-item--active > .masterstudy-mega-menu__mobile-direct-link' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-item--active > .masterstudy-mega-menu__mobile-section-toggle' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-trigger:hover .masterstudy-mega-menu__mobile-item-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-direct-link:hover .masterstudy-mega-menu__mobile-item-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-section-toggle:hover .masterstudy-mega-menu__mobile-item-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-section.is-open > .masterstudy-mega-menu__mobile-section-toggle .masterstudy-mega-menu__mobile-item-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-item--active > .masterstudy-mega-menu__mobile-direct-link .masterstudy-mega-menu__mobile-item-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-item--active > .masterstudy-mega-menu__mobile-section-toggle .masterstudy-mega-menu__mobile-item-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - Inline Images
		$this->start_controls_section(
			'section_style_item_images_inline',
			array(
				'label' => esc_html__( 'Inline Images', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_image_border',
				'label'    => esc_html__( 'Border', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-mega-menu__item--with-image:not(.masterstudy-mega-menu__item--image-top) .masterstudy-mega-menu__item-image, {{WRAPPER}} .masterstudy-mega-menu__mobile-item--with-image:not(.masterstudy-mega-menu__mobile-item--image-top) .masterstudy-mega-menu__item-image',
			)
		);

		$this->add_responsive_control(
			'item_image_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-mega-menu__item--with-image:not(.masterstudy-mega-menu__item--image-top) .masterstudy-mega-menu__item-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-mega-menu__item--with-image:not(.masterstudy-mega-menu__item--image-top) .masterstudy-mega-menu__item-image-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-item--with-image:not(.masterstudy-mega-menu__mobile-item--image-top) .masterstudy-mega-menu__item-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-item--with-image:not(.masterstudy-mega-menu__mobile-item--image-top) .masterstudy-mega-menu__item-image-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_image_hover_border_color',
			array(
				'label'     => esc_html__( 'Hover Border Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__item--with-image:not(.masterstudy-mega-menu__item--image-top):hover .masterstudy-mega-menu__item-image' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-item--with-image:not(.masterstudy-mega-menu__mobile-item--image-top):hover .masterstudy-mega-menu__item-image' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - Large Images
		$this->start_controls_section(
			'section_style_item_images_large',
			array(
				'label' => esc_html__( 'Large Images', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_image_large_border',
				'label'    => esc_html__( 'Border', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-mega-menu__item--image-top .masterstudy-mega-menu__item-image, {{WRAPPER}} .masterstudy-mega-menu__heading--image-top .masterstudy-mega-menu__item-image, {{WRAPPER}} .masterstudy-mega-menu__mobile-item--image-top .masterstudy-mega-menu__item-image',
			)
		);

		$this->add_responsive_control(
			'item_image_large_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-mega-menu__item--image-top .masterstudy-mega-menu__item-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-mega-menu__item--image-top .masterstudy-mega-menu__item-image-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-mega-menu__heading--image-top .masterstudy-mega-menu__item-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-mega-menu__heading--image-top .masterstudy-mega-menu__item-image-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-item--image-top .masterstudy-mega-menu__item-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-item--image-top .masterstudy-mega-menu__item-image-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_image_large_hover_border_color',
			array(
				'label'     => esc_html__( 'Hover Border Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__item--image-top:hover .masterstudy-mega-menu__item-image' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__item--image-top.masterstudy-mega-menu__item--active .masterstudy-mega-menu__item-image' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__heading--image-top:hover .masterstudy-mega-menu__item-image' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-item--image-top:hover .masterstudy-mega-menu__item-image' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-item--image-top.masterstudy-mega-menu__mobile-item--active .masterstudy-mega-menu__item-image' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_image_large_height',
			array(
				'label'      => esc_html__( 'Image Height', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 40,
						'max' => 500,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 260,
				),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-mega-menu__item--image-top .masterstudy-mega-menu__item-image' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-mega-menu__heading--image-top .masterstudy-mega-menu__item-image' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-item--image-top .masterstudy-mega-menu__item-image' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();

		// Style Tab - Mobile Panel
		$this->start_controls_section(
			'section_style_mobile_panel',
			array(
				'label' => esc_html__( 'Mobile Panel', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'panel_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu--mobile' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-trigger' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-panel' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-header' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-content' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-list' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .is-mobile-nav-open' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'panel_padding',
			array(
				'label'      => esc_html__( 'Dropdowns Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'mobile_toggle_heading',
			array(
				'label'     => esc_html__( 'Toggle Button', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'mobile_toggle_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-nav-toggle' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.is-mobile-nav-open .masterstudy-mega-menu__mobile-nav-toggle' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'mobile_toggle_border',
				'label'    => esc_html__( 'Border', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-mega-menu__mobile-nav-toggle, {{WRAPPER}} .masterstudy-mega-menu__mobile-nav-toggle:hover, {{WRAPPER}} .masterstudy-mega-menu__mobile-nav-toggle:focus, {{WRAPPER}}.is-mobile-nav-open .masterstudy-mega-menu__mobile-nav-toggle, {{WRAPPER}}.is-mobile-nav-open .masterstudy-mega-menu__mobile-nav-toggle:hover, {{WRAPPER}}.is-mobile-nav-open .masterstudy-mega-menu__mobile-nav-toggle:focus',
			)
		);

		$this->add_responsive_control(
			'mobile_toggle_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-nav-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}.is-mobile-nav-open .masterstudy-mega-menu__mobile-nav-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'mobile_toggle_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-nav-toggle' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-nav-toggle-icon::before' => 'color: {{VALUE}};',
					'{{WRAPPER}}.is-mobile-nav-open .masterstudy-mega-menu__mobile-nav-toggle' => 'color: {{VALUE}};',
					'{{WRAPPER}}.is-mobile-nav-open .masterstudy-mega-menu__mobile-nav-toggle-icon::before' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mobile_toggle_hover_icon_color',
			array(
				'label'     => esc_html__( 'Hover Icon Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-nav-toggle:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-nav-toggle:hover .masterstudy-mega-menu__mobile-nav-toggle-icon::before' => 'color: {{VALUE}};',
					'{{WRAPPER}}.is-mobile-nav-open .masterstudy-mega-menu__mobile-nav-toggle:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}}.is-mobile-nav-open .masterstudy-mega-menu__mobile-nav-toggle:hover .masterstudy-mega-menu__mobile-nav-toggle-icon::before' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mobile_toggle_hover_bg',
			array(
				'label'     => esc_html__( 'Hover Background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-nav-toggle:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.is-mobile-nav-open .masterstudy-mega-menu__mobile-nav-toggle:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mobile_toggle_hover_border_color',
			array(
				'label'     => esc_html__( 'Hover Border Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__mobile-nav-toggle:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}}.is-mobile-nav-open .masterstudy-mega-menu__mobile-nav-toggle:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - Desktop Panel
		$this->start_controls_section(
			'section_style_desktop_panel',
			array(
				'label' => esc_html__( 'Desktop Panel', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'desktop_panel_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-mega-menu__panel' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .masterstudy-mega-menu__submenu' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'desktop_panel_shadow',
				'label'    => esc_html__( 'Box Shadow', 'masterstudy-lms-learning-management-system' ),
				'selector' => '{{WRAPPER}} .masterstudy-mega-menu__panel, {{WRAPPER}} .masterstudy-mega-menu__submenu',
			)
		);

		$this->add_responsive_control(
			'desktop_panel_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-mega-menu__panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-mega-menu__submenu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'desktop_full_width_content_width',
			array(
				'label'      => esc_html__( 'Full-Width Content Width', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 320,
						'max' => 1920,
					),
					'%'  => array(
						'min' => 50,
						'max' => 100,
					),
					'vw' => array(
						'min' => 50,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 1170,
				),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-mega-menu--full-width .masterstudy-mega-menu__panel-wrap' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'desktop_panel_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-mega-menu__panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-mega-menu__submenu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'desktop_panel_offset',
			array(
				'label'      => esc_html__( 'Panel Offset Top', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -100,
						'max' => 200,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-mega-menu__panel' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// Styles/scripts are auto-enqueued via get_style_depends() and get_script_depends()
		// But ensure they're loaded on frontend
		wp_enqueue_style( 'masterstudy-mega-menu' );
		wp_enqueue_script( 'masterstudy-mega-menu' );

		$menu_item_image_positions = array();
		if ( ! empty( $settings['menu_item_images'] ) && is_array( $settings['menu_item_images'] ) ) {
			foreach ( $settings['menu_item_images'] as $row ) {
				$target_id = absint( $row['menu_item_id'] ?? 0 );
				$position  = in_array( $row['menu_item_image_position'] ?? 'left', array( 'left', 'top' ), true ) ? $row['menu_item_image_position'] : 'left';

				if ( $target_id && ! isset( $menu_item_image_positions[ $target_id ] ) ) {
					$menu_item_image_positions[ $target_id ] = $position;
				}
			}
		}

		$desktop_nested_views        = array();
		$desktop_dropdown_sides      = array();
		$desktop_centered_items      = array();
		$top_level_large_image_sizes = array();
		if ( ! empty( $settings['desktop_nested_views'] ) && is_array( $settings['desktop_nested_views'] ) ) {
			foreach ( $settings['desktop_nested_views'] as $row ) {
				$target_id       = absint( $row['menu_item_id'] ?? 0 );
				$nested_view     = in_array( $row['desktop_nested_view'] ?? 'cascade', array( 'headings', 'cascade' ), true ) ? $row['desktop_nested_view'] : 'cascade';
					$side        = in_array( $row['desktop_dropdown_side'] ?? 'auto', array( 'auto', 'right', 'left' ), true ) ? $row['desktop_dropdown_side'] : 'auto';
					$is_centered = 'yes' === ( $row['desktop_center_content'] ?? '' );

				if ( $target_id && ! isset( $desktop_nested_views[ $target_id ] ) ) {
					$desktop_nested_views[ $target_id ] = $nested_view;
				}

				if ( $target_id && ! isset( $desktop_dropdown_sides[ $target_id ] ) ) {
					$desktop_dropdown_sides[ $target_id ] = $side;
				}

				if ( $target_id && $is_centered && ! isset( $desktop_centered_items[ $target_id ] ) ) {
					$desktop_centered_items[ $target_id ] = true;
				}
			}
		}

		if ( ! empty( $settings['top_level_large_image_sizes'] ) && is_array( $settings['top_level_large_image_sizes'] ) ) {
			foreach ( $settings['top_level_large_image_sizes'] as $row ) {
				$target_id          = absint( $row['menu_item_id'] ?? 0 );
				$size_setting       = $row['large_image_height'] ?? '';
				$size_value         = 0;
				$size_unit          = 'px';
				$normalized_setting = is_string( $size_setting ) ? trim( $size_setting ) : '';

				if ( is_array( $size_setting ) ) {
					$size_value = isset( $size_setting['size'] ) ? floatval( $size_setting['size'] ) : 0;
					$size_unit  = ! empty( $size_setting['unit'] ) ? sanitize_key( $size_setting['unit'] ) : 'px';
				} elseif ( is_numeric( $size_setting ) ) {
					$size_value = floatval( $size_setting );
				} elseif ( ! empty( $normalized_setting ) && preg_match( '/^(\d+(?:\.\d+)?)(px)?$/i', $normalized_setting, $matches ) ) {
					$size_value = floatval( $matches[1] );
					$size_unit  = ! empty( $matches[2] ) ? strtolower( $matches[2] ) : 'px';
				}

				$size_string = $size_value > 0 ? $size_value . $size_unit : '';

				if ( $target_id && ! isset( $top_level_large_image_sizes[ $target_id ] ) && ! empty( $size_string ) ) {
					$top_level_large_image_sizes[ $target_id ] = $size_string;
				}
			}
		}

		$atts = array(
			'menu_id'                     => $settings['menu'],
			'menu_item_images'            => $settings['menu_item_images'] ?? array(),
			'full_width_triggers'         => $settings['full_width_triggers'] ?? array(),
			'desktop_nested_view'         => 'cascade',
			'desktop_nested_views'        => $desktop_nested_views,
			'desktop_dropdown_sides'      => $desktop_dropdown_sides,
			'desktop_centered_items'      => $desktop_centered_items,
			'menu_item_image_position'    => 'left',
			'menu_item_image_positions'   => $menu_item_image_positions,
			'top_level_large_image_sizes' => $top_level_large_image_sizes,
		);
		\STM_LMS_Templates::show_lms_template( 'elementor-widgets/mega-menu/main', $atts );
	}
}
