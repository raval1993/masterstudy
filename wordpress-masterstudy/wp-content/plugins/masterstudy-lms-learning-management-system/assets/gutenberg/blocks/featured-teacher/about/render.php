<?php
/**
 * Block render callback.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content Block default content.
 * @param WP_Block $block The block instance.
 */

$block_wrapper_attributes = '';
$user_label               = '';
$name                     = '';
$position                 = '';
$description              = '';
$image_inner_content      = '';

if ( isset( $block->context['masterstudy/label'] ) ) {
	$user_label     = $block->context['masterstudy/label'] ? $block->context['masterstudy/label'] : __( 'Teacher of Month', 'masterstudy-lms-learning-management-system' );
	$show_label     = $block->context['masterstudy/showLabel'];
	$show_position  = $block->context['masterstudy/showPosition'];
	$show_biography = $block->context['masterstudy/showBiography'];
}

if ( isset( $block->context['masterstudy/teacherId'] ) ) {
	$user_id = $block->context['masterstudy/teacherId'];

	$user_data = get_userdata( $user_id );

	if ( $user_data ) {
		$block_wrapper_attributes = get_block_wrapper_attributes();
		$name                     = $user_data->display_name;
		$position                 = $user_data->position;
		$description              = $user_data->description;
	}
}

if ( ! empty( $block->inner_blocks[0] ) && 'core/image' === $block->inner_blocks[0]->parsed_block['blockName'] ) {
	$image_inner_content = $block->inner_blocks[0]->parsed_block['innerContent'][0];
}
?>

<div <?php echo wp_kses_data( $block_wrapper_attributes ); ?>>
	<div class="lms-teacher-about-columns">
		<div class="lms-teacher-about-columns__instructor-info">
			<?php if ( $show_label ) : ?>
			<div class="lms-teacher-about-label"><?php echo esc_attr( $user_label ); ?></div>
			<?php endif; ?>
			<div class="lms-teacher-about-name"><?php echo esc_attr( $name ); ?></div>
			<?php if ( $show_position ) : ?>
			<div class="lms-teacher-about-position"><?php echo esc_attr( $position ); ?></div>
				<?php
				endif;
			if ( $show_biography ) :
				?>
			<div class="lms-teacher-about-biography">
				<p><?php echo wp_kses_post( $description ); ?></p>
			</div>
			<?php endif; ?>
			<div class="lms-teacher-about-courses">
				<?php echo esc_html__( 'Instructor Courses', 'masterstudy-lms-learning-management-system' ); ?>
			</div>
		</div>
		<div class="lms-teacher-about-columns__instructor-image">
			<div class="lms-teacher-about-avatar">
				<?php echo wp_kses_post( $image_inner_content ); ?>
			</div>
		</div>
	</div>
</div>
