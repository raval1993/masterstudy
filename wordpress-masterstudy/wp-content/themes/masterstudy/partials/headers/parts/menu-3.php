<?php
if (!empty($page_id)) $transparent_header = get_post_meta($page_id, 'transparent_header', true);
$header_margin = stm_option('menu_top_margin');
if (empty($header_margin)) {
	$header_margin = 5;
}
if (!empty($transparent_header) and !$transparent_header) {
	$header_margin += 4;
}
$menu_style = 'style="margin-top:' . $header_margin . 'px;"';
$show_wishlist = stm_option('default_show_wishlist', true);
$show_search = stm_option('default_show_search', true);
$show_login = stm_option('show_login_buttons', true);
?>

<div class="stm_menu_toggler" data-text="<?php esc_html_e('Menu', 'masterstudy'); ?>"></div>
<div class="header_main_menu_wrapper clearfix" <?php echo sanitize_text_field($menu_style); ?>>

    <div class="pull-right hidden-xs right_buttons">

		<?php if (defined('STM_LMS_URL') and $show_wishlist): ?>
			<?php STM_LMS_Templates::show_lms_template('global/wishlist-button', array('icon' => 'lnr lnr-heart', 'class' => 'mtc_h')); ?>
		<?php endif; ?>

        <?php if($show_login): ?>
            <?php if( is_user_logged_in() ): ?>
                <?php if( class_exists( 'STM_LMS_Templates' ) ): ?>
                    <div class="header-login-button account">
                        <?php STM_LMS_Templates::show_lms_template( 'global/account-dropdown' ); ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="header-login-button sign-up">
                    <?php get_template_part( 'partials/headers/parts/sign-up' ); ?>
                </div>
                <div class="header-login-button log-in">
                    <?php get_template_part( 'partials/headers/parts/log-in' ); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

		<?php if ($show_search): ?>
            <div class="search-toggler-unit">
                <div class="search-toggler" data-toggle="modal" data-target="#searchModal"><i class="fa fa-search"></i>
                </div>
            </div>
		<?php endif; ?>

    </div>

    <div class="collapse navbar-collapse pull-right">
        <ul class="header-menu clearfix">
			<?php
			wp_nav_menu(array(
					'theme_location' => 'primary',
					'depth'          => 3,
					'container'      => false,
					'menu_class'     => 'header-menu clearfix',
					'items_wrap'     => '%3$s',
					'fallback_cb'    => false
				)
			);
			?>
        </ul>
    </div>

</div>