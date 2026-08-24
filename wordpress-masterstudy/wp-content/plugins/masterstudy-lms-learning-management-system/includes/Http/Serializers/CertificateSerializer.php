<?php

namespace MasterStudy\Lms\Http\Serializers;

final class CertificateSerializer extends AbstractSerializer {

	/**
	 * @param \WP_Post $data
	 *
	 * @return array
	 */
	public function toArray( $data ): array {
		return array(
			'id'        => $data->ID,
			'label'     => self::truncate_text( html_entity_decode( $data->post_title ) ),
			'image_url' => self::get_certificate_preview( $data->ID ),
		);
	}

	public function truncate_text( $text, $max_length = 20 ) {
		if ( strlen( $text ) > $max_length ) {
			return substr( $text, 0, 17 ) . '...';
		}

		return $text;
	}

	public function get_certificate_preview( $ID ) {
		$preview_certificate = get_post_meta( $ID, 'certificate_preview', true );

		if ( empty( $preview_certificate ) ) {
			$preview_certificate = STM_LMS_URL . 'assets/img/pro-features/certifiacate_placeholder.png';
		}

		return $preview_certificate;
	}
}
