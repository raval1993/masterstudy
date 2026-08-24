<?php
/**
 * @var $id
 * @var $img_size
 */

$lazyload            = STM_LMS_Options::get_option( 'enable_lazyload', false );
$img_size            = masterstudy_get_image_size( ! empty( $img_size ) ? $img_size : '330x185' );
$post_status         = STM_LMS_Course::get_post_status( $id );
$is_featured_enabled = STM_LMS_Options::get_option( 'enable_featured_courses', true );

if ( $lazyload ) {
	wp_enqueue_script( 'masterstudy_lazysizes' );
	wp_enqueue_style( 'masterstudy_lazysizes' );
}

if ( ! empty( $img_container_height ) ) {
	$container_height     = preg_replace( '/[^0-9]/', '', $img_container_height );
	$img_container_height = ( is_admin() ? 'style=height:' : 'data-height=' ) . $container_height . 'px';
} else {
	$img_container_height = '';
}

$progress = 0;
if ( is_user_logged_in() ) {
	$my_progress = STM_LMS_Helpers::simplify_db_array( stm_lms_get_user_course( get_current_user_id(), $id, array( 'progress_percent' ) ) );
	if ( ! empty( $my_progress['progress_percent'] ) ) {
		$progress = $my_progress['progress_percent'];
	}

	if ( $progress > 100 ) {
		$progress = 100;
	}
}

?>

<div class="stm_lms_courses__single--image">

	<?php if ( ! empty( $progress ) ) : ?>
		<div class="stm_lms_courses__single--image__progress">
			<div class="stm_lms_courses__single--image__progress_bar"
				style="width : <?php echo esc_attr( $progress ); ?>%">
				<span class="stm_lms_courses__single--image__progress_label"><?php echo esc_html( "{$progress}%" ); ?></span>
			</div>
		</div>
	<?php endif; ?>
	<div class="featured-course-container">
		<?php if ( ! empty( $featured ) && $is_featured_enabled ) : ?>
			<div class="elab_is_featured_product"><?php esc_html_e( 'Featured', 'masterstudy-lms-learning-management-system' ); ?></div>
		<?php endif; ?>
	</div>
	<?php if ( ! empty( $post_status ) ) : ?>
		<div
		class="stm_lms_post_status heading_font <?php echo esc_html( sanitize_text_field( $post_status['status'] ) ); ?>"
		style="background-color: <?php echo esc_attr( sanitize_text_field( $post_status['bg_color'] ) ); ?>;
		color: <?php echo esc_attr( sanitize_text_field( $post_status['text_color'] ) ); ?>;">
			<?php echo esc_html( sanitize_text_field( $post_status['label'] ) ); ?>
		</div>
	<?php endif; ?>

	<a href="<?php the_permalink(); ?>"
	class="heading_font"
	data-preview="<?php esc_attr_e( 'Preview this course', 'masterstudy-lms-learning-management-system' ); ?>">
		<div class="stm_lms_courses__single--image__container" <?php echo esc_attr( $img_container_height ); ?>>
			<?php echo wp_kses_post( masterstudy_get_image( $id, $lazyload, null, $img_size[0], $img_size[1] ) ); ?>
		</div>
	</a>

</div>
