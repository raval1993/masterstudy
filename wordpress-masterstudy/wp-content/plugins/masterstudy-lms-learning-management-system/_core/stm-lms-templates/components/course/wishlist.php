<?php
/**
 * @var integer $course_id
 * @var boolean $without_title
 */

$without_title = isset( $without_title ) ? $without_title : false;
$wishlisted    = STM_LMS_User::is_wishlisted( $course_id );

wp_enqueue_script( 'masterstudy-single-course-wishlist' );
wp_localize_script(
	'masterstudy-single-course-components',
	'wishlist_data',
	array(
		'without_title' => $without_title,
	)
);

if ( is_user_logged_in() ) { ?>
	<div class="masterstudy-single-course-wishlist <?php echo esc_attr( $without_title ? 'masterstudy-single-course-wishlist_without-title' : '' ); ?>" data-id="<?php echo intval( $course_id ); ?>">
		<?php if ( $wishlisted ) { ?>
			<span class="masterstudy-single-course-wishlist__title masterstudy-single-course-wishlist_added">
				<?php
				if ( ! $without_title ) {
					echo esc_html__( 'Remove from wishlist', 'masterstudy-lms-learning-management-system' );
				}
				?>
			</span>
		<?php } else { ?>
			<span class="masterstudy-single-course-wishlist__title">
				<?php
				if ( ! $without_title ) {
					echo esc_html__( 'Add to wishlist', 'masterstudy-lms-learning-management-system' );
				}
				?>
			</span>
		<?php } ?>
	</div>
<?php } else { ?>
	<div class="masterstudy-single-course-wishlist">
		<a href="<?php echo esc_url( STM_LMS_User::login_page_url() ); ?>">
			<span class="masterstudy-single-course-wishlist__title">
				<?php
				if ( ! $without_title ) {
					echo esc_html__( 'Add to wishlist', 'masterstudy-lms-learning-management-system' );
				}
				?>
			</span>
		</a>
	</div>
	<?php
}
