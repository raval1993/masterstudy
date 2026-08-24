<?php

namespace MasterStudy\Lms\Http\Controllers\CourseCategory;

use WP_REST_Response;

final class GetIconsController {
	public function __invoke(): WP_REST_Response {
		$icons_file = STM_WPCFTO_PATH . '/helpers/icons.php';

		if ( ! function_exists( 'stm_wpcfto_new_fa_icons' ) && file_exists( $icons_file ) ) {
			require_once $icons_file;
		}

		$fa_icons  = function_exists( 'stm_wpcfto_new_fa_icons' ) ? stm_wpcfto_new_fa_icons() : array();
		$all_icons = function_exists( 'stm_wpcfto_add_vc_icons_linear' ) ? stm_wpcfto_add_vc_icons_linear( $fa_icons ) : $fa_icons;

		if ( ! empty( $all_icons['Linear'] ) && is_array( $all_icons['Linear'] ) ) {
			$all_icons['Linear'] = array_map(
				function ( $icon ) {
					$converted = array();
					foreach ( $icon as $class => $name ) {
						$converted[ str_replace( 'lnr-', 'lnricons-', $class ) ] = $name;
					}
					return $converted;
				},
				$all_icons['Linear']
			);
		}

		$selection_file = STM_LMS_PATH . '/assets/icons/selection.json';

		if ( is_readable( $selection_file ) ) {
			$selection_content = file_get_contents( $selection_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local plugin asset file.
			$ms_data           = false !== $selection_content ? json_decode( $selection_content, true ) : array();

			if ( ! empty( $ms_data['icons'] ) && is_array( $ms_data['icons'] ) ) {
				$all_icons['MasterStudy'] = array();

				foreach ( $ms_data['icons'] as $icon ) {
					if ( ! empty( $icon['properties']['name'] ) ) {
						$icon_name                  = $icon['properties']['name'];
						$all_icons['MasterStudy'][] = array( "stmlms-{$icon_name}" => $icon_name );
					}
				}
			}
		}

		return new WP_REST_Response( array( 'icons' => $all_icons ) );
	}
}
