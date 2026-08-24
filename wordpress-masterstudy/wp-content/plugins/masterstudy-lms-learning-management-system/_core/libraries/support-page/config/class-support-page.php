<?php
if ( class_exists( 'STM_Support_Page' ) ) {
	return;
}

class STM_Support_Page {
	protected static $data           = array();
	protected static $api_urls       = array();
	protected static $promo_response = null;

	public static function init() {
		self::handle_mailchimp_form();
		self::load_textdomain();
	}

	public static function set_api_urls( $textdomain, $urls ) {
		self::$api_urls[ $textdomain ] = $urls;
	}

	public static function get_promo_response( $textdomain = 'support-page' ) {
		if ( is_null( self::$promo_response ) ) {
			if ( ! empty( self::$api_urls[ $textdomain ]['promo'] ) ) {
				self::$promo_response = wp_remote_get( self::$api_urls[ $textdomain ]['promo'] );
			}
		}

		return self::$promo_response;
	}

	public static function get_freemius_data( $textdomain ) {
		if ( isset( self::$api_urls[ $textdomain ]['freemius'] ) ) {
			return self::$api_urls[ $textdomain ]['freemius'];
		}

		return null;
	}

	public static function get_freemius_ticket_url( $textdomain ) {
		$freemius = self::get_freemius_data( $textdomain );

		$plugin_slug = $freemius['plugin_slug'] ?? null;
		$item_id     = $freemius['item_id'] ?? null;

		if ( ! $plugin_slug || ! $item_id ) {
			return 'https://support.stylemixthemes.com/tickets/new/support';
		}

		$fs_data = get_option( 'fs_accounts' );

		if (
			isset( $fs_data['sites'][ $plugin_slug ] ) &&
			isset( $fs_data['sites'][ $plugin_slug ]->user_id )
		) {
			$fs_user_id = $fs_data['sites'][ $plugin_slug ]->user_id;
			$fs_user    = $fs_data['users'][ $fs_user_id ] ?? null;

			if ( $fs_user ) {
				return add_query_arg(
					array(
						'item_id'    => $item_id,
						'fs_id'      => $fs_user_id,
						'fs_email'   => $fs_user->email,
						'fs_fl_name' => trim( $fs_user->first . ' ' . $fs_user->last ),
					),
					'https://support.stylemixthemes.com/fs-ticket/new'
				);
			}
		}

		return add_query_arg(
			array( 'item_id' => $item_id ),
			'https://support.stylemixthemes.com/tickets/new/support'
		);
	}

	public static function load_textdomain() {
		if ( ! is_textdomain_loaded( 'support-page' ) ) {
			load_plugin_textdomain(
				'support-page',
				false,
				dirname( plugin_basename( SUPPORT_PAGE_FILE ) ) . '/languages'
			);
		}
	}

	public static function default_data( $textdomain = 'support-page' ) {
		return include SUPPORT_PAGE_PATH . '/config/default.php';
	}

	/**
	 * Set support data for specific product via textdomain.
	 *
	 * @param string $textdomain
	 * @param array  $data
	 */
	public static function set_data( $textdomain, $data ) {
		self::$data[ $textdomain ] = $data;
	}

	/**
	 * Get support data for specific product via textdomain.
	 *
	 * @param string $textdomain
	 *
	 * @return array
	 */
	public static function get_data( $textdomain = 'support-page' ) {
		$default      = self::default_data( $textdomain );
		$product_data = isset( self::$data[ $textdomain ] ) ? self::$data[ $textdomain ] : array();

		return array_replace_recursive( $default, $product_data );
	}

	public static function handle_mailchimp_form() {
		if ( isset( $_POST['subscribe_to_mailchimp'] ) ) {
			if ( ! isset( $_POST['subscribe_nonce'] ) || ! wp_verify_nonce( $_POST['subscribe_nonce'], 'subscribe_to_mailchimp' ) ) {
				return;
			}

			if ( empty( $_POST['agree_terms'] ) ) {
				return;
			}

			$email = sanitize_email( $_POST['subscriber_email'] );
			$name  = isset( $_POST['subscriber_name'] ) ? sanitize_text_field( $_POST['subscriber_name'] ) : '';

			if ( class_exists( 'STMMailChimpBase' ) ) {
				$result = STMMailChimpBase::subscribeUserFromFrontend( $email, $name );

				if ( is_wp_error( $result ) ) {
					$redirect_url = add_query_arg( 'subscribed', 'error', wp_get_referer() );
				} else {
					$redirect_url = add_query_arg( 'subscribed', 'success', wp_get_referer() );
				}

				wp_safe_redirect( $redirect_url );
				exit;
			}
		}
	}

	/**
	 * Render the support page with optional textdomain for specific data.
	 *
	 * @param string $textdomain
	 */
	public static function render_support_page( $textdomain = 'support-page' ) {
		wp_enqueue_style( 'support-page', SUPPORT_PAGE_URL . 'assets/css/main.css', array(), SUPPORT_PAGE_VERSION, false );
		wp_enqueue_style( 'support-icons', SUPPORT_PAGE_URL . 'assets/icons/style.css', array(), SUPPORT_PAGE_VERSION, false );

		$data = self::get_data( $textdomain );
		include SUPPORT_PAGE_PATH . '/templates/main.php';
	}
}
