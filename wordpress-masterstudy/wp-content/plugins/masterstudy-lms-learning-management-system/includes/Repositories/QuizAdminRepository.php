<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Enums\QuestionType;
use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Utility\WpDate;
use RuntimeException;
use WP_Query;

final class QuizAdminRepository {
	private const ERROR_BAD_REQUEST = 400;
	private const ERROR_FORBIDDEN   = 403;
	private const MAX_PER_PAGE      = 100;
	private const ALLOWED_STATUSES  = array( 'publish', 'pending', 'draft', 'trash', 'private' );

	public function get_list( array $params ): array {
		$per_page  = min( self::MAX_PER_PAGE, max( 1, (int) ( $params['per_page'] ?? 10 ) ) );
		$page      = max( 1, (int) ( $params['page'] ?? 1 ) );
		$search    = isset( $params['search'] ) ? trim( (string) $params['search'] ) : '';
		$status    = isset( $params['status'] ) ? (string) $params['status'] : 'any';
		$post_type = get_post_type_object( PostType::QUIZ );

		$query_args = array(
			'post_type'              => PostType::QUIZ,
			'post_status'            => 'any' === $status ? self::ALLOWED_STATUSES : $status,
			'ignore_sticky_posts'    => true,
			'posts_per_page'         => $per_page,
			'paged'                  => $page,
			'no_found_rows'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		if ( '' !== $search ) {
			$query_args['s'] = $search;
		}

		$date_range = isset( $params['date_range'] ) ? trim( (string) $params['date_range'] ) : '';
		if ( '' !== $date_range ) {
			$range = WpDate::date_range_to_site_bounds( $date_range );

			if ( $range['after'] || $range['before'] ) {
				$date_query = array(
					'inclusive' => true,
					'column'    => 'post_modified',
				);

				if ( $range['after'] ) {
					$date_query['after'] = $range['after'];
				}

				if ( $range['before'] ) {
					$date_query['before'] = $range['before'];
				}

				$query_args['date_query'] = array( $date_query );
			}
		}

		if ( $post_type && ! current_user_can( $post_type->cap->edit_others_posts ) ) {
			$query_args['author'] = get_current_user_id();
		}

		$sort               = isset( $params['sort'] ) ? trim( (string) $params['sort'] ) : '';
		$modified_order     = 'DESC';
		$use_modified_order = false;

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
				case 'author':
					$query_args['orderby'] = 'author';
					$query_args['order']   = $direction;
					break;
				case 'date':
				default:
					$query_args['orderby'] = 'none';
					$query_args['order']   = $direction;
					$modified_order        = $direction;
					$use_modified_order    = true;
			}
		} else {
			$query_args['orderby'] = 'none';
			$query_args['order']   = 'DESC';
			$use_modified_order    = true;
		}

		$query           = $this->get_list_query( $query_args, $use_modified_order, $modified_order );
		$counts          = ( new CurriculumRepository() )->get_course_counts_by_post_ids(
			wp_list_pluck( $query->posts, 'ID' )
		);
		$question_counts = $this->get_question_counts_by_quiz_ids( $query->posts );

		foreach ( $query->posts as $post ) {
			$post->linked_courses_count = (int) ( $counts[ $post->ID ] ?? 0 );
			$post->question_banks_count = (int) ( $question_counts[ $post->ID ]['question_banks_count'] ?? 0 );
			$post->questions_count      = (int) ( $question_counts[ $post->ID ]['questions_count'] ?? 0 );
		}

		$this->prime_author_caches( $query->posts );

		return array(
			'posts'         => $query->posts,
			'pages'         => (int) $query->max_num_pages,
			'current_page'  => $page,
			'total_quizzes' => (int) $query->found_posts,
		);
	}

	private function get_list_query( array $query_args, bool $use_modified_order, string $direction ): WP_Query {
		if ( ! $use_modified_order ) {
			return new WP_Query( $query_args );
		}

		$query_args['masterstudy_modified_order'] = true;

		$orderby_filter = static function ( $orderby, $query ) use ( $direction ) {
			if ( ! $query->get( 'masterstudy_modified_order' ) ) {
				return $orderby;
			}

			return WpDate::modified_posts_orderby_sql( $direction );
		};

		add_filter( 'posts_orderby', $orderby_filter, 10, 2 );

		try {
			return new WP_Query( $query_args );
		} finally {
			remove_filter( 'posts_orderby', $orderby_filter, 10 );
		}
	}

	public function bulk_delete( array $quizzes ): void {
		foreach ( $quizzes as $quiz ) {
			$quiz_id = $this->resolve_quiz_id( $quiz );

			if ( ! $quiz_id || PostType::QUIZ !== get_post_type( $quiz_id ) ) {
				throw new RuntimeException(
					esc_html__( 'Invalid quiz provided for bulk delete.', 'masterstudy-lms-learning-management-system' ),
					self::ERROR_BAD_REQUEST
				);
			}

			if ( ! $this->can_delete_quiz( $quiz_id ) ) {
				throw new RuntimeException(
					esc_html__( 'You do not have permission to delete this quiz.', 'masterstudy-lms-learning-management-system' ),
					self::ERROR_FORBIDDEN
				);
			}

			$result = wp_delete_post( $quiz_id, true );
			if ( false === $result || null === $result ) {
				throw new RuntimeException(
					sprintf(
						/* translators: %d: quiz ID. */
						esc_html__( 'Unable to delete quiz %d.', 'masterstudy-lms-learning-management-system' ),
						$quiz_id
					)
				);
			}
		}
	}

	private function can_delete_quiz( int $quiz_id ): bool {
		if ( current_user_can( 'delete_post', $quiz_id ) ) {
			return true;
		}

		$post = get_post( $quiz_id );

		if ( ! $post || PostType::QUIZ !== $post->post_type ) {
			return false;
		}

		return current_user_can( 'stm_lms_instructor' ) && get_current_user_id() === (int) $post->post_author;
	}

	public function update_status( int $quiz_id, string $status ): void {
		if ( PostType::QUIZ !== get_post_type( $quiz_id ) ) {
			throw new RuntimeException(
				esc_html__( 'Invalid quiz provided for status update.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_BAD_REQUEST
			);
		}

		if ( ! current_user_can( 'edit_post', $quiz_id ) ) {
			throw new RuntimeException(
				esc_html__( 'You do not have permission to edit this quiz.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_FORBIDDEN
			);
		}

		if ( 'trash' === get_post_status( $quiz_id ) ) {
			$untrashed = wp_untrash_post( $quiz_id );
			if ( ! $untrashed ) {
				throw new RuntimeException(
					esc_html__( 'Unable to restore quiz from trash.', 'masterstudy-lms-learning-management-system' )
				);
			}
		}

		$updated = wp_update_post(
			array(
				'ID'          => $quiz_id,
				'post_status' => $status,
			),
			true
		);

		if ( is_wp_error( $updated ) ) {
			throw new RuntimeException( $updated->get_error_message() );
		}
	}

	public function bulk_update_status( array $quizzes, string $status ): void {
		foreach ( $quizzes as $quiz ) {
			$quiz_id = $this->resolve_quiz_id( $quiz );

			$this->update_status( $quiz_id, $status );
		}
	}

	/**
	 * @param mixed $quiz
	 */
	private function resolve_quiz_id( $quiz ): int {
		if ( is_numeric( $quiz ) ) {
			return (int) $quiz;
		}

		if ( is_array( $quiz ) && isset( $quiz['id'] ) ) {
			return (int) $quiz['id'];
		}

		return 0;
	}

	/**
	 * @param array<int, \WP_Post> $quizzes
	 *
	 * @return array<int, array{question_banks_count: int, questions_count: int}>
	 */
	private function get_question_counts_by_quiz_ids( array $quizzes ): array {
		$counts   = array();
		$quiz_ids = array_values(
			array_filter(
				array_map(
					static function ( $quiz ) {
						return isset( $quiz->ID ) ? absint( $quiz->ID ) : 0;
					},
					$quizzes
				)
			)
		);

		if ( empty( $quiz_ids ) ) {
			return $counts;
		}

		$quiz_question_ids = $this->get_quiz_question_ids_map( $quiz_ids );
		$all_question_ids  = array();

		foreach ( $quiz_question_ids as $question_ids ) {
			foreach ( $question_ids as $question_id ) {
				$all_question_ids[ $question_id ] = $question_id;
			}
		}

		$question_types = $this->get_question_types( array_values( array_unique( $all_question_ids ) ) );

		foreach ( $quiz_ids as $quiz_id ) {
			$question_ids         = $quiz_question_ids[ $quiz_id ] ?? array();
			$question_banks_count = 0;

			foreach ( $question_ids as $question_id ) {
				if ( QuestionType::QUESTION_BANK === ( $question_types[ $question_id ] ?? '' ) ) {
					++$question_banks_count;
				}
			}

			$counts[ $quiz_id ] = array(
				'question_banks_count' => $question_banks_count,
				'questions_count'      => max( 0, count( $question_ids ) - $question_banks_count ),
			);
		}

		return $counts;
	}

	/**
	 * @param array<int, int> $quiz_ids
	 *
	 * @return array<int, array<int, int>>
	 */
	private function get_quiz_question_ids_map( array $quiz_ids ): array {
		global $wpdb;

		$quiz_ids = array_values(
			array_filter(
				array_map( 'absint', $quiz_ids )
			)
		);

		if ( empty( $quiz_ids ) ) {
			return array();
		}

		$map          = array_fill_keys( $quiz_ids, array() );
		$placeholders = implode( ',', array_fill( 0, count( $quiz_ids ), '%d' ) );
		$params       = array_merge( array( 'questions' ), $quiz_ids );

		$results = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
				"SELECT post_id, meta_value FROM {$wpdb->postmeta}
				WHERE meta_key = %s
				AND post_id IN ({$placeholders})",
				$params
				// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
			),
			ARRAY_A
		);

		if ( ! is_array( $results ) ) {
			return $map;
		}

		foreach ( $results as $result ) {
			$quiz_id = isset( $result['post_id'] ) ? absint( $result['post_id'] ) : 0;

			if ( ! $quiz_id || ! isset( $map[ $quiz_id ] ) ) {
				continue;
			}

			$map[ $quiz_id ] = array_values(
				array_filter(
					array_map( 'absint', explode( ',', (string) ( $result['meta_value'] ?? '' ) ) )
				)
			);
		}

		return $map;
	}

	/**
	 * @param array<int, int> $question_ids
	 *
	 * @return array<int, string>
	 */
	private function get_question_types( array $question_ids ): array {
		global $wpdb;

		$question_ids = array_values(
			array_filter(
				array_map( 'absint', $question_ids )
			)
		);

		if ( empty( $question_ids ) ) {
			return array();
		}

		$placeholders = implode( ',', array_fill( 0, count( $question_ids ), '%d' ) );
		$params       = array_merge( array( 'type' ), $question_ids );

		$results = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
				"SELECT post_id, meta_value FROM {$wpdb->postmeta}
				WHERE meta_key = %s
				AND post_id IN ({$placeholders})",
				$params
				// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
			),
			ARRAY_A
		);

		$types = array();

		if ( ! is_array( $results ) ) {
			return $types;
		}

		foreach ( $results as $result ) {
			$question_id = isset( $result['post_id'] ) ? absint( $result['post_id'] ) : 0;

			if ( $question_id ) {
				$types[ $question_id ] = (string) ( $result['meta_value'] ?? '' );
			}
		}

		return $types;
	}

	/**
	 * @param array<int, \WP_Post> $posts
	 */
	private function prime_author_caches( array $posts ): void {
		$author_ids = array_values(
			array_unique(
				array_filter(
					array_map(
						static function ( $post ) {
							return isset( $post->post_author ) ? absint( $post->post_author ) : 0;
						},
						$posts
					)
				)
			)
		);

		if ( empty( $author_ids ) ) {
			return;
		}

		if ( ! function_exists( 'cache_users' ) ) {
			require_once ABSPATH . 'wp-includes/pluggable.php';
		}

		if ( function_exists( 'cache_users' ) ) {
			cache_users( $author_ids );
		}
	}
}
