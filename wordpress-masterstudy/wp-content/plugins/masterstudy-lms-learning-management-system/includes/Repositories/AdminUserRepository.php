<?php

namespace MasterStudy\Lms\Repositories;

use WP_User;
use WP_User_Query;
use wpdb;

final class AdminUserRepository {
	private const MAX_PER_PAGE = 100;

	/**
	 * @return array{items: array<int, WP_User>, total: int, pages: int}
	 */
	public function search_users( array $params ): array {
		$per_page       = min( self::MAX_PER_PAGE, max( 1, (int) ( $params['per_page'] ?? 10 ) ) );
		$page           = max( 1, (int) ( $params['page'] ?? 1 ) );
		$search         = isset( $params['search'] ) ? trim( sanitize_text_field( (string) $params['search'] ) ) : '';
		$exclude_emails = $this->normalize_emails( $params['exclude_emails'] ?? '' );
		$exclude_roles  = $this->normalize_roles( $params['exclude_roles'] ?? '' );
		$include_roles  = $this->normalize_roles( $params['include_roles'] ?? '' );
		$include_ids    = $this->resolve_user_ids_by_roles( $include_roles );

		if ( ! empty( $include_roles ) && empty( $include_ids ) ) {
			return array(
				'items' => array(),
				'total' => 0,
				'pages' => 1,
			);
		}

		$exclude_ids    = array_values(
			array_unique(
				array_merge(
					$this->resolve_user_ids_by_emails( $exclude_emails ),
					$this->resolve_user_ids_by_roles( $exclude_roles )
				)
			)
		);
		$user_ids       = $this->get_matching_user_ids( $search, $exclude_ids, $include_ids, $page, $per_page );
		$items          = $this->hydrate_users( $user_ids );
		$total          = $this->count_matching_users( $search, $exclude_ids, $include_ids );

		return array(
			'items' => $items,
			'total' => $total,
			'pages' => max( 1, (int) ceil( $total / $per_page ) ),
		);
	}

	/**
	 * @param mixed $emails
	 *
	 * @return array<int, string>
	 */
	private function normalize_emails( $emails ): array {
		if ( is_string( $emails ) ) {
			$emails = explode( ',', $emails );
		}

		if ( ! is_array( $emails ) ) {
			return array();
		}

		$emails = array_map(
			static function ( $email ) {
				return sanitize_email( trim( (string) $email ) );
			},
			$emails
		);

		return array_values(
			array_unique(
				array_filter( $emails )
			)
		);
	}

	/**
	 * @param mixed $roles
	 *
	 * @return array<int, string>
	 */
	private function normalize_roles( $roles ): array {
		if ( is_string( $roles ) ) {
			$roles = explode( ',', $roles );
		}

		if ( ! is_array( $roles ) ) {
			return array();
		}

		$roles = array_map(
			static function ( $role ) {
				return sanitize_key( trim( (string) $role ) );
			},
			$roles
		);

		return array_values(
			array_unique(
				array_filter( $roles )
			)
		);
	}

	/**
	 * @param array<int, string> $emails
	 *
	 * @return array<int, int>
	 */
	private function resolve_user_ids_by_emails( array $emails ): array {
		if ( empty( $emails ) ) {
			return array();
		}

		$ids = array();

		foreach ( $emails as $email ) {
			$user = get_user_by( 'email', $email );

			if ( $user instanceof WP_User ) {
				$ids[] = (int) $user->ID;
			}
		}

		return array_values( array_unique( $ids ) );
	}

	/**
	 * @param array<int, string> $roles
	 *
	 * @return array<int, int>
	 */
	private function resolve_user_ids_by_roles( array $roles ): array {
		if ( empty( $roles ) ) {
			return array();
		}

		$query = new WP_User_Query(
			array(
				'fields'   => 'ids',
				'number'   => -1,
				'role__in' => $roles,
			)
		);

		$user_ids = $query->get_results();

		if ( ! is_array( $user_ids ) ) {
			return array();
		}

		return array_values( array_map( 'intval', $user_ids ) );
	}

	/**
	 * @param array<int, int> $exclude_ids
	 *
	 * @return array<int, int>
	 */
	private function get_matching_user_ids( string $search, array $exclude_ids, array $include_ids, int $page, int $per_page ): array {
		$wpdb   = $this->wpdb();
		$offset = ( $page - 1 ) * $per_page;
		$sql    = "SELECT ID FROM {$wpdb->users}";
		$params = array();

		$sql .= $this->build_where_clause( $search, $exclude_ids, $include_ids, $params );
		$sql .= ' ORDER BY display_name ASC, user_login ASC';
		$sql .= ' LIMIT %d OFFSET %d';

		$params[] = $per_page;
		$params[] = $offset;

		$results = $wpdb->get_col( $wpdb->prepare( $sql, $params ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Placeholder list is built dynamically in build_where_clause().

		return array_values( array_map( 'intval', $results ) );
	}

	/**
	 * @param array<int, int> $exclude_ids
	 */
	private function count_matching_users( string $search, array $exclude_ids, array $include_ids ): int {
		$wpdb   = $this->wpdb();
		$sql    = "SELECT COUNT(*) FROM {$wpdb->users}";
		$params = array();

		$sql .= $this->build_where_clause( $search, $exclude_ids, $include_ids, $params );

		return (int) $wpdb->get_var( $wpdb->prepare( $sql, $params ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Placeholder list is built dynamically in build_where_clause().
	}

	/**
	 * @param array<int, int> $exclude_ids
	 * @param array<int, mixed> $params
	 */
	private function build_where_clause( string $search, array $exclude_ids, array $include_ids, array &$params ): string {
		$wpdb       = $this->wpdb();
		$conditions = array(
			"user_email <> ''",
		);

		if ( ! empty( $exclude_ids ) ) {
			$conditions[] = 'ID NOT IN (' . implode( ', ', array_fill( 0, count( $exclude_ids ), '%d' ) ) . ')';
			$params       = array_merge( $params, array_map( 'intval', $exclude_ids ) );
		}

		if ( ! empty( $include_ids ) ) {
			$conditions[] = 'ID IN (' . implode( ', ', array_fill( 0, count( $include_ids ), '%d' ) ) . ')';
			$params       = array_merge( $params, array_map( 'intval', $include_ids ) );
		}

		if ( '' !== $search ) {
			$like         = '%' . $wpdb->esc_like( $search ) . '%';
			$conditions[] = '(user_login LIKE %s OR display_name LIKE %s OR user_email LIKE %s)';
			$params[]     = $like;
			$params[]     = $like;
			$params[]     = $like;
		}

		return ' WHERE ' . implode( ' AND ', $conditions );
	}

	/**
	 * @param array<int, int> $user_ids
	 *
	 * @return array<int, WP_User>
	 */
	private function hydrate_users( array $user_ids ): array {
		$users = array();

		foreach ( $user_ids as $user_id ) {
			$user = get_userdata( $user_id );

			if ( $user instanceof WP_User ) {
				$users[] = $user;
			}
		}

		return $users;
	}

	private function wpdb(): wpdb {
		global $wpdb;

		return $wpdb;
	}
}
