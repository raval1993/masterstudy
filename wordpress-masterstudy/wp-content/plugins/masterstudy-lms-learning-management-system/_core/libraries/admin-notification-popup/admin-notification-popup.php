<?php

if ( ! is_admin() ) return;

if ( ! defined( 'STM_ANP_PATH' ) ) {
	define( 'STM_ANP_PATH', dirname( __FILE__ ) );
}

if ( ! defined( 'STM_ANP_URL' ) ) {
	define( 'STM_ANP_URL', ( false !== strpos( STM_ANP_PATH, 'plugins' ) ) ? plugin_dir_url( __FILE__ ) : get_template_directory_uri() . '/admin/admin-notifications-popup/' );
}

if ( ! function_exists( 'get_product_name' ) ) {
	function get_product_name()	{
		$path         = STM_ANP_PATH;
		$position     = strstr( $path, 'wp-content/plugins/');
		$product_name = array();

		if ( $position !== false ) {
			$plugin_path = substr( $position, strlen( 'wp-content/plugins/' ) );
			$parts       = explode( '/', $plugin_path );

			$product_name['plugin_name'] = $parts[0];
		} else {
			$product_name['plugin_name'] = wp_get_theme( get_template() )->get( 'TextDomain' );
		}

		return $product_name;
	}
}

if ( ! class_exists( 'NotificationInit' ) ) {
	require_once __DIR__ . '/classes/NotificationInit.php';
}

NotificationInit::init( get_product_name() );

spl_autoload_register(
	function ( $class_name ) {
		$class_path = str_replace( array( '\\', 'ANP' ), array( '/', 'classes' ), $class_name );

		if ( file_exists( STM_ANP_PATH . '/' . $class_path . '.php' ) ) {
			include STM_ANP_PATH . '/' . $class_path . '.php';
		}
	}
);

ANP\EnqueueSS::init();
ANP\AdminbarItem::init();
\ANP\Popup\DefaultHooks::init();
