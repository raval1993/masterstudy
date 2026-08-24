<?php // phpcs:ignoreFile

class DMSLMS_SingleCourseCarousel extends DMSLMS_BaseModule {

	public $slug = 'dmslms_single_course_carousel';

	protected $module_credits = array(
		'module_uri' => 'masterstudy-lms-divi-modules',
		'author'     => 'StylemixThemes',
		'author_uri' => 'https://stylemixthemes.com/',
	);

	public function init() {
		$this->name = esc_html__( 'MS Single Course Carousel', 'masterstudy-lms-divi' );
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
				'default_on_front' => 'MS Single Course Carousel Module title',
				'toggle_slug'      => 'main_content',
			),
			'query'              => array(
				'label'            => esc_html__( 'Sorting options “Sort by”', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'none'    => esc_html__( 'none', 'masterstudy-lms-divi' ),
					'popular' => esc_html__( 'popular', 'masterstudy-lms-divi' ),
					'free'    => esc_html__( 'free', 'masterstudy-lms-divi' ),
					'rating'  => esc_html__( 'rating', 'masterstudy-lms-divi' ),
				),
				'default_on_front' => 'none',
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
			),
			'prev_next'          => array(
				'label'            => esc_html__( 'Enable or Disable Previous/Next Buttons', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'enable'  => esc_html__( 'enable', 'masterstudy-lms-divi' ),
					'disable' => esc_html__( 'disable', 'masterstudy-lms-divi' ),
				),
				'default_on_front' => 'enable',
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
			$output = sprintf( '<h2>%1$s</h2>', esc_html__( 'Single Courses Carousel error: Please select at least one category!', 'masterstudy-lms-divi' ) );

			return $this->_render_module_wrapper( $output, $render_slug );
		}
		$terms = stm_lms_get_lms_terms_with_meta( '', 'stm_lms_course_taxonomy' );

		$result = array();
		foreach ( $terms as $term ) {
			$result[ $term->term_id ] = $term->name;
		}

		$selected_categories    = $this->props['include_categories'];
		$selected_categories    = $selected_categories . ']';
		$selected_categories[0] = '[';

		// Render module content
		$output = sprintf( '%1$s', do_shortcode( '[stm_lms_single_course_carousel query=' . $this->props['query'] . ' prev_next=' . $this->props['prev_next'] . 'taxonomy = "' . $selected_categories . ' ' ) );

		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DMSLMS_SingleCourseCarousel();
