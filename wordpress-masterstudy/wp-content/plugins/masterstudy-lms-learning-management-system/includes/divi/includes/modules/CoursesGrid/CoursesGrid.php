<?php // phpcs:ignoreFile

class DMSLMS_CoursesGrid extends DMSLMS_BaseModule {

	public $slug = 'dmslms_courses_grid';

	protected $module_credits = array(
		'module_uri' => 'masterstudy-lms-divi-modules',
		'author'     => 'StylemixThemes',
		'author_uri' => 'https://stylemixthemes.com/',
	);

	public function init() {
		$this->name = esc_html__( 'MS Courses Grid', 'masterstudy-lms-divi' );
		$this->icon = 'a';
	}

	public function get_fields() {
		return array(
			'hide_top_bar' => array(
				'label'            => esc_html__( 'Hide Top bar', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'show' => esc_html__( 'Show', 'masterstudy-lms-divi' ),
					'hide' => esc_html__( 'Hide', 'masterstudy-lms-divi' ),
				),
				'default_on_front' => 'hide',
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
			),
			'title'        => array(
				'label'            => esc_html__( 'Module Title', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default_on_front' => 'MS Courses Grid Module Title',
				'description'      => esc_html__( 'Input your desired heading here.', 'masterstudy-lms-divi' ),
				'toggle_slug'      => 'main_content',
			),
			'load_more'    => array(
				'label'            => esc_html__( 'Load more', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'show' => esc_html__( 'Show', 'masterstudy-lms-divi' ),
					'hide' => esc_html__( 'Hide', 'masterstudy-lms-divi' ),
				),
				'default_on_front' => 'hide',
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
			),
			'sort_courses' => array(
				'label'            => esc_html__( 'Sort Courses', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'show' => esc_html__( 'Show', 'masterstudy-lms-divi' ),
					'hide' => esc_html__( 'Hide', 'masterstudy-lms-divi' ),
				),
				'default_on_front' => 'hide',
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
			),
			'image_size'   => array(
				'label'            => esc_html__( 'Image size (Ex.: thumbnail, medium, full, large )', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default_on_front' => 'full',
				'toggle_slug'      => 'main_content',
			),
			'per_row'      => array(
				'label'            => esc_html__( 'Courses Per Row', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'default_on_front' => '6',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
			),
			'per_pag'      => array(
				'label'            => esc_html__( 'Courses Per Page', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'default_on_front' => '12',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
			),
		);
	}

	public function render( $attrs, $render_slug, $content = null ) {

		$atts = array(
			'hide_top_bar'   => ! empty( $this->props['hide_top_bar'] ) ? $this->props['hide_top_bar'] : 'showing',
			'title'          => ! empty( $this->props['title'] ) ? $this->props['title'] : '',
			'hide_load_more' => ! empty( $this->props['hide_load_more'] ) ? $this->props['load_more'] : 'showing',
			'hide_sort'      => ! empty( $this->props['hide_sort'] ) ? $this->props['sort_courses'] : 'showing',
			'per_row'        => ! empty( $this->props['per_row'] ) ? $this->props['per_row'] : 6,
			'image_size'     => ! empty( $this->props['image_size'] ) ? $this->props['image_size'] : '',
			'posts_per_page' => ! empty( $this->props['per_pag'] ) ? $this->props['per_pag'] : '',
		);

		return \STM_LMS_Templates::load_lms_template( 'shortcodes/stm_lms_courses_grid', $atts );

	}
}

new DMSLMS_CoursesGrid();
