<?php // phpcs:ignoreFile

if ( ! class_exists( 'ET_Builder_Module' ) ) {
	return;
}

/**
 * Shared base module for Divi compatibility flags.
 */
abstract class DMSLMS_BaseModule extends ET_Builder_Module {
	/**
	 * Tell Divi Visual Builder this module supports VB rendering.
	 *
	 * This prevents legacy module notice banners in newer Divi versions
	 * while keeping existing server-side shortcode rendering intact.
	 *
	 * @var string
	 */
	public $vb_support = 'on';
}
