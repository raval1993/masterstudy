<?php

namespace MasterStudy\Lms\Http\Serializers;

final class QuestionCategorySerializer extends AbstractSerializer {

	/**
	 * @param \WP_Term $data
	 *
	 * @return array
	 */
	public function toArray( $data ): array {
		return array(
			'id'               => $data->term_id,
			'term_id'          => $data->term_id,
			'name'             => html_entity_decode( $data->name ),
			'slug'             => $data->slug,
			'term_group'       => $data->term_group,
			'term_taxonomy_id' => $data->term_taxonomy_id,
			'taxonomy'         => $data->taxonomy,
			'description'      => $data->description,
			'parent'           => $data->parent,
			'parent_name'      => $data->parent ? html_entity_decode( get_term( $data->parent, \MasterStudy\Lms\Plugin\Taxonomy::QUESTION_CATEGORY )->name ?? '' ) : null,
			'count'            => $data->count,
		);
	}
}
