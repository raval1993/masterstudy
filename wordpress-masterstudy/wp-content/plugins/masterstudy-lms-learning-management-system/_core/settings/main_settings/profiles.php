<?php
function stm_lms_settings_profiles_section() {
	$pages                    = WPCFTO_Settings::stm_get_post_type_array( 'page' );
	$submenu_general          = esc_html__( 'General', 'masterstudy-lms-learning-management-system' );
	$submenu_auth             = esc_html__( 'Authorization', 'masterstudy-lms-learning-management-system' );
	$submenu_social_login     = esc_html__( 'Social Login', 'masterstudy-lms-learning-management-system' );
	$is_pro                   = STM_LMS_Helpers::is_pro();
	$is_pro_plus              = STM_LMS_Helpers::is_pro_plus();
	$layout_preview_label     = esc_html__( 'Demo preview', 'masterstudy-lms-learning-management-system' );
	$instructor_public_layout = array(
		array(
			'value'         => 'compact',
			'alt'           => esc_html__( 'Compact', 'masterstudy-lms-learning-management-system' ),
			'img'           => STM_LMS_URL . 'assets/img/instructor-compact.png',
			'preview_url'   => 'https://masterstudy.stylemixthemes.com/lms-plugin/instructor-public-account/3139/?public=compact',
			'preview_label' => $layout_preview_label,
			'disabled'      => false,
		),
		array(
			'value'         => 'extended',
			'alt'           => esc_html__( 'Extended', 'masterstudy-lms-learning-management-system' ),
			'img'           => STM_LMS_URL . 'assets/img/instructor-expanded.png',
			'preview_url'   => 'https://masterstudy.stylemixthemes.com/lms-plugin/instructor-public-account/3139/?public=extended',
			'preview_label' => $layout_preview_label,
			'disabled'      => false,
		),
	);
	$student_public_layout    = array(
		array(
			'value'         => 'compact',
			'alt'           => esc_html__( 'Compact', 'masterstudy-lms-learning-management-system' ),
			'img'           => STM_LMS_URL . 'assets/img/student-compact.png',
			'preview_url'   => 'https://masterstudy.stylemixthemes.com/lms-plugin/student-public-account/3394/?public=compact',
			'preview_label' => $layout_preview_label,
			'disabled'      => false,
		),
		array(
			'value'         => 'extended',
			'alt'           => esc_html__( 'Extended', 'masterstudy-lms-learning-management-system' ),
			'img'           => STM_LMS_URL . 'assets/img/student-expanded.png',
			'preview_url'   => 'https://masterstudy.stylemixthemes.com/lms-plugin/student-public-account/3394/?public=extended',
			'preview_label' => $layout_preview_label,
			'disabled'      => false,
		),
	);

	$general_fields = array(
		'pro_banner'                        => array(
			'type'    => 'pro_banner',
			'label'   => esc_html__( 'Course pre-moderation', 'masterstudy-lms-learning-management-system' ),
			'img'     => STM_LMS_URL . 'assets/img/pro-features/course-premoderation.png',
			'desc'    => esc_html__( 'This will help you maintain quality control and student confidence. Courses from instructors will need admin approval before their publication.', 'masterstudy-lms-learning-management-system' ),
			'submenu' => $submenu_general,
			'hint'    => esc_html__( 'Enable', 'masterstudy-lms-learning-management-system' ),
		),
		'instructor_public_profile'         => array(
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Instructor public profile', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( "Display the instructor's public profile with bio, courses, and achievements", 'masterstudy-lms-learning-management-system' ),
			'value'       => true,
			'submenu'     => $submenu_general,
		),
		'instructor_reviews_public_profile' => array(
			'type'         => 'checkbox',
			'label'        => esc_html__( "Show reviews on the instructor's page", 'masterstudy-lms-learning-management-system' ),
			'description'  => esc_html__( 'Enable to display student reviews for courses', 'masterstudy-lms-learning-management-system' ),
			'submenu'      => $submenu_general,
			'value'        => true,
			'dependency'   => array(
				array(
					'key'   => 'instructor_public_profile',
					'value' => 'not_empty',
				),
				array(
					'key'     => 'course_tab_reviews',
					'value'   => 'not_empty',
					'section' => 'section_course',
				),
			),
			'dependencies' => '&&',
		),
		'instructor_public_profile_style'   => array(
			'type'       => 'data_select',
			'label'      => esc_html__( 'Choose a layout for the instructor profile', 'masterstudy-lms-learning-management-system' ),
			'options'    => $instructor_public_layout,
			'value'      => 'compact',
			'submenu'    => $submenu_general,
			'dependency' => array(
				'key'   => 'instructor_public_profile',
				'value' => 'not_empty',
			),
		),
		'student_public_profile'            => array(
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Student public profile', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Show courses, certificates, quizzes, and other data', 'masterstudy-lms-learning-management-system' ),
			'value'       => true,
			'submenu'     => $submenu_general,
		),
		'student_stats_public_profile'      => array(
			'type'        => 'checkbox',
			'label'       => esc_html__( "Show progress on the student's page", 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Display information on learning progress', 'masterstudy-lms-learning-management-system' ),
			'submenu'     => $submenu_general,
			'dependency'  => array(
				'key'   => 'student_public_profile',
				'value' => 'not_empty',
			),
		),
		'show_students_to_instructors'      => array(
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Show students page to instructors', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Allow instructors to view all their students and track individual progress.', 'masterstudy-lms-learning-management-system' ),
			'submenu'     => $submenu_general,
		),
		'student_public_profile_style'      => array(
			'type'       => 'data_select',
			'label'      => esc_html__( 'Choose a layout for the student profile', 'masterstudy-lms-learning-management-system' ),
			'options'    => $student_public_layout,
			'value'      => 'compact',
			'submenu'    => $submenu_general,
			'dependency' => array(
				'key'   => 'student_public_profile',
				'value' => 'not_empty',
			),
		),
		'instructor_can_add_students'       => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Allow instructors to add students to their own course', 'masterstudy-lms-learning-management-system' ),
			'hint'    => esc_html__( 'Decide if instructors can add students to their own courses', 'masterstudy-lms-learning-management-system' ),
			'submenu' => $submenu_general,
			'pro'     => true,
		),
		'have_a_question_form'              => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Have a question form', 'masterstudy-lms-learning-management-system' ),
			'value'   => true,
			'submenu' => $submenu_general,
		),
		'instructors_page'                  => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Instructors archive page', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( "Choose the page where all instructors' profiles will be displayed", 'masterstudy-lms-learning-management-system' ),
			'options'     => $pages,
			'submenu'     => $submenu_general,
		),
		'cancel_subscription'               => array(
			'type'    => 'select',
			'label'   => esc_html__( 'Cancel subscription page', 'masterstudy-lms-learning-management-system' ),
			'options' => $pages,
			'hint'    => esc_html__( 'Choose the page where users can cancel their subscriptions', 'masterstudy-lms-learning-management-system' ),
			'submenu' => $submenu_general,
		),
		'float_menu'                        => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Side profile menu', 'masterstudy-lms-learning-management-system' ),
			'value'   => false,
			'submenu' => $submenu_general,
			'hint'    => esc_html__( 'Enable a side menu for user profiles', 'masterstudy-lms-learning-management-system' ),
		),
		'float_menu_guest'                  => array(
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Side profile menu for guest users', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( "Enable a side menu for guest users' profiles", 'masterstudy-lms-learning-management-system' ),
			'value'       => true,
			'dependency'  => array(
				'key'   => 'float_menu',
				'value' => 'not_empty',
			),
			'submenu'     => $submenu_general,
		),
		'float_menu_position'               => array(
			'type'        => 'select',
			'label'       => esc_html__( 'Side profile menu position', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Choose whether the side profile menu appears on the left or right side of the page', 'masterstudy-lms-learning-management-system' ),
			'options'     => array(
				'left'  => esc_html__( 'Left', 'masterstudy-lms-learning-management-system' ),
				'right' => esc_html__( 'Right', 'masterstudy-lms-learning-management-system' ),
			),
			'value'       => 'left',
			'dependency'  => array(
				'key'   => 'float_menu',
				'value' => 'not_empty',
			),
			'submenu'     => $submenu_general,
		),
		/*GROUP STARTED*/
		'float_background_color'            => array(
			'group'       => 'started',
			'type'        => 'color',
			'label'       => esc_html__( 'Background color', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Choose the background color for the side profile menu', 'masterstudy-lms-learning-management-system' ),
			'columns'     => '33',
			'group_title' => esc_html__( 'Side Profile Menu Colors', 'masterstudy-lms-learning-management-system' ),
			'dependency'  => array(
				'key'   => 'float_menu',
				'value' => 'not_empty',
			),
			'submenu'     => $submenu_general,
		),
		'float_text_color'                  => array(
			'group'       => 'ended',
			'type'        => 'color',
			'label'       => esc_html__( 'Text color', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Choose the text color for the side profile menu', 'masterstudy-lms-learning-management-system' ),
			'columns'     => '33',
			'dependency'  => array(
				'key'   => 'float_menu',
				'value' => 'not_empty',
			),
			'submenu'     => $submenu_general,
		),
		'masterstudy_limit_active_sessions' => array(
			'group'       => 'started',
			'group_title' => esc_html__( 'Active Login Sessions', 'masterstudy-lms-learning-management-system' ),
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Limit Active Sessions', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Enable to limit how many active login sessions a user can have at the same time.', 'masterstudy-lms-learning-management-system' ),
			'submenu'     => $submenu_auth,
			'value'       => false,
		),
		'masterstudy_max_active_sessions'   => array(
			'group'       => 'ended',
			'type'        => 'number',
			'label'       => esc_html__( 'Max Active Sessions', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Set the maximum number of active login sessions allowed per user.', 'masterstudy-lms-learning-management-system' ),
			'submenu'     => $submenu_auth,
			'value'       => 2,
			'min'         => 1,
			'dependency'  => array(
				'key'   => 'masterstudy_limit_active_sessions',
				'value' => 'not_empty',
			),
		),
		'user_premoderation'                => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Email confirmation', 'masterstudy-lms-learning-management-system' ),
			'hint'    => esc_html__( 'Decide if new users need to confirm their email addresses through a verification email', 'masterstudy-lms-learning-management-system' ),
			'submenu' => $submenu_auth,
		),
		'authorization_shortcode'           => array(
			'type'     => 'text',
			'label'    => esc_html__( 'Shortcode for authorization form', 'masterstudy-lms-learning-management-system' ),
			'value'    => '[masterstudy_authorization_form]',
			'hint'     => esc_html__( 'Add type="login" or type="register" to choose the starting form', 'masterstudy-lms-learning-management-system' ),
			'submenu'  => $submenu_auth,
			'readonly' => true,
		),
		'restrict_registration'             => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Turn off registration for all new users', 'masterstudy-lms-learning-management-system' ),
			'submenu' => $submenu_auth,
			'value'   => false,
		),
		'register_as_instructor'            => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Instructor registration', 'masterstudy-lms-learning-management-system' ),
			'hint'    => esc_html__( 'By disabling the instructor registration, you remove the checkbox "I want to sign up as instructor" from the registration form', 'masterstudy-lms-learning-management-system' ),
			'submenu' => $submenu_auth,
			'value'   => true,
		),
		'instructor_premoderation'          => array(
			'type'       => 'checkbox',
			'label'      => esc_html__( 'Instructor pre-moderation', 'masterstudy-lms-learning-management-system' ),
			'hint'       => esc_html__( 'Decide if admin approval is required for users to become instructors', 'masterstudy-lms-learning-management-system' ),
			'submenu'    => $submenu_auth,
			'value'      => true,
			'dependency' => array(
				'key'   => 'register_as_instructor',
				'value' => 'not_empty',
			),
		),
		'separate_instructor_registration'  => array(
			'type'       => 'checkbox',
			'label'      => esc_html__( 'Show instructor registration form on separate page', 'masterstudy-lms-learning-management-system' ),
			'hint'       => esc_html__( 'Choose where instructors can register: on the same page as students or on a separate page', 'masterstudy-lms-learning-management-system' ),
			'submenu'    => $submenu_auth,
			'value'      => false,
			'dependency' => array(
				'key'   => 'register_as_instructor',
				'value' => 'not_empty',
			),
		),
		'instructor_registration_page'      => array(
			'type'         => 'select',
			'label'        => esc_html__( 'Instructor registration page', 'masterstudy-lms-learning-management-system' ),
			'hint'         => esc_html__( 'Select a separate page with a form for instructor registration', 'masterstudy-lms-learning-management-system' ),
			'options'      => WPCFTO_Settings::stm_get_post_type_array( 'page' ),
			'submenu'      => $submenu_auth,
			'dependency'   => array(
				array(
					'key'   => 'separate_instructor_registration',
					'value' => 'not_empty',
				),
				array(
					'key'   => 'register_as_instructor',
					'value' => 'not_empty',
				),
			),
			'dependencies' => '&&',
		),
		'instructor_registration_link'      => array(
			'type'         => 'select',
			'label'        => esc_html__( 'Show a link to a separate page in the form', 'masterstudy-lms-learning-management-system' ),
			'hint'         => esc_html__( 'Enable this if you want to show a link to a separate page for instructor registration in the authorization form', 'masterstudy-lms-learning-management-system' ),
			'submenu'      => $submenu_auth,
			'options'      => array(
				'hide' => esc_html__( 'Hide', 'masterstudy-lms-learning-management-system' ),
				'show' => esc_html__( 'Show', 'masterstudy-lms-learning-management-system' ),
			),
			'value'        => 'hide',
			'dependency'   => array(
				array(
					'key'   => 'separate_instructor_registration',
					'value' => 'not_empty',
				),
				array(
					'key'   => 'register_as_instructor',
					'value' => 'not_empty',
				),
			),
			'dependencies' => '&&',
		),
		'instructor_registration_shortcode' => array(
			'type'        => 'text',
			'label'       => esc_html__( 'Shortcode for the instructor registration form', 'masterstudy-lms-learning-management-system' ),
			'description' => esc_html__( 'Use this shortcode to display the instructor registration form on your website', 'masterstudy-lms-learning-management-system' ),
			'value'       => '[masterstudy_instructor_registration]',
			'submenu'     => $submenu_auth,
			'dependency'  => array(
				'key'   => 'register_as_instructor',
				'value' => 'not_empty',
			),
			'readonly'    => true,
		),
		'registration_strength_password'    => array(
			'group'       => 'started',
			'group_title' => esc_html__( 'Password requirements', 'masterstudy-lms-learning-management-system' ),
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Show password strength', 'masterstudy-lms-learning-management-system' ),
			'submenu'     => $submenu_auth,
			'value'       => false,
		),
		'registration_weak_password'        => array(
			'group'   => 'ended',
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Allow weak passwords', 'masterstudy-lms-learning-management-system' ),
			'submenu' => $submenu_auth,
			'value'   => false,
		),
	);

	if ( ! $is_pro_plus ) {
		$general_fields = array_diff_key(
			$general_fields,
			array(
				'masterstudy_limit_active_sessions' => true,
				'masterstudy_max_active_sessions'   => true,
			)
		);
	}

	$is_social_login_enabled = is_ms_lms_addon_enabled( 'social_login' );

	if ( $is_pro_plus && $is_social_login_enabled ) {
		$social_login_fields = array(
			'social_login_position'              => array(
				'type'        => 'select',
				'label'       => esc_html__( 'Social login position in authorization form', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Choose whether the social login buttons appear at the top or bottom of the authorization form', 'masterstudy-lms-learning-management-system' ),
				'options'     => array(
					'top'    => esc_html__( 'Top', 'masterstudy-lms-learning-management-system' ),
					'bottom' => esc_html__( 'Bottom', 'masterstudy-lms-learning-management-system' ),
				),
				'value'       => 'top',
				'submenu'     => $submenu_social_login,
			),
			/*GROUP STARTED*/
			'social_login_google_enabled'        => array(
				'group'       => 'started',
				'group_title' => esc_html__( 'Google', 'masterstudy-lms-learning-management-system' ),
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Login via Google', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Allow users to sign in using their Google accounts', 'masterstudy-lms-learning-management-system' ),
				'value'       => false,
				'submenu'     => $submenu_social_login,
			),
			'social_login_google_client_id'      => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Client ID', 'masterstudy-lms-learning-management-system' ),
				'description' => sprintf(
					'%1$s <a href="%2$s" target="_blank">%3$s</a>',
					esc_html__( 'You can get Client ID from Google APIs console.', 'masterstudy-lms-learning-management-system' ),
					'https://docs.stylemixthemes.com/masterstudy-lms/lms-pro-addons/social-login',
					esc_html__( 'Learn more', 'masterstudy-lms-learning-management-system' ),
				),
				'placeholder' => esc_html__( 'Enter your Google Client ID here', 'masterstudy-lms-learning-management-system' ),
				'value'       => '',
				'submenu'     => $submenu_social_login,
				'dependency'  => array(
					'key'   => 'social_login_google_enabled',
					'value' => 'not_empty',
				),
			),
			'social_login_google_client_secret'  => array(
				'type'        => 'text',
				'label'       => esc_html__( 'Client secret', 'masterstudy-lms-learning-management-system' ),
				'description' => sprintf(
					'%1$s <a href="%2$s" target="_blank">%3$s</a>',
					esc_html__( 'You can get Secret Key from Google APIs console.', 'masterstudy-lms-learning-management-system' ),
					'https://docs.stylemixthemes.com/masterstudy-lms/lms-pro-addons/social-login',
					esc_html__( 'Learn more', 'masterstudy-lms-learning-management-system' ),
				),
				'placeholder' => esc_html__( 'Enter your Google Client Secret key here', 'masterstudy-lms-learning-management-system' ),
				'value'       => '',
				'submenu'     => $submenu_social_login,
				'dependency'  => array(
					'key'   => 'social_login_google_enabled',
					'value' => 'not_empty',
				),
			),
			'social_login_google_redirect_url'   => array(
				'group'       => 'ended',
				'type'        => 'text',
				'label'       => esc_html__( 'Redirect URL', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Copy and paste the redirect URL to the Authorized redirect URL in the Google API Console', 'masterstudy-lms-learning-management-system' ),
				'value'       => site_url( '/?addon=social_login&provider=google' ),
				'submenu'     => $submenu_social_login,
				'dependency'  => array(
					'key'   => 'social_login_google_enabled',
					'value' => 'not_empty',
				),
				'readonly'    => true,
			),
			/*GROUP STARTED*/
			'social_login_facebook_enabled'      => array(
				'group'       => 'started',
				'group_title' => esc_html__( 'Facebook', 'masterstudy-lms-learning-management-system' ),
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Login via Facebook', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Allow users to sign in using their Facebook accounts', 'masterstudy-lms-learning-management-system' ),
				'value'       => false,
				'submenu'     => $submenu_social_login,
			),
			'social_login_facebook_app_id'       => array(
				'type'        => 'text',
				'label'       => esc_html__( 'App ID', 'masterstudy-lms-learning-management-system' ),
				'description' => sprintf(
					'%1$s <a href="%2$s" target="_blank">%3$s</a>',
					esc_html__( 'You can get App ID from Facebook Developers Page.', 'masterstudy-lms-learning-management-system' ),
					'https://docs.stylemixthemes.com/masterstudy-lms/lms-pro-addons/social-login',
					esc_html__( 'Learn more', 'masterstudy-lms-learning-management-system' ),
				),
				'placeholder' => esc_html__( 'Enter your Facebook Client Token here', 'masterstudy-lms-learning-management-system' ),
				'value'       => '',
				'submenu'     => $submenu_social_login,
				'dependency'  => array(
					'key'   => 'social_login_facebook_enabled',
					'value' => 'not_empty',
				),
			),
			'social_login_facebook_app_secret'   => array(
				'type'        => 'text',
				'label'       => esc_html__( 'App Secret', 'masterstudy-lms-learning-management-system' ),
				'description' => sprintf(
					'%1$s <a href="%2$s" target="_blank">%3$s</a>',
					esc_html__( 'You can get App Secret from Facebook Developers Page.', 'masterstudy-lms-learning-management-system' ),
					'https://docs.stylemixthemes.com/masterstudy-lms/lms-pro-addons/social-login',
					esc_html__( 'Learn more', 'masterstudy-lms-learning-management-system' ),
				),
				'placeholder' => esc_html__( 'Enter your Facebook Access Token here', 'masterstudy-lms-learning-management-system' ),
				'value'       => '',
				'submenu'     => $submenu_social_login,
				'dependency'  => array(
					'key'   => 'social_login_facebook_enabled',
					'value' => 'not_empty',
				),
			),
			'social_login_facebook_redirect_url' => array(
				'group'       => 'ended',
				'type'        => 'text',
				'label'       => esc_html__( 'Redirect URL', 'masterstudy-lms-learning-management-system' ),
				'description' => esc_html__( 'Copy and paste the redirect URL to the Site URL in the Facebook Developers Page', 'masterstudy-lms-learning-management-system' ),
				'value'       => site_url( '/?addon=social_login&provider=facebook' ),
				'submenu'     => $submenu_social_login,
				'dependency'  => array(
					'key'   => 'social_login_facebook_enabled',
					'value' => 'not_empty',
				),
				'readonly'    => true,
			),
		);
	}

	if ( ! $is_pro_plus || ( $is_pro_plus && ! $is_social_login_enabled ) ) {
		$social_login_fields = array(
			'pro_banner_social_login' => array(
				'type'        => 'pro_banner',
				'label'       => esc_html__( 'Social Login', 'masterstudy-lms-learning-management-system' ),
				'img'         => STM_LMS_URL . 'assets/img/pro-features/addons/social-login.png',
				'desc'        => esc_html__( "Users can log in using their Google or Facebook accounts with this addon. No more struggling with passwords – just one click and they're in!", 'masterstudy-lms-learning-management-system' ),
				'submenu'     => $submenu_social_login,
				'is_enable'   => $is_pro_plus && ! $is_social_login_enabled,
				'search'      => esc_html__( 'Social Login', 'masterstudy-lms-learning-management-system' ),
				'is_pro_plus' => true,
				'utm_url'     => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=mswpadmin&utm_medium=social-login-addon-button&utm_campaign=masterstudy-plugin',
			),
		);
	}
	$general_fields = array_merge( $general_fields, $social_login_fields ?? array() );

	if ( $is_pro ) {
		$course_moderation_field = array(
			'course_premoderation' => array(
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Course pre-moderation', 'masterstudy-lms-learning-management-system' ),
				'hint'    => esc_html__( 'Choose whether courses need approval before they are published', 'masterstudy-lms-learning-management-system' ),
				'pro'     => true,
				'pro_url' => admin_url( 'admin.php?page=stm-lms-go-pro&source=pre-moderation-profile-settings' ),
				'submenu' => $submenu_general,
			),
		);

		$general_fields = array_merge( $course_moderation_field, $general_fields );
	}

	return array(
		'name'   => esc_html__( 'Profiles', 'masterstudy-lms-learning-management-system' ),
		'label'  => esc_html__( 'Profiles Settings', 'masterstudy-lms-learning-management-system' ),
		'icon'   => 'stmlms-user-circle',
		'fields' => array_merge( $general_fields, stm_lms_settings_sorting_the_menu_section() ),
	);
}
