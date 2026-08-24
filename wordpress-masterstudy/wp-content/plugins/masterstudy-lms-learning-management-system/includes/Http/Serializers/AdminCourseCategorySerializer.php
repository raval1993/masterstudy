<?php

namespace MasterStudy\Lms\Http\Serializers;

use MasterStudy\Lms\Plugin\Taxonomy;

final class AdminCourseCategorySerializer extends AbstractSerializer {

	private static ?array $native_templates = null;

	/**
	 * @param \WP_Term $data
	 *
	 * @return array
	 */
	public function toArray( $data ): array {
		$course_image      = get_term_meta( $data->term_id, 'course_image', true );
		$course_page_style = get_term_meta( $data->term_id, 'course_page_style', true );
		$course_icon       = get_term_meta( $data->term_id, 'course_icon', true );
		$course_color      = get_term_meta( $data->term_id, 'course_color', true );
		$parent_term       = $data->parent ? get_term( $data->parent, Taxonomy::COURSE_CATEGORY ) : null;

		if ( empty( $course_page_style ) ) {
			$course_page_style = '';
		}

		if ( empty( $course_icon ) ) {
			$course_icon = '';
		}

		if ( empty( $course_color ) ) {
			$course_color = '';
		}

		return array(
			'id'                          => $data->term_id,
			'term_id'                     => $data->term_id,
			'name'                        => html_entity_decode( $data->name, ENT_QUOTES, 'UTF-8' ),
			'slug'                        => $data->slug,
			'description'                 => $data->description,
			'parent'                      => $data->parent,
			'parent_name'                 => $parent_term instanceof \WP_Term
				? html_entity_decode( $parent_term->name, ENT_QUOTES, 'UTF-8' )
				: null,
			'count'                       => $data->count,
			'course_page_style'           => $course_page_style,
			'course_page_style_image_url' => $this->get_template_image_url( $course_page_style ),
			'course_image'                => $course_image ? (int) $course_image : null,
			'course_image_url'            => $course_image
				? esc_url( (string) wp_get_attachment_image_url( (int) $course_image, 'full' ) )
				: null,
			'course_icon'                 => $course_icon,
			'course_color'                => $course_color,
		);
	}

	private function get_template_image_url( string $style ): ?string {
		if ( empty( $style ) || 'none' === $style ) {
			return null;
		}

		$native_templates = $this->get_native_templates_cached();

		foreach ( $native_templates as $template ) {
			if ( $template['name'] === $style ) {
				return esc_url( STM_LMS_URL . 'assets/img/course/' . $style . '.png' );
			}
		}

		if ( function_exists( 'masterstudy_lms_get_my_templates' ) ) {
			$my_templates = masterstudy_lms_get_my_templates();

			foreach ( $my_templates as $template ) {
				if ( $template['name'] === $style && ! empty( $template['thumbnail'] ) ) {
					return esc_url( $template['thumbnail'] );
				}
			}
		}

		return null;
	}

	private function get_native_templates_cached(): array {
		if ( null === self::$native_templates ) {
			self::$native_templates = masterstudy_lms_get_native_templates();
		}

		return self::$native_templates;
	}
}
