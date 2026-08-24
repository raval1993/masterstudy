<?php
// phpcs:ignoreFile
// added ignoreFile phpcs because the sniffer swore at some lines in which, after correcting the menu, it broke, I didn’t want to ignore the class everywhere because the file is small
function stm_lms_mobile_custom_menus() {
	$menu_name = 'primary'; // specify custom menu slug
	$menu_list = '';
	$locations = get_nav_menu_locations();

	if ( $locations && isset( $locations[ $menu_name ] ) ) {
		$menu       = wp_get_nav_menu_object( $locations[ $menu_name ] );
		$menu_items = wp_get_nav_menu_items( $menu->term_id );

		$count           = 0;
		$parent_sub_menu = false;
		$single_parent   = false;

		foreach ( (array) $menu_items as $key => $menu_item ) {

			$title = $menu_item->title;
			$link  = $menu_item->url;

			if ( $menu_item->menu_item_parent == 0 ) {
				$single_parent = true;
			} else {
				$single_parent = false;
			}

			if ( $single_parent ) {
				$menu_list .= '<div class="stm_lms_categories_dropdown__parent">';
				$menu_list .= '<a href=' . $link . ' class="sbc_h" >' . $title . '</a>' . "\n";
				if ( isset( $menu_items[ $key + 1 ] ) && $menu_items[ $key + 1 ]->menu_item_parent == 0 ) {
					$menu_list .= '</div>' . "\n";
				} else if ( isset( $menu_items[ $key + 1 ] ) ) {
					$menu_list       .= '<span class="stm_lms_cat_toggle"></span>' . "\n";
					$menu_list       .= '<div class="stm_lms_categories_dropdown__childs">' . "\n";
					$menu_list       .= '<div class="stm_lms_categories_dropdown__childs_container">' . "\n";
					$parent_sub_menu = true;
				}

			} else if ( $parent_sub_menu ) {
				$is_third  = false;
				$menu_list .= '<div class="stm_lms_categories_dropdown__child">' . "\n";

				foreach ( $menu_items as $item ) {
					if ( $item->menu_item_parent > 0 && $item->ID == $menu_item->menu_item_parent ) {
						$is_third  = true;
						$menu_list .= '<a href=' . $link . ' class="third-sub-mobile">' . $title . '</a>' . "\n";
					}
				}
				if ( ! $is_third ) {
					$menu_list .= '<a href=' . $link . ' >' . $title . '</a>' . "\n";
				} else {
					$is_third = false;
				}
				$menu_list .= '</div>' . "\n";

				if ( isset( $menu_items[ $key + 1 ] ) && $menu_items[ $key + 1 ]->menu_item_parent == 0 || !isset($menu_items[$key + 1]) ) {
					$parent_sub_menu = false;
					$menu_list       .= '</div>' . "\n";
					$menu_list       .= '</div>' . "\n";
					$menu_list       .= '</div>' . "\n";
				} else {
					$parent_sub_menu = true;
				}
			}
			$count ++;
		}
	}
	$end_menu_item = end($menu_items);
	if ( $end_menu_item->menu_item_parent == 0  ) {
		$menu_list .= '</div>' . "\n";
	}
	echo $menu_list;
}

?>
	<div class="stm_lms_categories_dropdown vue_is_disabled stm_lms_categories">
		<div class="stm_lms_categories_dropdown__parents">
			<?php
			stm_lms_mobile_custom_menus();
			?>
		</div>
	</div>
