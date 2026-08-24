<?php // phpcs:ignoreFile

class DMSLMS_CoursesCategories extends DMSLMS_BaseModule {

	public $slug = 'dmslms_courses_categories';

	protected $module_credits = array(
		'module_uri' => 'masterstudy-lms-divi-modules',
		'author'     => 'StylemixThemes',
		'author_uri' => 'https://stylemixthemes.com/',
	);

	public function init() {
		$this->name = esc_html__( 'MS Courses Categories', 'masterstudy-lms-divi' );
		$this->icon = 'a';
	}

	public function get_fields() {

		$terms = stm_lms_get_lms_terms_with_meta( '', 'stm_lms_course_taxonomy' );

		$result = array();
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$result[ $term->term_id ] = $term->name;
			}
		}

		return array(

			'title'              => array(
				'label'            => esc_html__( 'Module Title', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Input your desired heading here.', 'masterstudy-lms-divi' ),
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'MS Courses Categories Module Title',
			),
			'style'              => array(
				'label'            => esc_html__( 'Style', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'style_1' => esc_html__( 'Style 1', 'masterstudy-lms-divi' ),
					'style_2' => esc_html__( 'Style 2', 'masterstudy-lms-divi' ),
					'style_3' => esc_html__( 'Style 3', 'masterstudy-lms-divi' ),
					'style_4' => esc_html__( 'Style 4', 'masterstudy-lms-divi' ),
				),
				'default_on_front' => 'style_1',
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
			),
			'include_categories' => array(
				'label'            => esc_html__( 'Included Categories', 'masterstudy-lms-divi' ),
				'type'             => 'categories',
				'meta_categories'  => $result,
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Select the categories that you would like to include in the feed.', 'masterstudy-lms-divi' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__projects',
				),
				'taxonomy_name'    => 'project_category',
			),
		);
	}

	public function render( $attrs, $render_slug, $content = null ) {

		if ( empty( $this->props['include_categories'] ) ) {
			$output = sprintf( '<h2>%1$s</h2>', esc_html__( 'Courses Categories error: Please select at least one category!', 'masterstudy-lms-divi' ) );

			return $this->_render_module_wrapper( $output, $render_slug );
		}
		$terms = stm_lms_get_lms_terms_with_meta( '', 'stm_lms_course_taxonomy' );

		$result = array();
		foreach ( $terms as $term ) {
			$result[ $term->term_id ] = $term->name;
		}

		$selected_categories = $this->props['include_categories'];
		$selected_categories = ltrim( $selected_categories, $selected_categories[0] );
		$selected_categories = str_replace( ',', ', ', $selected_categories );

		// Render module content
		$output = sprintf( '%1$s', do_shortcode( '[stm_lms_courses_categories  style =' . $this->props['style'] . ' taxonomy = "' . $selected_categories . '"]' ) );
		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DMSLMS_CoursesCategories;