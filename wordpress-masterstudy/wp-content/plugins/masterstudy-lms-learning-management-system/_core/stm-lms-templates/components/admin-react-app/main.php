<?php
/**
 * React app template for easy creation of new apps inside wp_admin
 *
 * @package masterstudy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $ms_lms_loaded_textdomain_path;
do_action( 'admin_head' );

$translations_path   = ! empty( $ms_lms_loaded_textdomain_path ) ? $ms_lms_loaded_textdomain_path : MS_LMS_PATH . '/languages';
$react_entry         = 'wp_admin_app';
$manifest_assets     = masterstudy_lms_resolve_manifest_assets( 'wp-admin', $react_entry );
$manifest_assets     = is_array( $manifest_assets ) ? $manifest_assets : array();
$entry_script_url    = ! empty( $manifest_assets['entry_url'] )
	? $manifest_assets['entry_url']
	: MS_LMS_URL . 'assets/react/wp-admin/js/index.js';
$entry_key           = sanitize_key( str_replace( '-', '_', $react_entry ) );
$react_app_id        = ! empty( $entry_key ) ? 'ms_wp_react_' . $entry_key : 'ms_wp_react_wp_admin';
$translations_handle = $react_app_id . '-translations';

wp_enqueue_style(
	$react_app_id . '-style',
	apply_filters( $react_app_id . '_css', MS_LMS_URL . 'assets/react/wp-admin/css/wp-admin.css' ),
	array(),
	MS_LMS_VERSION
);
wp_enqueue_script(
	$translations_handle,
	apply_filters( $react_app_id . '_translations_js', MS_LMS_URL . 'assets/react/wp-admin/js/i18n-translations.js' ),
	array(),
	MS_LMS_VERSION,
	true
);

wp_set_script_translations( $translations_handle, 'masterstudy-lms-learning-management-system', $translations_path );

$scripts      = wp_scripts();
$load_scripts = array(
	'wp-polyfill-inert',
	'regenerator-runtime',
	'wp-polyfill',
	'wp-hooks',
	'wp-i18n',
	'utils',
);
?>
<div id="<?php echo esc_attr( $react_app_id ); ?>" class="ms-react-app__no-container-padding"></div>
<script>
	window.lmsApiSettings = {
		lmsUrl: '<?php echo esc_url_raw( rest_url( 'masterstudy-lms/v2' ) ); ?>',
		wpUrl: '<?php echo esc_url_raw( rest_url( 'wp/v2' ) ); ?>',
		nonce: '<?php echo esc_html( wp_create_nonce( 'wp_rest' ) ); ?>',
		isWpAdmin: true
	};

	<?php if ( function_exists( 'pll_current_language' ) ) { ?>
	window.lmsApiSettings.lang = '<?php echo esc_js( pll_current_language() ); ?>';
	<?php } ?>

	window.lmsApiSettings.locale = '<?php echo esc_attr( get_locale() ); ?>';
	window.lmsApiSettings.wp_date_format = '<?php echo esc_attr( get_option( 'date_format' ) ); ?>';
</script>
<?php
foreach ( $load_scripts as $handle ) {
	$handle_src = $scripts->registered[ $handle ]->src;
	$src_url    = filter_var( $handle_src, FILTER_VALIDATE_URL ) ? $handle_src : site_url( $handle_src );
	?>
	<script src="<?php echo esc_url( $src_url ); // phpcs:ignore ?>"></script>
<?php } ?>
<script type="module" src="<?php echo esc_url( $entry_script_url ); // phpcs:ignore ?>"></script>
<?php if ( ! empty( $manifest_assets['imports'] ) && is_array( $manifest_assets['imports'] ) ) { ?>
	<?php foreach ( $manifest_assets['imports'] as $import_url ) { ?>
		<link rel="modulepreload" href="<?php echo esc_url( $import_url ); ?>">
	<?php } ?>
<?php } ?>
<?php
$scripts->print_translations( $translations_handle );
?>
