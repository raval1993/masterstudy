<?php
/**
 * @var int   $menu_id
 * @var array $menu_item_images
 * @var array $full_width_triggers
 * @var string $desktop_nested_view
 * @var array $desktop_nested_views
 * @var array $desktop_dropdown_sides
 * @var array $desktop_centered_items
 * @var array $menu_item_image_positions
 * @var array $top_level_large_image_sizes
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! wp_style_is( 'masterstudy-mega-menu', 'enqueued' ) ) {
	wp_enqueue_style( 'masterstudy-mega-menu' );
}

if ( ! function_exists( 'masterstudy_build_menu_tree' ) ) {
	function masterstudy_build_menu_tree( $menu_id ) {
		$items = wp_get_nav_menu_items( $menu_id );
		if ( ! $items ) {
			return array();
		}
		$tree    = array();
		$indexed = array();
		foreach ( $items as $item ) {
			$item->children       = array();
			$indexed[ $item->ID ] = $item;
		}
		foreach ( $indexed as $item ) {
			if ( 0 === (int) $item->menu_item_parent ) {
				$tree[] = $item;
			} elseif ( isset( $indexed[ $item->menu_item_parent ] ) ) {
				$indexed[ $item->menu_item_parent ]->children[] = $item;
			}
		}
		return $tree;
	}
}

if ( ! function_exists( 'masterstudy_parse_badge_data' ) ) {
	function masterstudy_parse_badge_data( $item, $defaults ) {
		$badge_text  = get_post_meta( $item->ID, '_menu_item_stm_lms_badge', true );
		$badge_color = get_post_meta( $item->ID, '_menu_item_stm_lms_badge_color', true );
		$badge_bg    = get_post_meta( $item->ID, '_menu_item_stm_lms_badge_bg_color', true );
		return array(
			'text'  => ! empty( $badge_text ) ? $badge_text : ( ! empty( $defaults['text'] ) ? $defaults['text'] : '' ),
			'color' => ! empty( $badge_color ) ? $badge_color : $defaults['color'],
			'bg'    => ! empty( $badge_bg ) ? $badge_bg : $defaults['bg'],
		);
	}
}

if ( ! function_exists( 'masterstudy_is_menu_item_image_top' ) ) {
	function masterstudy_is_menu_item_image_top( $item_id, $map, $default_position = 'left' ) {

		$item_id = absint( $item_id );
		if ( ! empty( $map[ $item_id ] ) ) {
				return 'top' === $map[ $item_id ];
		}
		return 'top' === $default_position;
	}
}

if ( ! function_exists( 'masterstudy_get_large_image_style_attr' ) ) {
	function masterstudy_get_large_image_style_attr( $is_image_top, $large_image_height ) {

		$large_image_height = is_string( $large_image_height ) ? trim( $large_image_height ) : '';

		if ( ! $is_image_top || empty( $large_image_height ) ) {
			return '';
		}

		return ' style="' . esc_attr( 'height:' . $large_image_height . ';min-height:' . $large_image_height . ';max-height:' . $large_image_height . ';' ) . '"';
	}
}

if ( ! function_exists( 'masterstudy_render_cascade_menu_items' ) ) {
	function masterstudy_render_cascade_menu_items( $items, $badge_defaults, $menu_item_images_map, $menu_item_image_positions_map, $default_image_position, $current_url, $large_image_height = '' ) {

		if ( empty( $items ) || ! is_array( $items ) ) {
			return;
		}
		foreach ( $items as $item ) {
			$item_badge     = masterstudy_parse_badge_data( $item, $badge_defaults );
			$is_active      = ( $item->url === $current_url || ! empty( $item->current ) );
			$image_id       = $menu_item_images_map[ $item->ID ] ?? 0;
			$has_image      = ! empty( $image_id );
			$has_submenu    = ! empty( $item->children );
			$is_image_top   = $has_image && masterstudy_is_menu_item_image_top( $item->ID, $menu_item_image_positions_map, $default_image_position );
			$image_style    = masterstudy_get_large_image_style_attr( $is_image_top, $large_image_height );
			$item_classes   = array( 'masterstudy-mega-menu__item' );
			$item_classes[] = $has_image ? 'masterstudy-mega-menu__item--with-image' : '';
			$item_classes[] = $is_image_top ? 'masterstudy-mega-menu__item--image-top' : '';
			$item_classes[] = $is_active ? 'masterstudy-mega-menu__item--active' : '';
			$item_classes[] = $has_submenu ? 'masterstudy-mega-menu__item--has-submenu' : '';
			?>
			<li class="<?php echo esc_attr( trim( implode( ' ', $item_classes ) ) ); ?>">
				<a href="<?php echo esc_url( $item->url ); ?>" <?php echo $item->target ? 'target="' . esc_attr( $item->target ) . '"' : ''; ?>>
				<?php
				if ( $has_image ) :
					?>
						<span class="masterstudy-mega-menu__item-image"<?php echo $image_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper returns a safe attribute string. ?>>
							<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'masterstudy-mega-menu__item-image-img' ) ); ?>
						</span>
					<?php endif; ?>
					<span class="masterstudy-mega-menu__item-label">
						<span class="masterstudy-mega-menu__item-text"><?php echo esc_html( $item->title ); ?></span>
					<?php if ( ! empty( $item_badge['text'] ) ) : ?>
							<span class="masterstudy-mega-menu__badge" style="color: <?php echo esc_attr( $item_badge['color'] ); ?>; background-color: <?php echo esc_attr( $item_badge['bg'] ); ?>;">
								<?php echo esc_html( $item_badge['text'] ); ?>
							</span>
						<?php endif; ?>
					<?php if ( $has_submenu ) : ?>
							<span class="masterstudy-mega-menu__submenu-icon"></span>
						<?php endif; ?>
					</span>
				</a>
			<?php
			if ( $has_submenu ) :
				?>
					<ul class="masterstudy-mega-menu__submenu">
						<?php masterstudy_render_cascade_menu_items( $item->children, $badge_defaults, $menu_item_images_map, $menu_item_image_positions_map, $default_image_position, $current_url, $large_image_height ); ?>
					</ul>
				<?php endif; ?>
			</li>
			<?php
		}
	}
}

if ( ! function_exists( 'masterstudy_render_mega_menu_items' ) ) {
	function masterstudy_render_mega_menu_items( $items, $badge_defaults, $menu_item_images_map, $menu_item_image_positions_map, $default_image_position, $current_url, $is_nested = false, $large_image_height = '' ) {

		if ( empty( $items ) || ! is_array( $items ) ) {
			return;
		}
		?>
		<ul class="masterstudy-mega-menu__list<?php echo $is_nested ? ' masterstudy-mega-menu__list--nested' : ''; ?>">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$item_badge     = masterstudy_parse_badge_data( $item, $badge_defaults );
				$is_active      = ( $item->url === $current_url || ! empty( $item->current ) );
				$image_id       = $menu_item_images_map[ $item->ID ] ?? 0;
				$has_image      = ! empty( $image_id );
				$has_submenu    = ! empty( $item->children );
				$is_image_top   = $has_image && masterstudy_is_menu_item_image_top( $item->ID, $menu_item_image_positions_map, $default_image_position );
				$image_style    = masterstudy_get_large_image_style_attr( $is_image_top, $large_image_height );
				$item_classes   = array( 'masterstudy-mega-menu__item' );
				$item_classes[] = $has_image ? 'masterstudy-mega-menu__item--with-image' : '';
				$item_classes[] = $is_image_top ? 'masterstudy-mega-menu__item--image-top' : '';
				$item_classes[] = $is_active ? 'masterstudy-mega-menu__item--active' : '';
				$item_classes[] = $has_submenu ? 'masterstudy-mega-menu__item--has-submenu' : '';
				?>
				<li class="<?php echo esc_attr( trim( implode( ' ', $item_classes ) ) ); ?>">
					<a href="<?php echo esc_url( $item->url ); ?>" <?php echo $item->target ? 'target="' . esc_attr( $item->target ) . '"' : ''; ?>>
						<?php
						if ( $has_image ) :
							?>
							<span class="masterstudy-mega-menu__item-image"<?php echo $image_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper returns a safe attribute string. ?>>
								<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'masterstudy-mega-menu__item-image-img' ) ); ?>
							</span>
						<?php endif; ?>
						<span class="masterstudy-mega-menu__item-label">
							<span class="masterstudy-mega-menu__item-text"><?php echo esc_html( $item->title ); ?></span>
							<?php if ( ! empty( $item_badge['text'] ) ) : ?>
								<span class="masterstudy-mega-menu__badge" style="color: <?php echo esc_attr( $item_badge['color'] ); ?>; background-color: <?php echo esc_attr( $item_badge['bg'] ); ?>;">
									<?php echo esc_html( $item_badge['text'] ); ?>
								</span>
							<?php endif; ?>
							<?php if ( $has_submenu ) : ?>
								<span class="masterstudy-mega-menu__submenu-icon"></span>
							<?php endif; ?>
						</span>
					</a>
					<?php
					if ( $has_submenu ) :
						?>
						<?php masterstudy_render_mega_menu_items( $item->children, $badge_defaults, $menu_item_images_map, $menu_item_image_positions_map, $default_image_position, $current_url, true, $large_image_height ); ?>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}

if ( ! function_exists( 'masterstudy_render_mobile_menu_items' ) ) {
	function masterstudy_render_mobile_menu_items( $items, $badge_defaults, $menu_item_images_map, $menu_item_image_positions_map, $default_image_position, $current_url, $is_root = false, $large_image_height = '' ) {

		if ( empty( $items ) || ! is_array( $items ) ) {
			return;
		}
		?>
		<ul class="masterstudy-mega-menu__mobile-list<?php echo $is_root ? ' masterstudy-mega-menu__mobile-list--root' : ''; ?>"<?php echo $is_root ? '' : ' style="display: none;"'; ?>>
			<?php foreach ( $items as $item ) : ?>
				<?php
				$item_badge     = masterstudy_parse_badge_data( $item, $badge_defaults );
				$is_active      = ( $item->url === $current_url || ! empty( $item->current ) );
				$image_id       = $menu_item_images_map[ $item->ID ] ?? 0;
				$has_image      = ! empty( $image_id );
				$has_children   = ! empty( $item->children );
				$is_image_top   = $has_image && masterstudy_is_menu_item_image_top( $item->ID, $menu_item_image_positions_map, $default_image_position );
				$image_style    = masterstudy_get_large_image_style_attr( $is_image_top, $large_image_height );
				$item_classes   = array( 'masterstudy-mega-menu__mobile-section' );
				$item_classes[] = $has_children ? 'masterstudy-mega-menu__mobile-section--has-children' : 'masterstudy-mega-menu__mobile-section--leaf';
				$item_classes[] = $has_image ? 'masterstudy-mega-menu__mobile-item--with-image' : '';
				$item_classes[] = $is_image_top ? 'masterstudy-mega-menu__mobile-item--image-top' : '';
				$item_classes[] = $is_active ? 'masterstudy-mega-menu__mobile-item--active' : '';
				?>
				<li class="<?php echo esc_attr( trim( implode( ' ', $item_classes ) ) ); ?>">
					<?php if ( $has_children ) : ?>
						<button class="masterstudy-mega-menu__mobile-section-toggle" type="button" aria-expanded="false">
							<span class="masterstudy-mega-menu__mobile-section-title">
								<?php
								if ( $has_image ) :
									?>
									<span class="masterstudy-mega-menu__item-image"<?php echo $image_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper returns a safe attribute string. ?>>
										<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'masterstudy-mega-menu__item-image-img' ) ); ?>
									</span>
								<?php endif; ?>
								<span class="masterstudy-mega-menu__mobile-item-text"><?php echo esc_html( $item->title ); ?></span>
								<?php if ( ! empty( $item_badge['text'] ) ) : ?>
									<span class="masterstudy-mega-menu__badge" style="color: <?php echo esc_attr( $item_badge['color'] ); ?>; background-color: <?php echo esc_attr( $item_badge['bg'] ); ?>;">
										<?php echo esc_html( $item_badge['text'] ); ?>
									</span>
								<?php endif; ?>
							</span>
							<span class="masterstudy-mega-menu__mobile-section-icon"></span>
						</button>
						<?php masterstudy_render_mobile_menu_items( $item->children, $badge_defaults, $menu_item_images_map, $menu_item_image_positions_map, $default_image_position, $current_url, false, $large_image_height ); ?>
						<?php
					else :
						?>
						<a href="<?php echo esc_url( $item->url ); ?>" class="masterstudy-mega-menu__mobile-direct-link" <?php echo $item->target ? 'target="' . esc_attr( $item->target ) . '"' : ''; ?>>
							<span class="masterstudy-mega-menu__mobile-section-title">
								<?php
								if ( $has_image ) :
									?>
									<span class="masterstudy-mega-menu__item-image"<?php echo $image_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper returns a safe attribute string. ?>>
										<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'masterstudy-mega-menu__item-image-img' ) ); ?>
									</span>
								<?php endif; ?>
								<span class="masterstudy-mega-menu__mobile-item-text"><?php echo esc_html( $item->title ); ?></span>
															<?php if ( ! empty( $item_badge['text'] ) ) : ?>
									<span class="masterstudy-mega-menu__badge" style="color: <?php echo esc_attr( $item_badge['color'] ); ?>; background-color: <?php echo esc_attr( $item_badge['bg'] ); ?>;">
																<?php echo esc_html( $item_badge['text'] ); ?>
									</span>
								<?php endif; ?>
							</span>
						</a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}

$current_url    = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$menu_tree      = masterstudy_build_menu_tree( $menu_id );
$badge_defaults = array(
	'text'  => '',
	'color' => '#ffffff',
	'bg'    => '#227aff',
);

if ( empty( $menu_tree ) ) {
	echo '<p>' . esc_html__( 'No menu selected or menu is empty.', 'masterstudy-lms-learning-management-system' ) . '</p>';
	return;
}

$menu_item_images_map = array();
if ( ! empty( $menu_item_images ) && is_array( $menu_item_images ) ) {
	foreach ( $menu_item_images as $row ) {
		$target_id = absint( $row['menu_item_id'] ?? 0 );
		$image_id  = absint( $row['image']['id'] ?? 0 );
		if ( $target_id && $image_id && ! isset( $menu_item_images_map[ $target_id ] ) ) {
			$menu_item_images_map[ $target_id ] = $image_id;
		}
	}
}

$menu_item_image_positions_map = array();
if ( ! empty( $menu_item_image_positions ) && is_array( $menu_item_image_positions ) ) {
	foreach ( $menu_item_image_positions as $item_id => $position ) {
		$item_id = absint( $item_id );
		if ( $item_id && in_array( $position, array( 'left', 'top' ), true ) ) {
			$menu_item_image_positions_map[ $item_id ] = $position;
		}
	}
}

$default_image_position          = ! empty( $menu_item_image_position ) ? $menu_item_image_position : 'left';
$desktop_nested_view             = ! empty( $desktop_nested_view ) ? $desktop_nested_view : 'cascade';
$desktop_nested_views_map        = array();
$desktop_dropdown_sides_map      = array();
$desktop_centered_items_map      = array();
$top_level_large_image_sizes_map = array();
if ( ! empty( $desktop_nested_views ) && is_array( $desktop_nested_views ) ) {
	foreach ( $desktop_nested_views as $item_id => $nested_view ) {
		$item_id = absint( $item_id );
		if ( $item_id && in_array( $nested_view, array( 'headings', 'cascade' ), true ) ) {
			$desktop_nested_views_map[ $item_id ] = $nested_view;
		}
	}
}
if ( ! empty( $desktop_dropdown_sides ) && is_array( $desktop_dropdown_sides ) ) {
	foreach ( $desktop_dropdown_sides as $item_id => $side ) {
		$item_id = absint( $item_id );
		if ( $item_id && in_array( $side, array( 'auto', 'right', 'left' ), true ) ) {
			$desktop_dropdown_sides_map[ $item_id ] = $side;
		}
	}
}
if ( ! empty( $desktop_centered_items ) && is_array( $desktop_centered_items ) ) {
	foreach ( $desktop_centered_items as $item_id => $is_centered ) {
		$item_id = absint( $item_id );
		if ( $item_id && $is_centered ) {
			$desktop_centered_items_map[ $item_id ] = true;
		}
	}
}
$top_level_large_image_sizes = ! empty( $top_level_large_image_sizes ) && is_array( $top_level_large_image_sizes ) ? $top_level_large_image_sizes : array();
foreach ( $top_level_large_image_sizes as $item_id => $size ) {
	$item_id = absint( $item_id );
	$size    = is_string( $size ) ? trim( $size ) : '';

	if ( $item_id && ! empty( $size ) ) {
		$top_level_large_image_sizes_map[ $item_id ] = $size;
	}
}
$full_width_triggers_list = ! empty( $full_width_triggers ) ? array_values( array_filter( array_map( 'absint', (array) $full_width_triggers ) ) ) : array();

?>

<div class="masterstudy-mega-menu-wrapper" data-breakpoint="768">
	<button class="masterstudy-mega-menu__mobile-nav-toggle" type="button" aria-label="<?php esc_attr_e( 'Toggle menu', 'masterstudy-lms-learning-management-system' ); ?>" aria-expanded="false">
		<span class="masterstudy-mega-menu__mobile-nav-toggle-icon" aria-hidden="true"></span>
	</button>
	<?php foreach ( $menu_tree as $mega_item ) : ?>
		<?php
		$has_children    = ! empty( $mega_item->children );
		$trigger_url     = trim( $mega_item->url );
		$trigger_is_link = ( ! empty( $trigger_url ) && '#' !== $trigger_url );
		$is_full_width   = in_array( (int) $mega_item->ID, $full_width_triggers_list, true );
		if ( empty( $full_width_triggers_list ) && is_array( $mega_item->classes ) && in_array( 'masterstudy-mega-menu-full-width', $mega_item->classes, true ) ) {
			$is_full_width = true;
		}
		$item_desktop_nested_view    = $desktop_nested_views_map[ (int) $mega_item->ID ] ?? $desktop_nested_view;
		$item_dropdown_side          = $desktop_dropdown_sides_map[ (int) $mega_item->ID ] ?? 'auto';
		$item_center_content         = ! empty( $desktop_centered_items_map[ (int) $mega_item->ID ] );
		$is_cascade_view             = 'cascade' === $item_desktop_nested_view;
		$columns_classes             = 'masterstudy-mega-menu__columns';
		$has_heading_sections        = false;
		$has_top_image_leaf_sections = false;
		if ( ! $is_cascade_view ) {
			foreach ( $mega_item->children as $section ) {
				$section_has_child = ! empty( $section->children );
				$section_image_id  = $menu_item_images_map[ $section->ID ] ?? 0;
				$section_image_top = $section_image_id && masterstudy_is_menu_item_image_top( $section->ID, $menu_item_image_positions_map, $default_image_position );

				if ( $section_has_child ) {
					$has_heading_sections = true;
				}

				if ( ! $section_has_child && $section_image_top ) {
					$has_top_image_leaf_sections = true;
				}
			}

			if ( $has_heading_sections ) {
				$columns_classes .= ' masterstudy-mega-menu__columns--has-headings';
			} else {
				$columns_classes .= ' masterstudy-mega-menu__columns--no-headings';
			}

			if ( $has_top_image_leaf_sections ) {
				$columns_classes .= ' masterstudy-mega-menu__columns--cards';
			}
		}
		$desktop_classes         = 'masterstudy-mega-menu masterstudy-mega-menu--desktop';
		$desktop_classes        .= $is_full_width ? ' masterstudy-mega-menu--full-width' : '';
		$desktop_classes        .= $is_cascade_view ? ' masterstudy-mega-menu--cascade' : ' masterstudy-mega-menu--headings-hybrid';
		$desktop_classes        .= ( ! $is_cascade_view && ! $has_heading_sections && $has_top_image_leaf_sections ) ? ' masterstudy-mega-menu--cards-layout' : '';
		$desktop_classes        .= $item_center_content ? ' masterstudy-mega-menu--center-content' : '';
		$item_large_image_height = $top_level_large_image_sizes_map[ (int) $mega_item->ID ] ?? '';

		?>
		<div class="<?php echo esc_attr( $desktop_classes ); ?>" data-menu-id="<?php echo esc_attr( $mega_item->ID ); ?>" data-open-side="<?php echo esc_attr( $item_dropdown_side ); ?>">
			<?php if ( $trigger_is_link ) : ?>
				<a href="<?php echo esc_url( $mega_item->url ); ?>" class="masterstudy-mega-menu__trigger" <?php echo $has_children ? 'aria-expanded="false"' : ''; ?> <?php echo $mega_item->target ? 'target="' . esc_attr( $mega_item->target ) . '"' : ''; ?>>
					<?php echo esc_html( $mega_item->title ); ?>
					<?php
					if ( $has_children ) :
						?>
						<span class="masterstudy-mega-menu__trigger-icon"></span><?php endif; ?>
				</a>
			<?php else : ?>
				<span class="masterstudy-mega-menu__trigger" role="button" tabindex="0" <?php echo $has_children ? 'aria-expanded="false"' : ''; ?>>
					<?php echo esc_html( $mega_item->title ); ?>
					<?php
					if ( $has_children ) :
						?>
						<span class="masterstudy-mega-menu__trigger-icon"></span><?php endif; ?>
				</span>
			<?php endif; ?>

			<?php if ( $has_children ) : ?>
				<div class="masterstudy-mega-menu__panel" role="menu">
					<div class="masterstudy-mega-menu__panel-wrap">
						<div class="<?php echo esc_attr( $is_cascade_view ? $columns_classes . ' masterstudy-mega-menu__columns--cascade' : $columns_classes ); ?>">
							<?php if ( $is_cascade_view ) : ?>
								<div class="masterstudy-mega-menu__column">
									<ul class="masterstudy-mega-menu__list">
										<?php masterstudy_render_cascade_menu_items( $mega_item->children, $badge_defaults, $menu_item_images_map, $menu_item_image_positions_map, $default_image_position, $current_url, $item_large_image_height ); ?>
									</ul>
								</div>
							<?php else : ?>
								<?php foreach ( $mega_item->children as $section ) : ?>
									<?php
									$section_badge     = masterstudy_parse_badge_data( $section, $badge_defaults );
									$section_has_child = ! empty( $section->children );
									$section_image_id  = $menu_item_images_map[ $section->ID ] ?? 0;
									$section_image_top = $section_image_id && masterstudy_is_menu_item_image_top( $section->ID, $menu_item_image_positions_map, $default_image_position );
									?>
									<div class="masterstudy-mega-menu__column">
										<?php if ( $section_has_child ) : ?>
											<?php
											$heading_classes     = 'masterstudy-mega-menu__heading';
											$heading_classes    .= $section_image_top ? ' masterstudy-mega-menu__heading--image-top' : '';
											$heading_image_style = masterstudy_get_large_image_style_attr( $section_image_top, $item_large_image_height );

											?>
											<h3 class="<?php echo esc_attr( $heading_classes ); ?>">
												<?php
												if ( $section_image_id ) :
													?>
													<span class="masterstudy-mega-menu__item-image"<?php echo $heading_image_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper returns a safe attribute string. ?>><?php echo wp_get_attachment_image( $section_image_id, 'full', false, array( 'class' => 'masterstudy-mega-menu__item-image-img' ) ); ?></span>
												<?php endif; ?>
												<?php echo esc_html( $section->title ); ?>
												<?php
												if ( ! empty( $section_badge['text'] ) ) :
													?>
													<span class="masterstudy-mega-menu__badge" style="color: <?php echo esc_attr( $section_badge['color'] ); ?>; background-color: <?php echo esc_attr( $section_badge['bg'] ); ?>;"><?php echo esc_html( $section_badge['text'] ); ?></span>
												<?php endif; ?>
											</h3>
											<?php masterstudy_render_mega_menu_items( $section->children, $badge_defaults, $menu_item_images_map, $menu_item_image_positions_map, $default_image_position, $current_url, false, $item_large_image_height ); ?>
											<?php
										else :
											?>
											<?php masterstudy_render_mega_menu_items( array( $section ), $badge_defaults, $menu_item_images_map, $menu_item_image_positions_map, $default_image_position, $current_url, false, $item_large_image_height ); ?>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<div class="masterstudy-mega-menu masterstudy-mega-menu--mobile" data-menu-id="<?php echo esc_attr( $mega_item->ID ); ?>">
			<?php if ( $has_children ) : ?>
				<a href="<?php echo esc_url( $mega_item->url ); ?>" class="masterstudy-mega-menu__mobile-trigger masterstudy-mega-menu__mobile-trigger--has-children" <?php echo $mega_item->target ? 'target="' . esc_attr( $mega_item->target ) . '"' : ''; ?>>
					<?php echo esc_html( $mega_item->title ); ?>
					<span class="masterstudy-mega-menu__trigger-icon"></span>
				</a>
				<div class="masterstudy-mega-menu__mobile-overlay"></div>
				<div class="masterstudy-mega-menu__mobile-panel" data-side="right">
					<div class="masterstudy-mega-menu__mobile-header">
						<h2 class="masterstudy-mega-menu__mobile-title"><?php echo esc_html( $mega_item->title ); ?></h2>
						<button class="masterstudy-mega-menu__mobile-close" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'masterstudy-lms-learning-management-system' ); ?>">&times;</button>
					</div>
					<div class="masterstudy-mega-menu__mobile-content">
						<?php masterstudy_render_mobile_menu_items( $mega_item->children, $badge_defaults, $menu_item_images_map, $menu_item_image_positions_map, $default_image_position, $current_url, true, $item_large_image_height ); ?>
					</div>
				</div>
			<?php else : ?>
				<a href="<?php echo esc_url( $mega_item->url ); ?>" class="masterstudy-mega-menu__mobile-trigger masterstudy-mega-menu__mobile-trigger--no-children" <?php echo $mega_item->target ? 'target="' . esc_attr( $mega_item->target ) . '"' : ''; ?>>
					<?php echo esc_html( $mega_item->title ); ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
