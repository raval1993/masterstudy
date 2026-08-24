<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Utility\WpDate;
use RuntimeException;
use WP_Query;
use WP_User;
use WP_User_Query;

final class LessonAdminRepository {
	private const ERROR_BAD_REQUEST    = 400;
	private const ERROR_FORBIDDEN      = 403;
	private const MAX_PER_PAGE         = 100;
	private const ALLOWED_STATUSES     = array( 'publish', 'pending', 'draft', 'trash', 'private' );
	private const ALLOWED_AUTHOR_ROLES = array( 'keymaster', 'administrator', 'stm_lms_instructor' );

	public function get_list( array $params ): array {
		$per_page  = min( self::MAX_PER_PAGE, max( 1, (int) ( $params['per_page'] ?? 10 ) ) );
		$page      = max( 1, (int) ( $params['page'] ?? 1 ) );
		$search    = isset( $params['search'] ) ? trim( (string) $params['search'] ) : '';
		$status    = isset( $params['status'] ) ? (string) $params['status'] : 'any';
		$post_type = get_post_type_object( PostType::LESSON );

		$query_args = array(
			'post_type'              => PostType::LESSON,
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
				case 'comments':
					$query_args['orderby'] = 'comment_count';
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

		$query      = $this->get_list_query( $query_args, $use_modified_order, $modified_order );
		$lesson_ids = wp_list_pluck( $query->posts, 'ID' );
		$counts     = ( new CurriculumRepository() )->get_course_counts_by_lesson_ids( $lesson_ids );
		$types      = $this->get_types_by_lesson_ids( $lesson_ids );
		$authors    = $this->get_author_labels_by_author_ids(
			wp_list_pluck( $query->posts, 'post_author' )
		);

		foreach ( $query->posts as $post ) {
			$post->linked_courses_count = (int) ( $counts[ $post->ID ] ?? 0 );
			$post->lesson_type          = $types[ $post->ID ] ?? 'text';
			$post->author_label         = $authors[ (int) $post->post_author ] ?? '';
		}

		return array(
			'posts'         => $query->posts,
			'pages'         => (int) $query->max_num_pages,
			'current_page'  => $page,
			'total_lessons' => (int) $query->found_posts,
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

	/**
	 * @return WP_User[]
	 */
	public function get_authors(): array {
		$query = new WP_User_Query(
			array(
				'role__in' => self::ALLOWED_AUTHOR_ROLES,
				'order'    => 'ASC',
				'orderby'  => 'display_name',
			)
		);

		$authors = $query->get_results();

		return array_values(
			array_filter(
				$authors,
				static function ( $author ) {
					return $author instanceof WP_User;
				}
			)
		);
	}

	public function update_author( int $lesson_id, int $author_id ): void {
		if ( PostType::LESSON !== get_post_type( $lesson_id ) ) {
			throw new RuntimeException(
				esc_html__( 'Invalid lesson provided for author update.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_BAD_REQUEST
			);
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			throw new RuntimeException(
				esc_html__( 'You do not have permission to change lesson authors.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_FORBIDDEN
			);
		}

		$user = get_userdata( $author_id );
		if ( ! $user instanceof WP_User ) {
			throw new RuntimeException(
				esc_html__( 'Invalid author provided for lesson update.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_BAD_REQUEST
			);
		}

		if ( empty( array_intersect( self::ALLOWED_AUTHOR_ROLES, (array) $user->roles ) ) ) {
			throw new RuntimeException(
				esc_html__( 'This user cannot be assigned as a lesson author.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_BAD_REQUEST
			);
		}

		$updated = wp_update_post(
			array(
				'ID'          => $lesson_id,
				'post_author' => $author_id,
			),
			true
		);

		if ( is_wp_error( $updated ) ) {
			throw new RuntimeException( $updated->get_error_message() );
		}
	}

	public function bulk_delete( array $lessons ): void {
		foreach ( $lessons as $lesson ) {
			$lesson_id = $this->resolve_lesson_id( $lesson );

			if ( ! $lesson_id || PostType::LESSON !== get_post_type( $lesson_id ) ) {
				throw new RuntimeException(
					esc_html__( 'Invalid lesson provided for bulk delete.', 'masterstudy-lms-learning-management-system' ),
					self::ERROR_BAD_REQUEST
				);
			}

			if ( ! current_user_can( 'delete_post', $lesson_id ) ) {
				throw new RuntimeException(
					esc_html__( 'You do not have permission to delete this lesson.', 'masterstudy-lms-learning-management-system' ),
					self::ERROR_FORBIDDEN
				);
			}

			$result = wp_delete_post( $lesson_id, true );
			if ( false === $result || null === $result ) {
				throw new RuntimeException(
					sprintf(
						/* translators: %d: lesson ID. */
						esc_html__( 'Unable to delete lesson %d.', 'masterstudy-lms-learning-management-system' ),
						$lesson_id
					)
				);
			}
		}
	}

	public function update_status( int $lesson_id, string $status ): void {
		if ( PostType::LESSON !== get_post_type( $lesson_id ) ) {
			throw new RuntimeException(
				esc_html__( 'Invalid lesson provided for status update.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_BAD_REQUEST
			);
		}

		if ( ! current_user_can( 'edit_post', $lesson_id ) ) {
			throw new RuntimeException(
				esc_html__( 'You do not have permission to edit this lesson.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_FORBIDDEN
			);
		}

		if ( 'trash' === get_post_status( $lesson_id ) ) {
			$untrashed = wp_untrash_post( $lesson_id );
			if ( ! $untrashed ) {
				throw new RuntimeException(
					esc_html__( 'Unable to restore lesson from trash.', 'masterstudy-lms-learning-management-system' )
				);
			}
		}

		$updated = wp_update_post(
			array(
				'ID'          => $lesson_id,
				'post_status' => $status,
			),
			true
		);

		if ( is_wp_error( $updated ) ) {
			throw new RuntimeException( $updated->get_error_message() );
		}
	}

	public function bulk_update_status( array $lessons, string $status ): void {
		foreach ( $lessons as $lesson ) {
			$lesson_id = $this->resolve_lesson_id( $lesson );

			$this->update_status( $lesson_id, $status );
		}
	}

	/**
	 * @param mixed $lesson
	 */
	private function resolve_lesson_id( $lesson ): int {
		if ( is_numeric( $lesson ) ) {
			return (int) $lesson;
		}

		if ( is_array( $lesson ) && isset( $lesson['id'] ) ) {
			return (int) $lesson['id'];
		}

		return 0;
	}

	/**
	 * @param array<int, int> $lesson_ids
	 *
	 * @return array<int, string>
	 */
	private function get_types_by_lesson_ids( array $lesson_ids ): array {
		global $wpdb;

		$lesson_ids = array_values(
			array_filter(
				array_map( 'absint', $lesson_ids )
			)
		);

		if ( empty( $lesson_ids ) ) {
			return array();
		}

		$types        = array();
		$meta_table   = esc_sql( $wpdb->postmeta );
		$placeholders = implode( ',', array_fill( 0, count( $lesson_ids ), '%d' ) );
		$params       = array_merge( array( 'type' ), $lesson_ids );
		$results      = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
				"SELECT post_id, meta_value FROM {$meta_table}
				WHERE meta_key = %s
				AND post_id IN ({$placeholders})",
				$params
				// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
			),
			ARRAY_A
		);

		if ( ! is_array( $results ) ) {
			return $types;
		}

		foreach ( $results as $result ) {
			$post_id = isset( $result['post_id'] ) ? (int) $result['post_id'] : 0;

			if ( $post_id <= 0 ) {
				continue;
			}

			$type = isset( $result['meta_value'] ) ? (string) $result['meta_value'] : '';

			$types[ $post_id ] = '' !== $type ? $type : 'text';
		}

		return $types;
	}

	/**
	 * @param array<int, int|string> $author_ids
	 *
	 * @return array<int, string>
	 */
	private function get_author_labels_by_author_ids( array $author_ids ): array {
		$author_ids = array_values(
			array_unique(
				array_filter(
					array_map( 'absint', $author_ids )
				)
			)
		);

		if ( empty( $author_ids ) ) {
			return array();
		}

		if ( function_exists( 'cache_users' ) ) {
			cache_users( $author_ids );
		}

		$authors = array();

		foreach ( $author_ids as $author_id ) {
			$user = get_userdata( $author_id );

			if ( $user instanceof WP_User ) {
				$authors[ $author_id ] = (string) $user->user_login;
			}
		}

		return $authors;
	}
}
