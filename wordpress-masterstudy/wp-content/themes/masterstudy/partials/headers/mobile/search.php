<div class="stm_lms_search_popup">
	<div class="stm_lms_search_popup__close">
		<i class="lnr lnr-cross"></i>
	</div>
	<div class="inner">
		<?php
		if ( wp_is_mobile() && stm_get_layout_is_mobile() ) {
			if ( ! empty( get_nav_menu_locations() ) ) {
				?>
				<h2><?php esc_html_e( 'Menu', 'masterstudy' ); ?></h2>
				<div class="header_top ms_mobile_menu">
					<?php get_template_part( 'partials/headers/parts/mobile_menu' ); ?>
				</div>
			<?php } ?>

			<h2><?php esc_html_e( 'Search', 'masterstudy' ); ?></h2>
			<div class="header_top">
				<?php get_template_part( 'partials/headers/parts/center' ); ?>
			</div>
			<?php
		} else {
			?>
			<h2><?php esc_html_e( 'Search', 'masterstudy' ); ?></h2>
			<div class="header_top">
				<?php get_template_part( 'partials/headers/parts/center' ); ?>
			</div>
			<?php
		}
		?>

	</div>
</div>
