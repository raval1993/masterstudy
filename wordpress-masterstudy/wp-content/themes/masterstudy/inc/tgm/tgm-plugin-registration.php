<?php

require_once dirname( __FILE__ ) . '/tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'stm_require_plugins' );

function stm_get_download_url( $plugName, $ver = '', $s3Dir = 'masterstudy' ) {
	$ver = ( ! empty( $ver ) ) ? '-' . $ver : '';

	return 'downloads://' . $s3Dir . '/' . $plugName . $ver . '.zip';
}

function stm_require_plugins( $return = false ) {
	$stm_post_type_ver       = '4.3.9';
	$stm_lms_pro_ver         = '3.7.9';
	$js_composer_ver         = '6.9.0';
	$revslider_ver           = '6.5.17';
	$ms_elementor_widget_ver = '1.1.8';
	$gdpr_ver                = '1.1';

	$plugins = array(
		'envato-market'                                  => array(
			'name'     => 'Envato Market',
			'slug'     => 'envato-market',
			'source'   => 'https://envato.github.io/wp-envato-market/dist/envato-market.zip',
			'required' => true,
		),
		'stm-post-type'                                  => array(
			'name'     => 'STM Configurations',
			'slug'     => 'stm-post-type',
			'source'   => stm_get_download_url( 'stm-post-type', $stm_post_type_ver ),
			'version'  => $stm_post_type_ver,
			'required' => true,
			'core'     => true,
		),
		'masterstudy-lms-learning-management-system'     => array(
			'name'     => 'MasterStudy LMS',
			'slug'     => 'masterstudy-lms-learning-management-system',
			'required' => true,
			'core'     => true,
		),
		'masterstudy-lms-learning-management-system-pro' => array(
			'name'     => 'MasterStudy LMS PRO',
			'slug'     => 'masterstudy-lms-learning-management-system-pro',
			'source'   => stm_get_download_url( 'masterstudy-lms-learning-management-system-pro', $stm_lms_pro_ver ),
			'version'  => $stm_lms_pro_ver,
			'required' => true,
			'core'     => true,
			'premium'  => true,
		),
		'js_composer'                                    => array(
			'name'         => 'WPBakery Page Builder',
			'slug'         => 'js_composer',
			'source'       => stm_get_download_url( 'js_composer', $js_composer_ver, 'js_composer' ),
			'version'      => $js_composer_ver,
			'required'     => false,
			'premium'      => true,
			'external_url' => 'https://wpbakery.com',
		),
		'elementor'                                      => array(
			'name'     => 'Elementor',
			'slug'     => 'elementor',
			'required' => false,
		),
		'header-footer-elementor'                        => array(
			'name'     => 'Elementor – Header, Footer & Blocks Template',
			'slug'     => 'header-footer-elementor',
			'required' => false,
		),
		'masterstudy-elementor-widgets'                  => array(
			'name'     => 'Masterstudy Elementor',
			'slug'     => 'masterstudy-elementor-widgets',
			'source'   => stm_get_download_url( 'masterstudy-elementor-widgets', $ms_elementor_widget_ver ),
			'version'  => $ms_elementor_widget_ver,
			'required' => false,
		),
		'revslider'                                      => array(
			'name'         => 'Revolution Slider',
			'slug'         => 'revslider',
			'source'       => stm_get_download_url( 'revslider', $revslider_ver, 'revslider' ),
			'version'      => $revslider_ver,
			'required'     => false,
			'premium'      => true,
			'external_url' => 'http://www.themepunch.com/revolution/',
		),
		'paid-memberships-pro'                           => array(
			'name'     => 'Paid Memberships Pro',
			'slug'     => 'paid-memberships-pro',
			'required' => false,
		),
		'breadcrumb-navxt'                               => array(
			'name'     => 'Breadcrumb NavXT',
			'slug'     => 'breadcrumb-navxt',
			'required' => false,
		),
		'contact-form-7'                                 => array(
			'name'     => 'Contact Form 7',
			'slug'     => 'contact-form-7',
			'required' => false,
		),
		'buddypress'                                     => array(
			'name'     => 'BuddyPress',
			'slug'     => 'buddypress',
			'required' => false,
		),
		'woocommerce'                                    => array(
			'name'     => 'Woocommerce',
			'slug'     => 'woocommerce',
			'required' => false,
		),
		'eroom-zoom-meetings-webinar'                    => array(
			'name'     => 'eRoom – Zoom Meetings & Webinar',
			'slug'     => 'eroom-zoom-meetings-webinar',
			'required' => false,
		),
		'accesspress-social-share'                       => array(
			'name'     => 'AccessPress Social Share',
			'slug'     => 'accesspress-social-share',
			'required' => false,
		),
		'stm-gdpr-compliance'                            => array(
			'name'     => 'GDPR Compliance & Cookie Consent',
			'slug'     => 'stm-gdpr-compliance',
			'source'   => stm_get_download_url( 'stm-gdpr-compliance', $gdpr_ver ),
			'version'  => $gdpr_ver,
			'required' => false,
		),
		'add-to-any'                                     => array(
			'name'     => 'AddToAny Share Buttons',
			'slug'     => 'add-to-any',
			'required' => false,
		),
	);

	if ( $return ) {
		return $plugins;
	} else {

		$layout_plugins      = stm_layout_plugins( stm_lms_get_layout() );
		$recommended_plugins = masterstudy_premium_bundled_plugins();
		$layout_plugins      = array_merge( $layout_plugins, $recommended_plugins );

		$tgm_layout_plugins = array();
		foreach ( $layout_plugins as $layout_plugin ) {
			$tgm_layout_plugins[ $layout_plugin ] = $plugins[ $layout_plugin ];
		}

		tgmpa( $plugins );
	};

}

function masterstudy_premium_bundled_plugins() {
	return array(
		'js_composer',
		'elementor',
		'masterstudy-elementor-widgets',
	);
}
