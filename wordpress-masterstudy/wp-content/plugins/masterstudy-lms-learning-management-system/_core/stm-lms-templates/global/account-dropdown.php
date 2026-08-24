<?php if ( is_user_logged_in() ) :
	stm_lms_register_style( 'user' );
	$user                 = STM_LMS_User::get_current_user();
	$new_messages         = apply_filters( 'stm_lms_header_messages_counter', STM_LMS_Chat::user_new_messages( $user['id'] ) );
	$lms_template_current = get_query_var( 'lms_template' );
	$object_id            = get_queried_object_id();
	$menu_items           = STM_LMS_User_Menu::stm_lms_user_menu_display( $user, $lms_template_current, $object_id );

	$learning_menu = array_filter(
		$menu_items,
		function( $item ) {
			return isset( $item['menu_place'] ) && 'learning' === $item['menu_place'];
		}
	);

	$main_menu = array_filter(
		$menu_items,
		function( $item ) {
			return isset( $item['menu_place'] ) && 'main' === $item['menu_place'];
		}
	);

	$icon = ! empty( $elementor_icon ) ? $elementor_icon : 'stmlms-user11';
	?>
	<div class="stm_lms_account_dropdown">
		<div class="dropdown">
			<button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="<?php echo esc_attr( $icon ); ?> masterstudy-dropdown-menu__icon"></i>
				<span class="login_name"><?php echo esc_html( stm_lms_minimize_word( sprintf( esc_html__( 'Hey, %s', 'masterstudy-lms-learning-management-system' ), $user['login'] ), 15 ) ); ?></span>
				<span class="caret"></span>
				<?php if ( ! empty( $new_messages ) ) : ?>
					<div class="stm-lms-user_message_btn__counter">
						<?php echo wp_kses_post( $new_messages ); ?>
					</div>
				<?php endif; ?>
			</button>

			<div class="masterstudy-dropdown-menu dropdown-menu" aria-labelledby="dLabel">
				<div class="masterstudy-dropdown-menu__wrap">
					<div class="masterstudy-dropdown-menu__learning-column">
						<h3><?php esc_html_e( 'Learning Area', 'masterstudy-lms-learning-management-system' ); ?></h3>
						<ul class="masterstudy-dropdown-menu__list">
							<?php
							foreach ( $learning_menu as $menu_item ) {
								$item_type = ! empty( $menu_item['type'] ) ? 'dropdown-' . $menu_item['type'] : 'dropdown_menu_item';
								STM_LMS_Templates::show_lms_template( "account/float_menu/menu_items/{$item_type}", $menu_item );
							}
							?>
						</ul>
						<div class="masterstudy-dropdown-menu__logout">
							<a href="#" class="stm_lms_logout"><span><?php esc_html_e( 'Logout', 'masterstudy-lms-learning-management-system' ); ?></span></a>
						</div>
					</div>
					<?php if ( ! empty( $main_menu ) ) : ?>
					<div class="masterstudy-dropdown-menu__main-column">
						<h3><?php esc_html_e( 'Instructor Area', 'masterstudy-lms-learning-management-system' ); ?></h3>
						<ul class="masterstudy-dropdown-menu__list">
							<?php
							foreach ( $main_menu as $menu_item ) {
								$item_type = ! empty( $menu_item['type'] ) ? 'dropdown-' . $menu_item['type'] : 'dropdown_menu_item';
								STM_LMS_Templates::show_lms_template( "account/float_menu/menu_items/{$item_type}", $menu_item );
							}
							?>
						</ul>
					</div>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
	<?php
endif;
