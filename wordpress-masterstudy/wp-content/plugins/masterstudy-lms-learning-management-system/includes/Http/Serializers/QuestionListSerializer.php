<?php

namespace MasterStudy\Lms\Http\Serializers;

use MasterStudy\Lms\Plugin\Taxonomy;

final class QuestionListSerializer extends AbstractSerializer {

	/**
	 * @param \WP_Post $post
	 *
	 * @return array
	 */
	public function toArray( $post ): array {
		$terms = wp_get_post_terms( $post->ID, Taxonomy::QUESTION_CATEGORY );
		$term  = is_array( $terms ) && ! empty( $terms ) ? $terms[0] : null;

		return array(
			'id'       => (int) $post->ID,
			'title'    => get_the_title( $post->ID ),
			'type'     => (string) get_post_meta( $post->ID, 'type', true ),
			'category' => $term ? array(
				'id'   => (int) $term->term_id,
				'name' => (string) $term->name,
				'slug' => (string) $term->slug,
			) : null,
			'date'           => (string) $post->post_date,
			'date_formatted' => \STM_LMS_Helpers::format_date( $post->post_date ),
			'status'         => (string) $post->post_status,
		);
	}
}
