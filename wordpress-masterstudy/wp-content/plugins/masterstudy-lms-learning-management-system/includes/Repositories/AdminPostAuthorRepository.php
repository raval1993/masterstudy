<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Plugin\PostType;
use RuntimeException;
use WP_Post;
use WP_User;
use WP_User_Query;

final class AdminPostAuthorRepository {
	private const ERROR_BAD_REQUEST    = 400;
	private const ERROR_FORBIDDEN      = 403;
	private const ERROR_NOT_FOUND      = 404;
	private const ALLOWED_AUTHOR_ROLES = array( 'keymaster', 'administrator', 'stm_lms_instructor' );
	private const SUPPORTED_POST_TYPES = array(
		PostType::COURSE,
		PostType::LESSON,
		PostType::QUIZ,
	);

	/**
	 * @return WP_User[]
	 */
	public function get_authors(): array {
		if ( ! current_user_can( 'manage_options' ) ) {
			$current_user = wp_get_current_user();

			return $current_user instanceof WP_User && $current_user->exists()
				? array( $current_user )
				: array();
		}

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

	public function update_author( int $post_id, string $post_type, int $author_id ): void {
		$this->assert_supported_post_type( $post_type );

		$post = get_post( $post_id );
		if ( ! $post instanceof WP_Post || $post_type !== $post->post_type ) {
			throw new RuntimeException(
				esc_html__( 'The requested post was not found.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_NOT_FOUND
			);
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			throw new RuntimeException(
				esc_html__( 'You do not have permission to change this author.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_FORBIDDEN
			);
		}

		$user = get_userdata( $author_id );
		if ( ! $user instanceof WP_User ) {
			throw new RuntimeException(
				esc_html__( 'Invalid author provided for update.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_BAD_REQUEST
			);
		}

		if ( empty( array_intersect( self::ALLOWED_AUTHOR_ROLES, (array) $user->roles ) ) ) {
			throw new RuntimeException(
				esc_html__( 'This user cannot be assigned as an author.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_BAD_REQUEST
			);
		}

		$updated = wp_update_post(
			array(
				'ID'          => $post_id,
				'post_author' => $author_id,
			),
			true
		);

		if ( is_wp_error( $updated ) ) {
			throw new RuntimeException( $updated->get_error_message() );
		}
	}

	/**
	 * @return array<int, string>
	 */
	public static function supported_post_types(): array {
		return self::SUPPORTED_POST_TYPES;
	}

	private function assert_supported_post_type( string $post_type ): void {
		if ( ! in_array( $post_type, self::SUPPORTED_POST_TYPES, true ) ) {
			throw new RuntimeException(
				esc_html__( 'Unsupported post type provided for author update.', 'masterstudy-lms-learning-management-system' ),
				self::ERROR_BAD_REQUEST
			);
		}
	}
}
