<?php

namespace MasterStudy\Lms\Http\Serializers;

final class AdminReviewSerializer extends AbstractSerializer {

	/**
	 * @param \WP_Post $post
	 */
	public function toArray( $post ): array {
		$review_id = (int) $post->ID;
		$course_id = (int) get_post_meta( $review_id, 'review_course', true );
		$mark      = (int) get_post_meta( $review_id, 'review_mark', true );
		$author_id = (int) get_post_meta( $review_id, 'review_user', true );
		if ( ! $author_id ) {
			$author_id = (int) $post->post_author;
		}
		$author = get_userdata( $author_id );
		$course = $course_id ? get_post( $course_id ) : null;

		return array(
			'id'     => $review_id,
			'title'  => get_the_title( $review_id ),
			'status' => (string) $post->post_status,
			'date'   => \STM_LMS_Helpers::format_date( $post->post_date ),
			'course' => $course ? array(
				'id'    => $course_id,
				'title' => get_the_title( $course_id ),
			) : null,
			'user'   => array(
				'id'   => $author_id,
				'name' => $author ? (string) $author->display_name : '',
			),
			'mark'   => $mark,
		);
	}

	/**
	 * @param \WP_Post $post
	 */
	public function toDetailArray( $post ): array {
		$data            = $this->toArray( $post );
		$data['content'] = (string) $post->post_content;

		return $data;
	}
}
