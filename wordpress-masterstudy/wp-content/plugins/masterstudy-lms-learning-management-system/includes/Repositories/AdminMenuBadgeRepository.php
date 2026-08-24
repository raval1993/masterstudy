<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Plugin\PostType;

final class AdminMenuBadgeRepository {
	public const REVIEWS     = 'reviews';
	public const INSTRUCTORS = 'instructors';
	public const COURSES     = 'courses';

	private const SUPPORTED_BADGES = array(
		self::REVIEWS,
		self::INSTRUCTORS,
		self::COURSES,
	);

	/**
	 * @param array<int, string> $badge_keys
	 *
	 * @return array<string, array{count: int, label: string}>
	 */
	public function get_badges( array $badge_keys = array() ): array {
		$badge_keys = $this->normalize_badge_keys( $badge_keys );
		$badges     = array();

		foreach ( $badge_keys as $badge_key ) {
			$count                = $this->get_count( $badge_key );
			$badges[ $badge_key ] = array(
				'count' => $count,
				'label' => $this->get_label( $badge_key, $count ),
			);
		}

		return $badges;
	}

	/**
	 * @param array<int, string> $badge_keys
	 *
	 * @return array<int, string>
	 */
	public function normalize_badge_keys( array $badge_keys = array() ): array {
		if ( empty( $badge_keys ) ) {
			return self::SUPPORTED_BADGES;
		}

		return array_values(
			array_intersect(
				self::SUPPORTED_BADGES,
				array_unique(
					array_filter(
						array_map(
							static function ( $badge_key ) {
								return sanitize_key( $badge_key );
							},
							$badge_keys
						)
					)
				)
			)
		);
	}

	private function get_count( string $badge_key ): int {
		switch ( $badge_key ) {
			case self::REVIEWS:
				$reviews = wp_count_posts( PostType::REVIEW );

				return isset( $reviews->pending ) ? (int) $reviews->pending : 0;
			case self::INSTRUCTORS:
				return $this->get_pending_instructors_count();
			case self::COURSES:
				$courses = wp_count_posts( PostType::COURSE );

				return isset( $courses->pending ) ? (int) $courses->pending : 0;
		}

		return 0;
	}

	private function get_pending_instructors_count(): int {
		global $wpdb;

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT user_id)
				FROM {$wpdb->usermeta}
				WHERE meta_key = %s
				AND meta_value = %s",
				'submission_status',
				'pending'
			)
		);
	}

	private function get_label( string $badge_key, int $count ): string {
		switch ( $badge_key ) {
			case self::REVIEWS:
				return sprintf(
					_n( '%d pending review', '%d pending reviews', $count, 'masterstudy-lms-learning-management-system' ),
					$count
				);
			case self::INSTRUCTORS:
				return sprintf(
					_n( '%d pending instructor', '%d pending instructors', $count, 'masterstudy-lms-learning-management-system' ),
					$count
				);
			case self::COURSES:
				return sprintf(
					_n( '%d pending course', '%d pending courses', $count, 'masterstudy-lms-learning-management-system' ),
					$count
				);
		}

		return '';
	}
}
