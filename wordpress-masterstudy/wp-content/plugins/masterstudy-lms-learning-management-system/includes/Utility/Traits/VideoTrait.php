<?php

namespace MasterStudy\Lms\Utility\Traits;

use MasterStudy\Lms\Enums\LessonVideoType;

trait VideoTrait {
	private static array $video_fields_mapping = array(
		LessonVideoType::EMBED         => array(
			'embed_ctx' => 'lesson_embed_ctx',
		),
		LessonVideoType::EXT_LINK      => array(
			'external_url'            => 'lesson_ext_link_url',
			'video_poster'            => 'lesson_video_poster',
			'video_required_progress' => 'video_required_progress',
		),
		LessonVideoType::HTML          => array(
			'video'                   => 'lesson_video',
			'video_poster'            => 'lesson_video_poster',
			'video_width'             => 'lesson_video_width',
			'video_required_progress' => 'video_required_progress',
		),
		LessonVideoType::PRESTO_PLAYER => array(
			'presto_player_idx'       => 'presto_player_idx',
			'video_required_progress' => 'video_required_progress',
		),
		LessonVideoType::VDOCIPHER     => array(
			'vdocipher_id'            => 'vdocipher_id',
			'video_required_progress' => 'video_required_progress',
		),
		LessonVideoType::SHORTCODE     => array(
			'shortcode' => 'lesson_shortcode',
		),
		LessonVideoType::VIMEO         => array(
			'video_poster'            => 'lesson_video_poster',
			'vimeo_url'               => 'lesson_vimeo_url',
			'video_required_progress' => 'video_required_progress',
		),
		LessonVideoType::FILE          => array(
			'file' => 'file',
		),
		LessonVideoType::YOUTUBE       => array(
			'video_poster'            => 'lesson_video_poster',
			'youtube_url'             => 'lesson_youtube_url',
			'video_required_progress' => 'video_required_progress',
		),
	);

	private function hydrate_video( array $post, array $meta, $post_type ): array {
		$key_type = ! empty( $post['video_type'] ) ? $post['video_type'] : ( ! empty( $post['audio_type'] ) ? $post['audio_type'] : '' );

		if ( ! empty( $key_type ) ) {
			foreach ( $this->get_video_fields_mapping( $key_type ) as $prop => $meta_key ) {
				if ( 'stm-questions' === $post_type ) {
					$meta_key = str_replace( 'lesson_', 'question_', $meta_key );
				}
				$value = $this->cast( $meta_key, $meta[ $meta_key ][0] ?? null );
				if ( in_array( $prop, array( 'video', 'video_poster', 'file' ), true ) && $value ) {
					$value = $this->get_attachment( (int) $value );
				}

				$post[ $prop ] = $value;
			}
		} else {
			unset( $post['video_type'] );
		}

		unset( $post['video_captions_ids'] );

		return $post;
	}

	private function get_attachment( ?int $attachment_id ): ?array {
		$attachment = get_post( $attachment_id );

		if ( $attachment ) {
			return array(
				'id'    => $attachment->ID,
				'title' => $attachment->post_title,
				'type'  => get_post_mime_type( $attachment->ID ),
				'url'   => wp_get_attachment_url( $attachment->ID ),
			);
		}

		return null;
	}

	/**
	 * @return array<string, string>
	 */
	private function get_video_fields_mapping( $video_type ): array {
		return self::$video_fields_mapping[ $video_type ] ?? array();
	}
}
