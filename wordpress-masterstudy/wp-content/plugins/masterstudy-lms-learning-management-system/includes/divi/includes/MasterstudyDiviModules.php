<?php
// phpcs:ignoreFile
class DMSLMS_MasterstudyLmsDiviModules extends DiviExtension {

	/**
	 * The gettext domain for the extension's translations.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $gettext_domain = 'masterstudy-lms-divi';

	/**
	 * The extension's WP Plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name = 'masterstudy-lms-divi';

	/**
	 * The extension's version
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * DMSLMS_MasterstudyLmsDiviModules constructor.
	 *
	 * @param string $name
	 * @param array  $args
	 */
	public function __construct( $name = 'masterstudy-lms-divi', $args = array() ) {
		$this->plugin_dir     = DMSLMS_DIR_PATH;
		$this->plugin_dir_url = DMSLMS_DIR_URL;

		parent::__construct( $name, $args );
	}
}

new DMSLMS_MasterstudyLmsDiviModules;
