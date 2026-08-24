<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Plugin\PostType;
use WP_Query;

final class AdminReviewRepository {
	private const MAX_PER_PAGE     = 100;
	private const ALLOWED_STATUSES = array( 'publish', 'pending', 'draft' );

	public function get_list( array $params ): array {
		$per_page = min( self::MAX_PER_PAGE, max( 1, (int) ( $params['per_page'] ?? 10 ) ) );
		$page     = max( 1, (int) ( $params['page'] ?? 1 ) );
		$search   = isset( $params['search'] ) ? trim( (string) $params['search'] ) : '';
		$status   = isset( $params['status'] ) ? (string) $params['status'] : 'any';

		$allowed_status = in_array( $status, self::ALLOWED_STATUSES, true ) ? $status : 'any';

		$query_args = array(
			'post_type'              => PostType::REVIEW,
			'post_status'            => 'any' === $allowed_status ? self::ALLOWED_STATUSES : $allowed_status,
			'ignore_sticky_posts'    => true,
			'posts_per_page'         => $per_page,
			'paged'                  => $page,
			'no_found_rows'          => false,
			'update_post_term_cache' => false,
		);

		if ( '' !== $search ) {
			$query_args['s'] = $search;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			$course_ids = $this->get_current_instructor_course_ids();

			$query_args['meta_query'] = array(
				array(
					'key'     => 'review_course',
					'value'   => ! empty( $course_ids ) ? $course_ids : array( 0 ),
					'compare' => 'IN',
				),
			);
		}

		$date_range = isset( $params['date_range'] ) ? trim( (string) $params['date_range'] ) : '';
		if ( '' !== $date_range ) {
			$dates = explode( ',', $date_range );
			$from  = ! empty( $dates[0] ) ? trim( $dates[0] ) : '';
			$end   = ! empty( $dates[1] ) ? trim( $dates[1] ) : '';

			if ( $from || $end ) {
				$date_query = array(
					'inclusive' => true,
					'column'    => 'post_date',
				);

				if ( $from ) {
					$date_query['after'] = $from . ' 00:00:00';
				}

				if ( $end ) {
					$date_query['before'] = $end . ' 23:59:59';
				}

				$query_args['date_query'] = array( $date_query );
			}
		}

		$sort = isset( $params['sort'] ) ? trim( (string) $params['sort'] ) : '';
		if ( '' !== $sort && class_exists( 'STM_LMS_Helpers' ) ) {
			$sort_params = \STM_LMS_Helpers::get_sort_params_by_string( $sort );
			$key         = $sort_params['key'] ?? '';
			$direction   = strtoupper( (string) ( $sort_params['direction'] ?? 'desc' ) );
			$direction   = in_array( $direction, array( 'ASC', 'DESC' ), true ) ? $direction : 'DESC';

			switch ( $key ) {
				case 'title':
					$query_args['orderby'] = 'title';
					$query_args['order']   = $direction;
					break;
				case 'date':
					$query_args['orderby'] = 'date';
					$query_args['order']   = $direction;
					break;
				case 'status':
					$query_args['orderby'] = 'post_status';
					$query_args['order']   = $direction;
					break;
				default:
					$query_args['orderby'] = 'date';
					$query_args['order']   = 'DESC';
					break;
			}
		} else {
			$query_args['orderby'] = 'date';
			$query_args['order']   = 'DESC';
		}

		$query = new WP_Query( $query_args );

		$this->prime_review_caches( $query->posts );

		return array(
			'posts'        => $query->posts,
			'pages'        => (int) $query->max_num_pages,
			'current_page' => $page,
			'total'        => (int) $query->found_posts,
		);
	}

	public function get_review( int $review_id ): ?\WP_Post {
		$post = get_post( $review_id );

		if ( ! $post || PostType::REVIEW !== $post->post_type ) {
			return null;
		}

		if ( ! $this->current_user_can_manage_review( $review_id ) ) {
			return null;
		}

		return $post;
	}

	public function create_review( array $data ): int {
		if ( ! $this->current_user_can_manage_course( absint( $data['course_id'] ?? 0 ) ) ) {
			return 0;
		}

		$student_id = absint( $data['student_id'] ?? 0 );

		$post_data = array(
			'post_type'    => PostType::REVIEW,
			'post_title'   => sanitize_text_field( $data['title'] ?? '' ),
			'post_content' => wp_kses_post( $data['content'] ?? '' ),
			'post_status'  => in_array( $data['status'] ?? '', self::ALLOWED_STATUSES, true ) ? $data['status'] : 'publish',
			'post_author'  => $student_id,
		);

		$post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $post_id ) ) {
			return 0;
		}

		if ( ! empty( $data['course_id'] ) ) {
			update_post_meta( $post_id, 'review_course', absint( $data['course_id'] ) );
		}

		if ( $student_id ) {
			update_post_meta( $post_id, 'review_user', $student_id );
		}

		if ( ! empty( $data['mark'] ) ) {
			update_post_meta( $post_id, 'review_mark', absint( $data['mark'] ) );
		}

		$this->refresh_course_rating( absint( $data['course_id'] ) );

		return $post_id;
	}

	public function update_review( int $review_id, array $data ): bool {
		$post = $this->get_review( $review_id );

		if ( ! $post ) {
			return false;
		}

		$affected_course_ids = array(
			absint( get_post_meta( $review_id, 'review_course', true ) ),
		);

		if ( isset( $data['course_id'] ) && ! $this->current_user_can_manage_course( absint( $data['course_id'] ) ) ) {
			return false;
		}

		$post_data = array(
			'ID' => $review_id,
		);

		if ( isset( $data['content'] ) ) {
			$post_data['post_content'] = wp_kses_post( $data['content'] );
		}

		if ( isset( $data['status'] ) && in_array( $data['status'], self::ALLOWED_STATUSES, true ) ) {
			$post_data['post_status'] = $data['status'];
		}

		if ( isset( $data['student_id'] ) ) {
			$student_id = absint( $data['student_id'] );

			if ( $student_id && get_userdata( $student_id ) ) {
				$post_data['post_author'] = $student_id;
			}
		}

		if ( isset( $data['title'] ) ) {
			$post_data['post_title'] = sanitize_text_field( $data['title'] );
		}

		$result = wp_update_post( $post_data, true );

		if ( is_wp_error( $result ) ) {
			return false;
		}

		if ( isset( $data['course_id'] ) ) {
			update_post_meta( $review_id, 'review_course', absint( $data['course_id'] ) );
		}

		if ( isset( $post_data['post_author'] ) ) {
			update_post_meta( $review_id, 'review_user', absint( $post_data['post_author'] ) );
		}

		if ( isset( $data['mark'] ) ) {
			update_post_meta( $review_id, 'review_mark', absint( $data['mark'] ) );
		}

		$affected_course_ids[] = absint( get_post_meta( $review_id, 'review_course', true ) );

		foreach ( array_unique( array_filter( $affected_course_ids ) ) as $course_id ) {
			$this->refresh_course_rating( $course_id );
		}

		return true;
	}

	public function delete_review( int $review_id ): bool {
		$post = $this->get_review( $review_id );

		if ( ! $post ) {
			return false;
		}

		return false !== wp_delete_post( $review_id, true );
	}

	public function get_course_students( int $course_id, array $params ): array {
		global $wpdb;

		if ( ! $this->current_user_can_manage_course( $course_id ) ) {
			return array(
				'students' => array(),
				'total'    => 0,
				'pages'    => 0,
			);
		}

		$per_page = min( self::MAX_PER_PAGE, max( 1, (int) ( $params['per_page'] ?? 10 ) ) );
		$page     = max( 1, (int) ( $params['page'] ?? 1 ) );
		$search   = isset( $params['search'] ) ? trim( (string) $params['search'] ) : '';
		$offset   = ( $page - 1 ) * $per_page;

		$course_table = stm_lms_user_courses_name( $wpdb );
		$user_table   = $wpdb->users;

		$where_parts  = array( 'uc.course_id = %d' );
		$where_values = array( $course_id );

		if ( '' !== $search ) {
			$like           = '%' . $wpdb->esc_like( $search ) . '%';
			$where_parts[]  = '(u.display_name LIKE %s OR u.user_login LIKE %s OR u.user_email LIKE %s)';
			$where_values[] = $like;
			$where_values[] = $like;
			$where_values[] = $like;
		}

		$where_sql = implode( ' AND ', $where_parts );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders
		$total = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT uc.user_id) FROM {$course_table} AS uc
				INNER JOIN {$user_table} AS u ON uc.user_id = u.ID
				WHERE {$where_sql}",
				...$where_values
			)
		);

		$students = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DISTINCT uc.user_id AS id, COALESCE(NULLIF(u.display_name, ''), u.user_login) AS name
				FROM {$course_table} AS uc
				INNER JOIN {$user_table} AS u ON uc.user_id = u.ID
				WHERE {$where_sql}
				ORDER BY name ASC
				LIMIT %d OFFSET %d",
				...array_merge( $where_values, array( $per_page, $offset ) )
			),
			ARRAY_A
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders

		$students = array_map(
			function ( $student ) {
				$student['id'] = (int) $student['id'];

				return $student;
			},
			$students ?? array()
		);

		return array(
			'students' => $students,
			'total'    => $total,
			'pages'    => (int) ceil( $total / $per_page ),
		);
	}

	/**
	 * @param array<int, \WP_Post> $posts
	 */
	private function prime_review_caches( array $posts ): void {
		if ( empty( $posts ) ) {
			return;
		}

		$author_ids = array_values(
			array_unique(
				array_filter(
					array_map(
						static function ( $post ) {
							$review_user = absint( get_post_meta( $post->ID, 'review_user', true ) );

							return $review_user ? $review_user : ( isset( $post->post_author ) ? absint( $post->post_author ) : 0 );
						},
						$posts
					)
				)
			)
		);

		$course_ids = array_values(
			array_unique(
				array_filter(
					array_map(
						static function ( $post ) {
							return absint( get_post_meta( $post->ID, 'review_course', true ) );
						},
						$posts
					)
				)
			)
		);

		if ( ! empty( $author_ids ) ) {
			if ( ! function_exists( 'cache_users' ) ) {
				require_once ABSPATH . 'wp-includes/pluggable.php';
			}

			if ( function_exists( 'cache_users' ) ) {
				cache_users( $author_ids );
			}
		}

		if ( ! empty( $course_ids ) ) {
			_prime_post_caches( $course_ids, false, true );
		}
	}

	private function current_user_can_manage_review( int $review_id ): bool {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		return $this->current_user_can_manage_course(
			absint( get_post_meta( $review_id, 'review_course', true ) )
		);
	}

	private function current_user_can_manage_course( int $course_id ): bool {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		if ( $course_id <= 0 || PostType::COURSE !== get_post_type( $course_id ) ) {
			return false;
		}

		return get_current_user_id() === (int) get_post_field( 'post_author', $course_id );
	}

	private function refresh_course_rating( int $course_id ): void {
		if ( $course_id <= 0 || PostType::COURSE !== get_post_type( $course_id ) ) {
			return;
		}

		$reviews = new WP_Query(
			array(
				'post_type'              => PostType::REVIEW,
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'meta_query'             => array(
					array(
						'key'     => 'review_course',
						'value'   => $course_id,
						'compare' => '=',
					),
				),
			)
		);

		$marks = array();

		foreach ( $reviews->posts as $review ) {
			$user_id = absint( get_post_meta( $review->ID, 'review_user', true ) );
			$mark    = absint( get_post_meta( $review->ID, 'review_mark', true ) );

			if ( ! $user_id || ! $mark ) {
				continue;
			}

			$marks[ $user_id ] = $mark;
		}

		if ( class_exists( 'STM_LMS_Course' ) ) {
			$rates = \STM_LMS_Course::course_average_rate( $marks );
		} else {
			$rates = array(
				'average' => empty( $marks ) ? 0 : round( array_sum( $marks ) / count( $marks ), 1 ),
			);
		}

		update_post_meta( $course_id, 'course_mark_average', $rates['average'] );
		update_post_meta( $course_id, 'course_marks', $marks );

		if ( class_exists( 'STM_LMS_Instructor' ) ) {
			$transient_name = \STM_LMS_Instructor::transient_name( get_post_field( 'post_author', $course_id ), 'rating' );
			delete_transient( $transient_name );
		}
	}

	/**
	 * @return array<int, int>
	 */
	private function get_current_instructor_course_ids(): array {
		$course_ids = get_posts(
			array(
				'post_type'              => PostType::COURSE,
				'post_status'            => 'any',
				'author'                 => get_current_user_id(),
				'posts_per_page'         => -1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		return array_map( 'intval', $course_ids );
	}
}
