<?php // phpcs:ignoreFile

class DMSLMS_CoursesCarousel extends DMSLMS_BaseModule {

	public $slug = 'dmslms_courses_carousel';

	protected $module_credits = array(
		'module_uri' => 'masterstudy-lms-divi-modules',
		'author'     => 'StylemixThemes',
		'author_uri' => 'https://stylemixthemes.com/',
	);

	public function init() {
		$this->name = esc_html__( 'MS Courses Carousel', 'masterstudy-lms-divi' );
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
			'title' => array(
				'label'            => esc_html__( 'Module Title ', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default_on_front' => 'MS Courses Carousel Module Title',
				'description'      => esc_html__( 'Input your desired heading here.', 'masterstudy-lms-divi' ),
				'toggle_slug'      => 'main_content',
			),

			'query' => array(
				'label'            => esc_html__( 'Sorting options — Sort by', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'none'    => esc_html__( 'Default', 'masterstudy-lms-divi' ),
					'popular' => esc_html__( 'Popular', 'masterstudy-lms-divi' ),
					'free'    => esc_html__( 'Free', 'masterstudy-lms-divi' ),
					'rating'  => esc_html__( 'Rating', 'masterstudy-lms-divi' ),
				),
				'default_on_front' => 'none',
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
			),

			'prev_next'       => array(
				'label'            => esc_html__( 'Previous/Next buttons', 'masterstudy-lms-divi' ),
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
			'show_categories' => array(
				'label'            => esc_html__( ' Displaying of categories', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'enable'  => esc_html__( 'Enable', 'masterstudy-lms-divi' ),
					'disable' => esc_html__( 'Disable', 'masterstudy-lms-divi' ),
				),
				'default_on_front' => 'disable',
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
			),

			'per_row'            => array(
				'label'            => esc_html__( 'Specify the number of courses per row ', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default_on_front' => '4',
				'description'      => esc_html__( 'Input your desired heading here.', 'masterstudy-lms-divi' ),
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
			$output = sprintf( '<h2>%1$s</h2>', esc_html__( 'Courses Carousel error: Please select at least one category!', 'masterstudy-lms-divi' ) );

			return $this->_render_module_wrapper( $output, $render_slug );
		}
		$terms = stm_lms_get_lms_terms_with_meta( '', 'stm_lms_course_taxonomy' );

		$result = array();
		foreach ( $terms as $term ) {
			$result[ $term->term_id ] = $term->name;
		}
		$selected_categories = ltrim( $this->props['include_categories'], ',' );

		$atts         = array(
			'css'              => '',
			'title'            => ! empty( $this->props['title'] ) ? $this->props['title'] : '',
			'query'            => ! empty( $this->props['query'] ) ? $this->props['query'] : 'none',
			'prev_next'        => ! empty( $this->props['prev_next'] ) ? $this->props['prev_next'] : 'enable',
			'per_row'          => ! empty( $this->props['per_row'] ) ? $this->props['per_row'] : 6,
			'posts_per_page'   => ! empty( $this->props['posts_per_page'] ) ? $this->props['posts_per_page'] : 12,
			'taxonomy'         => $selected_categories,
			'taxonomy_default' => $selected_categories ?? '',
			'image_size'       => ! empty( $this->props['image_size'] ) ? $this->props['image_size'] : '',
			'show_categories'  => ! empty( $this->props['show_categories'] ) ? $this->props['show_categories'] : 'disable',
		);
		$uniq         = stm_lms_create_unique_id( $atts );
		$atts['uniq'] = $uniq;

		return STM_LMS_Templates::load_lms_template( 'shortcodes/stm_lms_courses_carousel', $atts );

	}
}

new DMSLMS_CoursesCarousel();
