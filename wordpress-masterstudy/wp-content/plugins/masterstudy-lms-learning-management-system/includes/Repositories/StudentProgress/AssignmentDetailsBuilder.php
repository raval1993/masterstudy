<?php

namespace MasterStudy\Lms\Repositories\StudentProgress;

use MasterStudy\Lms\Plugin\PostType;

final class AssignmentDetailsBuilder {
	private ProgressHelper $helper;

	public function __construct( ProgressHelper $helper ) {
		$this->helper = $helper;
	}

	public function build( int $course_id, int $student_id, int $assignment_id ): array {
		$query = new \WP_Query(
			array(
				'post_type'      => PostType::USER_ASSIGNMENT,
				'post_status'    => array( 'pending', 'publish', 'draft' ),
				'posts_per_page' => -1,
				'meta_key'       => 'try_num',
				'orderby'        => 'meta_value_num',
				'order'          => 'ASC',
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => 'course_id',
						'value'   => $course_id,
						'compare' => '=',
					),
					array(
						'key'     => 'assignment_id',
						'value'   => $assignment_id,
						'compare' => '=',
					),
					array(
						'key'     => 'student_id',
						'value'   => $student_id,
						'compare' => '=',
					),
				),
			)
		);

		$attempts = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$post_id       = get_the_ID();
				$review_status = (string) get_post_meta( $post_id, 'status', true );
				$start_time    = get_post_meta( $post_id, 'start_time', true );
				$start_time    = is_numeric( $start_time ) ? (int) $start_time : 0;

				$attempts[] = array(
					'id'                     => $post_id,
					'attempt_number'         => (int) get_post_meta( $post_id, 'try_num', true ),
					'status'                 => $review_status,
					'post_status'            => (string) get_post_status( $post_id ),
					'title'                  => $this->helper->decode_progress_text( get_the_title( $post_id ) ),
					'content'                => $this->helper->normalize_rich_content( get_post_field( 'post_content', $post_id ) ),
					'grade'                  => (int) get_post_meta( $post_id, 'grade', true ),
					'review'                 => wp_kses_post( (string) get_post_meta( $post_id, 'editor_comment', true ) ),
					'student_attachments'    => $this->helper->normalize_assignment_attachments( $post_id, 'student_attachments' ),
					'instructor_attachments' => $this->helper->normalize_assignment_attachments( $post_id, 'instructor_attachments' ),
					'instructor'             => $this->helper->normalize_current_user_info( get_current_user_id() ),
					'created_at'             => $this->helper->format_assignment_datetime( $start_time ),
				);
			}

			wp_reset_postdata();
		}

		return array(
			'attempts' => $attempts,
		);
	}
}
