<?php // phpcs:ignoreFile

class DMSLMS_GoogleClassrooms extends DMSLMS_BaseModule {

	public $slug = 'dmslms_google_classrooms';

	protected $module_credits = array(
		'module_uri' => 'masterstudy-lms-divi-modules',
		'author'     => 'StylemixThemes',
		'author_uri' => 'https://stylemixthemes.com/',
	);

	public function init() {
		$this->name = esc_html__( 'MS Google Classrooms', 'masterstudy-lms-divi' );
		$this->icon = 'a';
	}

	public function get_fields() {
		return array(

			'title'           => array(
				'label'            => esc_html__( 'Module Title', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'MS Google Classrooms Module Title',
			),
			'number_of_rooms' => array(
				'label'            => esc_html__( 'Number of Classrooms on the Page', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => '3',
			),
		);
	}

	function render( $attrs, $render_slug, $content = null ) {
		// Module specific props added on $this->get_fields()
		$title = $this->props['title'];

		// Render module content
		$output = sprintf( '%1$s',
			do_shortcode( '[stm_lms_google_classroom title="' . $this->props['title'] . '" number_of_rooms=' . $this->props['number_of_rooms'] . ' ]' )
		);

		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DMSLMS_GoogleClassrooms;