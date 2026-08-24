<?php

namespace MasterStudy\Lms\Utility;

/**
 * Captures HTML output and auto-discovers all CSS/JS assets
 * enqueued during template rendering.
 *
 * Useful for dynamically loading PHP templates in SPA contexts
 * where wp_enqueue_style/script calls don't deliver assets to the page.
 *
 * In REST API context, asset registration hooks (wp_enqueue_scripts,
 * admin_enqueue_scripts) don't fire, so assets enqueued by templates
 * won't have their src URLs registered. Pass registration callbacks
 * via $register_fns to ensure handles are resolved.
 */
class TemplateAssetCapture {

	/**
	 * Renders a template via callback and captures all newly enqueued assets.
	 *
	 * @param callable   $render_fn    Callback that renders the template.
	 * @param callable[] $register_fns Optional callbacks to register assets before capture
	 *                                 (e.g., functions that call wp_register_style/wp_register_script).
	 *                                 Needed in REST context where admin_enqueue_scripts doesn't fire.
	 *
	 * @return array{html: string, css_urls: array<string, string>, js_urls: array<string, string>}
	 */
	public static function capture( callable $render_fn, array $register_fns = array() ): array {
		// Ensure asset handles are registered (critical for REST API context)
		foreach ( $register_fns as $fn ) {
			call_user_func( $fn );
		}

		$wp_styles  = wp_styles();
		$wp_scripts = wp_scripts();

		$styles_before  = $wp_styles->queue;
		$scripts_before = $wp_scripts->queue;

		ob_start();
		call_user_func( $render_fn );
		$html = ob_get_clean();

		$new_style_handles  = array_diff( $wp_styles->queue, $styles_before );
		$new_script_handles = array_diff( $wp_scripts->queue, $scripts_before );

		$css_urls = self::resolve_urls( $wp_styles, $new_style_handles );
		$js_urls  = self::resolve_urls( $wp_scripts, $new_script_handles );

		return array(
			'html'     => $html,
			'css_urls' => $css_urls,
			'js_urls'  => $js_urls,
		);
	}

	/**
	 * Resolves enqueued handles to their source URLs.
	 *
	 * @param \WP_Dependencies $registry wp_styles() or wp_scripts() instance.
	 * @param array            $handles  Array of handle names.
	 *
	 * @return array<string, string> Handle => URL map.
	 */
	private static function resolve_urls( $registry, array $handles ): array {
		$urls = array();

		foreach ( $handles as $handle ) {
			if ( isset( $registry->registered[ $handle ] ) ) {
				$src = $registry->registered[ $handle ]->src;

				if ( $src ) {
					if ( 0 !== strpos( $src, 'http' ) && 0 !== strpos( $src, '//' ) ) {
						$src = site_url( $src );
					}

					$urls[ $handle ] = $src;
				}
			}
		}

		return $urls;
	}
}
