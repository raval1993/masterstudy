<?php

class STM_Review_Notice {

	public static function init() {
		add_action( 'wp_ajax_stm_ajax_add_review', [ self::class, 'stm_ajax_add_review' ] );
		add_action( 'wp_ajax_nopriv_stm_ajax_add_review', [ self::class, 'stm_ajax_add_review' ] );
		add_action( 'admin_notices', [ self::class, 'stm_review_admin_notice' ] );
	}

	public static function stm_ajax_add_review () {
		check_ajax_referer('stm_ajax_add_review', 'security');

		update_option('add_review_status', sanitize_text_field($_GET['add_review_status']));
	}

	public static function stm_review_admin_notice() {
		if ( empty( get_option('add_review_status', '') ) ) {
			wp_enqueue_script('review-notice', get_template_directory_uri() . '/admin/assets/js/review-notice.js', 'jQuery', NULL, true);

			echo '<style>.review-message { border-color: #d54e21 !important; }
            .review-message p:last-child { margin-top: 0; }
            .review-message p.submit a.button-primary {
                background: #d54e21;
                border-color: #d54e21 #d54e21 #d61c00;
                box-shadow: 0 1px 0 #d61c00;
                color: #fff;
                text-decoration: none;
                text-shadow: 0 -1px 1px #d54e21, 1px 0 1px #d54e21, 0 1px 1px #d54e21, -1px 0 1px #d54e21;
              }</style>
          
            <div id="message" class="notice notice-info review-message">
                <p>If you are happy with the <b>' . STM_THEME_NAME . '</b>, please give it a review on ThemeForest.net :)</p>
                <p class="submit">
                    <a href="https://themeforest.net/downloads" class="add_review button-primary" target="_blank">Leave a Review</a>
                    <a href="#" class="skip_review button-secondary">No, thank you</a>
                </p>
            </div>';
		}
	}

}
