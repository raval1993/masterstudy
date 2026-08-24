<?php

namespace MasterStudy\Lms\Http\Serializers;

use MasterStudy\Lms\Utility\WpDate;

final class LessonListSerializer extends AbstractSerializer {

	/**
	 * @param \WP_Post $post
	 */
	public function toArray( $post ): array {
		$author_id = (int) $post->post_author;
		$author    = isset( $post->author_label ) ? (string) $post->author_label : null;
		$type      = isset( $post->lesson_type ) ? (string) $post->lesson_type : null;

		if ( null === $author ) {
			$user   = get_userdata( $author_id );
			$author = $user ? (string) $user->user_login : '';
		}

		if ( null === $type ) {
			$type = (string) get_post_meta( $post->ID, 'type', true );
		}

		$modified = WpDate::format_gmt_for_site(
			(string) $post->post_modified_gmt,
			(string) $post->post_modified
		);

		return array(
			'id'                   => (int) $post->ID,
			'title'                => get_the_title( $post->ID ),
			'type'                 => ! empty( $type ) ? $type : 'text',
			'date'                 => (string) $post->post_date,
			'date_formatted'       => \STM_LMS_Helpers::format_date( $post->post_date ),
			'modified'             => $modified['datetime'],
			'modified_gmt'         => (string) $post->post_modified_gmt,
			'modified_formatted'   => $modified['formatted'],
			'status'               => (string) $post->post_status,
			'view_url'             => $this->get_view_url( $post ),
			'comment_count'        => (int) $post->comment_count,
			'linked_courses_count' => isset( $post->linked_courses_count ) ? (int) $post->linked_courses_count : 0,
			'author'               => array(
				'id'    => $author_id,
				'label' => $author,
			),
		);
	}

	private function get_view_url( \WP_Post $post ): string {
		$post_type = get_post_type_object( $post->post_type );

		if ( ! $post_type || ! is_post_type_viewable( $post_type ) ) {
			return '';
		}

		if ( in_array( $post->post_status, array( 'publish', 'private' ), true ) ) {
			return esc_url_raw( (string) get_permalink( $post ) );
		}

		if ( 'trash' === $post->post_status || ! current_user_can( 'edit_post', $post->ID ) ) {
			return '';
		}

		return esc_url_raw( (string) get_preview_post_link( $post ) );
	}
}
