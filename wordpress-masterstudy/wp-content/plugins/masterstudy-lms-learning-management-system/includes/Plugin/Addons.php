<?php

namespace MasterStudy\Lms\Plugin;

class Addons {
	private const OPTION_NAME = 'stm_lms_addons';

	public const UDEMY               = 'udemy';
	public const PREREQUISITE        = 'prerequisite';
	public const ONLINE_TESTING      = 'online_testing';
	public const STATISTICS          = 'statistics';
	public const SHAREWARE           = 'shareware';
	public const DRIP_CONTENT        = 'sequential_drip_content';
	public const GRADEBOOK           = 'gradebook';
	public const COOMING_SOON        = 'coming_soon';
	public const LIVE_STREAMS        = 'live_streams';
	public const ENTERPRISE_COURSES  = 'enterprise_courses';
	public const ASSIGNMENTS         = 'assignments';
	public const POINT_SYSTEM        = 'point_system';
	public const COURSE_BUNDLE       = 'course_bundle';
	public const MULTI_INSTRUCTORS   = 'multi_instructors';
	public const GOOGLE_CLASSROOMS   = 'google_classrooms';
	public const ZOOM_CONFERENCE     = 'zoom_conference';
	public const SCORM               = 'scorm';
	public const EMAIL_MANAGER       = 'email_manager';
	public const EMAIL_BRANDING      = 'email_branding';
	public const CERTIFICATE_BUILDER = 'certificate_builder';
	public const FORM_BUILDER        = 'form_builder';
	public const MEDIA_LIBRARY       = 'media_library';
	public const GOOGLE_MEET         = 'google_meet';
	public const QUESTION_MEDIA      = 'question_media';
	public const SOCIAL_LOGIN        = 'social_login';
	public const AUDIO_LESSON        = 'audio_lesson';
	public const GRADES              = 'grades';
	public const AI_LAB              = 'ai_lab';
	public const SUBSCRIPTIONS       = 'subscriptions';

	public static function all(): array {
		return array(
			self::UDEMY,
			self::PREREQUISITE,
			self::ONLINE_TESTING,
			self::STATISTICS,
			self::SHAREWARE,
			self::DRIP_CONTENT,
			self::GRADEBOOK,
			self::LIVE_STREAMS,
			self::ENTERPRISE_COURSES,
			self::ASSIGNMENTS,
			self::POINT_SYSTEM,
			self::COURSE_BUNDLE,
			self::MULTI_INSTRUCTORS,
			self::GOOGLE_CLASSROOMS,
			self::ZOOM_CONFERENCE,
			self::SCORM,
			self::EMAIL_MANAGER,
			self::EMAIL_BRANDING,
			self::CERTIFICATE_BUILDER,
			self::FORM_BUILDER,
			self::MEDIA_LIBRARY,
			self::GOOGLE_MEET,
			self::QUESTION_MEDIA,
			self::SOCIAL_LOGIN,
			self::AUDIO_LESSON,
			self::GRADES,
			self::AI_LAB,
			self::SUBSCRIPTIONS,
		);
	}

	public static function enabled_addons(): array {
		return array_map(
			function ( $value ) {
				return (bool) $value;
			},
			get_option( self::OPTION_NAME, array() )
		);
	}

	public static function list(): array {
		$addons_list = array(
			self::CERTIFICATE_BUILDER => array(
				'name'          => esc_html__( 'Certificate Builder', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/certtificate_builder.png' ),
				'settings'      => admin_url( 'admin.php?page=certificate_builder' ),
				'description'   => esc_html__( 'Сreate and design your own certificates to award them to students after the course completion.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-certificatebuilder&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'certificate-builder',
				'video_url'     => 'https://www.youtube.com/embed/oi_7pfhqhgw?rel=0&autoplay=1',
			),
			self::SUBSCRIPTIONS       => array(
				'name'          => esc_html__( 'Subscriptions', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/subscriptions.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings&submenu=subscriptions#section_ecommerce' ),
				'description'   => esc_html__( 'Allow students to access courses with easy-to-use subscription plans. Manage memberships, recurring payments, and give learners continuous access to your content.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-subscriptions&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'pro_plus'      => true,
				'documentation' => 'subscriptions',
				'video_url' => 'https://www.youtube.com/embed/7NqPcDGVOZM?rel=0&amp;autoplay=1',
			),
			self::EMAIL_MANAGER       => array(
				'name'          => esc_html__( 'Email Manager', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/email_manager.png' ),
				'settings'      => admin_url( 'admin.php?page=email_manager_settings' ),
				'description'   => esc_html__( 'Adjust your email templates for different types of notifications and make your messages look good and clear.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-emailmanager&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'email-manager',
				'video_url'     => 'https://www.youtube.com/embed/BuD8xm_fGY4?rel=0&autoplay=1',
			),
			self::EMAIL_BRANDING      => array(
				'name'          => esc_html__( 'Email Branding', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/email_branding.png' ),
				'description'   => esc_html__( 'Adjust your email templates for different types of notifications and make your messages look good and clear.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin-ms&utm_medium=addons&utm_campaign=get-now-addons',
				'pro_plus'      => true,
				'documentation' => 'email-manager',
			),
			self::AI_LAB              => array(
				'name'          => esc_html__( 'AI Lab', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/ai_lab.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings#ai_lab' ),
				'description'   => esc_html__( 'Instantly generate complete lessons and quizzes with AI features. Let AI create structured, ready-to-use content by describing your topic.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ai_lab&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'ai-lab',
				'pro_plus'      => true,
			),
			self::GRADES              => array(
				'name'          => esc_html__( 'Grades', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/grades.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings#section_7' ),
				'description'   => esc_html__( 'Grade assignments and quizzes, and give each student personalized feedback. View all scores in one place to see how your students are doing throughout the course.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=grades&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'grades',
				'pro_plus'      => true,
				'video_url'     => 'https://www.youtube.com/embed/OfDtaP-u6SE?rel=0&autoplay=1',
			),
			self::DRIP_CONTENT        => array(
				'name'          => esc_html__( 'Drip Content', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/sequential.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings&submenu=drip-content#section_2' ),
				'description'   => esc_html__( 'Use this tool to provide a proper flow of the education process. Regulate the order of the lessons by date or in your own sequence.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-dripcontent&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'drip-content',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=481&rel=0&autoplay=1',
			),
			self::ASSIGNMENTS         => array(
				'name'          => esc_html__( 'Assignments', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/assignment.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings&submenu=assignments#section_2' ),
				'description'   => esc_html__( 'Use assignments to check your students\' knowledge. Create interesting tasks for them and ask them to upload essays.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-assignments&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'assignments',
				'video_url'     => 'https://www.youtube.com/embed/Zw3lDxCMyxk?rel=0&autoplay=1',
			),
			self::FORM_BUILDER        => array(
				'name'          => esc_html__( 'LMS Forms Editor', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/custom_fields.png' ),
				'settings'      => admin_url( 'admin.php?page=form_builder' ),
				'description'   => esc_html__( 'Customize the forms in your LMS. Change fields in menus on contact forms, profiles, and registration/login pages.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-formbuilder&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'lms-form-editor',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=236&rel=0&autoplay=1',
			),
			self::PREREQUISITE        => array(
				'name'          => esc_html__( 'Prerequisites', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/msp.png' ),
				'description'   => esc_html__( 'Set the requirements for students. So they will need to complete certain courses before they can enroll in higher-level courses.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-prerequisites&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'prerequisites',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=1331&rel=0&autoplay=1',
			),
			self::MEDIA_LIBRARY       => array(
				'name'          => esc_html__( 'Media File Manager', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/media_library.jpg' ),
				'description'   => esc_html__( 'Manage, keep and load files of various formats while creating e-learning content in the front-end.', 'masterstudy-lms-learning-management-system' ),
				'documentation' => 'media-file-manager',
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings#section_media_library' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=838&rel=0&autoplay=1',
			),
			self::ENTERPRISE_COURSES  => array(
				'name'          => esc_html__( 'Group Courses', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/enterprise-groups.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings&submenu=enterprise-courses#section_2' ),
				'description'   => esc_html__( 'Sell online courses to groups of students. Whether they are an organization, team, or any group interested, offer your courses.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-groupcourses&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'group-courses',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=558&rel=0&autoplay=1',
			),
			self::QUESTION_MEDIA      => array(
				'name'          => esc_html__( 'Question Media', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/question-media.png' ),
				'description'   => esc_html__( 'Quizzes are more interactive and engaging with this addon. Let admins and instructors create quiz questions while adding videos, audio, and images.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-question-media&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'question-media-addon',
				'pro_plus'      => true,
				'video_url'     => 'https://www.youtube.com/embed/3uxU03DV-e8?rel=0&autoplay=1',
			),
			self::SOCIAL_LOGIN        => array(
				'name'          => esc_html__( 'Social Login', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/social-login.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings&submenu=social-login#section_4' ),
				'description'   => esc_html__( "Let your users log in super easily using their Google or Facebook accounts with this addon. No more struggling with passwords – just one click and they're in!", 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=social-login&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'social-login',
				'pro_plus'      => true,
				'video_url'     => 'https://www.youtube.com/embed/NCE3ynXKzGI?rel=0&autoplay=1',
			),
			self::COURSE_BUNDLE       => array(
				'name'          => esc_html__( 'Course Bundle', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/bundle.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings&submenu=course-bundles#section_2' ),
				'description'   => esc_html__( 'Add similar or related courses to the one bundle and sell them as a package at a discount price.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-bundles&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'course-bundles',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=668&rel=0&autoplay=1',
			),
			self::COOMING_SOON        => array(
				'name'          => esc_html__( 'Upcoming Course Status', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/coming-soon.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings&submenu=upcoming-courses#section_2' ),
				'description'   => esc_html__( 'Create and promote courses that are not yet available for enrollment. This addon lets you give a preview of the upcoming courses and a countdown to the launch date.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-coming-soon&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'upcoming-course-status',
				'pro_plus'      => true,
				'video_url'     => 'https://www.youtube.com/embed/wlZ3BnmpHkk?rel=0&autoplay=1',
			),
			self::GRADEBOOK           => array(
				'name'          => esc_html__( 'The Gradebook', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/gradebook.png' ),
				'description'   => esc_html__( 'Track your student\'s progress. Watch what students have completed, their progress and how well they\'re doing.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-gradebook&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'the-gradebook',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=1509&rel=0&autoplay=1',
			),
			self::GOOGLE_MEET         => array(
				'name'          => esc_html__( 'Google Meet', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/google_meet.png' ),
				'settings'      => admin_url( 'admin.php?page=google_meet_settings' ),
				'description'   => esc_html__( 'Connect MasterStudy LMS with Google Meet to host live online classes. Students can attend live classes right from the lesson page.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin-ms&utm_medium=addons&utm_campaign=get-now-addons',
				'documentation' => 'google-meet',
				'pro_plus'      => true,
				'video_url'     => 'https://www.youtube.com/embed/rgjEGTZMjbc?rel=0&autoplay=1',
			),
			self::ZOOM_CONFERENCE     => array(
				'name'          => esc_html__( 'Zoom Conference', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/zoom_conference.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings&submenu=zoom#section_2' ),
				'description'   => esc_html__( 'Enjoy the new type of lesson — connect Zoom Video Conferencing with your website and interact with your students in real-time.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-zoom&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'zoom-video-conferencing',
			),
			self::MULTI_INSTRUCTORS   => array(
				'name'          => esc_html__( 'Multi-instructors', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/multi_instructors.png' ),
				'description'   => esc_html__( 'Use the help of a colleague and assign one more instructor to the same course to share responsibilities.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-multi-instructor&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'multi-instructors',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=1133&rel=0&autoplay=1',
			),
			self::STATISTICS          => array(
				'name'          => esc_html__( 'Statistics and Payout', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/statistics.png' ),
				'settings'      => admin_url( 'admin.php?page=stm_lms_statistics' ),
				'description'   => esc_html__( 'Manage all payments and track affiliated statistics for the sold courses, such as Total Profit, Total Payments, and manage authors fee.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-payouts&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'statistics-and-payouts',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=1023&rel=0&autoplay=1',
			),
			self::SHAREWARE           => array(
				'name'          => esc_html__( 'Trial Courses', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/trial_courses.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings&submenu=trial-courses#section_2' ),
				'description'   => esc_html__( 'Let your potential students try out your online courses for free. Give trial access to specific lessons within your courses and attract more learners.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-trial&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'trial-courses',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=968&rel=0&autoplay=1',
			),
			self::AUDIO_LESSON        => array(
				'name'          => esc_html__( 'Audio Lesson', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/audio_lesson.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings&submenu=course#section_2:~:text=Preferred%20Audio%20Source' ),
				'description'   => esc_html__( 'Now you can share audio lessons. Upload audio files or add from Spotify or SoundCloud. Mix them with text, video and quizzes in your courses.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=audio-lesson&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'audio-lesson',
				'pro_plus'      => true,
				'video_url'     => 'https://www.youtube.com/embed/sNmXeINHDFI?rel=0&autoplay=1',
			),
			self::LIVE_STREAMS        => array(
				'name'          => esc_html__( 'Live Streaming', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/live-stream.png' ),
				'description'   => esc_html__( 'Have live lessons and interact with your students in real time. Answer their questions and give feedback immediately.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-livestream&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'live-streaming',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=609&rel=0&autoplay=1',
			),
			self::POINT_SYSTEM        => array(
				'name'          => esc_html__( 'Point System', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/points.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings&submenu=interface#section_point_system' ),
				'description'   => esc_html__( 'Motivate and engage students by awarding them points for their progress and activity on the website.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-points&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'point-system',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=736&rel=0&autoplay=1',
			),
			self::SCORM               => array(
				'name'          => esc_html__( 'Scorm', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/scorm.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings&submenu=scorm#section_2' ),
				'description'   => esc_html__( 'Easily upload to your LMS any course that was created with the help of different content authoring tools.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-scorm&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'scorm',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=893&rel=0&autoplay=1',
			),
			self::GOOGLE_CLASSROOMS   => array(
				'name'          => esc_html__( 'Google Classrooms', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/google_classroom.png' ),
				'settings'      => admin_url( 'admin.php?page=google_classrooms' ),
				'description'   => esc_html__( 'Ease the process of structuring the workflow by connecting your Google Classroom account with your website and import the needed classes.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-gclassroom&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'google-classroom',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=1174&rel=0&autoplay=1',
			),
			self::ONLINE_TESTING      => array(
				'name'          => esc_html__( 'Online Testing', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/mst.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-settings#stm_lms_shortcodes' ),
				'description'   => esc_html__( 'Easily put any quizzes on any page of your website, not just confined to courses. This feature-rich addon lets both your students and site visitors take these quizzes.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-onlinetestings&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'online-testing',
				'video_url'     => 'https://www.youtube.com/embed/98LKgphFSCY?start=1086&rel=0&autoplay=1',
			),
		);

		$udemy_visible = get_option( 'ms_lms_udemy_visible', false );

		if ( ! $udemy_visible && is_ms_lms_addon_enabled( self::UDEMY ) ) {
			$udemy_visible = true;
			update_option( 'ms_lms_udemy_visible', 1 );
		}

		if ( $udemy_visible ) {
			$addons_list[ self::UDEMY ] = array(
				'name'          => esc_html__( 'Udemy Importer', 'masterstudy-lms-learning-management-system' ),
				'url'           => esc_url( STM_LMS_URL . 'assets/addons/udemy.png' ),
				'settings'      => admin_url( 'admin.php?page=stm-lms-udemy-settings' ),
				'description'   => esc_html__( 'Import courses from Udemy and display them on your website. Enrich your course catalog and earn affiliate commissions.', 'masterstudy-lms-learning-management-system' ),
				'pro_url'       => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/?utm_source=wpadmin&utm_medium=ms-udemy&utm_campaign=masterstudy-plugin&licenses=1&billing_cycle=annual',
				'documentation' => 'udemy-course-importer',
			);
		}

		return $addons_list;
	}
}
