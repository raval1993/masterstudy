<?php

namespace MasterStudy\Lms\Repositories\StudentProgress;

use STM_LMS_Helpers;

final class ProgressHelper {
	public function normalize_assignment_attachments( int $assignment_post_id, string $meta_key ): array {
		if ( ! class_exists( '\STM_LMS_Assignments' ) || ! method_exists( '\STM_LMS_Assignments', 'get_draft_attachments' ) ) {
			return array();
		}

		$attachments = \STM_LMS_Assignments::get_draft_attachments( $assignment_post_id, $meta_key );
		if ( empty( $attachments ) || ! is_array( $attachments ) ) {
			return array();
		}

		return array_values(
			array_filter(
				array_map(
					function( $attachment ) {
						return $this->normalize_attachment( $attachment );
					},
					$attachments
				)
			)
		);
	}

	public function normalize_attachment( $attachment ): ?array {
		$attachment_id = 0;
		$title         = '';
		$url           = '';
		$format        = 'unknown';
		$size          = '';
		$preview_url   = '';

		if ( $attachment instanceof \WP_Post ) {
			$attachment_id = (int) $attachment->ID;
			$title         = $attachment->post_title;
			$url           = wp_get_attachment_url( $attachment_id );
			$url           = false !== $url ? $url : '';
		} elseif ( is_array( $attachment ) ) {
			$attachment_id = (int) ( $attachment['ID'] ?? $attachment['id'] ?? 0 );
			$title         = (string) ( $attachment['title'] ?? '' );
			$url           = (string) ( $attachment['url'] ?? '' );
		} elseif ( is_numeric( $attachment ) ) {
			$attachment_id = (int) $attachment;
			$title         = get_the_title( $attachment_id );
			$url           = wp_get_attachment_url( $attachment_id );
			$url           = false !== $url ? $url : '';
		}

		if ( $attachment_id <= 0 && empty( $url ) ) {
			return null;
		}

		if ( empty( $title ) && $attachment_id > 0 ) {
			$title = get_the_title( $attachment_id );
		}

		if ( empty( $url ) && $attachment_id > 0 ) {
			$url = wp_get_attachment_url( $attachment_id );
			$url = false !== $url ? $url : '';
		}

		if ( $attachment_id > 0 && function_exists( 'ms_plugin_attachment_data' ) ) {
			$attachment_post = get_post( $attachment_id );

			if ( $attachment_post instanceof \WP_Post ) {
				$file = ms_plugin_attachment_data( $attachment_post );

				if ( ! empty( $file['file_title'] ) ) {
					$title = (string) $file['file_title'];
				}

				if ( ! empty( $file['url'] ) ) {
					$url = (string) $file['url'];
				}

				if ( ! empty( $file['current_format'] ) ) {
					$format = (string) $file['current_format'];
				}

				if ( isset( $file['filesize'], $file['filesize_label'] ) ) {
					$size = trim( $file['filesize'] . ' ' . $file['filesize_label'] );
				}
			}
		}

		if ( 'unknown' === $format ) {
			$format = $this->infer_attachment_format( $url );
		}

		if ( 'img' === $format && $attachment_id > 0 ) {
			$preview = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
			if ( ! empty( $preview[0] ) ) {
				$preview_url = (string) $preview[0];
			}
		}

		return array(
			'id'          => $attachment_id,
			'title'       => (string) $title,
			'url'         => esc_url_raw( $url ),
			'format'      => (string) $format,
			'size'        => (string) $size,
			'preview_url' => esc_url_raw( $preview_url ),
			'icon_url'    => esc_url_raw( $this->get_attachment_icon_url( $format ) ),
		);
	}

	private function infer_attachment_format( string $url ): string {
		if ( empty( $url ) || ! function_exists( 'ms_plugin_files_formats' ) ) {
			return 'unknown';
		}

		$path      = wp_parse_url( $url, PHP_URL_PATH );
		$extension = strtolower( (string) pathinfo( (string) $path, PATHINFO_EXTENSION ) );

		if ( empty( $extension ) ) {
			return 'unknown';
		}

		foreach ( ms_plugin_files_formats() as $type => $extensions ) {
			if ( in_array( $extension, $extensions, true ) ) {
				return (string) $type;
			}
		}

		return 'unknown';
	}

	private function get_attachment_icon_url( string $format ): string {
		$allowed_formats = array( 'archive', 'audio', 'excel', 'img', 'pdf', 'powerpoint', 'unknown', 'video', 'word' );
		$format          = in_array( $format, $allowed_formats, true ) ? $format : 'unknown';

		return STM_LMS_URL . "assets/icons/files/new/{$format}.svg";
	}

	public function normalize_current_user_info( int $user_id ): array {
		$user = \STM_LMS_User::get_current_user( $user_id );

		return array(
			'id'         => $user_id,
			'login'      => (string) ( $user['login'] ?? '' ),
			'avatar_url' => $this->extract_avatar_url( (string) ( $user['avatar'] ?? '' ) ),
		);
	}

	public function normalize_rich_content( string $content ): string {
		return wp_kses_post( apply_filters( 'the_content', $content ) );
	}

	public function format_assignment_datetime( int $timestamp ): array {
		if ( $timestamp <= 0 ) {
			return array(
				'date' => '',
				'time' => '',
			);
		}

		if ( $timestamp > 9999999999 ) {
			$timestamp = (int) floor( $timestamp / 1000 );
		}

		$formatted = STM_LMS_Helpers::format_date( gmdate( 'Y-m-d H:i:s', $timestamp ) );

		return array(
			'date' => $formatted['date'] ?? '',
			'time' => $formatted['time'] ?? '',
		);
	}

	public function extract_avatar_url( string $avatar ): string {
		if ( empty( $avatar ) ) {
			return '';
		}

		if ( preg_match( '/src=["\']([^"\']+)["\']/', $avatar, $matches ) ) {
			return esc_url_raw( html_entity_decode( $matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
		}

		return esc_url_raw( html_entity_decode( $avatar, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
	}

	public function decode_progress_text( string $value ): string {
		$value = html_entity_decode( wp_strip_all_tags( $value ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		if ( str_contains( $value, '%' ) ) {
			$value = rawurldecode( $value );
		}

		return trim( $value );
	}
}
