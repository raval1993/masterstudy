<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $ms_lms_loaded_textdomain_path;

$translations_path  = ! empty( $ms_lms_loaded_textdomain_path ) ? $ms_lms_loaded_textdomain_path : MS_LMS_PATH . '/languages';
$additional_styles  = apply_filters( 'ms_lms_course_builder_additional_styles', array() );
$additional_scripts = apply_filters( 'ms_lms_course_builder_additional_scripts', array() );
$wp_referer         = wp_get_referer();
$manifest_assets    = masterstudy_lms_resolve_manifest_assets( 'course-builder', 'main' );
$manifest_assets    = is_array( $manifest_assets ) ? $manifest_assets : array();
$entry_script_url   = ! empty( $manifest_assets['entry_url'] ) ? $manifest_assets['entry_url'] : '';

wp_register_style( 'ms-lms-course-builder', apply_filters( 'ms_lms_course_builder_css', MS_LMS_URL . 'assets/react/course-builder/css/main.css' ), array(), MS_LMS_VERSION );
wp_register_script( 'ms-lms-course-builder-translations', apply_filters( 'ms_lms_course_builder_translations_js', MS_LMS_URL . 'assets/react/course-builder/js/i18n-translations.js' ), array(), MS_LMS_VERSION, true );

wp_set_script_translations( 'ms-lms-course-builder-translations', 'masterstudy-lms-learning-management-system', $translations_path );

$scripts        = wp_scripts();
$styles         = wp_styles();
$append_version = static function ( $url, $ver ) {
	if ( null === $ver || '' === (string) $ver ) {
		return $url;
	}

	return add_query_arg( 'ver', (string) $ver, $url );
};

$ms_lms_get_asset_src = static function ( $asset, $dependencies ) {
	if ( empty( $asset ) || empty( $asset->src ) ) {
		return '';
	}

	$src = filter_var( $asset->src, FILTER_VALIDATE_URL ) ? $asset->src : site_url( $asset->src );
	$ver = $asset->ver ?? $dependencies->default_version;

	if ( false === $ver ) {
		return $src;
	}

	if ( empty( $ver ) ) {
		$ver = $dependencies->default_version;
	}

	if ( ! empty( $ver ) ) {
		$src = add_query_arg( 'ver', $ver, $src );
	}

	return $src;
};

$load_scripts = array(
	'wp-polyfill-inert',
	'regenerator-runtime',
	'wp-polyfill',
	'wp-hooks',
	'wp-i18n',
	'utils',
);

do_action( 'stm_lms_template_main' );
?>
<!doctype html>
<html lang="<?php echo esc_attr( get_locale() ); ?>" <?php echo is_rtl() ? ' dir="rtl"' : ''; ?>>
<head>
	<title><?php esc_html_e( 'Course Builder', 'masterstudy-lms-learning-management-system' ); ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo esc_url( ms_plugin_favicon_url() ); ?>" />
	<link href="<?php echo esc_url( $ms_lms_get_asset_src( $styles->registered['dashicons'], $styles ) ); // phpcs:ignore ?>" rel="stylesheet">
	<link href="<?php echo esc_url( $ms_lms_get_asset_src( $styles->registered['editor-buttons'], $styles ) ); // phpcs:ignore ?>" rel="stylesheet">
	<link href="<?php echo esc_url( $ms_lms_get_asset_src( $styles->registered['ms-lms-course-builder'], $styles ) ); // phpcs:ignore ?>" rel="stylesheet">
	<?php
	if ( ! empty( $additional_styles ) ) {
		foreach ( $additional_styles as $style_url ) {
			?>
			<link href="<?php echo esc_url( $style_url ); // phpcs:ignore ?>" rel="stylesheet">
			<?php
		}
	}
	?>
	<?php if ( ! empty( $manifest_assets['imports'] ) && is_array( $manifest_assets['imports'] ) ) { ?>
		<?php foreach ( $manifest_assets['imports'] as $import_url ) { ?>
			<link rel="modulepreload" href="<?php echo esc_url( $import_url ); ?>">
		<?php } ?>
	<?php } ?>
</head>
<body>
<div id="ms_plugin_root"></div>
<?php
foreach ( $load_scripts as $handle ) {
	$src_url = $ms_lms_get_asset_src( $scripts->registered[ $handle ], $scripts );
	?>
	<script src="<?php echo esc_url( $src_url ); // phpcs:ignore ?>"></script>
<?php } ?>
<script>
	window.react_default_vars = {
		...( window.react_default_vars || {} ),
		admin_url: '<?php echo esc_url_raw( admin_url() ); ?>',
	};

	window.lmsApiSettings = {
		lmsUrl: '<?php echo esc_url_raw( rest_url( 'masterstudy-lms/v2' ) ); ?>',
		wpUrl: '<?php echo esc_url_raw( rest_url( 'wp/v2' ) ); ?>',
		nonce: '<?php echo esc_html( wp_create_nonce( 'wp_rest' ) ); ?>',
		wp_date_format: '<?php echo esc_attr( get_option( 'date_format' ) ); ?>',
		wp_time_format: '<?php echo esc_attr( get_option( 'time_format' ) ); ?>',
		locale: '<?php echo esc_attr( get_locale() ); ?>',
	};
	<?php if ( function_exists( 'pll_current_language' ) ) { ?>
	window.lmsApiSettings.lang = '<?php echo esc_js( pll_current_language() ); ?>';
	<?php } ?>

	<?php if ( ! empty( $wp_referer ) ) : ?>
	window.lmsApiSettings.prevUrl = '<?php echo esc_url_raw( $wp_referer ); ?>';
	<?php endif; ?>
</script>
<?php
$scripts->print_translations( 'ms-lms-course-builder-translations' );

if ( ! class_exists( '\_WP_Editors', false ) ) {
	require ABSPATH . WPINC . '/class-wp-editor.php';
}
?>
<script type="module" src="<?php echo esc_url( $entry_script_url ); // phpcs:ignore ?>"></script>
<script src="<?php echo esc_url( $ms_lms_get_asset_src( $scripts->registered['ms-lms-course-builder-translations'], $scripts ) ); // phpcs:ignore ?>"></script>
<?php
if ( ! empty( $additional_scripts ) ) {
	foreach ( $additional_scripts as $script_url ) {
		?>
		<script src="<?php echo esc_url( $script_url ); // phpcs:ignore ?>"></script>
		<?php
	}
}
?>
</body>
</html>
