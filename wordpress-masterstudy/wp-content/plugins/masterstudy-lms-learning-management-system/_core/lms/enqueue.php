<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function stm_lms_wp_head() {
	?>
	<script type="text/javascript">
		var stm_lms_ajaxurl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
		var stm_lms_resturl = '<?php echo rest_url( 'stm-lms/v1', 'json' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>';
		var ms_lms_resturl = '<?php echo rest_url( 'masterstudy-lms/v2', 'json' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>';
		var ms_lms_nonce = '<?php echo wp_create_nonce( 'wp_rest' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>';
		var stm_ajax_add_pear_hb = '<?php echo wp_create_nonce( 'stm_ajax_add_pear_hb' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>';
		<?php if ( function_exists( 'pll_current_language' ) ) : ?>
		var pll_current_language = '<?php echo esc_js( pll_current_language() ); ?>';
		<?php endif; ?>
	</script>
	<style>
		.vue_is_disabled {
			display: none;
		}
		#wp-admin-bar-lms-settings img {
			max-width: 16px;
			vertical-align: sub;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'stm_lms_wp_head' );
add_action( 'admin_head', 'stm_lms_wp_head' );

function stm_lms_enqueue_ss() {
	$assets = STM_LMS_URL . 'assets';
	$base   = STM_LMS_URL . 'libraries/nuxy/metaboxes/assets/'; // Rewrite STM_WPCFTO_URL

	wp_register_style( 'masterstudy-fonts', $assets . '/css/variables/fonts.css', null, MS_LMS_VERSION );
	wp_enqueue_style( 'stm_lms_icons', $assets . '/icons/style.css', null, MS_LMS_VERSION );
	wp_enqueue_style( 'video.js', $assets . '/vendors/video-js.min.css', null, MS_LMS_VERSION, 'all' );
	wp_register_style( 'owl.carousel', $assets . '/vendors/owl.carousel.min.css', null, MS_LMS_VERSION, 'all' );
	wp_register_style( 'masterstudy_lazysizes', $assets . '/css/lazysizes.css', null, MS_LMS_VERSION );
	STM_LMS_Helpers::enqueue_font_awesome_icons();

	wp_enqueue_script( 'jquery' );

	if ( STM_LMS_Helpers::is_stripe_enabled() ) {
		wp_enqueue_script( 'stripe.js', 'https://js.stripe.com/v3/#lms_defer', array(), false, false ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NoExplicitVersion
	}

	wp_register_script( 'vue.js', $base . 'js/vue.min.js', array( 'jquery' ), MS_LMS_VERSION, false );
	wp_register_script( 'vue-resource.js', $base . 'js/vue-resource.min.js', array( 'vue.js' ), MS_LMS_VERSION, false );
	wp_register_script( 'vue2-editor.js', $base . 'js/vue2-editor.min.js', array( 'vue.js' ), MS_LMS_VERSION, false );
	wp_register_script( 'vue2-datepicker', $base . 'js/vue2-datepicker.min.js', array( 'vue.js' ), MS_LMS_VERSION, false );

	if ( STM_LMS_Helpers::g_recaptcha_enabled() ) :
		$recaptcha = STM_LMS_Helpers::g_recaptcha_keys();

		wp_register_script(
			'stm_grecaptcha',
			'https://www.google.com/recaptcha/api.js?T=1&render=' . $recaptcha['public'],
			array( 'jquery' ),
			MS_LMS_VERSION,
			true
		);
	endif;

	wp_register_script( 'jquery.cookie', $assets . '/vendors/jquery.cookie.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'resize-sensor', $assets . '/vendors/ResizeSensor.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'sticky-sidebar', $assets . '/vendors/sticky-sidebar.min.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'video.js', $assets . '/vendors/video.min.js', array(), MS_LMS_VERSION, true );
	wp_register_script( 'owl.carousel', $assets . '/vendors/owl.carousel.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'vue2-autocomplete', $assets . '/vendors/vue2-autocomplete.js', array( 'vue.js' ), MS_LMS_VERSION, true );
	wp_register_script( 'stm-lms-countdown', $assets . '/js/countdown.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'lazysizes', $assets . '/vendors/lazysizes.min.js', array(), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy_lazysizes', $assets . '/js/lazyload.js', array( 'jquery', 'lazysizes' ), MS_LMS_VERSION, true );
	wp_register_script( 'jquery.countdown', $assets . '/vendors/jquery.countdown.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'js.countdown', $assets . '/vendors/js.countdown.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_localize_script(
		'jquery.countdown',
		'stm_lms_jquery_countdown_vars',
		array(
			'days'    => __( 'Days', 'masterstudy-lms-learning-management-system' ),
			'hours'   => __( 'Hours', 'masterstudy-lms-learning-management-system' ),
			'minutes' => __( 'Minutes', 'masterstudy-lms-learning-management-system' ),
			'seconds' => __( 'Seconds', 'masterstudy-lms-learning-management-system' ),
		)
	);
	wp_register_script( 'stm-lms-wishlist', $assets . '/js/wishlist.js', array( 'jquery' ), MS_LMS_VERSION, true );

	if ( stm_lms_has_custom_colors() ) {
		wp_enqueue_style( 'masterstudy-lms-learning-management-system', stm_lms_custom_styles_url() . '/stm_lms_styles/stm_lms.css', array(), stm_lms_custom_styles_v() );
	} else {
		wp_enqueue_style( 'masterstudy-lms-learning-management-system', $assets . '/css/stm_lms.css', array(), MS_LMS_VERSION );
	}

	if ( is_rtl() ) {
		wp_enqueue_style( 'masterstudy-lms-learning-management-system-rtl-styles', $assets . '/css/rtl-styles.css', array(), MS_LMS_VERSION );
	}

	if ( function_exists( 'vc_asset_url' ) ) {
		wp_register_style( 'stm_lms_wpb_front_css', vc_asset_url( 'css/js_composer.min.css' ), array(), MS_LMS_VERSION );
	}

	stm_lms_register_script( 'lms' );
	wp_localize_script(
		'stm-lms-lms',
		'stm_lms_vars',
		array(
			'symbol'             => STM_LMS_Options::get_option( 'currency_symbol', '$' ),
			'position'           => STM_LMS_Options::get_option( 'currency_position', 'left' ),
			'currency_thousands' => STM_LMS_Options::get_option( 'currency_thousands', ',' ),
			'wp_rest_nonce'      => wp_create_nonce( 'wp_rest' ),
			'translate'          => array(
				'delete' => esc_html__( 'Are you sure you want to delete this course from cart?', 'masterstudy-lms-learning-management-system' ),
			),
		)
	);

	if ( STM_LMS_Subscriptions::subscription_enabled() ) {
		stm_lms_register_style( 'pmpro' );
	}

	/*Enqueue not MasterStudy theme related styles*/
	if ( ! stm_lms_is_masterstudy_theme() ) {
		stm_lms_register_style( 'noconflict/main' );
	}
}

function stm_lms_enqueue_component_scripts( $hook_suffix ) {
	/*Components scripts registration*/
	wp_register_script( 'masterstudy-lamejs', STM_LMS_URL . 'assets/vendors/lamejs.js', array( 'jquery' ), MS_LMS_VERSION, true );

	/*Pages styles & scripts*/
	wp_register_style( 'masterstudy-login-page', STM_LMS_URL . 'assets/css/pages/login.css', null, MS_LMS_VERSION );
	wp_register_script( 'masterstudy-enrolled-courses', STM_LMS_URL . 'assets/js/account/v1/enrolled-courses.js', array( 'jquery', 'vue.js', 'vue-resource.js' ), MS_LMS_VERSION, true );
	wp_localize_script(
		'masterstudy-enrolled-courses',
		'student_data',
		array(
			'id'         => get_current_user_id(),
			'hide_stats' => __( 'Hide Statistics', 'masterstudy-lms-learning-management-system' ),
			'show_stats' => __( 'Show Statistics', 'masterstudy-lms-learning-management-system' ),
		)
	);

	wp_register_script( 'masterstudy-list-students', STM_LMS_URL . 'assets/js/students.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_style( 'masterstudy-list-students', STM_LMS_URL . 'assets/css/parts/students.css', array(), MS_LMS_VERSION, 'all' );

	wp_register_script( 'masterstudy-enrolled-quizzes', STM_LMS_URL . 'assets/js/enrolled-quizzes.js', array( 'jquery' ), MS_LMS_VERSION, true );

	/*Components vendors*/
	wp_register_script( 'masterstudy-select2', STM_LMS_URL . 'assets/vendors/select2.min.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_style( 'masterstudy-select2', STM_LMS_URL . 'assets/vendors/select2.min.css', array(), MS_LMS_VERSION );

	/*Components scripts registration*/
	wp_register_script( 'masterstudy-authorization-main', STM_LMS_URL . 'assets/js/components/authorization/main.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-authorization-ajax', STM_LMS_URL . 'assets/js/components/authorization/ajax.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_localize_script(
		'masterstudy-authorization-ajax',
		'masterstudy_authorization_data',
		array(
			'bad'    => esc_html__( 'Bad', 'masterstudy-lms-learning-management-system' ),
			'normal' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			'good'   => esc_html__( 'Good', 'masterstudy-lms-learning-management-system' ),
			'hard'   => esc_html__( 'Hard', 'masterstudy-lms-learning-management-system' ),
		)
	);
	wp_register_script( 'masterstudy-authorization-new-pass', STM_LMS_URL . 'assets/js/components/authorization/new-pass.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-countdown', STM_LMS_URL . 'assets/js/components/countdown.js', array( 'jquery', 'jquery.countdown', 'js.countdown' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-curriculum-accordion', STM_LMS_URL . 'assets/js/components/curriculum-accordion.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-curriculum-list', STM_LMS_URL . 'assets/js/components/course/curriculum.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-discussions', STM_LMS_URL . 'assets/js/components/discussions.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-hint', STM_LMS_URL . 'assets/js/components/hint.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-loader', STM_LMS_URL . 'assets/js/components/loader.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-pagination', STM_LMS_URL . 'assets/js/components/pagination.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-ajax-pagination', STM_LMS_URL . 'assets/js/components/ajax-pagination.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-api-pagination', STM_LMS_URL . 'assets/js/components/api-pagination.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-tabs-pagination', STM_LMS_URL . 'assets/js/components/tabs-pagination.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-wp-editor', STM_LMS_URL . 'assets/js/components/wp-editor.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-buy-button', STM_LMS_URL . 'assets/js/components/buy-button.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-dark-mode-button', STM_LMS_URL . 'assets/js/components/dark-mode-button.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-radio-buttons', STM_LMS_URL . 'assets/js/components/radio-buttons.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-message', STM_LMS_URL . 'assets/js/components/message.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-audio-player', STM_LMS_URL . 'assets/js/components/audio-player.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-video-recorder', STM_LMS_URL . 'assets/js/components/video-recorder.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-audio-recorder', STM_LMS_URL . 'assets/js/components/audio-recorder.js', array( 'jquery', 'masterstudy-lamejs' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-modals', STM_LMS_URL . 'assets/js/components/modals/modals.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-message-modal', STM_LMS_URL . 'assets/js/components/modals/message.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-membership-trigger', STM_LMS_URL . 'assets/js/components/modals/membership-trigger.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-membership-add-to-cart', STM_LMS_URL . 'assets/js/components/modals/membership-add-to-cart.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-search', STM_LMS_URL . 'assets/js/components/search.js', array(), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-sort-indicator', STM_LMS_URL . 'assets/js/components/sort-indicator.js', array(), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-attachment-media', STM_LMS_URL . 'assets/js/components/attachment-media.js', array( 'masterstudy-video-recorder', 'masterstudy-audio-recorder', 'masterstudy-audio-player' ), MS_LMS_VERSION, true );
	wp_register_script( 'ms_lms_courses_searchbox_autocomplete', STM_LMS_URL . 'assets/vendors/vue2-autocomplete.js', array( 'jquery', 'vue.js', 'vue2-autocomplete' ), MS_LMS_VERSION, true );
	wp_register_script( 'ms_lms_courses_searchbox', STM_LMS_URL . 'assets/js/elementor-widgets/course-search-box/course-search-box.js', array( 'jquery', 'vue.js' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-mega-menu', STM_LMS_URL . 'assets/js/elementor-widgets/mega-menu/mega-menu.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-single-course-complete', STM_LMS_URL . 'assets/js/components/course/complete.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-single-course-stickybar', STM_LMS_URL . 'assets/js/components/course/stickybar.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-single-course-components', STM_LMS_URL . 'assets/js/components/course/main.js', array( 'jquery', 'jquery.cookie' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-file-upload', STM_LMS_URL . 'assets/js/components/file-upload.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-orders', STM_LMS_URL . 'assets/js/orders/main.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-pmpro-checkout', STM_LMS_URL . 'assets/js/pmpro-checkout.js', array( 'jquery', 'masterstudy-select2' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-course-templates', STM_LMS_URL . 'assets/js/components/course-templates.js', array( 'jquery', 'masterstudy-select2' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-pricing', STM_LMS_URL . 'assets/js/components/pricing.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-pricing-subscription', STM_LMS_URL . 'assets/js/components/pricing-subscription.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-image-upload', STM_LMS_URL . 'assets/js/components/image-upload.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-share', STM_LMS_URL . 'assets/js/components/share.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-personal-info', STM_LMS_URL . 'assets/js/components/personal-info.js', array( 'jquery', 'masterstudy-select2' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-drawer-component', STM_LMS_URL . 'assets/js/components/drawer.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-modal-component', STM_LMS_URL . 'assets/js/components/modal.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-no-records', STM_LMS_URL . 'assets/js/components/no-records.js', null, MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-instructor-courses-select', STM_LMS_URL . 'assets/js/components/selects/instructor-courses-select.js', array( 'jquery' ), MS_LMS_VERSION, true );

	/*Single Course Video*/
	wp_register_style( 'masterstudy-single-course-video-plyr', STM_LMS_URL . 'assets/css/components/course/plyr.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-single-course-video', STM_LMS_URL . 'assets/css/components/course/video.css', null, MS_LMS_VERSION );
	wp_register_script( 'plyr', STM_LMS_URL . 'assets/vendors/plyr/plyr.js', array(), MS_LMS_VERSION, false );
	wp_register_script( 'masterstudy-single-course-video', STM_LMS_URL . 'assets/js/components/course/video.js', array( 'jquery', 'plyr' ), MS_LMS_VERSION, true );

	/*Single Course styles & scripts registration*/
	wp_register_style( 'masterstudy-single-course-default', STM_LMS_URL . 'assets/css/course/main.css', array(), MS_LMS_VERSION );
	wp_register_script( 'masterstudy-single-course-default', STM_LMS_URL . 'assets/js/course/main.js', array( 'jquery' ), MS_LMS_VERSION, true );

	/*Public Accounts styles & scripts registration*/
	wp_register_style( 'masterstudy-instructor-public-account', STM_LMS_URL . 'assets/css/public-accounts/instructor.css', array( 'masterstudy-course-card', 'masterstudy-pagination' ), MS_LMS_VERSION );
	wp_register_script( 'masterstudy-instructor-public-account', STM_LMS_URL . 'assets/js/public-accounts/instructor.js', array( 'jquery', 'masterstudy-api-pagination' ), MS_LMS_VERSION, true );
	wp_register_style( 'masterstudy-student-public-account', STM_LMS_URL . 'assets/css/public-accounts/student.css', array(), MS_LMS_VERSION );
	wp_register_script( 'masterstudy-student-public-account', STM_LMS_URL . 'assets/js/public-accounts/student.js', array( 'jquery', 'masterstudy-api-pagination' ), MS_LMS_VERSION, true );
	/*Analytics preview page styles & scripts*/
	wp_register_style( 'masterstudy-analytics-preview-page', STM_LMS_URL . 'assets/css/analytics-preview.css', array(), MS_LMS_VERSION );
	wp_register_script( 'masterstudy-analytics-preview-page', STM_LMS_URL . 'assets/js/analytics-preview.js', array( 'jquery' ), MS_LMS_VERSION, true );

	/*Components styles registration*/
	wp_register_style( 'masterstudy-search', STM_LMS_URL . 'assets/css/components/search.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-input', STM_LMS_URL . 'assets/css/components/input.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-sort-indicator', STM_LMS_URL . 'assets/css/components/sort-indicator.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-loader', STM_LMS_URL . 'assets/css/components/loader.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-single-course-complete', STM_LMS_URL . 'assets/css/components/course/complete.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-single-course-curriculum-list', STM_LMS_URL . 'assets/css/components/course/curriculum.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-single-course-components', STM_LMS_URL . 'assets/css/components/course/main.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-card', STM_LMS_URL . 'assets/css/components/course/card.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-student-course-card', STM_LMS_URL . 'assets/css/components/course/student-card.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-authorization', STM_LMS_URL . 'assets/css/components/authorization.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-components-fonts', STM_LMS_URL . 'assets/css/components/fonts.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-alert', STM_LMS_URL . 'assets/css/components/alert.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-back-link', STM_LMS_URL . 'assets/css/components/back-link.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-button', STM_LMS_URL . 'assets/css/components/button.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-countdown', STM_LMS_URL . 'assets/css/components/countdown.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-curriculum-accordion', STM_LMS_URL . 'assets/css/components/curriculum-accordion.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-curriculum-list', STM_LMS_URL . 'assets/css/components/curriculum-list.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-discussions', STM_LMS_URL . 'assets/css/components/discussions.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-file-attachment', STM_LMS_URL . 'assets/css/components/file-attachment.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-file-upload', STM_LMS_URL . 'assets/css/components/file-upload.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-hint', STM_LMS_URL . 'assets/css/components/hint.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-nav-button', STM_LMS_URL . 'assets/css/components/nav-button.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-pagination', STM_LMS_URL . 'assets/css/components/pagination.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-progress', STM_LMS_URL . 'assets/css/components/progress.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-switch-button', STM_LMS_URL . 'assets/css/components/switch-button.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-tabs-pagination', STM_LMS_URL . 'assets/css/components/tabs-pagination.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-tabs', STM_LMS_URL . 'assets/css/components/tabs.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-wp-editor', STM_LMS_URL . 'assets/css/components/wp-editor.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-buy-button', STM_LMS_URL . 'assets/css/components/buy-button.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-dark-mode-button', STM_LMS_URL . 'assets/css/components/dark-mode-button.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-message', STM_LMS_URL . 'assets/css/components/message.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-radio-buttons', STM_LMS_URL . 'assets/css/components/radio-buttons.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-audio-player', STM_LMS_URL . 'assets/css/components/audio-player.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-video-player', STM_LMS_URL . 'assets/css/components/video-player.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-video-recorder', STM_LMS_URL . 'assets/css/components/video-recorder.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-audio-recorder', STM_LMS_URL . 'assets/css/components/audio-recorder.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-become-instructor-modal', STM_LMS_URL . 'assets/css/components/become-instructor-modal.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-enterprise-modal', STM_LMS_URL . 'assets/css/components/enterprise-modal.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-message-modal', STM_LMS_URL . 'assets/css/components/message-modal.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-membership-modal', STM_LMS_URL . 'assets/css/components/membership-modal.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-attachment-media', STM_LMS_URL . 'assets/css/components/attachment-media.css', array( 'masterstudy-audio-player', 'masterstudy-video-player', 'masterstudy-file-attachment' ), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-call-to-action', STM_LMS_URL . 'assets/css/elementor-widgets/call-to-action.css', array(), STM_LMS_VERSION, false );
	wp_register_style( 'masterstudy-icon-box', STM_LMS_URL . 'assets/css/elementor-widgets/icon-box.css', array(), STM_LMS_VERSION, false );
	wp_register_style( 'masterstudy-membership-levels', STM_LMS_URL . 'assets/css/elementor-widgets/membership-levels.css', array(), STM_LMS_VERSION, false );
	wp_register_style( 'masterstudy-review-card', STM_LMS_URL . 'assets/css/components/review-card.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-statistics-block', STM_LMS_URL . 'assets/css/components/statistics-block.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-stats-loader', STM_LMS_URL . 'assets/css/components/stats-loader.css', array(), MS_LMS_VERSION );
	wp_register_style( 'ms_lms_courses_searchbox', STM_LMS_URL . 'assets/css/elementor-widgets/course-search-box/course-search-box.css', array(), STM_LMS_VERSION, false );
	wp_register_style( 'profile-auth-links-style', STM_LMS_URL . 'assets/css/elementor-widgets/auth-links.css', array(), STM_LMS_VERSION, false );
	wp_register_style( 'masterstudy-mega-menu', STM_LMS_URL . 'assets/css/elementor-widgets/mega-menu/mega-menu.css', array(), MS_LMS_VERSION, false );
	wp_register_style( 'stm_lms_icons', STM_LMS_URL . 'assets/icons/style.css', null, STM_LMS_VERSION );
	wp_register_style( 'linear', STM_LMS_URL . 'libraries/nuxy/taxonomy_meta/assets/linearicons/linear.css', null, STM_LMS_VERSION, 'all' );
	wp_register_style( 'premium-templates', STM_LMS_URL . 'assets/css/parts/premium-templates/premium-templates.css', array(), MS_LMS_VERSION, 'all' );
	wp_register_style( 'masterstudy-course-templates', STM_LMS_URL . 'assets/css/components/course-templates.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-course-templates-modal', STM_LMS_URL . 'assets/css/components/course-templates-modal.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-share', STM_LMS_URL . 'assets/css/components/share.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-public-page-block', STM_LMS_URL . 'assets/css/components/public-page-block.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-personal-info', STM_LMS_URL . 'assets/css/components/personal-info.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-separator', STM_LMS_URL . 'assets/css/components/separator.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-image-upload', STM_LMS_URL . 'assets/css/components/image-upload.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-pricing', STM_LMS_URL . 'assets/css/components/pricing.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-pricing-subscription', STM_LMS_URL . 'assets/css/components/pricing-subscription.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-switcher', STM_LMS_URL . 'assets/css/components/switcher.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-share', STM_LMS_URL . 'assets/css/components/share.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-public-page-block', STM_LMS_URL . 'assets/css/components/public-page-block.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy_membership_pricing', STM_LMS_URL . 'assets/css/components/membership-pricing.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-no-records', STM_LMS_URL . 'assets/css/components/no-records.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-checkbox-component', STM_LMS_URL . 'assets/css/components/checkbox.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-drawer-component', STM_LMS_URL . 'assets/css/components/drawer.css', array(), MS_LMS_VERSION );
	wp_register_style( 'masterstudy-modal-component', STM_LMS_URL . 'assets/css/components/modal.css', array(), MS_LMS_VERSION );
	wp_register_script( 'masterstudy-api-provider-class', STM_LMS_URL . 'assets/js/api-provider.js', array(), MS_LMS_VERSION, true );
	wp_localize_script(
		'masterstudy-api-provider-class',
		'api_data',
		array(
			'rest_url'    => esc_url_raw( rest_url( 'masterstudy-lms/v2/' ) ),
			'wp_rest_url' => esc_url_raw( rest_url( 'wp/v2/' ) ),
			'nonce'       => wp_create_nonce( 'wp_rest' ),
		)
	);

	$locale          = masterstudy_get_locale_info();
	$datatables_data = array(
		'per_page_placeholder' => __( 'per page', 'masterstudy-lms-learning-management-system' ),
		'not_found'            => __( 'No matching items found', 'masterstudy-lms-learning-management-system' ),
		'not_available'        => __( 'No data to display', 'masterstudy-lms-learning-management-system' ),
		'not_started_lesson'   => __( 'Not Started', 'masterstudy-lms-learning-management-system' ),
		'failed_lesson'        => __( 'Failed', 'masterstudy-lms-learning-management-system' ),
		'completed_lesson'     => __( 'Complete', 'masterstudy-lms-learning-management-system' ),
		'progress_lesson'      => __( 'In progress', 'masterstudy-lms-learning-management-system' ),
		'img_route'            => STM_LMS_URL,
	);

	wp_register_script( 'masterstudy-date-helpers', STM_LMS_URL . 'assets/js/analytics/helpers/date.js', array( 'jquery' ), STM_LMS_VERSION, true );
	wp_register_script( 'masterstudy-loaders-helpers', STM_LMS_URL . 'assets/js/analytics/helpers/loaders.js', array( 'jquery' ), STM_LMS_VERSION, true );
	wp_register_script( 'masterstudy-datatables-library', STM_LMS_URL . 'assets/vendors/datatables.min.js', array( 'jquery' ), STM_LMS_VERSION, true );
	wp_register_script( 'masterstudy-datatables-helpers', STM_LMS_URL . 'assets/js/analytics/helpers/datatables.js', array( 'jquery', 'masterstudy-datatables-library' ), STM_LMS_VERSION, true );

	wp_register_style( 'masterstudy-datatables-library', STM_LMS_URL . 'assets/vendors/datatables.min.css', null, STM_LMS_VERSION );
	wp_register_style( 'masterstudy-datatables', STM_LMS_URL . 'assets/css/components/analytics/datatables.css', null, STM_LMS_VERSION );
	wp_register_style( 'masterstudy-date-field', STM_LMS_URL . 'assets/css/components/analytics/date-field.css', null, STM_LMS_VERSION );
	wp_register_style( 'masterstudy-datepicker-library', STM_LMS_URL . 'assets/vendors/flatpickr.min.css', null, STM_LMS_VERSION );
	wp_register_style( 'masterstudy-datepicker', STM_LMS_URL . 'assets/css/components/analytics/datepicker.css', null, STM_LMS_VERSION );
	wp_register_style( 'masterstudy-skeleton-loader', STM_LMS_URL . 'assets/css/components/skeleton-loader.css', null, STM_LMS_VERSION );

	wp_register_script( 'masterstudy-datepicker-library', STM_LMS_URL . 'assets/vendors/flatpickr.min.js', array( 'jquery' ), STM_LMS_VERSION, true );
	wp_register_script( 'masterstudy-datepicker-locale', "https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/{$locale['current_locale']}.js", array( 'masterstudy-datepicker-library' ), STM_LMS_VERSION, true );
	wp_register_script( 'masterstudy-datepicker-helpers', STM_LMS_URL . 'assets/js/analytics/helpers/datepicker.js', array( 'jquery', 'masterstudy-datepicker-library', 'masterstudy-datepicker-locale' ), STM_LMS_VERSION, true );
	wp_register_script( 'masterstudy-datepicker-component', STM_LMS_URL . 'assets/js/components/datepicker.js', array( 'jquery', 'masterstudy-datepicker-library', 'masterstudy-datepicker-locale' ), STM_LMS_VERSION, true );
	wp_register_script( 'masterstudy-datatables-component', STM_LMS_URL . 'assets/js/components/datatables.js', array( 'jquery', 'masterstudy-datatables-library', 'masterstudy-api-provider-class' ), STM_LMS_VERSION, true );
	wp_localize_script(
		'masterstudy-datatables-helpers',
		'table_data',
		$datatables_data
	);
	wp_localize_script(
		'masterstudy-datatables-component',
		'table_data',
		$datatables_data
	);
	wp_localize_script(
		'masterstudy-datepicker-component',
		'datepicker_data',
		array(
			'custom_period' => __( 'Date range', 'masterstudy-lms-learning-management-system' ),
			'locale'        => masterstudy_get_locale_info(),
			'short_months'  => masterstudy_get_short_months_translations(),
		)
	);
	wp_localize_script(
		'masterstudy-date-helpers',
		'date_helpers_data',
		array(
			'short_months' => masterstudy_get_short_months_translations(),
		)
	);

	masterstudy_enqueue_students_page( $hook_suffix );
	masterstudy_register_js_utils();
}

function stm_lms_account_scripts() {
	wp_register_style( 'masterstudy-account-main', STM_LMS_URL . 'assets/css/account/main.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-profile', STM_LMS_URL . 'assets/css/account/profile.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-menu', STM_LMS_URL . 'assets/css/account/menu.css', null, MS_LMS_VERSION );
	wp_register_script( 'masterstudy-account-menu', STM_LMS_URL . 'assets/js/account/menu.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_style( 'masterstudy-account-mobile-menu', STM_LMS_URL . 'assets/css/account/mobile-menu.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-menu-divider', STM_LMS_URL . 'assets/css/account/menu-divider.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-become-instructor', STM_LMS_URL . 'assets/css/account/become-instructor.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-have-question', STM_LMS_URL . 'assets/css/account/have-question.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-edit-account', STM_LMS_URL . 'assets/css/account/edit-account.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-instructor-account', STM_LMS_URL . 'assets/css/account/instructor/account.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-announcement', STM_LMS_URL . 'assets/css/account/instructor/announcement.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-enrolled-quizzes', STM_LMS_URL . 'assets/css/account/student/enrolled-quizzes.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-enrolled-quiz-attempts', STM_LMS_URL . 'assets/css/account/student/enrolled-quiz-attempts.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-enrolled-quiz-attempt', STM_LMS_URL . 'assets/css/account/student/enrolled-quiz-attempt.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-enrolled-assignments', STM_LMS_URL . 'assets/css/account/enrolled-assignments.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-gradebook', STM_LMS_URL . 'assets/css/account/instructor/gradebook.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-wishlist', STM_LMS_URL . 'assets/css/account/wishlist.css', null, MS_LMS_VERSION );
	wp_register_script( 'masterstudy-account-wishlist', STM_LMS_URL . 'assets/js/account/wishlist.js', null, MS_LMS_VERSION, true );
	wp_register_style( 'masterstudy-account-user-certificates', STM_LMS_URL . 'assets/css/account/my-certificates.css', null, MS_LMS_VERSION );
	wp_register_script( 'masterstudy-account-user-certificates', STM_LMS_URL . 'assets/js/account/my-certificates.js', null, MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-account-announcement', STM_LMS_URL . 'assets/js/account/instructor/announcement.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_style( 'masterstudy-account-enrolled-courses', STM_LMS_URL . 'assets/css/account/enrolled-courses.css', null, MS_LMS_VERSION );
	wp_register_script( 'masterstudy-account-enrolled-courses', STM_LMS_URL . 'assets/js/account/enrolled-courses.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_style( 'masterstudy-account-instructor-courses', STM_LMS_URL . 'assets/css/account/instructor/courses.css', null, MS_LMS_VERSION );
	wp_register_script( 'masterstudy-account-instructor-courses', STM_LMS_URL . 'assets/js/account/instructor/courses.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_localize_script(
		'masterstudy-account-instructor-courses',
		'masterstudy_instructor_courses',
		array(
			'nonce'    => wp_create_nonce( 'wp_rest' ),
			'user_id'  => get_current_user_id(),
			'per_page' => 9,
			'status'   => 'all',
			'strings'  => array(
				'error'           => esc_html__( 'Something went wrong. Please try again.', 'masterstudy-lms-learning-management-system' ),
				'publish'         => esc_html__( 'Publish', 'masterstudy-lms-learning-management-system' ),
				'to_draft'        => esc_html__( 'Move to drafts', 'masterstudy-lms-learning-management-system' ),
				'featured'        => esc_html__( 'Remove from Featured', 'masterstudy-lms-learning-management-system' ),
				'not_featured'    => esc_html__( 'Make Featured', 'masterstudy-lms-learning-management-system' ),
				'featured_status' => esc_html__( 'Featured', 'masterstudy-lms-learning-management-system' ),
				'featured_limit'  => esc_html__( 'You have reached the limit of featured courses.', 'masterstudy-lms-learning-management-system' ),
				'status_labels'   => array(
					'publish'  => esc_html__( 'Published', 'masterstudy-lms-learning-management-system' ),
					'draft'    => esc_html__( 'In draft', 'masterstudy-lms-learning-management-system' ),
					'pending'  => esc_html__( 'Pending', 'masterstudy-lms-learning-management-system' ),
					'rejected' => esc_html__( 'Rejected', 'masterstudy-lms-learning-management-system' ),
					'private'  => esc_html__( 'Private', 'masterstudy-lms-learning-management-system' ),
				),
			),
		)
	);
	wp_register_script( 'masterstudy-account-instructor-co-courses', STM_LMS_URL . 'assets/js/account/instructor/co-courses.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_localize_script(
		'masterstudy-account-instructor-co-courses',
		'masterstudy_instructor_co_courses',
		array(
			'nonce'    => wp_create_nonce( 'wp_rest' ),
			'user_id'  => get_current_user_id(),
			'per_page' => 6,
			'rest_url' => esc_url_raw( rest_url( 'masterstudy-lms/v2' ) ),
			'strings'  => array(
				'error' => esc_html__( 'Something went wrong. Please try again.', 'masterstudy-lms-learning-management-system' ),
				'edit'  => esc_html__( 'Edit', 'masterstudy-lms-learning-management-system' ),
				'view'  => esc_html__( 'View', 'masterstudy-lms-learning-management-system' ),
			),
		)
	);
	wp_register_style( 'masterstudy-account-enterprise-groups', STM_LMS_URL . 'assets/css/account/enterprise-groups.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-enterprise-groups-create-edit-drawer', STM_LMS_URL . 'assets/css/account/parts/enterprise-groups/create-edit-drawer.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-enterprise-groups-view-drawer', STM_LMS_URL . 'assets/css/account/parts/enterprise-groups/view-drawer.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-enterprise-groups-import-csv-modal', STM_LMS_URL . 'assets/css/account/parts/enterprise-groups/import-csv-modal.css', null, MS_LMS_VERSION );
	wp_register_script( 'masterstudy-account-enterprise-groups-create-edit-drawer', STM_LMS_URL . 'assets/js/account/parts/enterprise-groups/create-edit-drawer.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-account-enterprise-groups-view-drawer', STM_LMS_URL . 'assets/js/account/parts/enterprise-groups/view-drawer.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-account-enterprise-groups-import-csv-modal', STM_LMS_URL . 'assets/js/account/parts/enterprise-groups/import-csv-modal.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-account-enterprise-groups', STM_LMS_URL . 'assets/js/account/enterprise-groups.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-account-enrolled-assignments', STM_LMS_URL . 'assets/js/account/enrolled-assignments.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-account-gradebook', STM_LMS_URL . 'assets/js/account/instructor/gradebook.js', array( 'jquery', 'masterstudy-datepicker-component', 'masterstudy-datatables-component' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-account-settings', STM_LMS_URL . 'assets/js/account/settings.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_style( 'masterstudy-account-settings', STM_LMS_URL . 'assets/css/account/settings.css', null, MS_LMS_VERSION );
	wp_register_script( 'masterstudy-account-enrolled-students', STM_LMS_URL . 'assets/js/account/instructor/enrolled-students.js', array( 'jquery', 'masterstudy-datatables-component' ), MS_LMS_VERSION, true );
	wp_register_style( 'masterstudy-account-enrolled-students', STM_LMS_URL . 'assets/css/account/instructor/enrolled-students.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-memberships-pmp', STM_LMS_URL . 'assets/css/account/student/memberships-pmp.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-user-assignment', STM_LMS_URL . 'assets/css/account/instructor/user_assignment.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-settings', STM_LMS_URL . 'assets/css/account/settings.css', null, MS_LMS_VERSION );
	wp_register_script( 'masterstudy-account-messages', STM_LMS_URL . 'assets/js/account/messages.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_style( 'masterstudy-account-messages', STM_LMS_URL . 'assets/css/account/messages.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-payout-statistics', STM_LMS_URL . 'assets/css/account/instructor/payout-statistics.css', null, MS_LMS_VERSION );
	wp_register_script(
		'masterstudy-account-payout-statistics',
		STM_LMS_URL . 'assets/js/account/instructor/payout-statistics.js',
		array(
			'jquery',
			'moment.min',
			'masterstudy-charts-helpers',
			'masterstudy-chartjs-library',
			'masterstudy-api-provider',
			'masterstudy-datepicker-helpers',
			'masterstudy-date-helpers',
			'masterstudy-datatables-component',
		),
		MS_LMS_VERSION,
		true
	);
	wp_register_style( 'masterstudy-account-manage-students-student-progress', STM_LMS_URL . 'assets/css/account/instructor/manage_students/student-progress.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-manage-students-main', STM_LMS_URL . 'assets/css/account/instructor/manage_students/main.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-manage-students-import-modal', STM_LMS_URL . 'assets/css/account/instructor/manage_students/import-modal.css', null, MS_LMS_VERSION );
	wp_register_script( 'masterstudy-account-manage-students-export-students', STM_LMS_URL . 'assets/js/account/instructor/manage_students/export-students.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-account-manage-students-import-modal', STM_LMS_URL . 'assets/js/account/instructor/manage_students/import-modal.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-account-manage-students-main', STM_LMS_URL . 'assets/js/account/instructor/manage_students/main.js', array( 'jquery', 'masterstudy-ajax-pagination' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-account-manage-students-student-progress', STM_LMS_URL . 'assets/js/account/instructor/manage_students/student-progress.js', array( 'jquery' ), MS_LMS_VERSION, true );
	wp_register_script( 'masterstudy-account-google-meets-wizard', STM_LMS_URL . 'assets/js/account/instructor/google-meets/wizard.js', array( 'jquery', 'jquery-ui-resizable' ), MS_LMS_VERSION, true );
	wp_register_style( 'masterstudy-account-google-meets-wizard', STM_LMS_URL . 'assets/css/account/instructor/google-meets/wizard.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-google-meets-meetings', STM_LMS_URL . 'assets/css/account/instructor/google-meets/meetings.css', null, MS_LMS_VERSION );
	wp_register_style( 'masterstudy-account-google-meets-main', STM_LMS_URL . 'assets/css/account/instructor/google-meets/main.css', null, MS_LMS_VERSION );
}

function masterstudy_enqueue_students_page( string $hook_suffix ) {
	$lms_template          = get_query_var( 'lms_template' );
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$student_id            = absint( is_admin() ? ( isset( $_GET['user_id'] ) ? wp_unslash( $_GET['user_id'] ) : 0 ) : get_query_var( 'student_id' ) );
	$is_student_admin_page = ( 'masterstudy_page_manage_students' === $hook_suffix ) && ! empty( $student_id );
	$is_student_list       = ( 'account/instructor/enrolled-students' === $lms_template );
	$is_student_item       = ( 'account/instructor/enrolled-student' === $lms_template );

	if ( $is_student_admin_page || $is_student_list ) {
		$date_scripts_styles = masterstudy_datepicker_handles();

		if ( ! empty( $date_scripts_styles ) ) {
			foreach ( $date_scripts_styles as $handle ) {
				if ( wp_script_is( $handle, 'registered' ) ) {
					wp_enqueue_script( $handle );
				}

				if ( wp_style_is( $handle, 'registered' ) ) {
					wp_enqueue_style( $handle );
				}
			}
		}

		wp_enqueue_style( 'linear' );
		wp_enqueue_style( 'masterstudy-list-students' );
		wp_enqueue_style( 'masterstudy-pagination' );
		wp_enqueue_script( 'masterstudy-select2' );
		wp_enqueue_style( 'masterstudy-select2' );
		wp_enqueue_style( 'font-awesome-min' );
		wp_dequeue_style( 'font-awesome' );

		if ( stm_lms_has_custom_colors() ) {
			wp_enqueue_style( 'masterstudy-lms-learning-management-system', stm_lms_custom_styles_url() . '/stm_lms_styles/stm_lms.css', array(), stm_lms_custom_styles_v() );
		} else {
			wp_enqueue_style( 'masterstudy-lms-learning-management-system', STM_LMS_URL . 'assets/css/stm_lms.css', array(), MS_LMS_VERSION );
		}
	}

	if ( $is_student_admin_page || $is_student_list || $is_student_item ) {
		if ( empty( $student_id ) ) {
			wp_localize_script(
				'masterstudy-list-students',
				'stats_data',
				array(
					'custom_period'    => __( 'Date range', 'masterstudy-lms-learning-management-system' ),
					'user_account_url' => STM_LMS_User::login_page_url(),
					'is_students'      => $is_student_admin_page || $is_student_list,
					'is_student'       => $is_student_item,
					'locale'           => masterstudy_get_locale_info(),
					'is_admin'         => is_admin(),
				)
			);

			wp_enqueue_script( 'masterstudy-list-students' );
		}

		wp_add_inline_script(
			'masterstudy-list-students',
			"const defaultDateRanges = getDefaultDateRanges();
					const currentUrl = window.location.href;
					const userAccountDashboardPage = currentUrl === stats_data.user_account_url;
					let storedPeriodKey = localStorage.getItem( 'StudentsListSelectedPeriodKey' );
					let selectedPeriod;

					if ( storedPeriodKey && defaultDateRanges[ storedPeriodKey ] && !userAccountDashboardPage ) {
						selectedPeriod = defaultDateRanges[ storedPeriodKey ];
					} else {
						const defaultDateRange = typeof customDateRange != 'undefined' ? customDateRange : defaultDateRanges.this_month;
						const lmsDateRange = userAccountDashboardPage ? defaultDateRanges.all_time : defaultDateRange;
						const storedPeriod = !userAccountDashboardPage ? localStorage.getItem( 'StudentsListSelectedPeriod' ) : null;
						selectedPeriod = storedPeriod ? JSON.parse( storedPeriod ) : lmsDateRange;
					}"
		);
	}
}

function stm_lms_enqueue_vss() {
	if ( apply_filters( 'stm_lms_enqueue_bootstrap', true ) ) {
		wp_enqueue_style( 'masterstudy-bootstrap', STM_LMS_URL . 'assets/vendors/bootstrap.min.css', array(), MS_LMS_VERSION, 'all' );
		wp_enqueue_style( 'masterstudy-bootstrap-custom', STM_LMS_URL . 'assets/vendors/ms-bootstrap-custom.css', array(), MS_LMS_VERSION, 'all' );

		wp_enqueue_script( 'masterstudy-bootstrap', STM_LMS_URL . 'assets/vendors/bootstrap.min.js', array( 'jquery' ), MS_LMS_VERSION, true );
		wp_enqueue_script( 'masterstudy-bootstrap-custom', STM_LMS_URL . 'assets/vendors/ms-bootstrap-custom.js', array( 'jquery' ), MS_LMS_VERSION, true );
	}
}

add_action( 'wp_enqueue_scripts', 'stm_lms_enqueue_ss' );
add_action( 'wp_enqueue_scripts', 'stm_lms_enqueue_vss', 1 );
add_action( 'wp_enqueue_scripts', 'stm_lms_enqueue_component_scripts' );
add_action( 'wp_enqueue_scripts', 'stm_lms_account_scripts' );
add_action( 'admin_enqueue_scripts', 'stm_lms_enqueue_component_scripts' );

add_action( 'admin_head', 'stm_lms_nonces' );
add_action( 'wp_head', 'stm_lms_nonces' );

function stm_lms_nonces() {
	$nonces = array(
		'stm_install_starter_theme',
		'load_modal',
		'load_content',
		'start_quiz',
		'user_answers',
		'get_order_info',
		'user_orders',
		'stm_lms_get_instructor_courses',
		'stm_lms_add_comment',
		'stm_lms_manage_students',
		'stm_lms_get_comments',
		'stm_lms_login',
		'stm_lms_register',
		'stm_lms_become_instructor',
		'stm_lms_enterprise',
		'stm_lms_get_user_courses',
		'stm_lms_get_user_quizzes',
		'stm_lms_wishlist',
		'stm_lms_user_wishlist',
		'stm_lms_save_user_info',
		'stm_lms_clear_login_sessions',
		'stm_lms_get_user_sessions',
		'stm_lms_destroy_user_session',
		'stm_lms_destroy_other_user_sessions',
		'stm_lms_clear_user_sessions',
		'stm_lms_lost_password',
		'stm_lms_change_avatar',
		'stm_lms_delete_avatar',
		'stm_lms_complete_lesson',
		'stm_lms_use_membership',
		'stm_lms_change_featured',
		'stm_lms_delete_course_subscription',
		'stm_lms_get_reviews',
		'stm_lms_add_review',
		'stm_lms_add_to_cart',
		'stm_lms_delete_from_cart',
		'stm_lms_purchase',
		'stm_lms_send_message',
		'stm_lms_get_user_conversations',
		'stm_lms_get_user_messages',
		'stm_lms_clear_new_messages',
		'stm_lms_tables_update',
		'stm_lms_get_enterprise_groups',
		'stm_lms_get_enterprise_member_groups',
		'stm_lms_get_enterprise_group',
		'stm_lms_get_users_with_ent_courses',
		'stm_lms_add_enterprise_group',
		'stm_lms_delete_enterprise_group',
		'stm_lms_add_to_cart_enterprise',
		'stm_lms_get_user_ent_courses',
		'stm_lms_delete_user_ent_courses',
		'stm_lms_add_user_ent_courses',
		'stm_lms_change_ent_group_admin',
		'stm_lms_delete_user_from_group',
		'stm_lms_import_groups',
		'stm_lms_edit_user_answer',
		'stm_lms_get_user_points_history',
		'stm_lms_buy_for_points',
		'stm_lms_get_point_users',
		'stm_lms_get_user_points_history_admin',
		'stm_lms_change_points',
		'stm_lms_delete_points',
		'stm_lms_get_user_bundles',
		'stm_lms_change_bundle_status',
		'stm_lms_delete_bundle',
		'stm_lms_check_certificate_code',
		'stm_lms_get_google_classroom_courses',
		'stm_lms_get_google_classroom_course',
		'stm_lms_get_google_classroom_publish_course',
		'stm_lms_get_g_c_get_archive_page',
		'install_zoom_addon',
		'stm_lms_get_course_cookie_redirect',
		'stm_get_certificates',
		'stm_get_certificate_fields',
		'stm_save_certificate',
		'stm_upload_certificate_images',
		'stm_generate_certificates_preview',
		'stm_save_default_certificate',
		'stm_delete_default_certificate',
		'stm_save_certificate_category',
		'stm_delete_certificate_category',
		'stm_get_certificate_categories',
		'stm_get_certificate',
		'stm_delete_certificate',
		'stm_lms_get_users_submissions',
		'stm_lms_update_user_status',
		'stm_lms_hide_become_instructor_notice',
		'stm_lms_ban_user',
		'stm_lms_save_forms',
		'stm_lms_get_forms',
		'stm_lms_upload_form_file',
		'stm_lms_add_to_cart_guest',
		'stm_lms_fast_login',
		'stm_lms_fast_register',
		'stm_lms_change_lms_author',
		'stm_lms_add_student_manually',
		'stm_lms_change_course_status',
		'stm_lms_total_progress',
		'stm_lms_add_h5p_result',
		'stm_lms_toggle_buying',
		'stm_lms_logout',
		'stm_lms_restore_password',
		'stm_lms_hide_announcement',
		'stm_lms_get_curriculum_v2',
		'stm_lms_wizard_save_settings',
		'stm_lms_wizard_save_business_type',
		'stm_lms_get_enrolled_assingments',
		'stm-lms-starter-theme-install',
		'stm_lms_enrolled_quizzes',
		'stm_lms_add_to_cart_subscription',
	);

	$nonces_list = array();

	foreach ( $nonces as $nonce_name ) {
		$nonces_list[ $nonce_name ] = wp_create_nonce( $nonce_name );
	}

	?>
	<script>
		var stm_lms_nonces = <?php echo wp_json_encode( $nonces_list ); ?>;
	</script>
	<?php
}

function masterstudy_datepicker_handles(): array {
	return array(
		'masterstudy-date-helpers',
		'masterstudy-loaders-helpers',
		'masterstudy-datatables-library',
		'masterstudy-datatables-helpers',
		'masterstudy-datatables',
		'masterstudy-date-field',
		'masterstudy-datepicker-library',
		'masterstudy-datepicker-locale',
		'masterstudy-datepicker-helpers',
		'masterstudy-datepicker',
	);
}

if ( ! function_exists( 'masterstudy_get_short_months_translations' ) ) {
	function masterstudy_get_short_months_translations() {
		return array(
			/* translators: Abbreviated month name for January */
			esc_html__( 'Jan', 'masterstudy-lms-learning-management-system' ),
			/* translators: Abbreviated month name for February */
			esc_html__( 'Feb', 'masterstudy-lms-learning-management-system' ),
			/* translators: Abbreviated month name for March */
			esc_html__( 'Mar', 'masterstudy-lms-learning-management-system' ),
			/* translators: Abbreviated month name for April */
			esc_html__( 'Apr', 'masterstudy-lms-learning-management-system' ),
			/* translators: Abbreviated month name for May */
			esc_html__( 'May', 'masterstudy-lms-learning-management-system' ),
			/* translators: Abbreviated month name for June */
			esc_html__( 'Jun', 'masterstudy-lms-learning-management-system' ),
			/* translators: Abbreviated month name for July */
			esc_html__( 'Jul', 'masterstudy-lms-learning-management-system' ),
			/* translators: Abbreviated month name for August */
			esc_html__( 'Aug', 'masterstudy-lms-learning-management-system' ),
			/* translators: Abbreviated month name for September */
			esc_html__( 'Sep', 'masterstudy-lms-learning-management-system' ),
			/* translators: Abbreviated month name for October */
			esc_html__( 'Oct', 'masterstudy-lms-learning-management-system' ),
			/* translators: Abbreviated month name for November */
			esc_html__( 'Nov', 'masterstudy-lms-learning-management-system' ),
			/* translators: Abbreviated month name for December */
			esc_html__( 'Dec', 'masterstudy-lms-learning-management-system' ),
		);
	}
}

if ( ! function_exists( 'masterstudy_get_locale_info' ) ) {
	function masterstudy_get_locale_info(): array {
		$locale         = get_locale();
		$current_locale = 'en';
		$locales        = array(
			'en_US' => 'en',
			'en_GB' => 'en',
			'ru_RU' => 'ru',
			'ja'    => 'ja',
			'zh_CN' => 'zh',
			'de_DE' => 'de',
			'it_IT' => 'it',
			'fr_FR' => 'fr',
			'es_ES' => 'es',
		);

		if ( array_key_exists( $locale, $locales ) ) {
			$current_locale = $locales[ $locale ];
		}

		$time_format = get_option( 'time_format', 'g:i a' );
		$time_24hr   = ( false !== strpos( $time_format, 'H' ) || false !== strpos( $time_format, 'G' ) );

		return array(
			'current_locale' => $current_locale,
			'locale_object'  => array(
				'weekdays'         => array(
					'shorthand' => array(
						/* translators: Abbreviated weekday name for Sunday */
						esc_html__( 'Sun', 'masterstudy-lms-learning-management-system' ),
						/* translators: Abbreviated weekday name for Monday */
						esc_html__( 'Mon', 'masterstudy-lms-learning-management-system' ),
						/* translators: Abbreviated weekday name for Tuesday */
						esc_html__( 'Tue', 'masterstudy-lms-learning-management-system' ),
						/* translators: Abbreviated weekday name for Wednesday */
						esc_html__( 'Wed', 'masterstudy-lms-learning-management-system' ),
						/* translators: Abbreviated weekday name for Thursday */
						esc_html__( 'Thu', 'masterstudy-lms-learning-management-system' ),
						/* translators: Abbreviated weekday name for Friday */
						esc_html__( 'Fri', 'masterstudy-lms-learning-management-system' ),
						/* translators: Abbreviated weekday name for Saturday */
						esc_html__( 'Sat', 'masterstudy-lms-learning-management-system' ),
					),
					'longhand'  => array(
						esc_html__( 'Sunday', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'Monday', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'Tuesday', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'Wednesday', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'Thursday', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'Friday', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'Saturday', 'masterstudy-lms-learning-management-system' ),
					),
				),
				'months'           => array(
					'shorthand' => masterstudy_get_short_months_translations(),
					'longhand'  => array(
						esc_html__( 'January', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'February', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'March', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'April', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'May', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'June', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'July', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'August', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'September', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'October', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'November', 'masterstudy-lms-learning-management-system' ),
						esc_html__( 'December', 'masterstudy-lms-learning-management-system' ),
					),
				),
				'daysInMonth'      => array( 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ),
				'firstDayOfWeek'   => absint( get_option( 'start_of_week', 0 ) ),
				/* translators: Date range separator, e.g., "Jan 1 to Jan 15" */
				'rangeSeparator'   => esc_html__( ' to ', 'masterstudy-lms-learning-management-system' ),
				/* translators: Abbreviation for "Week" used in date picker week numbers (e.g., "Wk 1", "Wk 2") */
				'weekAbbreviation' => esc_html__( 'Wk', 'masterstudy-lms-learning-management-system' ),
				'scrollTitle'      => esc_html__( 'Scroll to increment', 'masterstudy-lms-learning-management-system' ),
				'toggleTitle'      => esc_html__( 'Click to toggle', 'masterstudy-lms-learning-management-system' ),
				'amPM'             => array(
					/* translators: Time period indicator for morning hours (12:00-11:59), used in 12-hour time format */
					esc_html__( 'AM', 'masterstudy-lms-learning-management-system' ),
					/* translators: Time period indicator for afternoon/evening hours (12:00-11:59), used in 12-hour time format */
					esc_html__( 'PM', 'masterstudy-lms-learning-management-system' ),
				),
				/* translators: Accessibility label for year selector in date picker */
				'yearAriaLabel'    => esc_html__( 'Year', 'masterstudy-lms-learning-management-system' ),
				/* translators: Accessibility label for month selector in date picker */
				'monthAriaLabel'   => esc_html__( 'Month', 'masterstudy-lms-learning-management-system' ),
				/* translators: Accessibility label for hour selector in time picker */
				'hourAriaLabel'    => esc_html__( 'Hour', 'masterstudy-lms-learning-management-system' ),
				/* translators: Accessibility label for minute selector in time picker */
				'minuteAriaLabel'  => esc_html__( 'Minute', 'masterstudy-lms-learning-management-system' ),
				'time_24hr'        => $time_24hr,
			),
			'firstDayOfWeek' => absint( get_option( 'start_of_week', 0 ) ),
		);
	}
}

function masterstudy_register_js_utils() {
	wp_register_script( 'masterstudy-pagination-utils', STM_LMS_URL . 'assets/js/utils/pagination.js', array( 'jquery' ), STM_LMS_VERSION, true );
	wp_register_script( 'masterstudy-slots-utils', STM_LMS_URL . 'assets/js/utils/slots.js', null, STM_LMS_VERSION, true );
}
