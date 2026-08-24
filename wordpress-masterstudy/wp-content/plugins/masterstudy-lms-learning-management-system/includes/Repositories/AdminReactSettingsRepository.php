<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Plugin\Addons;

final class AdminReactSettingsRepository {
	private const APP_SLUGS = array(
		'react_orders',
		'react_lessons',
		'react_google_meets',
		'react_quizzes',
		'react_assignments',
		'react_grades',
		'react_questions',
		'react_memberships',
		'react_membership_plans',
		'react_bundles',
		'react_coupons',
		'react_questions_categories',
		'react_courses_categories',
		'react_courses',
		'react_students',
		'react_instructors',
		'react_student_course_progress',
		'react_students_assignments',
		'react_reviews',
		'react_enterprise_groups',
		'react_statistics',
	);

	private const INSTRUCTOR_APP_SLUGS = array(
		'react_lessons',
		'react_quizzes',
		'react_assignments',
		'react_questions',
		'react_bundles',
		'react_courses',
		'react_reviews',
		'react_enterprise_groups',
		'react_payouts',
	);

	/**
	 * @return array{
	 *     admin_url: string,
	 *     wp_time_format: string,
	 *     currency_info: array{
	 *         currency_symbol: string,
	 *         decimals_num: string,
	 *         currency_thousands: string,
	 *         currency_decimals: string,
	 *         currency_position: string
	 *     },
	 *     enabled_addons: array<string, mixed>
	 * }
	 */
	public static function default_vars(): array {
		return array(
			'admin_url'          => admin_url(),
			'wp_time_format'     => get_option( 'time_format' ),
			'is_pro'             => \STM_LMS_Helpers::is_pro(),
			'is_pro_plus'        => \STM_LMS_Helpers::is_pro_plus(),
			'is_lms_instructor'  => self::is_lms_instructor(),
			'can_manage_options' => current_user_can( 'manage_options' ),
			'has_ai_access'      => self::has_ai_access(),
			'currency_info'      => array(
				'currency_symbol'    => \STM_LMS_Options::get_option( 'currency_symbol', '$' ),
				'decimals_num'       => \STM_LMS_Options::get_option( 'decimals_num', '2' ),
				'currency_thousands' => \STM_LMS_Options::get_option( 'currency_thousands', ' ' ),
				'currency_decimals'  => \STM_LMS_Options::get_option( 'currency_decimals', '.' ),
				'currency_position'  => \STM_LMS_Options::get_option( 'currency_position', 'left' ),
			),
			'enabled_addons'     => Addons::enabled_addons(),
			'media_library'      => self::media_library_vars(),
		);
	}

	private static function is_lms_instructor(): bool {
		if ( current_user_can( 'manage_options' ) ) {
			return false;
		}

		if ( class_exists( '\STM_LMS_Instructor' ) ) {
			return \STM_LMS_Instructor::has_instructor_role();
		}

		return current_user_can( 'stm_lms_instructor' );
	}

	private static function has_ai_access(): bool {
		$repository = new AdminInstructorRepository();

		if ( ! $repository->is_ai_lab_available() ) {
			return false;
		}

		if ( current_user_can( 'manage_options' ) || current_user_can( 'administrator' ) ) {
			return true;
		}

		if ( ! self::is_lms_instructor() ) {
			return false;
		}

		if ( metadata_exists( 'user', get_current_user_id(), 'stm_lms_ai_enabled' ) ) {
			return class_exists( '\STM_LMS_Instructor' )
				? \STM_LMS_Instructor::has_ai_access( get_current_user_id() )
				: rest_sanitize_boolean( get_user_meta( get_current_user_id(), 'stm_lms_ai_enabled', true ) );
		}

		return $repository->is_ai_enabled_for_all();
	}

	/**
	 * @return array<int, string>
	 */
	public static function allowed_app_slugs(): array {
		$app_slugs = apply_filters( 'masterstudy_lms_admin_react_allowed_app_slugs', self::APP_SLUGS );

		return array_values(
			array_unique(
				array_filter(
					(array) $app_slugs,
					'is_string'
				)
			)
		);
	}

	/**
	 * @return array<int, string>
	 */
	public static function allowed_instructor_app_slugs(): array {
		$app_slugs = apply_filters( 'masterstudy_lms_admin_react_instructor_app_slugs', self::INSTRUCTOR_APP_SLUGS );

		return array_values(
			array_unique(
				array_filter(
					(array) $app_slugs,
					'is_string'
				)
			)
		);
	}

	/**
	 * @return array<string, mixed>|null
	 */
	public static function app_vars_by_slug( string $app_slug ): ?array {
		$app_vars = null;

		switch ( $app_slug ) {
			case 'react_orders':
				$app_vars = self::orders_vars();
				break;
			case 'react_lessons':
				$app_vars = self::lessons_vars();
				break;
			case 'react_google_meets':
				$app_vars = self::google_meets_vars();
				break;
			case 'react_quizzes':
				$app_vars = self::quizzes_vars();
				break;
			case 'react_assignments':
				$app_vars = self::assignments_vars();
				break;
			case 'react_grades':
				$app_vars = self::grades_vars();
				break;
			case 'react_questions':
				$app_vars = self::questions_vars();
				break;
			case 'react_memberships':
				$app_vars = self::memberships_vars();
				break;
			case 'react_membership_plans':
				$app_vars = self::membership_plans_vars();
				break;
			case 'react_bundles':
				$app_vars = self::bundles_vars();
				break;
			case 'react_coupons':
				$app_vars = self::coupons_vars();
				break;
			case 'react_courses':
				$app_vars = self::courses_vars();
				break;
			case 'react_courses_categories':
				$app_vars = self::course_categories_vars();
				break;
			case 'react_students':
				$app_vars = self::students_vars();
				break;
			case 'react_instructors':
				$app_vars = self::instructors_vars();
				break;
			case 'react_student_course_progress':
				$app_vars = self::student_course_progress_vars();
				break;
			case 'react_students_assignments':
				$app_vars = self::students_assignments_vars();
				break;
			case 'react_reviews':
				$app_vars = self::reviews_vars();
				break;
			case 'react_enterprise_groups':
				$app_vars = self::enterprise_groups_vars();
				break;
			case 'react_statistics':
				$app_vars = self::statistics_vars();
				break;
			default:
				break;
		}

		return apply_filters( 'masterstudy_lms_admin_react_app_settings_by_slug', $app_vars, $app_slug );
	}

	/**
	 * @return array{max_upload_size: int, allowed_extensions: array<int, string>, integrations: array<string, mixed>|\stdClass}
	 */
	private static function media_library_vars(): array {
		$options = apply_filters(
			'masterstudy_lms_course_options',
			array( 'max_upload_size' => wp_max_upload_size() )
		);

		$media = $options[ Addons::MEDIA_LIBRARY ] ?? array();

		return array(
			'max_upload_size'    => (int) ( $media['max_upload_size'] ?? wp_max_upload_size() ),
			'allowed_extensions' => $media['allowed_extensions'] ?? array(),
			'integrations'       => ! empty( $media['integrations'] ) ? $media['integrations'] : new \stdClass(),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function orders_vars(): array {
		return array(
			'statuses'               => class_exists( '\MasterStudy\Lms\Enums\OrderStatus' ) ? array_map( 'strval', \MasterStudy\Lms\Enums\OrderStatus::cases() ) : array(),
			'taxes_info'             => function_exists( 'masterstudy_lms_ecommerce_options' ) ? masterstudy_lms_ecommerce_options() : array(),
			'is_woocommerce'         => class_exists( '\STM_LMS_Cart' ) ? \STM_LMS_Cart::woocommerce_checkout_enabled() : '0',
			'woocommerce_orders_url' => admin_url( 'admin.php?page=wc-orders' ),
			'countries'              => function_exists( 'masterstudy_lms_get_countries' ) ? masterstudy_lms_get_countries( false ) : array(),
			'regions'                => function_exists( 'masterstudy_lms_get_us_states' ) ? array( 'US' => masterstudy_lms_get_us_states( false ) ) : array(),
			'is_coupons_enabled'     => class_exists( '\STM_LMS_Helpers' ) ? \STM_LMS_Helpers::is_coupons_enabled() : '0',
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function lessons_vars(): array {
		return array(
			'edit_lesson_url' => function_exists( 'ms_plugin_user_account_url' ) ? ms_plugin_user_account_url( 'edit-lesson' ) : '',
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function google_meets_vars(): array {
		return array(
			'edit_google_meet_url' => function_exists( 'ms_plugin_user_account_url' ) ? ms_plugin_user_account_url( 'edit-google-meet' ) : '',
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function quizzes_vars(): array {
		return array(
			'edit_quiz_url' => function_exists( 'ms_plugin_user_account_url' ) ? ms_plugin_user_account_url( 'edit-quiz' ) : '',
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function assignments_vars(): array {
		return array(
			'edit_assignment_url' => function_exists( 'ms_plugin_user_account_url' ) ? ms_plugin_user_account_url( 'edit-assignment' ) : '',
			'edit_course_url'     => function_exists( 'ms_plugin_user_account_url' ) ? ms_plugin_user_account_url( 'edit-course' ) : '',
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function grades_vars(): array {
		return array();
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function questions_vars(): array {
		return array(
			'question_types'    => class_exists( '\MasterStudy\Lms\Enums\QuestionType' ) ? array_map( 'strval', \MasterStudy\Lms\Enums\QuestionType::cases() ) : array(),
			'edit_question_url' => function_exists( 'ms_plugin_user_account_url' ) ? ms_plugin_user_account_url( 'edit-question' ) : '',
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function memberships_vars(): array {
		return array(
			'taxes_info'         => function_exists( 'masterstudy_lms_ecommerce_options' ) ? masterstudy_lms_ecommerce_options() : array(),
			'is_coupons_enabled' => function_exists( 'is_ms_lms_coupons_enabled' ) ? is_ms_lms_coupons_enabled() : '0',
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function membership_plans_vars(): array {
		return function_exists( 'masterstudy_lms_get_membership_plans_template_vars' ) ? masterstudy_lms_get_membership_plans_template_vars() : array(
			'recurring_intervals' => array(),
			'categories'          => array(),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function coupons_vars(): array {
		return array(
			'course_category_url'  => class_exists( '\STM_LMS_Course' ) ? esc_url( \STM_LMS_Course::courses_page_url() ) : '',
			'membership_plans_url' => admin_url( 'admin.php?page=manage_membership_plans' ),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function bundles_vars(): array {
		$bundle_limit         = 0;
		$bundle_courses_limit = 5;

		if ( class_exists( '\MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleSettings' ) ) {
			$settings             = new \MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleSettings();
			$bundle_limit         = (int) $settings->get_bundles_limit();
			$bundle_courses_limit = (int) $settings->get_bundle_courses_limit();
		}

		$coming_soon_settings = get_option( 'masterstudy_lms_coming_soon_settings', array() );

		return array(
			'bundle_limit'              => $bundle_limit,
			'bundle_courses_limit'      => $bundle_courses_limit,
			'allow_coming_soon_courses' => ! empty( $coming_soon_settings['lms_coming_soon_course_bundle_status'] ),
			'is_points_enabled'         => is_ms_lms_addon_enabled( Addons::POINT_SYSTEM ),
			'is_subscriptions_enabled'  => class_exists( '\STM_LMS_Subscriptions' ) && \STM_LMS_Subscriptions::subscription_enabled(),
			'bundles_page_url'          => trailingslashit( get_home_url() ) . 'stm-course-bundles/',
			'bundle_draft_page_url'     => trailingslashit( get_home_url() ),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function courses_vars(): array {
		$categories = array();

		$terms = get_terms(
			array(
				'taxonomy'   => \MasterStudy\Lms\Plugin\Taxonomy::COURSE_CATEGORY,
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$categories[] = array(
					'id'   => (int) $term->term_id,
					'name' => (string) $term->name,
					'slug' => (string) $term->slug,
				);
			}
		}

		return array(
			'categories'      => $categories,
			'edit_course_url' => function_exists( 'ms_plugin_user_account_url' ) ? ms_plugin_user_account_url( 'edit-course' ) : '',
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function students_vars(): array {
		return array(
			'can_clear_student_sessions' => masterstudy_lms_can_manage_user_sessions()
				&& current_user_can( 'manage_options' ),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function instructors_vars(): array {
		return array();
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function student_course_progress_vars(): array {
		$settings                   = get_option( 'stm_lms_settings', array() );
		$student_public_account_url = '';

		if ( ! empty( $settings['student_url_profile'] ) ) {
			$student_public_account_url = esc_url_raw( get_the_permalink( (int) $settings['student_url_profile'] ) );
		}

		return array(
			'edit_course_url'            => function_exists( 'ms_plugin_user_account_url' ) ? ms_plugin_user_account_url( 'edit-course' ) : '',
			'student_public_account_url' => $student_public_account_url,
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function students_assignments_vars(): array {
		return array();
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function reviews_vars(): array {
		return array();
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function enterprise_groups_vars(): array {
		return array();
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function statistics_vars(): array {
		return array();
	}

	private static function course_categories_vars(): array {
		return array(
			'icons' => array(
				'stmlms'      => STM_LMS_URL . 'assets/icons/style.css',
				'linear'      => STM_LMS_URL . 'libraries/nuxy/taxonomy_meta/assets/linearicons/linear.css',
				'fontawesome' => STM_LMS_URL . 'libraries/nuxy/metaboxes/assets/vendors/font-awesome.min.css',
			),
		);
	}
}
