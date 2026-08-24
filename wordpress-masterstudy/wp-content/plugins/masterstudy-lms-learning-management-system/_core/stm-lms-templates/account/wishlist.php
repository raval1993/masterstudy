<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_current_user = STM_LMS_User::get_current_user( '', false, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );

wp_enqueue_style( 'masterstudy-account-main' );
wp_enqueue_style( 'masterstudy-account-wishlist' );
wp_enqueue_script( 'masterstudy-account-wishlist' );
wp_localize_script(
	'masterstudy-account-wishlist',
	'masterstudy_wishlist',
	array(
		'user_id' => $lms_current_user['id'] ?? 0,
	)
);

$reviews              = STM_LMS_Options::get_option( 'course_tab_reviews', true );
$wishlist             = STM_LMS_User::get_user_wishlist( $lms_current_user['id'] ?? 0 );
$wishlist_total_pages = (int) $wishlist['pages'] ?? 0;
$wishlist_posts       = $wishlist['posts'] ?? array();
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<h1 class="masterstudy-account-wishlist__title">
			<?php echo esc_html__( 'Wishlist', 'masterstudy-lms-learning-management-system' ); ?>
		</h1>
		<?php do_action( 'stm_lms_before_wishlist_list', $wishlist ); ?>
		<div class="masterstudy-account-wishlist">
			<?php
			if ( ! empty( $wishlist ) && ! empty( $wishlist_posts ) ) {
				?>
				<div class="masterstudy-account-wishlist__list">
					<?php
					foreach ( $wishlist_posts as $course ) {
						STM_LMS_Templates::show_lms_template(
							'components/course/card/default',
							array(
								'course'   => $course,
								'public'   => true,
								'reviews'  => (bool) $reviews,
								'wishlist' => true,
							)
						);
					}
					?>
				</div>
				<?php
			} elseif ( is_user_logged_in() ) {
				?>
				<div class="masterstudy-account-wishlist__empty">
					<div class="masterstudy-account-wishlist__empty-block">
						<span class="masterstudy-account-wishlist__empty-icon"></span>
						<span class="masterstudy-account-wishlist__empty-text">
							<?php echo esc_html__( 'Wishlist is empty', 'masterstudy-lms-learning-management-system' ); ?>
						</span>
						<a href="<?php echo esc_url( STM_LMS_Course::courses_page_url() ); ?>" target="_blank" class="masterstudy-account-wishlist__empty-button">
							<?php echo esc_html__( 'Explore courses', 'masterstudy-lms-learning-management-system' ); ?>
						</a>
					</div>
				</div>
			<?php } else { ?>
				<div class="masterstudy-account-wishlist__available">
					<span class="masterstudy-account-wishlist__available-text">
						<?php echo esc_html__( 'Wishlist will be available after', 'masterstudy-lms-learning-management-system' ); ?>
					</span>
					<a class="masterstudy-account-wishlist__available-link" href="<?php echo esc_url( add_query_arg( 'mode', 'register', STM_LMS_User::login_page_url() ) ); ?>">
						<?php echo esc_html__( 'registration', 'masterstudy-lms-learning-management-system' ); ?>
					</a>
				</div>
			<?php } ?>

			<div class="masterstudy-account-wishlist__pagination">
				<?php
				if ( ! empty( $wishlist_posts ) && $wishlist_total_pages > 1 ) {
					STM_LMS_Templates::show_lms_template(
						'components/pagination',
						array(
							'max_visible_pages' => 5,
							'total_pages'       => $wishlist_total_pages,
							'current_page'      => 1,
							'dark_mode'         => false,
							'is_queryable'      => false,
							'done_indicator'    => false,
							'is_api'            => true,
							'thin'              => false,
						)
					);
				}
				?>
			</div>

			<div class="masterstudy-account-wishlist__loader">
				<div class="masterstudy-account-wishlist__loader-body"></div>
			</div>
		</div>
		<?php do_action( 'stm_lms_after_wishlist_list', $wishlist ); ?>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
