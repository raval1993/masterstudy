<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'masterstudy_lms_read_manifest' ) ) {
	/**
	 * Read Vite manifest for a specific app and cache it in-memory per request.
	 *
	 * @param string $app_slug App assets folder inside assets/react.
	 *
	 * @return array
	 */
	function masterstudy_lms_read_manifest( string $app_slug ): array {
		static $manifests = array();

		$app_slug = trim( (string) $app_slug );
		if ( empty( $app_slug ) || ! preg_match( '/^[a-z0-9_-]+$/', $app_slug ) ) {
			return array();
		}

		if ( isset( $manifests[ $app_slug ] ) ) {
			return $manifests[ $app_slug ];
		}

		$manifest_path = MS_LMS_PATH . '/assets/react/' . $app_slug . '/manifest.json';
		if ( file_exists( $manifest_path ) && is_readable( $manifest_path ) ) {
			$manifest_contents = file_get_contents( $manifest_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

			if ( false !== $manifest_contents ) {
				$decoded = json_decode( (string) $manifest_contents, true );

				if ( is_array( $decoded ) ) {
					$manifests[ $app_slug ] = $decoded;
					return $manifests[ $app_slug ];
				}
			}
		}

		$manifest_url = MS_LMS_URL . 'assets/react/' . $app_slug . '/manifest.json';
		$response     = function_exists( 'wp_remote_get' ) ? call_user_func( 'wp_remote_get', $manifest_url ) : false;
		$decoded      = array();

		if ( false !== $response ) {
			$is_wp_error = function_exists( 'is_wp_error' ) && (bool) is_wp_error( $response );
			if ( ! $is_wp_error ) {
				$response_body = function_exists( 'wp_remote_retrieve_body' ) ? wp_remote_retrieve_body( $response ) : '';
				$decoded       = json_decode( (string) $response_body, true );
			}
		}

		$manifests[ $app_slug ] = is_array( $decoded ) ? $decoded : array();

		return $manifests[ $app_slug ];
	}
}

if ( ! function_exists( 'masterstudy_lms_asset_url_from_manifest_file' ) ) {
	/**
	 * Convert Vite manifest file path to public plugin URL.
	 *
	 * @param string $app_slug App assets folder inside assets/react.
	 * @param string $file     File path from manifest.
	 *
	 * @return string
	 */
	function masterstudy_lms_asset_url_from_manifest_file( $app_slug, $file ): string {
		$app_slug = trim( (string) $app_slug );
		if ( empty( $app_slug ) || ! preg_match( '/^[a-z0-9_-]+$/', $app_slug ) ) {
			return '';
		}

		$file = ltrim( preg_replace( '#^static/#', '', (string) $file ), '/' );
		if ( empty( $file ) ) {
			return '';
		}

		$path_parts      = explode( '/', 'assets/react/' . $app_slug . '/' . $file );
		$normalized_path = array();

		foreach ( $path_parts as $path_part ) {
			if ( '' === $path_part || '.' === $path_part ) {
				continue;
			}

			if ( '..' === $path_part ) {
				if ( count( $normalized_path ) > 2 ) {
					array_pop( $normalized_path );
				}
				continue;
			}

			$normalized_path[] = $path_part;
		}

		return rtrim( MS_LMS_URL, '/' ) . '/' . implode( '/', $normalized_path );
	}
}

if ( ! function_exists( 'masterstudy_lms_resolve_manifest_assets' ) ) {
	/**
	 * Resolve entry file and imported chunks from Vite manifest.
	 *
	 * @param string $app_slug   App assets folder inside assets/react.
	 * @param string $entry_name Expected entry name (manifest name or file basename).
	 *
	 * @return array|null
	 */
	function masterstudy_lms_resolve_manifest_assets( string $app_slug, string $entry_name ): ?array {
		$manifest = masterstudy_lms_read_manifest( $app_slug );
		if ( empty( $manifest ) || empty( $entry_name ) ) {
			return null;
		}

		$entry_key = null;
		foreach ( $manifest as $key => $meta ) {
			if ( ! is_array( $meta ) || empty( $meta['isEntry'] ) ) {
				continue;
			}

			$manifest_name              = isset( $meta['name'] ) ? (string) $meta['name'] : '';
			$manifest_file              = isset( $meta['file'] ) ? basename( (string) $meta['file'], '.js' ) : '';
			$manifest_file_without_hash = preg_replace( '/\.[^.]+$/', '', $manifest_file );
			if ( $manifest_name === $entry_name || $manifest_file === $entry_name || $manifest_file_without_hash === $entry_name ) {
				$entry_key = (string) $key;
				break;
			}
		}

		if ( null === $entry_key || empty( $manifest[ $entry_key ]['file'] ) ) {
			return null;
		}

		$imports = array();
		$visited = array();
		$stack   = isset( $manifest[ $entry_key ]['imports'] ) && is_array( $manifest[ $entry_key ]['imports'] )
			? $manifest[ $entry_key ]['imports']
			: array();

		while ( ! empty( $stack ) ) {
			$import_key = array_pop( $stack );
			if ( isset( $visited[ $import_key ] ) ) {
				continue;
			}
			$visited[ $import_key ] = true;

			if ( empty( $manifest[ $import_key ]['file'] ) ) {
				continue;
			}

			$imports[] = masterstudy_lms_asset_url_from_manifest_file( $app_slug, $manifest[ $import_key ]['file'] );

			if ( isset( $manifest[ $import_key ]['imports'] ) && is_array( $manifest[ $import_key ]['imports'] ) ) {
				foreach ( $manifest[ $import_key ]['imports'] as $nested_import_key ) {
					$stack[] = $nested_import_key;
				}
			}
		}

		return array(
			'entry_url' => masterstudy_lms_asset_url_from_manifest_file( $app_slug, $manifest[ $entry_key ]['file'] ),
			'imports'   => array_values( array_unique( $imports ) ),
		);
	}
}
