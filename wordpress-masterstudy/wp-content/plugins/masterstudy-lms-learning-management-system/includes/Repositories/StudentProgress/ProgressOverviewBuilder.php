<?php

namespace MasterStudy\Lms\Repositories\StudentProgress;

final class ProgressOverviewBuilder {
	private ProgressHelper $helper;

	public function __construct( ProgressHelper $helper ) {
		$this->helper = $helper;
	}

	public function build( int $course_id, int $student_id, array $progress ): array {
		return array(
			'course'    => array(
				'id'    => $course_id,
				'title' => $this->helper->decode_progress_text( $progress['course_title'] ?? get_the_title( $course_id ) ),
			),
			'student'   => $this->normalize_progress_user( $progress['user'] ?? array(), $student_id ),
			'summary'   => array(
				'progress_percent' => (int) ( $progress['progress_percent'] ?? 0 ),
			),
			'sections'  => array_values(
				array_map(
					function( $section ) {
						return $this->normalize_progress_section( $section );
					},
					$progress['sections'] ?? array()
				)
			),
			'materials' => array_values(
				array_map(
					function( $material ) {
						return $this->normalize_progress_material( $material );
					},
					$progress['materials'] ?? array()
				)
			),
		);
	}

	private function normalize_progress_section( array $section ): array {
		return array(
			'id'    => (int) ( $section['id'] ?? 0 ),
			'title' => $this->helper->decode_progress_text( $section['title'] ?? '' ),
			'order' => (int) ( $section['order'] ?? 0 ),
		);
	}

	private function normalize_progress_material( array $material ): array {
		$type = ! empty( $material['type'] ) ? (string) $material['type'] : 'lesson';

		return array(
			'id'          => (int) ( $material['post_id'] ?? 0 ),
			'post_id'     => (int) ( $material['post_id'] ?? 0 ),
			'section_id'  => (int) ( $material['section_id'] ?? 0 ),
			'order'       => (int) ( $material['order'] ?? 0 ),
			'title'       => $this->helper->decode_progress_text( $material['title'] ?? '' ),
			'type'        => $type,
			'post_type'   => (string) ( $material['post_type'] ?? '' ),
			'completed'   => rest_sanitize_boolean( $material['completed'] ?? false ),
			'progress'    => (int) ( $material['progress'] ?? 0 ),
			'duration'    => (string) ( $material['duration'] ?? '' ),
			'questions'   => (string) ( $material['questions'] ?? '' ),
			'locked'      => rest_sanitize_boolean( $material['locked'] ?? false ),
			'expandable'  => in_array( $type, array( 'assignment', 'quiz' ), true ),
			'has_preview' => rest_sanitize_boolean( $material['has_preview'] ?? false ),
		);
	}

	private function normalize_progress_user( array $user, int $student_id ): array {
		$user_data = get_userdata( $student_id );

		return array(
			'id'         => (int) ( $user['id'] ?? $student_id ),
			'login'      => (string) ( $user['login'] ?? ( $user_data ? $user_data->user_login : '' ) ),
			'email'      => (string) ( $user['email'] ?? ( $user_data ? $user_data->user_email : '' ) ),
			'avatar_url' => $this->helper->extract_avatar_url( (string) ( $user['avatar'] ?? '' ) ),
			'url'        => (string) ( $user['url'] ?? '' ),
		);
	}
}
