<?php

namespace MasterStudy\Lms\Http\Serializers;

use MasterStudy\Lms\Utility\WpDate;

final class QuizListSerializer extends AbstractSerializer {

	/**
	 * @param \WP_Post $post
	 */
	public function toArray( $post ): array {
		$author_id = (int) $post->post_author;
		$author    = get_userdata( $author_id );
		$modified  = WpDate::format_gmt_for_site(
			(string) $post->post_modified_gmt,
			(string) $post->post_modified
		);

		return array(
			'id'                   => (int) $post->ID,
			'title'                => get_the_title( $post->ID ),
			'date'                 => (string) $post->post_date,
			'date_formatted'       => \STM_LMS_Helpers::format_date( $post->post_date ),
			'modified'             => $modified['datetime'],
			'modified_gmt'         => (string) $post->post_modified_gmt,
			'modified_formatted'   => $modified['formatted'],
			'status'               => (string) $post->post_status,
			'question_banks_count' => isset( $post->question_banks_count ) ? (int) $post->question_banks_count : 0,
			'questions_count'      => isset( $post->questions_count ) ? (int) $post->questions_count : 0,
			'linked_courses_count' => isset( $post->linked_courses_count ) ? (int) $post->linked_courses_count : 0,
			'author'               => array(
				'id'    => $author_id,
				'label' => $author ? (string) $author->user_login : '',
			),
		);
	}
}
