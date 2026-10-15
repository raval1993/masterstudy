<?php defined( 'ABSPATH' ) || exit; ?>
<!doctype html><html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class( 'mse-page' ); ?>><?php wp_body_open(); ?>
<a class="mse-skip" href="#main">Skip to content</a>
<header class="mse-header"><div class="mse-container mse-header-inner">
<a class="mse-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span class="mse-brand-mark"><?php echo mse_icon( 'welcome-learn-more' ); ?></span><span><?php echo esc_html( get_bloginfo( 'name' ) ); ?><small>MAKE YOUR NEXT MOVE</small></span></a>
<button class="mse-menu-toggle" type="button" aria-controls="mse-navigation" aria-expanded="false" aria-label="Open navigation" title="Menu"><?php echo mse_icon( 'menu' ); ?></button>
<nav id="mse-navigation" class="mse-nav" aria-label="Main navigation"><a href="<?php echo esc_url( home_url( '/courses/' ) ); ?>">Explore courses</a><a href="<?php echo esc_url( home_url( '/user-account/' ) ); ?>">My courses</a><a href="<?php echo esc_url( home_url( '/student-login/' ) ); ?>"><?php echo mse_icon( 'admin-users' ); ?> Account</a></nav>
</div></header>
<main id="main" class="mse-container mse-section"><h1 class="mls-hub-heading">My Learning</h1><?php echo mls_portal(); ?></main>
<footer class="mse-footer"><div class="mse-container mse-footer-bottom"><span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span><a href="<?php echo esc_url( home_url( '/courses/' ) ); ?>">Explore courses</a></div></footer>
<?php wp_footer(); ?></body></html>
