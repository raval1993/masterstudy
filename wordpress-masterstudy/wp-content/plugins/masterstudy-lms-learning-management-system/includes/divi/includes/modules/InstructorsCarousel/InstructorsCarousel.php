<?php // phpcs:ignoreFile

class DMSLMS_InstructorsCarousel extends DMSLMS_BaseModule {

	public $slug = 'dmslms_instructors_carousel';

	protected $module_credits = array(
		'module_uri' => 'masterstudy-lms-divi-modules',
		'author'     => 'StylemixThemes',
		'author_uri' => 'https://stylemixthemes.com/',
	);

	public function init() {
		$this->name = esc_html__( 'MS Instructors Carousel', 'masterstudy-lms-divi' );
		$this->icon = 'a';
	}

	public function get_fields() {
		return array(
			'title'       => array(
				'label'            => esc_html__( 'Module Title', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => ' ',
			),
			'title_color' => array(
				'label'           => esc_html__( 'Title Color', 'masterstudy-lms-divi' ),
				'type'            => 'color-alpha',
				'default'         => 'rgba(0,0,0,0.4)',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
			),
			'per_row'     => array(
				'label'            => esc_html__( 'Per row', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => '4',
			),
			'per_row_md'  => array(
				'label'            => esc_html__( 'Per row on Notebook', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => '3',
			),
			'per_row_sm'  => array(
				'label'            => esc_html__( 'Per row on Tablet', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => '2',
			),
			'per_row_xs'  => array(
				'label'            => esc_html__( 'Per row on Mobile', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => '1',
			),
			'style'       => array(
				'label'            => esc_html__( 'Style', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'style_1' => esc_html__( 'Style 1', 'masterstudy-lms-divi' ),
					'style_2' => esc_html__( 'Style 2', 'masterstudy-lms-divi' ),
				),
				'default_on_front' => 'style_1',
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
			),
			'prev_next'   => array(
				'label'            => esc_html__( 'Prev Next Buttons', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'enable'  => esc_html__( 'Enable', 'masterstudy-lms-divi' ),
					'disable' => esc_html__( 'Disable', 'masterstudy-lms-divi' ),
				),
				'default_on_front' => 'enable',
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
			),
			'sort'        => array(
				'label'            => esc_html__( 'Sort By', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'default' => esc_html__( 'Default', 'masterstudy-lms-divi' ),
					'rating'  => esc_html__( 'Rating', 'masterstudy-lms-divi' ),
				),
				'default_on_front' => 'default',
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
			),
		);
	}

	public function render( $attrs, $render_slug, $content = null ) {

		$atts = array(
			'css'         => '',
			'title'       => ! empty( $this->props['title'] ) ? $this->props['title'] : '',
			'per_row'     => ! empty( $this->props['per_row'] ) ? $this->props['per_row'] : '',
			'per_row_md'  => ! empty( $this->props['per_row_md'] ) ? $this->props['per_row_md'] : '',
			'limit'       => ! empty( $this->props['limit'] ) ? $this->props['limit'] : 10,
			'per_row_sm'  => ! empty( $this->props['per_row_sm'] ) ? $this->props['per_row_sm'] : '',
			'per_row_xs'  => ! empty( $this->props['per_row_xs'] ) ? $this->props['per_row_xs'] : '',
			'title_color' => ! empty( $this->props['title_color'] ) ? $this->props['title_color'] : '',
			'style'       => ! empty( $this->props['style'] ) ? $this->props['style'] : 'style_1',
			'sort'        => ! empty( $this->props['sort'] ) ? $this->props['sort'] : '',
			'prev_next'   => ! empty( $this->props['prev_next'] ) ? $this->props['prev_next'] : '',
			'pagination'  => ! empty( $this->props['pagination'] ) ? $this->props['pagination'] : '',
		);

		if ( 'default' === $atts['sort'] ) {
			$atts['sort'] = '';
		}

		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return \STM_LMS_Templates::load_lms_template( 'shortcodes/stm_lms_instructors_carousel', $atts );

	}
}

new DMSLMS_InstructorsCarousel();
