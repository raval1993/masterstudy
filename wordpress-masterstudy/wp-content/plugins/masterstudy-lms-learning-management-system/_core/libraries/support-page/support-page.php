<?php
/**
 * Submodule Name: Support page
 * Submodule URI: https://bitbucket.org/stylemixthemes/support-page/src/master/
 * Description: Support page.
 * Version: 1.0.0
 * License: http://www.gnu.org/licenses/gpl-3.0.html
 * Author: StylemixThemes
 * Author URI: https://stylemixthemes.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SUPPORT_PAGE_VERSION' ) ) {
	define( 'SUPPORT_PAGE_VERSION', '1.0.0' );
}

if ( ! defined( 'SUPPORT_PAGE_FILE' ) ) {
	define( 'SUPPORT_PAGE_FILE', __FILE__ );
}

if ( ! defined( 'SUPPORT_PAGE_PATH' ) ) {
	define( 'SUPPORT_PAGE_PATH', dirname( SUPPORT_PAGE_FILE ) );
}

if ( ! defined( 'SUPPORT_PAGE_URL' ) ) {
	define( 'SUPPORT_PAGE_URL', plugin_dir_url( SUPPORT_PAGE_FILE ) );
}

if ( ! is_admin() ) {
	return;
}

if ( file_exists( SUPPORT_PAGE_PATH . '/announcement/main.php' ) ) {
	require_once dirname( SUPPORT_PAGE_PATH ) . '/announcement/main.php';
}
require_once SUPPORT_PAGE_PATH . '/config/class-support-page.php';

if ( class_exists( 'STM_Support_Page' ) ) {
	add_action( 'admin_init', array( 'STM_Support_Page', 'init' ) );
}
