<?php  // phpcs:ignoreFile

/**
 * MS Certificate Checker (title with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DMSLMS_BlogList extends DMSLMS_BaseModule {
	// Module slug (also used as shortcode tag)
	public $slug = 'dmslms_blog_list';

	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	function init() {
		// Module name
		$this->name = esc_html__( 'MS Blog Module', 'masterstudy-lms-divi' );

		// Module Icon
		// This character will be rendered using etbuilder font-icon. For fully customized icon, create svg icon and
		// define its path on $this->icon_path property (see CustomCTAFull class)
		$this->icon = 'a';
	}

	/**
	 * Module's specific fields
	 *
	 * @return array
	 * @since 1.0.0
	 *
	 */
	function get_fields() {
		return array(
			'title' => array(
				'label'            => esc_html__( 'Module Title', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Text entered here will appear as title.', 'masterstudy-lms-divi' ),
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'Blog Module Title',
			),
			'count' => array(
				'label'            => esc_html__( 'Post Count', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the count for displaying posts', 'masterstudy-lms-divi' ),
				'toggle_slug'      => 'main_content',
				'default_on_front' => '3',
			),


			'type' => array(
				'label'            => esc_html__( 'Select the blog style', 'masterstudy-lms-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'list' => esc_html__( 'List View', 'masterstudy-lms-divi' ),
					'grid' => esc_html__( 'Grid View', 'masterstudy-lms-divi' ),
				),
				'default_on_front' => 'list',
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
			),

			'title_color' => array(
				'label'           => esc_html__( 'Post Date Title Color', 'masterstudy-lms-divi' ),
				'type'            => 'color-alpha',
				'default'         => '#fff',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
			),
			'back_color'  => array(
				'label'           => esc_html__( 'Post Date Background Color', 'masterstudy-lms-divi' ),
				'type'            => 'color-alpha',
				'default'         => '#F5B830',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
			),
		);
	}


	/**
	 * Render module output
	 *
	 * @param array $attrs List of unprocessed attributes
	 * @param string $content Content being processed
	 * @param string $render_slug Slug of module that is used for rendering output
	 *
	 * @return string module's rendered output
	 * @since 1.0.0
	 *
	 */
	function render( $attrs, $content = null, $render_slug = null ) {
		$args       = array(
			'posts_per_page' => $this->props['count']
		);
		$main_class = "blog-list-ms-main";
		if ( $this->props['type'] === 'grid' ) {
			$main_class = "blog-list-ms-main-grid";
			$args       = array(
				'posts_per_page' => $this->props['count']
			);
		} else {
			$main_class = "blog-list-ms-main-list";
		}
		$my_query = new WP_Query( $args );
		$content  = "<div class=" . $main_class . ">";
		if ( $my_query->have_posts() ) {

			while ( $my_query->have_posts() ) {
				$my_query->the_post();
				$day      = get_the_date( 'd' );
				$month    = get_the_date( 'M' );
				$category = get_the_category_list( ',', '', get_the_ID() );
				$tags     = get_the_tag_list( '', ',' );

				$content .= "<div class='blog-list-ms-single-post'>";

				$content .= "<div class='blog-list-ms-image'>";
				$content .= get_the_post_thumbnail( get_the_ID(), 'full' );
				$content .= "</div>";

				$content .= "<div class='blog-list-ms-item'>";

				$content .= "<div class='blog-list-ms-item-inner'>";
				$inline  = "color:" . $this->props['title_color'] . "; background-color:" . $this->props['back_color'] . "; border-color:" . $this->props['back_color'];
				$content .= "<div class='blog-list-ms-post-time'  style='$inline'>";

				$content .= "<div class='date-d'>";
				$content .= $day;
				$content .= "</div>";
				$content .= "<div class='date-m'>";
				$content .= $month;
				$content .= "</div>";

				$content .= "</div>"; //closing blog-list-ms-post-time
				$content .= "</div>"; //closing blog-list-item-inner

				$content .= "<div class='blog-list-ms-item-inner'>";

				$content .= "<div class='blog-list-ms-title'>";
				$content .= "<a href=" . get_permalink() . ">" . get_the_title() . "</a>";
				$content .= "</div>";

				$content .= "<div class='blog-list-ms-excerpt'>";
				$content .= get_the_excerpt();
				$content .= "</div>";

				$content .= "<div class='blog-list-ms-separator'>";
				$content .= "</div>";

				$content .= "<div class='blog-list-ms-cats'>";

				$content .= "<div class='ms-cats-label'>" . esc_html__( 'Posted in:', 'masterstudy-lms-divi' ) . "</div>";

				$content .= $category;
				$content .= "</div>";

				if ( ! ( empty( $tags ) ) ) {
					$content .= "<div class='blog-list-ms-cats'>";
					$content .= "<div class='ms-cats-label'>" . esc_html__( 'Tags:', 'masterstudy-lms-divi' ) . "</div>";
					$content .= $tags;
					$content .= "</div>";

				}

				$content .= "</div>"; //closing blog-list-item-inner

				$content .= "</div>"; //closing blog-list-item


				$content .= "<div class='blog-list-ms-separator-after'>";
				$content .= "<div class='blog-list-ms-separator-left'>";
				$content .= "</div>";
				$content .= "<div class='blog-list-ms-separator-right'>";
				$content .= "</div>";
				$content .= "</div>";

				$content .= "</div>"; //closing blog-list-ms-single-post


			}

		}
		$content .= "</div>"; // closing blog-list-ms-main

		wp_reset_postdata();

		// Module specific props added on $this->get_fields()
		$title = $this->props['title'];

		// Render module content
		$output = sprintf( '%1$s', $content );

		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DMSLMS_BlogList();
