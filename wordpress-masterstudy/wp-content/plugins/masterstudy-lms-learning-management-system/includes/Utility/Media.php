<?php

namespace MasterStudy\Lms\Utility;

class Media {
	public static function create_attachment_from_url( string $url, string $custom_title = '', bool $return_attachment = false ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$url              = html_entity_decode( $url );
		$attachment_title = $custom_title;

		if ( empty( $attachment_title ) ) {
			$filename         = basename( $url );
			$attachment_title = ucwords( str_replace( '-', ' ', pathinfo( $filename, PATHINFO_FILENAME ) ) );
		}

		$attachment_id = media_sideload_image(
			$url,
			0,
			$attachment_title,
			'id'
		);

		if ( is_wp_error( $attachment_id ) ) {
			return $return_attachment
				? $attachment_id
				: array(
					'error' => $attachment_id->get_error_message(),
				);
		}

		$attachment = array(
			'ID'          => $attachment_id,
			'post_status' => 'inherit',
		);

		wp_update_post( $attachment );

		update_post_meta( $attachment_id, '_wp_attachment_image_alt', wp_slash( $attachment_title ) );

		// Rename the file
		if ( ! empty( $custom_title ) ) {
			$attached_file = get_attached_file( $attachment_id, true );
			$file_type     = wp_check_filetype( $attached_file );
			$filename      = sanitize_file_name( $custom_title ) . '.' . $file_type['ext'];
			if ( ! empty( $filename ) ) {
				$file     = $attached_file;
				$dir      = dirname( $file );
				$new_file = $dir . '/' . $filename;

				if ( file_exists( $new_file ) ) {
					$new_file = $dir . '/' . uniqid() . '-' . $filename;
				}

				rename( $file, $new_file );
				update_attached_file( $attachment_id, $new_file );
			}
		}

		$attachment = get_post( $attachment_id );

		if ( $return_attachment ) {
			return $attachment;
		}

		return array(
			'id'       => $attachment_id,
			'title'    => $attachment->post_title,
			'url'      => wp_get_attachment_url( $attachment_id ),
			'type'     => $attachment->post_mime_type,
			'date'     => gmdate( 'Y-m-d', strtotime( $attachment->post_date ) ),
			'modified' => gmdate( 'Y-m-d', strtotime( $attachment->post_modified ) ),
			'size'     => size_format( filesize( get_attached_file( $attachment_id, true ) ) ),
		);
	}
}
