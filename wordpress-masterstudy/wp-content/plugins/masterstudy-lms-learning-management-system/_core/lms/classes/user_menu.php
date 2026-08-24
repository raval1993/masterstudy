<?php

use MasterStudy\Lms\Plugin\Addons;

new STM_LMS_User_Menu();

class STM_LMS_User_Menu {
	public function __construct() {
		add_action( 'wp_footer', array( $this, 'render_float_menu' ) );
		add_action( 'stm_lms_user_float_menu_before', array( $this, 'float_menu_styles' ) );

		add_filter( 'stm_lms_sorted_menu', array( $this, 'sorting_menu' ), 10, 2 );
		add_filter( 'stm_lms_sorted_student_menu', array( $this, 'sorting_student_menu' ) );
		add_filter( 'stm_lms_settings_menu_items', array( $this, 'settings_menu_prepare_data' ) );
		add_filter( 'stm_lms_float_menu_placed_items', array( $this, 'float_menu_placed_items' ), 10, 2 );
	}

	public function render_float_menu() {
		if ( self::float_menu_enabled() ) {
			STM_LMS_Templates::show_lms_template( 'account/float_menu/float_menu' );
		}
	}

	public static function is_account_page(): bool {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$path        = wp_parse_url( $request_uri, PHP_URL_PATH );
		$path        = trim( (string) $path, '/' );

		if ( empty( $path ) ) {
			return false;
		}

		$account_base_url  = STM_LMS_User::login_page_url();
		$account_base_path = wp_parse_url( $account_base_url, PHP_URL_PATH );
		$account_base_path = trim( (string) $account_base_path, '/' );

		if ( ! empty( $account_base_path ) && ( $path === $account_base_path || 0 === strpos( $path, "{$account_base_path}/" ) ) ) {
			return true;
		}

		$wishlist_url  = STM_LMS_User::wishlist_url();
		$wishlist_path = wp_parse_url( $wishlist_url, PHP_URL_PATH );
		$wishlist_path = trim( (string) $wishlist_path, '/' );

		return ! empty( $wishlist_path ) && $path === $wishlist_path;
	}

	public function float_menu_styles() {
		$float_background_color      = esc_attr( STM_LMS_Options::get_option( 'float_background_color', 'rgba(255, 255, 255, 1)' ) );
		$float_text_color            = esc_attr( STM_LMS_Options::get_option( 'float_text_color', 'rgba(39, 48, 68, 1)' ) );
		$is_background_color_default = ! empty( STM_LMS_Options::get_option( 'float_background_color' ) );
		$is_text_color_default       = ! empty( STM_LMS_Options::get_option( 'float_text_color' ) );

		if ( $is_background_color_default ) { ?>
			<style>
				@media (max-width: 768px) {
					body .stm_lms_user_float_menu:not(.__collapsed) .stm_lms_user_float_menu__toggle {
						background-color: <?php echo esc_attr( $float_background_color ); ?> !important;
					}
				}

				.stm_lms_button .stm_lms_user_float_menu__scrolled .stm_lms_user_float_menu__scrolled_label {
					background-color: <?php echo esc_attr( STM_LMS_Helpers::stm_rgba_change_alpha_dynamically( $float_text_color, 1 ) ); ?>;
				}

				.stm_lms_button .stm_lms_user_float_menu .float_menu_item_active {
					background-color: <?php echo esc_attr( STM_LMS_Helpers::stm_rgba_change_alpha_dynamically( $float_text_color, .2 ) ); ?>;
				}

				.stm_lms_button .stm_lms_user_float_menu .float_menu_item:hover:before, .stm_lms_user_float_menu .float_menu_item_active:before {
					background-color: <?php echo esc_attr( $float_text_color ); ?> !important;
				}

				.stm_lms_user_float_menu .stm-lms-logout-button {
					background-color: <?php echo esc_attr( $float_background_color ); ?> !important;
				}

				.stm_lms_user_float_menu .stm_lms_user_float_menu__empty {
					background-color: <?php echo esc_attr( STM_LMS_Helpers::stm_rgba_change_alpha_dynamically( $float_text_color, .2 ) ); ?>;
				}

				.stm_lms_user_float_menu .stm_lms_user_float_menu__tabs a.active {
					color: <?php echo esc_attr( $float_text_color ); ?> !important;
					background-color: <?php echo esc_attr( STM_LMS_Helpers::stm_rgba_change_alpha_dynamically( $float_text_color, .2 ) ); ?>;
				}

				.stm_lms_user_float_menu .stm_lms_user_float_menu__tabs a {
					background-color: <?php echo esc_attr( $float_background_color ); ?>;
					color: <?php echo esc_attr( $float_text_color ); ?> !important;
				}

				.stm_lms_user_float_menu .stm_lms_user_float_menu__tabs {
					border-bottom: 3px solid <?php echo esc_attr( STM_LMS_Helpers::stm_rgba_change_alpha_dynamically( $float_text_color, .2 ) ); ?> !important;
				}

				body .stm_lms_user_float_menu {
					background-color: <?php echo esc_attr( $float_background_color ); ?>;
				}

				body .stm_lms_user_float_menu .float_menu_item:hover {
					background-color: <?php echo esc_attr( STM_LMS_Helpers::stm_rgba_change_alpha_dynamically( $float_text_color, .2 ) ); ?>;
				}

				body .stm_lms_user_float_menu__user {
					border-top: rgb(<?php echo esc_attr( $float_background_color ); ?>, .1);
					border-bottom: rgba(<?php echo esc_attr( $float_background_color ); ?>, '0.1');
				}
			</style>
			<?php
		}

		if ( $is_text_color_default ) {
			?>
			<style>
				.stm_lms_button .stm_lms_user_float_menu__scrolled .stm_lms_user_float_menu__scrolled_label i {
					color: <?php echo esc_attr( STM_LMS_Helpers::stm_rgba_change_alpha_dynamically( $float_background_color, .5 ) ); ?> !important;
				}

				.stm_lms_user_float_menu .float_menu_item_active .stm_lms_user_float_menu__user_settings {
					color: <?php echo esc_attr( $float_text_color ); ?> !important;
				}

				.stm_lms_user_float_menu .stm-lms-logout-button {
					color: <?php echo esc_attr( $float_text_color ); ?> !important;
					border-top: 1px solid <?php echo esc_attr( STM_LMS_Helpers::stm_rgba_change_alpha_dynamically( $float_text_color, .2 ) ); ?> !important;
				}

				.stm_lms_user_float_menu .stm_lms_user_float_menu__user_settings {
					color: <?php echo esc_attr( $float_text_color ); ?>;
				}

				.stm_lms_user_float_menu .stm_lms_user_float_menu__empty {
					color: <?php echo esc_attr( $float_text_color ); ?>;
				}

				.stm_lms_user_float_menu .stm_lms_user_float_menu__login_head h4 {
					color: <?php echo esc_attr( $float_text_color ); ?>;
				}

				.stm_lms_user_float_menu .stm_lms_user_float_menu__login #stm-lms-login .stm_lms_login_wrapper .stm_lms_login_wrapper__actions .lostpassword {
					color: <?php echo esc_attr( $float_text_color ); ?> !important;
				}

				.stm_lms_user_float_menu__login .stm_lms_user_float_menu__login_head a {
					color: <?php echo esc_attr( $float_text_color ); ?>;
				}

				.stm_lms_user_float_menu__login .stm_lms_user_float_menu__login_head a:hover {
					color: <?php echo esc_attr( $float_text_color ); ?>;
				}

				.stm_lms_button .stm_lms_user_float_menu .float_menu_item__divider {
					border-top: 1px solid <?php echo esc_attr( STM_LMS_Helpers::stm_rgba_change_alpha_dynamically( $float_text_color, .15 ) ); ?> !important;
					color: <?php echo esc_attr( $float_text_color ); ?>;
				}

				.stm_lms_button .stm-lms-logout-button:hover i {
					color: <?php echo esc_attr( $float_text_color ); ?>;
				}

				.stm_lms_user_float_menu .stm_lms_user_float_menu__user {
					border-top: 1px solid <?php echo esc_attr( STM_LMS_Helpers::stm_rgba_change_alpha_dynamically( $float_text_color, .15 ) ); ?> !important;
					border-bottom: 1px solid <?php echo esc_attr( STM_LMS_Helpers::stm_rgba_change_alpha_dynamically( $float_text_color, .15 ) ); ?> !important;
				}

				.stm_lms_user_float_menu__toggle svg:hover path {
					fill: <?php echo esc_attr( STM_LMS_Helpers::stm_rgba_change_alpha_dynamically( $float_text_color, 1 ) ); ?> !important;
				}

				.stm_lms_user_float_menu__toggle svg path {
					fill: <?php echo esc_attr( $float_text_color ); ?> !important;
				}

				.stm_lms_button .stm_lms_user_float_menu .float_menu_item:hover .stm_lms_user_float_menu__user_settings, .stm_lms_user_float_menu .float_menu_item_active .stm_lms_user_float_menu__user_settings {
					color: <?php echo esc_attr( $float_text_color ); ?>;
				}

				.stm_lms_button .stm_lms_user_float_menu .float_menu_item:hover .float_menu_item__icon, .stm_lms_user_float_menu .float_menu_item_active .float_menu_item__icon {
					color: <?php echo esc_attr( $float_text_color ); ?>;
				}

				.stm_lms_user_float_menu .stm_lms_user_float_menu__user_info span, .stm_lms_user_float_menu .stm_lms_user_float_menu__user_info h3 {
					color: <?php echo esc_attr( $float_text_color ); ?>;
				}

				.stm_lms_button .stm_lms_user_float_menu .float_menu_item__inline .float_menu_item__icon {
					color: <?php echo esc_attr( $float_text_color ); ?>;
				}

				.stm_lms_button .stm_lms_user_float_menu.__collapsed .stm_lms_user_float_menu__toggle:hover {
					color: <?php echo esc_attr( $float_text_color ); ?>;
				}

				.stm_lms_button .stm_lms_user_float_menu.__collapsed .stm_lms_user_float_menu__toggle:hover svg path {
					fill: <?php echo esc_attr( $float_text_color ); ?>;
				}

				body .stm_lms_user_float_menu .float_menu_item:hover .float_menu_item__title {
					color: <?php echo esc_attr( $float_text_color ); ?>;
				}

				.stm_lms_user_float_menu .float_menu_item__inline .float_menu_item__title {
					color: <?php echo esc_attr( $float_text_color ); ?>;
				}
			</style>
			<?php
		}
	}

	public static function float_menu_enabled() {
		$float_menu       = STM_LMS_Options::get_option( 'float_menu', false );
		$float_menu_guest = STM_LMS_Options::get_option( 'float_menu_guest', true );

		if ( self::is_account_page() ) {
			return false;
		}

		if ( ! is_user_logged_in() && $float_menu ) {
			return $float_menu_guest;
		}

		return apply_filters( 'stm_lms_float_menu_enabled', $float_menu );
	}

	public static function float_menu_items() {
		$user_id       = get_current_user_id();
		$settings      = get_option( 'stm_lms_settings', array() );
		$is_instructor = STM_LMS_Instructor::is_instructor( $user_id );
		$current_slug  = self::get_current_account_slug();

		$menus = array();

		/*Instructor fields*/
		if ( $is_instructor ) {
			$menus[] = array(
				'order'        => 10,
				'id'           => 'dashboard',
				'lms_template' => 'account/main',
				'menu_title'   => esc_html__( 'Dashboard', 'masterstudy-lms-learning-management-system' ),
				'menu_icon'    => 'stmlms-menu-dashboard',
				'menu_url'     => STM_LMS_User::login_page_url(),
				'is_active'    => ! empty( $settings['user_url'] ) ? get_queried_object_id() === intval( $settings['user_url'] ) : '',
				'menu_place'   => 'main',
				'section'      => 'main',
			);

			if ( apply_filters( 'stm_lms_enable_add_course', true ) ) {
				$menus[] = array(
					'order'        => 55,
					'id'           => 'add_course',
					'slug'         => 'edit-course',
					'lms_template' => 'course-builder',
					'menu_title'   => esc_html__( 'Add Course', 'masterstudy-lms-learning-management-system' ),
					'menu_icon'    => 'stmlms-menu-add-course',
					'menu_url'     => ms_plugin_manage_course_url(),
					'menu_place'   => 'main',
					'section'      => 'main',
				);
			}
		}

		$menus[] = array(
			'order'        => 100,
			'id'           => 'enrolled_courses',
			'slug'         => 'enrolled-courses',
			'lms_template' => 'account/enrolled-courses',
			'menu_title'   => esc_html__( 'Enrolled Courses', 'masterstudy-lms-learning-management-system' ),
			'menu_icon'    => 'stmlms-menu-enrolled-courses',
			'menu_url'     => ms_plugin_user_account_url( 'enrolled-courses' ),
			'is_active'    => 'enrolled-courses' === $current_slug,
			'menu_place'   => 'learning',
			'section'      => 'main',
		);

		$menus[] = array(
			'order'        => 110,
			'id'           => 'settings',
			'slug'         => 'settings',
			'lms_template' => 'account/settings',
			'menu_title'   => esc_html__( 'Settings', 'masterstudy-lms-learning-management-system' ),
			'menu_icon'    => 'stmlms-menu-settings',
			'menu_url'     => ms_plugin_user_account_url( 'settings' ),
			'is_active'    => 'settings' === $current_slug,
			'section'      => 'account',
		);

		$menus[] = array(
			'order'        => 120,
			'id'           => 'messages',
			'slug'         => 'chat',
			'lms_template' => 'account/messages',
			'menu_title'   => esc_html__( 'Messages', 'masterstudy-lms-learning-management-system' ),
			'menu_icon'    => 'stmlms-menu-messages',
			'menu_url'     => ms_plugin_user_account_url( 'chat' ),
			'badge_count'  => STM_LMS_Chat::user_new_messages( $user_id ),
			'is_active'    => 'chat' === $current_slug,
			'menu_place'   => 'learning',
			'section'      => 'communication',
		);

		$menus[] = array(
			'order'        => 130,
			'id'           => 'favorite_courses',
			'slug'         => 'wishlist',
			'lms_template' => 'account/wishlist',
			'menu_title'   => esc_html__( 'Wishlist', 'masterstudy-lms-learning-management-system' ),
			'menu_icon'    => 'stmlms-menu-wishlist',
			'menu_url'     => STM_LMS_User::wishlist_url(),
			'is_active'    => ! empty( $settings['wishlist_url'] ) ? get_queried_object_id() === intval( $settings['wishlist_url'] ) : '',
			'menu_place'   => 'learning',
			'section'      => 'account',
		);

		$menus[] = array(
			'order'        => 140,
			'id'           => 'enrolled_quizzes',
			'slug'         => 'enrolled-quizzes',
			'lms_template' => 'account/enrolled-quizzes',
			'menu_title'   => esc_html__( 'Enrolled Quizzes', 'masterstudy-lms-learning-management-system' ),
			'menu_icon'    => 'stmlms-menu-enrolled-quizzes',
			'menu_url'     => ms_plugin_user_account_url( 'enrolled-quizzes' ),
			'menu_place'   => 'learning',
			'is_active'    => 'enrolled-quizzes' === $current_slug,
			'section'      => 'main',
		);

		if ( STM_LMS_Helpers::is_pro_plus() && ! empty( $settings['course_player_lesson_notes'] ) ) {
			$menus[] = array(
				'order'        => 145,
				'id'           => 'lesson_notes',
				'slug'         => 'notes',
				'lms_template' => 'account/notes',
				'menu_title'   => esc_html__( 'Notes', 'masterstudy-lms-learning-management-system' ),
				'menu_icon'    => 'stmlms-menu-notes',
				'menu_url'     => ms_plugin_user_account_url( 'notes' ),
				'menu_place'   => 'learning',
				'is_active'    => 'notes' === $current_slug,
				'section'      => 'main',
			);
		}

		$menus[] = array(
			'order'        => 150,
			'id'           => 'my_orders',
			'slug'         => 'my-orders',
			'lms_template' => 'account/my-orders',
			'menu_title'   => esc_html__( 'My Orders', 'masterstudy-lms-learning-management-system' ),
			'menu_icon'    => 'stmlms-menu-my-orders',
			'menu_url'     => ms_plugin_user_account_url( 'my-orders' ),
			'menu_place'   => 'learning',
			'is_active'    => 'my-orders' === $current_slug,
			'section'      => 'account',
		);

		if ( STM_LMS_Subscriptions::subscription_enabled() ) {
			$menus[] = array(
				'order'        => 125,
				'id'           => 'memberships',
				'slug'         => 'memberships-pmp',
				'lms_template' => 'account/memberships-pmp',
				'menu_title'   => esc_html__( 'Memberships', 'masterstudy-lms-learning-management-system' ),
				'menu_icon'    => 'stmlms-menu-memberships',
				'menu_url'     => STM_LMS_User::my_pmpro_url(),
				'menu_place'   => 'learning',
				'is_active'    => 'memberships-pmp' === $current_slug,
				'section'      => 'account',
			);
		}

		$menus = apply_filters( 'stm_lms_menu_items', $menus );

		array_multisort( array_column( $menus, 'order' ), SORT_ASC, $menus );

		return $menus;
	}

	public static function get_current_account_slug(): string {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$path        = wp_parse_url( $request_uri, PHP_URL_PATH );
		$path        = trim( (string) $path, '/' );

		$base_url  = STM_LMS_User::login_page_url();
		$base_path = wp_parse_url( $base_url, PHP_URL_PATH );
		$base_path = trim( (string) $base_path, '/' );

		if ( empty( $base_path ) || strpos( $path, $base_path ) !== 0 ) {
			return '';
		}

		$tail = trim( substr( $path, strlen( $base_path ) ), '/' );
		if ( '' === $tail ) {
			return '';
		}

		$parts = explode( '/', $tail );

		return sanitize_key( $parts[0] ?? '' );
	}

	/**
	 * Displays a sortable menu on the site after filtering it
	 */
	public static function stm_lms_user_menu_display() {
		$menu_items = self::float_menu_items();

		if ( STM_LMS_Instructor::is_instructor() && self::float_menu_enabled() ) {
			return self::get_instructor_float_menu_items( $menu_items );
		}

		if ( STM_LMS_Instructor::is_instructor() ) {
			$selected_menu = 'sorting_the_menu';
		} else {
			$selected_menu = self::float_menu_enabled() ? 'sorting_float_menu_learning' : 'sorting_the_menu_student';
		}

		return apply_filters( 'stm_lms_sorted_menu', $menu_items, $selected_menu );
	}

	public static function get_instructor_float_menu_items( $menu_items ) {
		$legacy_float_menu = self::get_menu_options( STM_LMS_Options::get_option( 'sorting_full_float_menu' ) );

		if ( false !== $legacy_float_menu ) {
			return apply_filters( 'stm_lms_sorted_menu', $menu_items, 'sorting_full_float_menu' );
		}

		$main_items     = apply_filters(
			'stm_lms_sorted_menu',
			apply_filters( 'stm_lms_float_menu_placed_items', $menu_items, 'main' ),
			'sorting_float_menu_main'
		);
		$learning_items = apply_filters(
			'stm_lms_sorted_menu',
			apply_filters( 'stm_lms_float_menu_placed_items', $menu_items, 'learning' ),
			'sorting_float_menu_learning'
		);

		return array_values(
			array_reduce(
				(array) array_merge( $main_items, $learning_items ),
				function ( $items, $menu_item ) {
					$menu_id = $menu_item['id'] ?? '';

					if ( empty( $menu_id ) || isset( $items[ $menu_id ] ) ) {
						return $items;
					}

					$items[ $menu_id ] = $menu_item;

					return $items;
				},
				array()
			)
		);
	}

	/**
	 * Sort default menu using Sorting Menu settings.
	 */
	public function sorting_menu( $menu_items, $menu_name ) {
		$menu_settings = self::get_menu_options( STM_LMS_Options::get_option( $menu_name ) );

		if ( false !== $menu_settings ) {
			$actual_menu = array();

			if ( ! empty( $menu_settings ) ) {
				foreach ( $menu_settings as $menu_item ) {
					$found_key   = array_search( $menu_item['id'], array_column( $menu_items, 'id' ), true );
					$existed_key = array_search( $menu_item['id'], array_column( $actual_menu, 'id' ), true );

					if ( -1 < $found_key && ! $existed_key ) {
						$actual_menu[] = $menu_items[ $found_key ];
					}
				}
			}

			$menu_items = $actual_menu;
		}

		return $this->sorted_menu_add_elements( $menu_items, $menu_name );
	}

	/**
	 * Add menu items to sortable menu.
	 */
	public function sorted_menu_add_elements( $menu_items, $menu_name ) {
		$is_instructor = STM_LMS_Instructor::is_instructor();
		$default_menu  = self::float_menu_items();
		$settings      = get_option( 'stm_lms_settings', array() );

		foreach ( $default_menu as $menu_item ) {
			if ( isset( $menu_item['id'] ) && ! $this->search_item_in_sortable_menu( $settings, $menu_name, $menu_item['id'] ) ) {
				$add_element     = false;
				$student_menu    = 'sorting_float_menu_learning' === $menu_name || 'sorting_the_menu_student' === $menu_name;
				$instructor_menu = 'sorting_the_menu' === $menu_name || 'sorting_float_menu_main' === $menu_name;
				$full_menu       = 'sorting_the_menu' === $menu_name || $student_menu;
				switch ( $menu_item['id'] ) {
					case 'settings':
						$add_element = true;
						break;
					case 'add_student':
						$add_element = STM_LMS_Instructor::instructor_can_add_students() && $is_instructor && $instructor_menu;
						break;
					case 'enrolled-students':
						$add_element = STM_LMS_Instructor::instructor_show_list_students() && $is_instructor && $instructor_menu;
						break;
					case 'assignments':
						$add_element = class_exists( '\MasterStudy\Lms\Pro\addons\assignments\Assignments' ) && $is_instructor && $instructor_menu;
						break;
					case 'add_course':
						$add_element = $is_instructor && $instructor_menu;
						break;
					case 'announcement':
						$add_element = stm_wpcfto_is_pro() && $is_instructor && $instructor_menu;
						break;
					case 'bundles':
						$add_element = class_exists( '\MasterStudy\Lms\Pro\addons\CourseBundle\CourseBundle' ) && $instructor_menu;
						break;
					case 'google_meets':
						$enabled_addons = Addons::enabled_addons();
						if ( isset( $enabled_addons['google_meet'] ) && true === $enabled_addons['google_meet'] && $instructor_menu ) {
							$add_element = true;
						}
						break;
					case 'gradebook':
						$add_element = class_exists( '\MasterStudy\Lms\Pro\addons\gradebook\Gradebook' ) && $instructor_menu;
						break;
					case 'payout':
						$add_element = class_exists( 'Stm_Lms_Statistics' ) && ! current_user_can( 'administrator' ) && $instructor_menu;
						break;
					case 'memberships':
						$add_element = STM_LMS_Subscriptions::subscription_enabled() && $full_menu;
						break;
					case 'my_assignments':
						$add_element = class_exists( '\MasterStudy\Lms\Pro\addons\assignments\Assignments' ) && $full_menu;
						break;
					case 'certificates':
						$add_element = is_ms_lms_addon_enabled( 'certificate_builder' ) && $full_menu;
						break;
					case 'instructor-certificates':
						$add_element = is_ms_lms_addon_enabled( 'certificate_builder' ) && $is_instructor && $instructor_menu;
						break;
					case 'groups':
						$add_element = class_exists( 'STM_LMS_Enterprise_Courses' ) && $full_menu;
						break;
					case 'my_points':
						$add_element = class_exists( 'STM_LMS_Point_System_Interface' ) && $full_menu;
						break;
					case 'analytics':
						$add_element = STM_LMS_Helpers::is_pro_plus() && $instructor_menu;
						break;
					case 'sales':
						$add_element = STM_LMS_Helpers::is_pro_plus() && $instructor_menu;
						break;
					case 'grades':
						$add_element = STM_LMS_Helpers::is_pro_plus() && $instructor_menu;
						break;
					case 'my-grades':
						$add_element = STM_LMS_Helpers::is_pro_plus() && $full_menu;
						break;
					case 'lesson_notes':
						$add_element = STM_LMS_Helpers::is_pro_plus() && ! empty( $settings['course_player_lesson_notes'] ) && $full_menu;
						break;
					case 'my-subscriptions':
						$add_element = is_ms_lms_addon_enabled( Addons::SUBSCRIPTIONS ) && $full_menu;
						break;
				}

				if ( $add_element ) {
					$menu_items[] = $menu_item;
				}
			}
		}

		if ( ! $is_instructor ) {
			$menu_items = apply_filters( 'stm_lms_sorted_student_menu', $menu_items );
		}

		return $this->sorted_menu_add_badges( $menu_items );
	}

	/**
	 * Removes inactive menu items from the sortable menu.
	 */
	public function sorted_menu_add_badges( $menu_items ) {
		$user_id = get_current_user_id();

		return array_map(
			function ( $menu_item ) use ( $user_id ) {
				if ( isset( $menu_item['id'] ) ) {
					switch ( $menu_item['id'] ) {
						case 'assignments':
							if ( class_exists( 'MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentTeacherRepository' ) ) {
								$menu_item['badge_count'] = MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentTeacherRepository::total_pending_assignments( $user_id );
							}
							break;
						case 'my_assignments':
							$menu_item['badge_count'] = STM_LMS_User_Assignment::my_assignments_statuses( $user_id );
							break;
						case 'my_points':
							$menu_item['badge_count'] = count( stm_lms_get_incompleted_user_points( $user_id ) );
							break;
						case 'messages':
							$menu_item['badge_count'] = STM_LMS_Chat::user_new_messages( $user_id );
							break;
					}
				}

				return $menu_item;
			},
			$menu_items
		);
	}

	/**
	 * Removes menu items that should not be shown to the student
	 */
	public function sorting_student_menu( $menu_items ) {
		$disabled_items = array(
			'dashboard',
			'divider',
			'gradebook',
			'assignments',
			'instructor-certificates',
			'bundles',
			'add_course',
			'add_student',
			'google_meets',
			'payout',
			'announcement',
			'analytics',
			'enrolled-students',
			'sales',
			'grades',
			'students',
		);

		return array_values(
			array_filter(
				$menu_items,
				function ( $menu_item ) use ( $disabled_items ) {
					$menu_id = $menu_item['id'] ?? null;

					return ! empty( $menu_id ) && ! in_array( $menu_id, $disabled_items, true );
				}
			)
		);
	}

	public function search_item_in_sortable_menu( $settings, $menu_name, $menu_item ) {
		if ( empty( $settings[ $menu_name ] ) ) {
			return true;
		}

		foreach ( $settings[ $menu_name ] as $menu ) {
			if ( -1 < array_search( $menu_item, array_column( $menu['options'] ?? array(), 'id' ), true ) ) {
				return true;
			}
		}

		return false;
	}

	public function float_menu_placed_items( $menu_items, $menu_place ) {
		return array_values(
			array_filter(
				$menu_items,
				function ( $menu_item ) use ( $menu_place ) {
					return ( $menu_item['menu_place'] ?? null ) === $menu_place &&
						'divider' !== ( $menu_item['type'] ?? null ) &&
						'settings' !== ( $menu_item['id'] ?? null );
				}
			)
		);
	}

	public function settings_menu_prepare_data( $menu_items ) {
		$menu_options = array();

		foreach ( $menu_items as $menu_item ) {
			$menu_options[] = array(
				'id'         => $menu_item['id'] ?? null,
				'label'      => $menu_item['menu_title'] ?? '',
				'menu_place' => $menu_item['menu_place'] ?? '',
			);
		}

		return $menu_options;
	}

	public static function get_menu_options( $menu ) {
		return $menu[0]['options'] ?? false;
	}

	public static function get_account_menu_sections( array $menu_items ): array {
		$section_labels = array(
			'main'          => esc_html__( 'Main', 'masterstudy-lms-learning-management-system' ),
			'communication' => esc_html__( 'Communication', 'masterstudy-lms-learning-management-system' ),
			'progress'      => esc_html__( 'Progress', 'masterstudy-lms-learning-management-system' ),
			'account'       => esc_html__( 'Account and settings', 'masterstudy-lms-learning-management-system' ),
			'finance'       => esc_html__( 'Finance', 'masterstudy-lms-learning-management-system' ),
		);

		$section_labels = apply_filters( 'masterstudy_account_menu_section_labels', $section_labels );
		$sections       = array();

		foreach ( $menu_items as $menu_item ) {
			$section_key = isset( $menu_item['section'] ) ? sanitize_key( (string) $menu_item['section'] ) : 'main';

			if ( ! isset( $sections[ $section_key ] ) ) {
				$sections[ $section_key ] = array();
			}

			$sections[ $section_key ][] = $menu_item;
		}

		$sections['account']   = $sections['account'] ?? array();
		$sections['account'][] = array(
			'order'      => 999999,
			'id'         => 'logout',
			'slug'       => 'logout',
			'menu_title' => esc_html__( 'Log out', 'masterstudy-lms-learning-management-system' ),
			'menu_icon'  => 'stmlms-menu-logout',
			'menu_url'   => wp_logout_url( get_home_url() ),
			'section'    => 'account',
			'is_active'  => false,
		);

		$desired_order = apply_filters(
			'masterstudy_account_menu_section_order',
			array( 'main', 'communication', 'progress', 'finance', 'account' )
		);

		$final_order = array_values(
			array_unique(
				array_merge( $desired_order, array_keys( $sections ) )
			)
		);

		return array(
			'list'   => $sections,
			'order'  => $final_order,
			'labels' => $section_labels,
		);
	}
}
