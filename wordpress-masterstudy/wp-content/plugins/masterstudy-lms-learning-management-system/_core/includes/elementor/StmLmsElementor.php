<?php

namespace StmLmsElementor;

use StmLmsElementor\Widgets\StmCoursesSearchbox;
use StmLmsElementor\Widgets\StmLmsSingleCourseCarousel;
use StmLmsElementor\Widgets\StmLmsCoursesCarousel;
use StmLmsElementor\Widgets\StmLmsCoursesCategories;
use StmLmsElementor\Widgets\StmLmsCoursesGrid;
use StmLmsElementor\Widgets\StmLmsFeaturedTeacher;
use StmLmsElementor\Widgets\StmLmsInstructorsCarousel;
use StmLmsElementor\Widgets\StmLmsRecentCourses;
use StmLmsElementor\Widgets\StmLmsCertificateChecker;
use StmLmsElementor\Widgets\StmLmsCourseBundles;
use StmLmsElementor\Widgets\StmLmsGoogleClassroom;
use StmLmsElementor\Widgets\StmLmsMembershipLevels;
use StmLmsElementor\Widgets\MasterstudyMembership;
use StmLmsElementor\Widgets\StmLmsCallToAction;
use StmLmsElementor\Widgets\MsLmsCoursesSearchbox;
use StmLmsElementor\Widgets\MsLmsInstructorsCarousel;
use StmLmsElementor\Widgets\MsLmsInstructorsGrid;
use StmLmsElementor\Widgets\MsLmsAuthorization;
use StmLmsElementor\Widgets\MsLmsCourses;
use StmLmsElementor\Widgets\MsLmsSlider;
use StmLmsElementor\Widgets\MsLmsBlog;
use StmLmsElementor\Widgets\MsLmsMailchimp;
use StmLmsElementor\Widgets\MsLmsCountdown;
use StmLmsElementor\Widgets\MsLmsIconBox;
use StmLmsElementor\Widgets\MsLmsCoursesCategories;
use StmLmsElementor\Widgets\Masterstudy_Lms_Faq;
use StmLmsElementor\Widgets\MsLmsMegaMenu;

use StmLmsElementor\Widgets\Course\MsLmsCourseCategories;
use StmLmsElementor\Widgets\Course\MsLmsCourseUpdated;
use StmLmsElementor\Widgets\Course\MsLmsCourseEnrolled;
use StmLmsElementor\Widgets\Course\MsLmsCourseDetails;
use StmLmsElementor\Widgets\Course\MsLmsCourseCurriculum;
use StmLmsElementor\Widgets\Course\MsLmsCourseAnnouncement;
use StmLmsElementor\Widgets\Course\MsLmsCourseCurrentStudents;
use StmLmsElementor\Widgets\Course\MsLmsCourseExcerpt;
use StmLmsElementor\Widgets\Course\MsLmsCourseFAQ;
use StmLmsElementor\Widgets\Course\MsLmsCourseInstructor;
use StmLmsElementor\Widgets\Course\MsLmsCourseMaterials;
use StmLmsElementor\Widgets\Course\MsLmsCourseInfo;
use StmLmsElementor\Widgets\Course\MsLmsCoursePopularCourses;
use StmLmsElementor\Widgets\Course\MsLmsCoursePriceInfo;
use StmLmsElementor\Widgets\Course\MsLmsCourseRating;
use StmLmsElementor\Widgets\Course\MsLmsCourseRelatedCourses;
use StmLmsElementor\Widgets\Course\MsLmsCourseReviews;
use StmLmsElementor\Widgets\Course\MsLmsCourseComplete;
use StmLmsElementor\Widgets\Course\MsLmsCourseShareButton;
use StmLmsElementor\Widgets\Course\MsLmsCourseWishlist;
use StmLmsElementor\Widgets\Course\MsLmsCourseStatus;
use StmLmsElementor\Widgets\Course\MsLmsCourseThumbnail;
use StmLmsElementor\Widgets\Course\MsLmsCourseTitle;
use StmLmsElementor\Widgets\Course\MsLmsCourseExpired;
use StmLmsElementor\Widgets\Course\MsLmsCourseGrades;
use StmLmsElementor\Widgets\Course\MsLmsCourseBuyButton;
use StmLmsElementor\Widgets\Course\MsLmsCourseComingSoon;
use StmLmsElementor\Widgets\Course\MsLmsCourseDescription;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin class
 */
final class Plugin {

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_action( 'elementor/init', array( $this, 'init' ) );
	}

	public function init() {
		require STM_LMS_PATH . '/includes/elementor/helpers/ajax_actions.php';

		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_widget_categories' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'editor_before_enqueue_scripts' ) );
		add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'editor_icons' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'editor_styles' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'controls_styles' ) );
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'editor_scripts' ) );
		add_action( 'elementor/document/before_save', array( $this, 'course_templates' ) );
	}

	public function course_templates( $document ) {
		$post_id = $document->get_main_id();

		if ( get_post_meta( $post_id, 'masterstudy_elementor_course_template', true ) ) {
			$message = __( 'This template is read-only. Please create a copy if you want to make changes.', 'masterstudy-lms-learning-management-system' );
			throw new \Exception( $message );
		}
	}

	public function add_elementor_widget_categories( $elements_manager ) {
		$new_categories = array(
			'stm_lms'        => array(
				'title' => esc_html__( 'MasterStudy', 'masterstudy-lms-learning-management-system' ),
			),
			'stm_lms_course' => array(
				'title' => esc_html__( 'MasterStudy Course', 'masterstudy-lms-learning-management-system' ),
			),
			'stm_lms_old'    => array(
				'title' => esc_html__( 'MasterStudy Old', 'masterstudy-lms-learning-management-system' ),
			),
		);

		$existing_categories = $elements_manager->get_categories();
		$categories          = array_merge( $new_categories, $existing_categories );

		$set_categories = function ( $categories ) {
			$this->categories = $categories;
		};
		$set_categories->call( $elements_manager, $categories );
	}

	public function editor_before_enqueue_scripts() {
		wp_enqueue_style( 'lms-elementor', STM_LMS_URL . 'assets/css/lms-elementor.css', array(), MS_LMS_VERSION, 'all' );
	}

	public function editor_scripts() {
		wp_register_script( 'stm_lms_add_overlay', STM_LMS_URL . 'assets/js/elementor-widgets/helpers/add-overlay.js', array(), MS_LMS_VERSION, true );
		wp_localize_script(
			'stm_lms_add_overlay',
			'stm_lms_add_overlay_change',
			array(
				'nonce'    => wp_create_nonce( 'add-overlay' ),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);
		wp_enqueue_script( 'masterstudy-unlock-banner', STM_LMS_URL . 'assets/js/elementor-widgets/helpers/unlock-banner.js', array(), MS_LMS_VERSION, true );
		/* swiper slider for widgets */
		wp_enqueue_script( 'ms_lms_swiper_slider', STM_LMS_URL . 'assets/vendors/swiper-bundle.min.js', array( 'elementor-frontend' ), MS_LMS_VERSION, true );
		/* slider widget scripts */
		wp_enqueue_script( 'ms_lms_slider_editor', STM_LMS_URL . 'assets/js/elementor-widgets/slider/slider-editor.js', array( 'elementor-frontend' ), MS_LMS_VERSION, true );
		/* courses widget scripts */
		wp_enqueue_script( 'ms_lms_courses_editor_select2', STM_LMS_URL . 'assets/vendors/select2.min.js', array( 'elementor-frontend' ), MS_LMS_VERSION, true );
		wp_enqueue_script( 'ms_lms_courses_editor', STM_LMS_URL . 'assets/js/elementor-widgets/courses/courses-editor.js', array( 'elementor-frontend' ), MS_LMS_VERSION, true );
		wp_enqueue_script( 'masterstudy-countdown-editor', STM_LMS_URL . 'assets/js/elementor-widgets/countdown.js', array( 'elementor-frontend', 'jquery', 'jquery.countdown', 'js.countdown' ), MS_LMS_VERSION, true );
		wp_enqueue_script( 'masterstudy_authorization_editor', STM_LMS_URL . 'assets/js/elementor-widgets/authorization.js', array( 'elementor-frontend' ), MS_LMS_VERSION, true );
		wp_localize_script(
			'ms_lms_courses_editor',
			'ms_lms_courses_archive_filter',
			array(
				'nonce'    => wp_create_nonce( 'filtering' ),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'editor'   => true,
			)
		);
		/* course search box widget scripts */
		wp_enqueue_script( 'ms_lms_courses_searchbox_editor_autocomplete', STM_LMS_URL . 'assets/vendors/vue2-autocomplete.js', array( 'elementor-frontend' ), MS_LMS_VERSION, true );
		wp_enqueue_script( 'ms_lms_courses_searchbox_editor', STM_LMS_URL . 'assets/js/elementor-widgets/course-search-box/course-search-box-editor.js', array( 'jquery', 'elementor-frontend' ), MS_LMS_VERSION, true );
		/* instructors carousel widget scripts */
		wp_enqueue_script( 'ms_lms_instructors_carousel_editor', STM_LMS_URL . 'assets/js/elementor-widgets/instructors-carousel/instructors-carousel-editor.js', array( 'elementor-frontend' ), MS_LMS_VERSION, true );
		wp_localize_script(
			'ms_lms_instructors_carousel_editor',
			'ms_lms_instructors_carousel_mode',
			array(
				'editor' => true,
			)
		);

		if ( is_ms_lms_addon_enabled( 'coming_soon' ) ) {
			stm_lms_register_style( 'coming_soon/coming_soon' );
			wp_enqueue_style( 'masterstudy-countdown' );
			wp_enqueue_script( 'masterstudy-countdown' );
		}

		if ( \STM_LMS_Options::get_option( 'enable_lazyload', false ) ) {
			wp_enqueue_script( 'masterstudy_lazysizes' );
			wp_enqueue_style( 'masterstudy_lazysizes' );
		}

		/* testimonials carousel widget scripts */
		wp_enqueue_script( 'lms-testimonials-carousel-editor', STM_LMS_URL . 'assets/js/elementor-widgets/testimonials_carousel_editor.js', array( 'elementor-frontend' ), MS_LMS_VERSION, true );

		wp_register_script( 'plyr', STM_LMS_URL . 'assets/vendors/plyr/plyr.js', array(), MS_LMS_VERSION, false );
		wp_register_script( 'masterstudy-video-media-editor', STM_LMS_URL . 'assets/js/elementor-widgets/video-media-editor.js', array( 'elementor-frontend', 'jquery', 'plyr' ), MS_LMS_VERSION, true );
		wp_register_script( 'masterstudy-course-reviews-editor', STM_LMS_URL . 'assets/js/elementor-widgets/course-reviews-editor.js', array( 'elementor-frontend', 'jquery' ), MS_LMS_VERSION, true );
		wp_localize_script(
			'masterstudy-course-reviews-editor',
			'reviews_data',
			array(
				'author_label'           => esc_html__( 'by', 'masterstudy-lms-learning-management-system' ),
				'editor_id'              => 'editor_add_review',
				'status'                 => 'pending for review',
				'student_public_profile' => \STM_LMS_Options::get_option( 'student_public_profile', true ),
			)
		);
		wp_register_script( 'masterstudy-course-grades-editor', STM_LMS_URL . 'assets/js/elementor-widgets/course-grades-editor.js', array( 'elementor-frontend', 'jquery', 'masterstudy-api-provider' ), MS_LMS_VERSION, true );
		wp_localize_script(
			'masterstudy-course-grades-editor',
			'course_grade',
			array(
				'attempts'        => esc_html__( 'attempts', 'masterstudy-lms-learning-management-system' ),
				'grade_separator' => esc_js( \STM_LMS_Options::get_option( 'grades_scores_separator', '/' ) ),
				'not_started'     => esc_html__( 'Not finished', 'masterstudy-lms-learning-management-system' ),
			)
		);
		wp_register_script( 'masterstudy-course-coming-soon-editor', STM_LMS_URL . 'assets/js/elementor-widgets/course-coming-soon-editor.js', array( 'elementor-frontend', 'jquery', 'jquery-ui-resizable', 'jquery.countdown', 'js.countdown' ), MS_LMS_VERSION, true );
		wp_localize_script(
			'masterstudy-course-coming-soon-editor',
			'coming_soon',
			array(
				'url'       => admin_url( 'admin-ajax.php' ),
				'is_logged' => is_user_logged_in(),
				'nonce'     => wp_create_nonce( 'masterstudy-lms-coming-soon-nonce' ),
			)
		);
		wp_register_script( 'masterstudy-course-buy-button-editor', STM_LMS_URL . 'assets/js/elementor-widgets/course-buy-button-editor.js', array( 'elementor-frontend', 'jquery' ), MS_LMS_VERSION, true );
		wp_register_script( 'masterstudy-course-components-editor', STM_LMS_URL . 'assets/js/elementor-widgets/course-components-editor.js', array( 'elementor-frontend', 'jquery' ), MS_LMS_VERSION, true );
		wp_localize_script(
			'masterstudy-course-components-editor',
			'components_data',
			array(
				'nonce'      => wp_create_nonce( 'stm_lms_total_progress' ),
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'more_title' => __( 'Show more', 'masterstudy-lms-learning-management-system' ),
				'less_title' => __( 'Show less', 'masterstudy-lms-learning-management-system' ),
			)
		);
		/* mega menu widget scripts and styles */
		wp_register_script(
			'masterstudy-mega-menu-editor',
			STM_LMS_URL . 'assets/js/elementor-widgets/mega-menu/mega-menu-editor.js',
			array( 'elementor-frontend', 'jquery' ),
			MS_LMS_VERSION,
			true
		);
	}

	public function editor_styles() {
		wp_register_style( 'stm_lms_add_overlay', STM_LMS_URL . 'assets/css/elementor-widgets/helpers/add-overlay.css', array(), MS_LMS_VERSION, false );
		wp_enqueue_style( 'stm_lms_add_overlay' );
		wp_enqueue_style( 'masterstudy-unlock-banner', STM_LMS_URL . 'assets/css/elementor-widgets/helpers/unlock-banner.css', array(), MS_LMS_VERSION, false );
		wp_enqueue_style( 'masterstudy-elementor-course-note', STM_LMS_URL . 'assets/css/elementor-widgets/helpers/course-note.css', array(), MS_LMS_VERSION, false );
		wp_register_style(
			'masterstudy-mega-menu',
			STM_LMS_URL . 'assets/css/elementor-widgets/mega-menu/mega-menu.css',
			array(),
			MS_LMS_VERSION
		);
	}

	public function controls_styles() {
		wp_enqueue_style( 'masterstudy-elementor-course-note', STM_LMS_URL . 'assets/css/elementor-widgets/helpers/course-note.css', array(), MS_LMS_VERSION, false );
	}

	public function editor_icons() {
		wp_enqueue_style( 'stm_lms_icons', STM_LMS_URL . 'assets/icons/style.css', null, STM_LMS_VERSION );
	}

	private function includes() {
		require STM_LMS_PATH . '/includes/elementor/helpers/add-controls-class.php';
		require STM_LMS_PATH . '/includes/elementor/helpers/add-overlay.php';
		require STM_LMS_PATH . '/includes/elementor/helpers/course-data.php';
		require STM_LMS_PATH . '/includes/elementor/helpers/unlock-banner.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/stm_lms_membership_levels.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/ms_lms_memberships.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/stm_lms_call_to_action.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/stm_lms_profile_auth_links.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/stm_lms_testimonials_carousel.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/deprecated/stm_courses_searchbox.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/deprecated/stm_lms_courses_carousel.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/deprecated/stm_lms_courses_categories.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/deprecated/stm_lms_courses_grid.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/deprecated/stm_lms_featured_teacher.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/deprecated/stm_lms_instructors_carousel.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/deprecated/stm_lms_recent_courses.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/deprecated/stm_lms_single_course_carousel.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/ms_lms_courses_searchbox.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/ms_lms_instructors_carousel.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/ms_lms_instructors_grid.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/ms_lms_authorization.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/courses/ms_lms_courses.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/slider/ms_lms_slider.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/ms_lms_blog.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/ms_lms_mailchimp.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/ms_lms_countdown.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/ms_lms_icon_box.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/ms_lms_mega_menu.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_categories.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_updated.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_enrolled.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_details.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_curriculum.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_announcement.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_current_students.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_excerpt.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_description.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_faq.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_instructor.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_materials.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_info.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_popular_courses.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_price_info.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_rating.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_related_courses.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_reviews.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_complete.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_share_button.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_wishlist.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_status.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_thumbnail.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_title.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_expired.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_buy_button.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_grades.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/course/ms_lms_course_coming_soon.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/ms_lms_courses_categories.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/masterstudy_lms_faq.php';
		// Pro widgets
		require STM_LMS_PATH . '/includes/elementor/widgets/deprecated/stm_lms_certificate_checker.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/deprecated/stm_lms_course_bundles.php';
		require STM_LMS_PATH . '/includes/elementor/widgets/deprecated/stm_lms_google_classroom.php';
	}

	public function register_widgets( $widgets_manager ) {
		$this->includes();
		$widgets_manager->register( new StmCoursesSearchbox() );
		$widgets_manager->register( new StmLmsSingleCourseCarousel() );
		$widgets_manager->register( new StmLmsCoursesCarousel() );
		$widgets_manager->register( new MsLmsCoursesCategories() );
		$widgets_manager->register( new Masterstudy_Lms_Faq() );
		$widgets_manager->register( new StmLmsCoursesCategories() );
		$widgets_manager->register( new StmLmsCoursesGrid() );
		$widgets_manager->register( new StmLmsFeaturedTeacher() );
		$widgets_manager->register( new StmLmsInstructorsCarousel() );
		$widgets_manager->register( new StmLmsRecentCourses() );
		$widgets_manager->register( new \StmLmsProTestimonials() );
		$widgets_manager->register( new \StmLmsProfileAuthLinks() );
		$widgets_manager->register( new StmLmsCallToAction() );
		$widgets_manager->register( new MsLmsCoursesSearchbox() );
		$widgets_manager->register( new MsLmsInstructorsCarousel() );
		$widgets_manager->register( new MsLmsInstructorsGrid() );
		$widgets_manager->register( new MsLmsAuthorization() );
		$widgets_manager->register( new MsLmsCountdown() );
		$widgets_manager->register( new MsLmsIconBox() );
		$widgets_manager->register( new MsLmsMegaMenu() );
		$widgets_manager->register( new MsLmsCourses() );
		$widgets_manager->register( new MsLmsSlider() );
		$widgets_manager->register( new \MsLmsBlog() );
		$widgets_manager->register( new MsLmsCourseCategories() );
		$widgets_manager->register( new MsLmsCourseUpdated() );
		$widgets_manager->register( new MsLmsCourseEnrolled() );
		$widgets_manager->register( new MsLmsCourseDetails() );
		$widgets_manager->register( new MsLmsCourseCurriculum() );
		$widgets_manager->register( new MsLmsCourseAnnouncement() );
		$widgets_manager->register( new MsLmsCourseCurrentStudents() );
		$widgets_manager->register( new MsLmsCourseExcerpt() );
		$widgets_manager->register( new MsLmsCourseFAQ() );
		$widgets_manager->register( new MsLmsCourseInstructor() );
		$widgets_manager->register( new MsLmsCourseMaterials() );
		$widgets_manager->register( new MsLmsCourseInfo() );
		$widgets_manager->register( new MsLmsCoursePopularCourses() );
		$widgets_manager->register( new MsLmsCoursePriceInfo() );
		$widgets_manager->register( new MsLmsCourseRating() );
		$widgets_manager->register( new MsLmsCourseRelatedCourses() );
		$widgets_manager->register( new MsLmsCourseReviews() );
		$widgets_manager->register( new MsLmsCourseComplete() );
		$widgets_manager->register( new MsLmsCourseShareButton() );
		$widgets_manager->register( new MsLmsCourseWishlist() );
		$widgets_manager->register( new MsLmsCourseStatus() );
		$widgets_manager->register( new MsLmsCourseThumbnail() );
		$widgets_manager->register( new MsLmsCourseTitle() );
		$widgets_manager->register( new MsLmsCourseExpired() );
		$widgets_manager->register( new MsLmsCourseBuyButton() );
		$widgets_manager->register( new MsLmsCourseGrades() );
		$widgets_manager->register( new MsLmsCourseComingSoon() );
		$widgets_manager->register( new MsLmsCourseDescription() );
		$widgets_manager->register( new StmLmsMembershipLevels() );
		$widgets_manager->register( new \MasterstudyMembership() );

		if ( defined( 'MC4WP_VERSION' ) ) {
			$widgets_manager->register( new \MsLmsMailchimp() );
		}
		if ( defined( 'STM_LMS_PRO_PATH' ) ) {
			$widgets_manager->register( new StmLmsCertificateChecker() );
		}
		if ( class_exists( 'MasterStudy\Lms\Pro\addons\CourseBundle\CourseBundle' ) ) {
			$widgets_manager->register( new StmLmsCourseBundles() );
		}
		if ( class_exists( 'STM_LMS_Google_Classroom' ) ) {
			$widgets_manager->register( new StmLmsGoogleClassroom() );
		}
	}
}

\StmLmsElementor\Plugin::instance();
