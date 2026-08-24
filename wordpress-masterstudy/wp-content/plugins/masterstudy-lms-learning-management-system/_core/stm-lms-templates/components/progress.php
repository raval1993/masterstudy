<?php

/**
 * @var $title
 * @var $progress
 * @var $dark_mode
 * @var $hide_info
 *
 * masterstudy-progress_dark-mode - for dark mode
 * masterstudy-progress_hidden    - to hide progress bar
 */

wp_enqueue_style( 'masterstudy-progress' );

$title           = isset( $title ) ? $title : '';
$progress_class  = ! empty( $dark_mode ) ? ' masterstudy-progress_dark-mode' : '';
$progress_class .= ! empty( $is_hidden ) ? ' masterstudy-progress_hidden' : '';
$progress        = ! empty( $progress ) ? $progress : 0;
$progress_title  = ! empty( $title ) ? $title . ':' : '';
$hide_info       = isset( $hide_info ) ? $hide_info : '';
$is_reset        = $is_reset ?? false;
?>

<div class="masterstudy-progress<?php echo esc_attr( $progress_class ); ?>">
	<div class="masterstudy-progress__bars">
		<span class="masterstudy-progress__bar-empty"></span>
		<span class="masterstudy-progress__bar-filled" style="width:<?php echo esc_html( $progress ); ?>%"></span>
	</div>
	<div class="masterstudy-progress__bottom">
		<?php if ( ! $hide_info ) : ?>
			<div class="masterstudy-progress__title">
				<?php echo esc_html( $progress_title ); ?>
				<span class="masterstudy-progress__percent"><?php echo esc_html( $progress ); ?></span>%
			</div>
		<?php endif; ?>
		<?php
		if ( $is_reset ) {
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'title' => esc_html__( 'Reset', 'masterstudy-lms-learning-management-system' ),
					'style' => 'disabled',
					'size'  => 'sm',
					'link'  => '#',
					'id'    => 'masterstudy-progress-reset',
				)
			);
		}
		?>
	</div>
</div>
