<?php

namespace MasterStudy\Lms\Http\Serializers;

final class LessonAuthorSerializer extends AbstractSerializer {

	/**
	 * @param \WP_User $author
	 */
	public function toArray( $author ): array {
		return array(
			'id'    => (int) $author->ID,
			'label' => (string) $author->user_login,
		);
	}
}
