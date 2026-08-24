<?php

namespace MasterStudy\Lms\Http\Serializers;

final class InstructorCoursesSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		return array(
			'courses'     => $data['courses'] ?? array(),
			'pagination'  => $data['pagination'] ?? '',
			'total_pages' => intval( $data['total_pages'] ?? 1 ),
			'total_posts' => intval( $data['total_posts'] ?? 0 ),
		);
	}
}
