<?php
wp_enqueue_style( 'masterstudy-icon-box' );

$button_custom_attributes = ( ! empty( $button_link['custom_attributes'] ) ) ? \Elementor\Utils::parse_custom_attributes( $button_link['custom_attributes'] ) : '';
?>
<div class="masterstudy-icon-box masterstudy-icon-box_<?php echo esc_attr( $preset ); ?>">
	<div class="masterstudy-icon-box__container">
		<span class="masterstudy-icon-box__icon">
			<?php
			if ( 'svg' === $library ) {
				echo wp_kses_post( $icon );
			} else {
				?>
				<i class="<?php echo esc_attr( $icon ); ?>"></i>
			<?php } ?>
		</span>
	</div>
	<div class="masterstudy-icon-box__container">
		<h2 class="masterstudy-icon-box__heading">
			<?php echo esc_html( $heading ); ?>
		</h2>
		<div class="masterstudy-icon-box__text">
			<?php echo esc_html( $text ); ?>
		</div>
		<a href="<?php echo esc_attr( $button_link['url'] ); ?>" class="masterstudy-icon-box__button"
			<?php
			echo ( ! empty( $button_link['nofollow'] ) ) ? 'rel="nofollow"' : '';
			echo ( ! empty( $button_link['is_external'] ) ) ? 'target="_blank"' : '';
			if ( is_array( $button_custom_attributes ) ) {
				foreach ( $button_custom_attributes as $key => $value ) {
					echo esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
				}
			}
			?>
		>
			<?php echo esc_html( $button_text ); ?>
		</a>
	</div>
</div>
