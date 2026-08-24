<?php // phpcs:ignoreFile

class DMSLMS_CoursesSearchbox extends DMSLMS_BaseModule {

	public $slug = 'dmslms_courses_searchbox';

	protected $module_credits = array(
		'module_uri' => 'masterstudy-lms-divi-modules',
		'author'     => 'StylemixThemes',
		'author_uri' => 'https://stylemixthemes.com/',
	);

	public function init() {
		$this->name = esc_html__( 'MS Courses Searchbox', 'masterstudy-lms-divi' );
		$this->icon = 'a';
	}

	public function get_fields() {
		return array(

			'title' => array(
				'label'            => esc_html__( 'Module Title', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'MS Courses Searchbox Module Title',
			),
			'style' => array(
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
		);
	}

	public function render( $attrs, $render_slug, $content = null ) {
		// Render module content
		$output = sprintf( '%1$s', do_shortcode( '[stm_courses_searchbox title="' . $this->props['title'] . '" style=' . $this->props['style'] . ' ]' ) );

		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DMSLMS_CoursesSearchbox;