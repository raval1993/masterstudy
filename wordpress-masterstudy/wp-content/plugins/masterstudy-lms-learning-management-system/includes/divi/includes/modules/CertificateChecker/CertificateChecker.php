<?php // phpcs:ignoreFile

/**
 * MS Certificate Checker (title with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DMSLMS_CertificateChecker extends DMSLMS_BaseModule {
	// Module slug (also used as shortcode tag)
	public $slug = 'dmslms_certificate_checker';

	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	public function init() {
		// Module name
		$this->name = esc_html__( 'MS Certificate Checker', 'masterstudy-lms-divi' );

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
	public function get_fields() {
		return array(
			'title' => array(
				'label'            => esc_html__( 'Module Title', 'masterstudy-lms-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Text entered here will appear as title.', 'masterstudy-lms-divi' ),
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'MS Certificate Checker Module Title',
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
	public function render( $attrs, $render_slug, $content = null ) {
		// Module specific props added on $this->get_fields()
		$title = $this->props['title'];

		// Render module content
		$output = sprintf( '%1$s', '<h3>' . $title . ' </h3>' . do_shortcode( '[stm_lms_certificate_checker title="' . $title . ']' ) );

		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DMSLMS_CertificateChecker();
