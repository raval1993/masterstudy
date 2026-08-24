<?php

( new STM_LMS_Students() )->init();

class STM_LMS_Students {
	public function init() {
		add_action( 'admin_menu', array( $this, 'manage_users' ), 10001 );

		add_filter( 'stm_lms_menu_items', array( $this, 'add_menu_item' ) );
	}

	public function add_menu_item( $menus ) {
		if ( ! STM_LMS_Instructor::instructor_show_list_students() || ! STM_LMS_Instructor::is_instructor() ) {
			return $menus;
		}

		$current_slug = STM_LMS_User_Menu::get_current_account_slug();

		$menus[] = array(
			'order'        => 180,
			'id'           => 'enrolled-students',
			'slug'         => 'enrolled-students',
			'lms_template' => 'stm-lms-enrolled-students',
			'menu_title'   => esc_html__( 'Students', 'masterstudy-lms-learning-management-system' ),
			'menu_icon'    => 'stmlms-menu-students',
			'menu_url'     => ms_plugin_user_account_url( 'enrolled-students' ),
			'is_active'    => 'enrolled-students' === $current_slug,
			'menu_place'   => 'main',
			'section'      => 'progress',
		);

		return $menus;
	}

	public function manage_users() {
		add_submenu_page(
			'stm-lms-settings',
			esc_html__( 'Students', 'masterstudy-lms-learning-management-system' ),
			'<span class="stm-lms-students-menu-title"><span class="stm-lms-menu-text">' . esc_html__( 'Students', 'masterstudy-lms-learning-management-system' ) . '</span></span>',
			'manage_options',
			'manage_students',
			array( $this, 'manage_users_template' ),
			( stm_lms_addons_menu_position() + 1 )
		);
	}

	public static function manage_users_template() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$student_id = intval( isset( $_GET['user_id'] ) ? wp_unslash( $_GET['user_id'] ) : 0 );

		if ( STM_LMS_Helpers::is_pro_plus() && ! empty( $student_id ) ) {
			STM_LMS_Templates::show_lms_template( 'account/instructor/enrolled-student' );
		} elseif ( ! empty( $student_id ) ) {
			STM_LMS_Templates::show_lms_template( 'account/instructor/parts/students-list' );
		} else {
			STM_LMS_Templates::show_lms_template( 'components/admin-react-app/main' );
		}
	}
}
