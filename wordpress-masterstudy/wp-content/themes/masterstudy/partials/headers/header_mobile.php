<?php stm_module_scripts( 'header_js', 'header_2' ); ?>
<div class="container">
	<div class="row">
		<div class="col-md-12">

			<div class="header_top">

				<div class="logo-unit">
					<?php get_template_part( 'partials/headers/parts/logo' ); ?>
				</div>


				<div class="center-unit">
					<?php get_template_part( 'partials/headers/parts/center' ); ?>
				</div>

				<div class="right-unit">
					<?php get_template_part( 'partials/headers/parts/right' ); ?>
				</div>

				<button type="button" class="navbar-toggle collapsed hidden-lg hidden-md stm_header_top_search "
						data-toggle="collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>


				<div class="stm_header_top_toggler mbc">
					<?php
					if ( is_user_logged_in() ) {
						$current_user = wp_get_current_user();
						if ( ( $current_user instanceof WP_User ) ) {
							echo get_avatar( $current_user->ID, 32 );
						}
					} else {
						?>
						<i class="lnr lnr-user"></i>
						<?php
					}
					?>
				</div>

			</div>

		</div>
	</div>
</div>

<?php
$cats = stm_option( 'header_course_categories_online', array() );
if ( ! empty( $cats ) ) :
	?>

	<div class="categories-courses">
		<?php get_template_part( 'partials/headers/parts/courses_categories_with_search' ); ?>
	</div>

<?php endif; ?>


<?php get_template_part( 'partials/headers/mobile/account' ); ?>
<?php get_template_part( 'partials/headers/mobile/search' ); ?>
<?php get_template_part( 'partials/headers/mobile/menu' ); ?>
