<?php

namespace MasterStudy\Lms\Utility;

final class WpDate {
	public static function date_range_to_gmt_bounds( string $date_range ): array {
		$dates = explode( ',', $date_range );
		$from  = self::sanitize_date( $dates[0] ?? '' );
		$to    = self::sanitize_date( $dates[1] ?? '' );

		return array(
			'after'  => $from ? get_gmt_from_date( "{$from} 00:00:00" ) : '',
			'before' => $to ? get_gmt_from_date( "{$to} 23:59:59" ) : '',
		);
	}

	public static function date_range_to_site_bounds( string $date_range ): array {
		$dates = explode( ',', $date_range );
		$from  = self::sanitize_date( $dates[0] ?? '' );
		$to    = self::sanitize_date( $dates[1] ?? '' );

		return array(
			'after'  => $from ? "{$from} 00:00:00" : '',
			'before' => $to ? "{$to} 23:59:59" : '',
		);
	}

	public static function format_gmt_for_site( string $gmt_datetime, string $fallback = '' ): array {
		$site_datetime = self::gmt_to_site_datetime( $gmt_datetime, $fallback );

		return array(
			'datetime'  => $site_datetime,
			'formatted' => '' !== $site_datetime && class_exists( '\STM_LMS_Helpers' )
				? \STM_LMS_Helpers::format_date( $site_datetime )
				: array(),
		);
	}

	public static function modified_gmt_posts_orderby_sql( string $direction ): string {
		global $wpdb;

		$direction = strtoupper( $direction );
		$direction = in_array( $direction, array( 'ASC', 'DESC' ), true ) ? $direction : 'DESC';

		return "{$wpdb->posts}.post_modified_gmt {$direction}, {$wpdb->posts}.ID DESC";
	}

	public static function modified_posts_orderby_sql( string $direction ): string {
		global $wpdb;

		$direction = strtoupper( $direction );
		$direction = in_array( $direction, array( 'ASC', 'DESC' ), true ) ? $direction : 'DESC';

		return "{$wpdb->posts}.post_modified {$direction}, {$wpdb->posts}.ID DESC";
	}

	private static function sanitize_date( string $date ): string {
		$date = sanitize_text_field( trim( $date ) );

		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			return '';
		}

		$parts = array_map( 'absint', explode( '-', $date ) );
		$year  = $parts[0] ?? 0;
		$month = $parts[1] ?? 0;
		$day   = $parts[2] ?? 0;

		return wp_checkdate( $month, $day, $year, $date ) ? $date : '';
	}

	private static function gmt_to_site_datetime( string $gmt_datetime, string $fallback = '' ): string {
		$gmt_datetime = trim( $gmt_datetime );

		if ( '' === $gmt_datetime || '0000-00-00 00:00:00' === $gmt_datetime ) {
			return $fallback;
		}

		if ( false === date_create( $gmt_datetime, new \DateTimeZone( 'UTC' ) ) ) {
			return $fallback;
		}

		$date = get_date_from_gmt( $gmt_datetime );

		return $date ?? $fallback;
	}
}
