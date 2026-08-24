<?php // phpcs:ignoreFile

class DMSLMS_RecentCourses extends DMSLMS_BaseModule {

	public $slug = 'dmslms_recent_courses';

	protected $module_credits = array(
		'module_uri' => 'masterstudy-lms-divi-modules',
		'author'     => 'StylemixThemes',
		'author_uri' => 'https://stylemixthemes.com/',
	);

	public function init() {
		$this->name = esc_html__( 'MS Recent Courses', 'masterstudy-lms-divi' );
		$this->icon = 'a';
	}

	public function get_fields() {
		return array(

			'title'             => array(
				'label'            => esc_html__( 'Module Title', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'MS Recent Courses Module Title',
			),
			'number_of_courses' => array(
				'label'            => esc_html__( 'Number of courses to show', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => '4',
			),
			'style'             => array(
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
			'courses_per_row'   => array(
				'label'            => esc_html__( 'Courses Per Row', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'2' => esc_html__( '2', 'masterstudy-lms-divi' ),
					'3' => esc_html__( '3', 'masterstudy-lms-divi' ),
					'4' => esc_html__( '4', 'masterstudy-lms-divi' ),
					'6' => esc_html__( '6', 'masterstudy-lms-divi' ),
				),
				'default_on_front' => '6',
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
			),
		);
	}

	public function render( $attrs, $render_slug, $content = null ) {

		// Render module content
		$output = sprintf( '%1$s', do_shortcode( '[stm_lms_recent_courses posts_per_page=' . $this->props['number_of_courses'] . ' per_row=' . $this->props['courses_per_row'] . ' style=' . $this->props['style'] . ' ]' ) );

		$atts = array(
			'css'            => '',
			'posts_per_page' => ! empty( $this->props['posts_per_page'] ) ? $this->props['posts_per_page'] : '',
			'image_size'     => ! empty( $this->props['image_size'] ) ? $this->props['image_size'] : '',
			'per_row'        => ! empty( $this->props['courses_per_row'] ) ? $this->props['courses_per_row'] : '6',
			'style'          => ! empty( $this->props['style'] ) ? $this->props['style'] : 'style_1',
		);

		return STM_LMS_Templates::load_lms_template( 'shortcodes/stm_lms_recent_courses', $atts );
		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
	}
}

new DMSLMS_RecentCourses();
