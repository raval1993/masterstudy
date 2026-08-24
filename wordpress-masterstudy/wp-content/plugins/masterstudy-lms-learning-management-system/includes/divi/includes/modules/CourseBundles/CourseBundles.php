<?php // phpcs:ignoreFile

class DMSLMS_CourseBundles extends DMSLMS_BaseModule {

	public $slug = 'dmslms_course_bundles';

	protected $module_credits = array(
		'module_uri' => 'masterstudy-lms-divi-modules',
		'author'     => 'StylemixThemes',
		'author_uri' => 'https://stylemixthemes.com/',
	);

	public function init() {
		$this->name = esc_html__( 'MS Course Bundles', 'masterstudy-lms-divi' );
		$this->icon = 'a';
	}

	public function get_fields() {
		return array(
			'title'    => array(
				'label'            => esc_html__( 'Module Title', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'MS Course Bundles Module Title',
			),
			'per_page' => array(
				'label'            => esc_html__( 'Number of Courses per page', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => '3',
			),
			'columns'  => array(
				'label'            => esc_html__( 'Number of Columns', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'2' => esc_html__( '2', 'masterstudy-lms-divi' ),
					'3' => esc_html__( '3', 'masterstudy-lms-divi' ),
					'4' => esc_html__( '4', 'masterstudy-lms-divi' ),
					'5' => esc_html__( '5', 'masterstudy-lms-divi' ),
					'6' => esc_html__( '6', 'masterstudy-lms-divi' ),
				),
				'default_on_front' => '3',
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
			),
		);
	}

	public function render( $attrs, $render_slug, $content = null ) {
		// Render module content
		$atts = array(
			'css'            => '',
			'title'          => ! empty( $this->props['title'] ) ? $this->props['title'] : '',
			'columns'        => ! empty( $this->props['columns'] ) ? $this->props['columns'] : '3',
			'posts_per_page' => ! empty( $this->props['per_page'] ) ? $this->props['per_page'] : '3',
		);

		if ( class_exists( 'MasterStudy\Lms\Pro\addons\CourseBundle\CourseBundle' ) ) {
			return \STM_LMS_Templates::load_lms_template( 'shortcodes/stm_lms_course_bundles', $atts );
		}
		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
	}
}

new DMSLMS_CourseBundles();
