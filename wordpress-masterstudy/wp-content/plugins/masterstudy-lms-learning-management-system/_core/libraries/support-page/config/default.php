<?php
$response = STM_Support_Page::get_promo_response( $textdomain );

if ( is_wp_error( $response ) ) {
	return;
}

$body = wp_remote_retrieve_body( $response );
$data = json_decode( $body, true );

$first_notice = null;

if ( isset( $data['notices'] ) && is_array( $data['notices'] ) ) {
	$notices      = $data['notices'];
	$first_notice = reset( $notices );
}

$promo_item = array();

$promo_notice = null;

if ( isset( $data['notices'] ) && is_array( $data['notices'] ) ) {
	$notices = $data['notices'];

	foreach ( $notices as $notice ) {
		if ( isset( $notice['post_terms']['type_category'] ) ) {
			$has_promo = array_filter( $notice['post_terms']['type_category'], function( $cat ) {
				return isset( $cat['slug'] ) && 'promo' === $cat['slug'];
			} );
			
			if ( ! empty( $has_promo ) ) {
				$promo_notice = $notice;
				break;
			}
		}
	}

	if ( $promo_notice === null ) {
		$promo_notice = reset( $notices );
	}
}

if ( ! empty( $promo_notice ) ) {
	$promo_item = array(
		'class'        => 'help-item-full-width help-item-promo',
		'title'        => isset( $promo_notice['post_title'] ) ? $promo_notice['post_title'] : '',
		'description'  => isset( $promo_notice['post_content'] ) ? $promo_notice['post_content'] : '',
		'buttons'      => array(
			array(
				'label' => isset( $promo_notice['button_text_post'] ) ? $promo_notice['button_text_post'] : __( 'Learn More', 'support-page' ),
				'href'  => isset( $promo_notice['button_url_post'] ) ? $promo_notice['button_url_post'] : '#',
				'type'  => 'primary',
			),
		),
		'image'        => isset( $promo_notice['thumbnail_url'] ) ? $promo_notice['thumbnail_url'] : '',
		'image-width'  => '338',
		'image-height' => '338',
	);
}

return array(
	'header'     => array(
		array(
			'title'       => '',
			'description' => __( 'Find instant help with all resources in one place.', 'support-page' ),
		),
	),
	'help_items' => array(
		'documentation' => array(
			'icon'        => 'support-page-icon-doc',
			'title'       => __( 'Documentation', 'support-page' ),
			'description' => __( 'Explore in-depth guides and resources to help you navigate and maximize our products.', 'support-page' ),
			'buttons'     => array(
				array(
					'label' => __( 'Read Docs', 'support-page' ),
					'href'  => '#',
					'type'  => 'primary',
				),
			),
		),
		'ticket'        => array(
			'icon'           => 'support-page-icon-ticket',
			'title'          => __( 'Have a problem?', 'support-page' ),
			'description'    => __( 'Submit a support ticket and we\'ll assist you as soon as possible.', 'support-page' ),
			'buttons'        => array(
				array(
					'label' => __( 'Create Ticket', 'support-page' ),
					'href'  => esc_url( STM_Support_Page::get_freemius_ticket_url( $textdomain ) ),
					'type'  => 'primary',
				),
			),
			'has-pro-notice' => __( 'Only for pro users', 'support-page' ),
		),
		'video'         => array(
			'icon'        => 'support-page-icon-video',
			'title'       => __( 'Video Guides', 'support-page' ),
			'description' => __( 'Watch step-by-step tutorials on how to use features and get the best results.', 'support-page' ),
			'buttons'     => array(
				array(
					'label' => __( 'Watch Guides', 'support-page' ),
					'href'  => 'https://www.youtube.com/@StylemixThemes',
					'type'  => 'primary',
				),
			),
		),
		'requests'      => array(
			'icon'        => 'support-page-icon-feature',
			'title'       => __( 'Feature Requests', 'support-page' ),
			'description' => __( 'Submit your ideas, vote on what matters most, and help us shape future updates.', 'support-page' ),
			'buttons'     => array(
				array(
					'label' => __( 'Request Feature', 'support-page' ),
					'href'  => '#',
					'type'  => 'primary',
				),
			),
		),
		'community'     => array(
			'icon'        => 'support-page-icon-community',
			'title'       => __( 'Community', 'support-page' ),
			'description' => __( 'Connect with fellow users, ask questions, and share experiences in a collaborative space.', 'support-page' ),
			'buttons'     => array(
				array(
					'label' => __( 'Join Community', 'support-page' ),
					'href'  => '#',
					'type'  => 'primary',
					'icon'  => 'support-page-icon-facebook',
				),
			),
		),
		'customization' => array(
			'icon'        => 'support-page-icon-code',
			'title'       => __( 'Customization', 'support-page' ),
			'description' => __( 'Get custom changes and improvements done to suit your website\'s needs.', 'support-page' ),
			'buttons'     => array(
				array(
					'label' => __( 'Get Quotes', 'support-page' ),
					'href'  => 'https://stylemix.net/ticket-form/',
					'type'  => 'primary',
				),
			),
		),
		'features'      => array(
			'class'        => 'help-item-full-width help-item-features',
			'title'        => '',
			'title_pro'    => '',
			'description'  => '',
			'buttons'      => array(
				array(
					'label'     => __( 'Get PRO', 'support-page' ),
					'label_pro' => __( 'Get PRO PLUS', 'support-page' ),
					'href'      => 'https://stylemixthemes.com/wordpress-lms-plugin/pricing/',
					'type'      => 'primary',
					'icon'      => 'support-page-icon-arrow-circle-right',
				),
				array(
					'label' => __( 'Learn more', 'support-page' ),
					'href'  => 'https://stylemixthemes.com/wordpress-lms-plugin/',
					'type'  => 'secondary',
				),
			),
			'image'        => SUPPORT_PAGE_URL . 'assets/images/feature-bg-ms.jpg',
			'image-width'  => '1110',
			'image-height' => '464',
		),
		'expert'        => array(
			'class'        => 'help-item-wide help-item-expert',
			'title'        => __( 'Expert Setup & Optimization', 'support-page' ),
			'description'  => __( 'Get expert help to get started, customize, and improve your website for the best results.', 'support-page' ),
			'list'         => array(
				__( 'Get a custom setup that\'s perfectly tailored to your specific needs.', 'support-page' ),
				__( 'Enjoy personalized assistance to ensure smooth operation and support.', 'support-page' ),
				__( 'Optimize performance by boosting your platform\'s speed and user experience.', 'support-page' ),
				__( 'Make your platform stand out with unique features and design changes.', 'support-page' ),
			),
			'buttons'      => array(
				array(
					'label' => __( 'Start a Project', 'support-page' ),
					'href'  => 'https://stylemix.net/',
					'type'  => 'secondary',
				),
			),
			'image'        => SUPPORT_PAGE_URL . 'assets/images/expert.png',
			'image-width'  => '360',
			'image-height' => '360',
		),
		'affiliate'     => array(
			'icon'        => 'support-page-icon-affiliate',
			'title'       => __( 'Affiliate', 'support-page' ),
			'description' => __( 'Join our affiliate program and earn 30% for each sale you make. Start promoting and earning today!', 'support-page' ),
			'buttons'     => array(
				array(
					'label' => __( 'Become an Affiliate', 'support-page' ),
					'href'  => 'https://stylemixthemes.com/affiliate/',
					'type'  => 'primary',
				),
			),
		),
		$promo_item,
		// 'onboarding'    => array(
		// 	'icon'        => 'support-page-icon-onboarding',
		// 	'title'       => __( 'Onboarding', 'support-page' ),
		// 	'description' => __( 'Book a quick call with us. We'll answer your questions and guide you in the right direction.', 'support-page' ),
		// 	'buttons'     => array(
		// 		array(
		// 			'label' => __( 'Book a Call', 'support-page' ),
		// 			'href'  => 'https://calendly.com/stylemix-onboarding',
		// 			'type'  => 'primary',
		// 		),
		// 	),
		// ),
	),
	'review'     => array(
		'review_form' => array(
			'class'       => 'help-item-full-width help-item-review',
			'title'       => __( 'Leave Review', 'support-page' ),
			'description' => __( 'We\'d love to hear your experience with our tools and services', 'support-page' ),
			'buttons'     => array(
				array(
					'label' => __( 'Write Review', 'support-page' ),
					'href'  => 'https://bit.ly/33D44gQ',
					'type'  => 'primary',
				),
			),
		),
	),
	'news'       => array(
		'blog_list' => array(
			'class'       => 'help-item-half',
			'category_id' => '394',
			'per_page'    => '3',
			'title'       => __( 'From Our Blog', 'support-page' ),
			'description' => __( 'Find instant help with all resources in one place.', 'support-page' ),
			'buttons'     => array(
				array(
					'label' => __( 'More Posts', 'support-page' ),
					'href'  => '#',
					'type'  => 'primary',
				),
			),
		),
	),
	'newsletter' => array(
		'newsletter_form' => array(
			'class'              => 'help-item-half help-item-newsletter',
			'icon'               => 'support-page-icon-contacts',
			'title'              => __( '<span>Get all the latest news & updates</span> delivered straight to your inbox.', 'support-page' ),
			'placeholder'        => __( 'Enter your email here', 'support-page' ),
			'label'              => __( 'I agree with storage and handling of my data by this website', 'support-page' ),
			'newsletter-icon'    => 'support-page-icon-check',
			'newsletter-title'   => __( 'Thank you, your sign-up request was successful!', 'support-page' ),
			'newsletter-message' => __( 'Please check your email inbox to confirm.', 'support-page' ),
		),
	),
);
