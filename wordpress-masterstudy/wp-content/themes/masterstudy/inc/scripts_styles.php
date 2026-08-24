<?php
/*
	Scripts and Styles (SS)
*/
if ( ! is_admin() ) {
	add_action( 'wp_enqueue_scripts', 'stm_load_theme_ss' );
}

function stm_load_theme_ss() {
	$header_style = stm_option( 'header_style', 'header_default' );

	wp_enqueue_style( 'linear', get_template_directory_uri() . '/assets/linearicons/linear.css', null, STM_THEME_VERSION, 'all' );
	wp_enqueue_style( 'masterstudy-bootstrap', get_template_directory_uri() . '/assets/vendors/bootstrap.min.css', null, STM_THEME_VERSION, 'all' );
	wp_enqueue_style( 'masterstudy-bootstrap-custom', get_template_directory_uri() . '/assets/css/ms-bootstrap-custom.css', null, STM_THEME_VERSION, 'all' );
	wp_enqueue_style( 'font-awesome-min', get_template_directory_uri() . '/assets/css/font-awesome.min.css', null, STM_THEME_VERSION, 'all' );
	wp_enqueue_style( 'font-icomoon', get_template_directory_uri() . '/assets/css/icomoon.fonts.css', null, STM_THEME_VERSION, 'all' );
	wp_enqueue_style( 'font-icomoon-rtl', get_template_directory_uri() . '/assets/css/rtl_demo/style.css', null, STM_THEME_VERSION, 'all' );
	wp_enqueue_style( 'select2', get_template_directory_uri() . '/assets/css/select2.min.css', null, STM_THEME_VERSION, 'all' );
	wp_enqueue_style( 'fancybox', get_template_directory_uri() . '/assets/vendors/jquery.fancybox.min.css', null, STM_THEME_VERSION, 'all' );
	wp_enqueue_style( 'animate', get_template_directory_uri() . '/assets/css/animate.css', null, STM_THEME_VERSION, 'all' );
	wp_enqueue_style( 'stm_theme_styles', get_template_directory_uri() . '/assets/css/styles.css', null, STM_THEME_VERSION, 'all' );
	stm_module_styles( 'stm_layout_styles', stm_get_layout(), 'stm_theme_styles' );
	wp_register_style( 'owl.carousel', get_template_directory_uri() . '/assets/css/owl.carousel.min.css', null, STM_THEME_VERSION, 'all' );

	if ( ! wp_is_mobile() ) {
		wp_enqueue_style( 'stm_theme_styles_animation', get_template_directory_uri() . '/assets/css/animation.css', null, STM_THEME_VERSION, 'all' );
	}

	if ( is_rtl() ) {
		stm_module_styles( 'rtl', 'rtl' );
		stm_module_scripts( 'rtl', 'rtl' );
		wp_enqueue_style( 'bootstrap-rtl.min', get_template_directory_uri() . '/assets/css/rtl_demo/bootstrap-rtl.css', null, STM_THEME_VERSION );
	}
	if ( wp_is_mobile() && stm_get_layout_is_mobile() ) {
		$header_style = 'header_2';
	}
	stm_module_styles( 'headers', $header_style );
	stm_module_styles( 'headers_transparent', "{$header_style}_transparent" );

	$enable_shop = stm_option( 'enable_shop', false );
	if ( $enable_shop ) {
		stm_module_styles( 'stm_woo_styles', 'woocommerce' );
	}

	wp_enqueue_style( 'stm_theme_style', get_stylesheet_uri(), null, STM_THEME_VERSION, 'all' );

	$header_desktop_bg = stm_option( 'header_desktop_bg', '' );
	if ( ! empty( $header_desktop_bg ) ) {
		wp_add_inline_style(
			'stm_theme_style',
			"#header:not(.transparent_header) .header_default {
	        background-color : 
	        {$header_desktop_bg}
	         !important;
	    }"
		);
	}

	$primary_color = stm_option( 'primary_color', '' );
	//	if ( stm_lms_get_layout() === 'tech' ) {
	//		//body.tech .widget_contacts .widget_contacts_style_2 li.widget_contacts_email a
	//	}

	$header_mobile_bg = stm_option( 'header_mobile_bg', '' );
	$header_main_color = stm_option( 'header_main_color', '' );
	if ( ! empty( $header_mobile_bg ) ) {
		wp_add_inline_style(
			'stm_theme_style',
			"@media (max-width: 1025px) {
	    #header .{$header_style} {
	        background-color : {$header_mobile_bg} !important;
	    }
	    #header .{$header_style} .stm_lms_search_popup .inner{
	     background-color : {$header_mobile_bg} !important;
	    }
	    #header .{$header_style} .stm_lms_account_popup .inner{
	     background-color : {$header_mobile_bg} !important;
	    }
	    #header .{$header_style} .stm_header_top_search{
	     background-color : {$header_mobile_bg} !important;
	    }
	    #header .{$header_style}  *{
	     color : {$header_main_color['color']} !important;
	    }
	}"
		);
	}

	if ( class_exists( 'BuddyPress' ) && ! defined( 'STM_LMS_FILE' ) ) {
		stm_module_styles( 'buddypress', 'buddypress' );
	}
	if ( function_exists( 'stm_lms_custom_styles_url' ) && file_exists( stm_lms_custom_styles_url( true, true ) . 'custom_styles.css' ) ) {
		wp_enqueue_style( 'stm_theme_custom_styles', stm_lms_custom_styles_url( true ) . 'custom_styles.css', array(), stm_lms_custom_styles_v() );
	} else {
		$upload     = wp_upload_dir();
		$upload_url = $upload['baseurl'];
		if ( is_ssl() ) {
			$upload_url = str_replace( 'http://', 'https://', $upload_url );
		}
		wp_enqueue_style( 'stm_theme_custom_styles', "{$upload_url}/stm_lms_styles/custom_styles.css", array(), STM_THEME_VERSION );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	/*Layout icons*/
	if ( function_exists( 'stm_layout_icons_sets' ) ) {
		$icons = stm_layout_icons_sets();
		foreach ( $icons as $icon_set ) {
			wp_enqueue_style( $icon_set, get_template_directory_uri() . "/assets/layout_icons/{$icon_set}/style.css", null, STM_THEME_VERSION, 'all' );
		}
	}

	wp_enqueue_script( 'masterstudy-bootstrap', get_template_directory_uri() . '/assets/vendors/bootstrap.min.js', array( 'jquery' ), STM_THEME_VERSION, true );
	wp_enqueue_script( 'fancybox', get_template_directory_uri() . '/assets/vendors/jquery.fancybox.min.js', array( 'jquery' ), STM_THEME_VERSION, true );
	wp_enqueue_script( 'select2', get_template_directory_uri() . '/assets/js/select2.full.min.js', array( 'jquery' ), STM_THEME_VERSION, true );
	wp_enqueue_script( 'stm_theme_scripts', get_template_directory_uri() . '/assets/js/custom.js', array( 'jquery' ), STM_THEME_VERSION, true );
	wp_enqueue_script( 'ajaxsubmit', get_template_directory_uri() . '/assets/js/ajax.submit.js', array( 'jquery' ), STM_THEME_VERSION, true );

	wp_register_script( 'owl.carousel', get_template_directory_uri() . '/assets/js/owl.carousel.js', 'jquery', STM_THEME_VERSION, true );
	wp_register_script( 'imagesloaded', get_template_directory_uri() . '/assets/js/imagesloaded.pkgd.min.js', 'jquery', STM_THEME_VERSION, true );
	wp_register_script( 'isotope', get_template_directory_uri() . '/assets/js/isotope.pkgd.min.js', 'jquery', STM_THEME_VERSION, true );
	wp_register_script( 'countUp.min.js', get_template_directory_uri() . '/assets/js/countUp.min.js', array( 'jquery' ), STM_THEME_VERSION, true );
	wp_register_script( 'jquery.countdown', get_template_directory_uri() . '/assets/js/jquery.countdown.js', 'jquery', STM_THEME_VERSION, true );
	wp_register_script( 'vue.js', get_template_directory_uri() . '/assets/js/vue.min.js', array( 'jquery' ), STM_THEME_VERSION, false );
	wp_register_script( 'vue-resource.js', get_template_directory_uri() . '/assets/js/vue-resource.min.js', array( 'vue.js' ), STM_THEME_VERSION, false );
	/*POSTS*/
	if ( is_post_type_archive( array( 'events' ) ) ) {
		stm_module_styles( 'event_grid' );
	} elseif ( is_post_type_archive( 'gallery' ) ) {
		stm_module_styles( 'gallery_grid' );
		wp_enqueue_script( 'imagesloaded' );
		wp_enqueue_script( 'isotope' );
		stm_module_scripts( 'gallery_grid' );
	} elseif ( is_post_type_archive( 'teachers' ) ) {
		stm_module_styles( 'teachers_grid' );
	} elseif ( is_post_type_archive( 'product' ) ) {
		stm_module_styles( 'featured_products' );
	}

	/*AOS*/
	wp_register_script( 'aos.js', get_template_directory_uri() . '/assets/js/aos.js', array(), STM_THEME_VERSION, false );
	wp_register_style( 'aos.css', get_template_directory_uri() . '/assets/css/aos.css', array(), STM_THEME_VERSION, 'all' );

	if ( ! defined( 'STM_POST_TYPE' ) ) {
		wp_enqueue_style( 'stm_theme_styles_dynamic', get_template_directory_uri() . '/assets/css/dynamic.css', null, STM_THEME_VERSION, 'all' );
		wp_enqueue_style( 'stm_theme_styles_fonts', stm_default_gfonts(), null, STM_THEME_VERSION, 'all' );
	}

	if ( class_exists( 'bbPress' ) ) {
		stm_module_styles( 'bbpress' );
	}

	if ( defined( 'HFE_DIR' ) ) {
		stm_module_styles( 'hfe' );
		stm_module_scripts( 'hfe' );
	}

}

function stm_admin_styles() {
	wp_enqueue_style( 'stm_theme_admin_styles', get_template_directory_uri() . '/assets/css/admin.css', null, STM_THEME_VERSION, 'all' );
	wp_enqueue_style( 'stm_theme_mstudy_icons', get_template_directory_uri() . '/assets/css/icomoon.fonts.css', null, STM_THEME_VERSION, 'all' );
	wp_enqueue_style( 'stm_theme_mstudy_rtl_icons', get_template_directory_uri() . '/assets/css/rtl_demo/style.css', null, STM_THEME_VERSION, 'all' );

	/*Layout icons*/
	if ( function_exists( 'stm_layout_icons_sets' ) ) {
		$icons = stm_layout_icons_sets();
		foreach ( $icons as $icon_set ) {
			wp_enqueue_style( $icon_set, get_template_directory_uri() . "/assets/layout_icons/{$icon_set}/style.css", null, STM_THEME_VERSION, 'all' );
		}
	}

	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', null, STM_THEME_VERSION, 'all' );
	wp_enqueue_style( 'stm_theme_styles_fonts', stm_default_gfonts(), null, STM_THEME_VERSION, 'all' );
}

add_action( 'admin_enqueue_scripts', 'stm_admin_styles' );

function stm_default_gfonts() {
	$font_url = '';

	if ( 'off' !== _x( 'on', 'Google font: on or off', 'masterstudy' ) ) {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
		$font_url = add_query_arg( 'family', urlencode( 'Montserrat|Open Sans:200,300,300italic,400,400italic,500,600,700&subset=latin,latin-ext' ), '//fonts.googleapis.com/css' );
	}

	return $font_url;
}
