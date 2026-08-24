<?php
/**
 * @var $current_user
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_enqueue_style( 'masterstudy-account-menu' );
wp_enqueue_script( 'masterstudy-account-menu' );
wp_localize_script(
	'masterstudy-account-menu',
	'masterstudy_account_data',
	array(
		'user_id'                 => $current_user['id'] ?? 0,
		'log_out_confirm_message' => esc_html__( 'Are you sure you want to log out?', 'masterstudy-lms-learning-management-system' ),
	)
);

$menu_items = STM_LMS_User_Menu::stm_lms_user_menu_display( get_query_var( 'lms_template' ) );

if ( empty( $menu_items ) || ! is_array( $menu_items ) ) {
	return;
}

$is_instructor = STM_LMS_Instructor::is_instructor( $current_user['id'] );
$sections      = STM_LMS_User_Menu::get_account_menu_sections( $menu_items );
?>

<div class="masterstudy-account-menu">
	<?php if ( $is_instructor ) { ?>
		<div class="masterstudy-account-menu__mode">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/switcher',
				array(
					'name'  => 'instructor_menu',
					'class' => 'masterstudy-account-menu-switcher',
					'on'    => false,
				)
			);
			echo esc_html__( 'Instructor mode', 'masterstudy-lms-learning-management-system' );
			?>
		</div>
		<?php
	}
	?>
	<div class="masterstudy-account-menu__list">
		<?php
		foreach ( $sections['order'] as $section_key ) {

			if ( empty( $sections['list'][ $section_key ] ) ) {
				continue;
			}

			$section_title = $sections['labels'][ $section_key ] ?? ucwords( str_replace( array( '-', '_' ), ' ', $section_key ) );
			?>
			<div class="masterstudy-account-menu__list-section">
				<div class="masterstudy-account-menu__list-section-title">
					<?php echo esc_html( $section_title ); ?>
				</div>

				<?php foreach ( $sections['list'][ $section_key ] as $menu_item ) { ?>
					<?php
					$url   = isset( $menu_item['menu_url'] ) ? (string) $menu_item['menu_url'] : '';
					$label = isset( $menu_item['menu_title'] ) ? (string) $menu_item['menu_title'] : '';
					$icon  = isset( $menu_item['menu_icon'] ) ? (string) $menu_item['menu_icon'] : '';
					$id    = isset( $menu_item['id'] ) ? (string) $menu_item['id'] : '';
					$badge = $menu_item['badge_count'] ?? null;

					$slug       = $menu_item['slug'] ?? '';
					$is_active  = $menu_item['is_active'] ?? false;
					$item_cls   = 'masterstudy-account-menu__list-item' . ( $is_active ? ' masterstudy-account-menu__list-item_active' : '' );
					$item_cls  .= 'settings' === $slug ? ' masterstudy-account-menu__list-item_settings' : '';
					$item_cls  .= 'chat' === $slug ? ' masterstudy-account-menu__list-item_messages' : '';
					$item_cls  .= 'logout' === $slug ? ' masterstudy-account-menu__list-item_logout' : '';
					$menu_place = isset( $menu_item['menu_place'] ) ? (string) $menu_item['menu_place'] : '';

					if ( $is_instructor && 'learning' === $menu_place && 'chat' !== $slug ) {
						$item_cls .= ' masterstudy-account-menu__list-item_hidden';
					} elseif ( ! $is_instructor && 'main' === $menu_place ) {
						$item_cls .= ' masterstudy-account-menu__list-item_hidden';
					}
					?>
					<a class="<?php echo esc_attr( $item_cls ); ?>" href="<?php echo esc_url( $url ); ?>" data-menu-place="<?php echo esc_attr( $menu_place ); ?>">
						<?php if ( 'lesson_notes' === $id ) { ?>
							<svg class="<?php echo esc_attr( $icon ); ?>" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
								<path class="masterstudy-account-menu__notes-icon-fill" d="M12.69 17H7v-5.69l6.18-6.18a4 4 0 0 1 5.66 5.66L12.69 17z"/>
								<path d="M20.28 3.72a6.02 6.02 0 0 0-8.52 0L5 10.48V19h8.52l6.76-6.76a6.02 6.02 0 0 0 0-8.52zM12.69 17H7v-5.69l6.18-6.18a4 4 0 0 1 5.66 5.66L12.69 17z"/>
								<path d="M17.71 6.29a1 1 0 0 1 0 1.42l-14 14a1 1 0 0 1-1.42-1.42l14-14a1 1 0 0 1 1.42 0z"/>
								<path d="M18.5 15a1 1 0 0 1-1 1H9a1 1 0 1 1 0-2h8.5a1 1 0 0 1 1 1z"/>
							</svg>
						<?php } else { ?>
							<i class="<?php echo esc_attr( $icon ); ?>"></i>
						<?php } ?>
						<span class="masterstudy-account-menu__list-item-label">
							<?php echo esc_html( $label ); ?>
						</span>

						<?php if ( null !== $badge && '' !== $badge && (int) $badge > 0 ) { ?>
							<span class="masterstudy-account-menu__list-item-badge">
								<?php echo (int) $badge; ?>
							</span>
						<?php } ?>
					</a>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</div>
