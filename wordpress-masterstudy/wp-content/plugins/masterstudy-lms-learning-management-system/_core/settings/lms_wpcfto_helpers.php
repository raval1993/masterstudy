<?php

new STM_LMS_WPCFTO_HELPERS();

class STM_LMS_WPCFTO_HELPERS {
	public function __construct() {
		add_filter( 'wpcfto_check_is_pro_field', array( $this, 'is_pro' ) );

		add_filter(
			'wpcfto_field_pro_banner',
			function () {
				return STM_LMS_PATH . '/stm-lms-templates/journey/unlock-banner-component.php';
			}
		);

		add_filter( 'wpcfto_iconpicker_sets', array( $this, 'add_wpcfto_category_icons' ) );

		add_filter( 'stm_wpcfto_single_field_classes', array( $this, 'wpcfto_field_addon_state' ), 10, 3 );

		add_action( 'stm_wpcfto_single_field_before_start', array( $this, 'start_field' ), 10, 6 );

		add_filter( 'stm_wpcfto_autocomplete_review_user', array( $this, 'users_search' ), 10, 2 );

		add_filter(
			'wpcfto_all_users_label',
			function() {
				return esc_html__( 'Choose User 1', 'masterstudy-lms-learning-management-system' );
			}
		);

		add_filter(
			'wpcfto_file_error_label',
			function() {
				return esc_html__( 'Error occurred, please try again', 'masterstudy-lms-learning-management-system' );
			}
		);

		add_filter(
			'wpcfto_empty_file_error_label',
			function() {
				return esc_html__( 'Please, select file', 'masterstudy-lms-learning-management-system' );
			}
		);

		add_filter(
			'wpcfto_file_error_ext_label',
			function() {
				return esc_html__( 'Invalid file extension', 'masterstudy-lms-learning-management-system' );
			}
		);

	}

	public function is_pro( $pro ) {
		return defined( 'STM_LMS_PRO_PATH' );
	}

	public function stm_wpcfto_add_vc_icons( $fonts ) {
		if ( empty( $fonts ) ) {
			$fonts = array();
		}

		$icons = json_decode( file_get_contents( STM_LMS_PATH . '/assets/icons/selection.json', true ), true );
		$icons = $icons['icons'];

		$fonts['STM LMS Icons'] = array();

		foreach ( $icons as $icon ) {
			$icon_name                = $icon['properties']['name'];
			$fonts['STM LMS Icons'][] = array(
				"stmlms-{$icon_name}" => $icon_name,
			);
		}

		return $fonts;
	}

	public function add_wpcfto_category_icons( $sets ) {
		$ms_icons   = $this->stm_wpcfto_add_vc_icons( array() );
		$ms_icons   = $ms_icons['STM LMS Icons'];
		$ms_icons_i = array();
		foreach ( $ms_icons as $icon ) {
			$icons        = array_keys( $icon );
			$ms_icons_i[] = $icons[0];
		}
		$ms_icons_i = apply_filters( 'stm_wpcfto_ms_icons', $ms_icons_i );

		$sets['MasterStudy'] = $ms_icons_i;

		return $sets;
	}

	public function wpcfto_field_addon_state( $classes, $field_name, $field ) {
		$is_addon = ( ! empty( $field['pro'] ) && empty( $is_pro ) );

		if ( 'addons' === $field['type'] ) {
			$is_addon = false;
		}

		$addon_state = apply_filters( "wpcfto_addon_option_{$field_name}", '' );

		if ( empty( $addon_state ) ) {
			$is_addon = false;
		}

		/*CHECK IF ADDON IS ENABLED*/
		if ( $this->stm_lms_check_addon_enabled( $addon_state ) ) {
			$is_addon = false;
		}

		if ( $is_addon ) {
			$classes[] = 'is_pro is_pro_in_addon';
		}

		if ( ! empty( $addon_state ) ) {
			$classes[] = "stm_lms_addon_group_settings_{$addon_state}";
		}

		return $classes;
	}

	public function is_addon( $classes, $field_name, $field ) {
		return in_array( 'is_pro is_pro_in_addon', $classes, true );
	}

	public function addon_state( $field_name ) {
		$addon_state = apply_filters( "wpcfto_addon_option_{$field_name}", '' );

		return $addon_state;
	}

	public function stm_lms_check_addon_enabled( $addon_name ) {
		if ( empty( $addon_name ) ) {
			return false;
		}

		$addons = get_option( 'stm_lms_addons' );

		return ( ! empty( $addons[ $addon_name ] ) && 'on' === $addons[ $addon_name ] );
	}

	public function start_field( $classes, $field_name, $field, $is_pro, $pro_url, $disable ) {
		$is_addon    = $this->is_addon( $classes, $field_name, $field );
		$addon_state = $this->addon_state( $field_name );

		if ( isset( $field['label'] ) && ! empty( $field['label'] ) ) {
			$converted_label = preg_replace( '/[^\p{L}\p{N}_]+/u', '_', $field['label'] );
		} else {
			$converted_label = '';
		}

		$field_label = rtrim( strtolower( $converted_label ), '_' );

		if ( empty( $pro_url ) ) {
			$pro_url = admin_url( 'admin.php?page=stm-lms-go-pro' );
		} else {
			$pro_url = admin_url( 'admin.php?page=stm-lms-go-pro&source=' . $field_label );
		}

		if ( 'is_pro' === $is_pro ) { ?>
			<div class="field_overlay"></div>
			<!--We have no pro plugin active-->
			<span class="pro-notice">
				<?php esc_html_e( 'Available in ', 'masterstudy-lms-learning-management-system' ); ?>
				<a href="<?php echo esc_url( $pro_url ); ?>" target="_blank"><?php esc_html_e( 'Pro Version', 'masterstudy-lms-learning-management-system' ); ?></a>
			</span>
			<?php
		}

		if ( $is_addon ) {
			/*We have pro plugin but addon seems to be disabled*/
			?>
			<div class="field_overlay"></div>
			<span class="pro-notice">
				<a href="#" @click.prevent="enableAddon($event, '<?php echo esc_attr( $addon_state ); ?>')">
					<i class="stmlms-power-off"></i>
				<?php esc_html_e( 'Enable addon', 'masterstudy-lms-learning-management-system' ); ?>
				</a>
			</span>
			<?php
		}

		if ( 'is_disabled' === $disable ) {
			$no_quota = STM_LMS_Subscriptions::get_featured_quota() < 1;
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$post_id  = ( isset( $_GET['post'] ) ) ? intval( $_GET['post'] ) : false;
			$featured = get_post_meta( $post_id, 'featured', true );

			if ( $no_quota && 'on' !== $featured ) {
				?>
				<div class="field_overlay"></div>
				<span class="is_disabled_notice">
					<?php esc_html_e( 'You have reached your featured courses quota limit!', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<?php
			}
		}
	}

	public function users_search( $response, $args ) {
		$s_args = array();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['s'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$s                        = sanitize_text_field( $_GET['s'] );
			$s_args['search']         = "*{$s}*";
			$s_args['search_columns'] = array(
				'user_login',
				'user_nicename',
			);
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['ids'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$s_args['include'] = explode( ',', sanitize_text_field( $_GET['ids'] ) );
		}

		$users = new WP_User_Query( $s_args );
		$users = $users->get_results();
		$data  = array();

		foreach ( $users as $user ) {
			$data[] = array(
				'id'        => $user->ID,
				'title'     => $user->data->user_nicename,
				'post_type' => 'user',
			);
		}

		return $data;
	}

}
