<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp;

wp_enqueue_style( 'masterstudy-account-mobile-menu' );

$menus = array(
	'home'     => array(
		'title' => __( 'Home', 'masterstudy-lms-learning-management-system' ),
		'url'   => STM_LMS_User::login_page_url(),
	),
	'courses'  => array(
		'title' => __( 'Courses', 'masterstudy-lms-learning-management-system' ),
		'url'   => ms_plugin_user_account_url( 'enrolled-courses' ),
	),
	'wishlist' => array(
		'title' => __( 'Wishlist', 'masterstudy-lms-learning-management-system' ),
		'url'   => STM_LMS_User::wishlist_url(),
	),
	'menu'     => array(
		'title' => __( 'Menu', 'masterstudy-lms-learning-management-system' ),
		'url'   => '#',
	),
);

$current_url = trailingslashit( home_url( add_query_arg( array(), $wp->request ) ) );
?>

<div class="masterstudy-account-mobile-menu">
	<?php foreach ( $menus as $key => $item ) : ?>
		<?php
		$item_url  = trailingslashit( strtok( $item['url'], '?#' ) );
		$is_active = ( $item_url && $item_url === $current_url );

		$link_class = 'masterstudy-account-mobile-menu__link';
		if ( $is_active ) {
			$link_class .= ' masterstudy-account-mobile-menu__link_active';
		}
		?>
		<a href="<?php echo esc_url( $item['url'] ); ?>"
			class="<?php echo esc_attr( $link_class ); ?>"
			data-id="<?php echo esc_attr( $key ); ?>">
			<i class="<?php echo esc_attr( 'menu' === $key ? 'stmlms-mobile-menu-hamburger' : 'stmlms-mobile-menu-' . $key ); ?>"></i>
			<div class="masterstudy-account-mobile-menu__item">
				<?php echo esc_html( $item['title'] ); ?>
			</div>
		</a>
	<?php endforeach; ?>
</div>
