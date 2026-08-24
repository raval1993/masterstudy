<?php
/**
 * MasterStudy LMS Divi module integration.
 *
 * Keeps the legacy Divi module slugs/classes available from the main LMS plugin,
 * while yielding to the standalone Masterstudy LMS Divi Modules plugin when it is
 * installed and active.
 */

defined( 'ABSPATH' ) || exit;

function masterstudy_lms_divi_integration_bootstrap() {
	if ( defined( 'MASTERSTUDY_LMS_DIVI_INTEGRATION_BOOTSTRAPPED' ) ) {
		return;
	}

	if ( defined( 'DMSLMS_FILE__' ) || function_exists( 'stm_lms_divi_init' ) ) {
		return;
	}

	define( 'MASTERSTUDY_LMS_DIVI_INTEGRATION_BOOTSTRAPPED', true );

	if ( ! defined( 'DMSLMS_VERSION' ) ) {
		define( 'DMSLMS_VERSION', MS_LMS_VERSION );
	}

	if ( ! defined( 'DMSLMS_FILE__' ) ) {
		define( 'DMSLMS_FILE__', MS_LMS_FILE );
	}

	if ( ! defined( 'DMSLMS_DIR_PATH' ) ) {
		define( 'DMSLMS_DIR_PATH', __DIR__ );
	}

	if ( ! defined( 'DMSLMS_DIR_URL' ) ) {
		define( 'DMSLMS_DIR_URL', trailingslashit( MS_LMS_URL . 'includes/divi' ) );
	}

	if ( ! defined( 'DMSLMS_ASSETS' ) ) {
		define( 'DMSLMS_ASSETS', trailingslashit( DMSLMS_DIR_URL . 'assets' ) );
	}

	if ( ! defined( 'DMSLMS_ENV' ) ) {
		define( 'DMSLMS_ENV', 'PROD' );
	}

	add_action( 'admin_enqueue_scripts', 'masterstudy_lms_divi_integration_enqueue_admin_style' );
	add_action( 'divi_extensions_init', 'masterstudy_lms_divi_initialize_extension' );
	add_action( 'et_builder_ready', 'masterstudy_lms_divi_register_modules_fallback' );
	masterstudy_lms_divi_bootstrap_d5_modules();
}
add_action( 'plugins_loaded', 'masterstudy_lms_divi_integration_bootstrap', 30 );

function masterstudy_lms_divi_integration_enqueue_admin_style() {
	wp_enqueue_style(
		'masterstudy_lms_divi_wp_admin_css',
		DMSLMS_DIR_URL . 'includes/admin-style.css',
		array(),
		DMSLMS_VERSION
	);
}

function masterstudy_lms_divi_initialize_extension() {
	if ( class_exists( 'DMSLMS_MasterstudyLmsDiviModules', false ) ) {
		return;
	}

	require_once DMSLMS_DIR_PATH . '/includes/MasterstudyDiviModules.php';
}

function masterstudy_lms_divi_register_modules_fallback() {
	if ( ! class_exists( 'ET_Builder_Element' ) ) {
		return;
	}

	require_once DMSLMS_DIR_PATH . '/includes/loader.php';
}

function masterstudy_lms_divi_bootstrap_d5_modules() {
	if ( ! defined( 'DMSLMS_D5_NATIVE_BOOTSTRAPPED' ) ) {
		define( 'DMSLMS_D5_NATIVE_BOOTSTRAPPED', true );
	}

	require_once DMSLMS_DIR_PATH . '/includes/d5/register.php';
}
