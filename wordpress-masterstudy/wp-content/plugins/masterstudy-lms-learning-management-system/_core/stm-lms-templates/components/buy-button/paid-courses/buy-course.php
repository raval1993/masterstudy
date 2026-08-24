<?php
/**
 * @var array $attributes
 * @var int $price
 * @var int $sale_price
 * @var bool $sale_price_active
 */

$is_sale = ! empty( $sale_price ) && ! empty( $sale_price_active );
?>
<a href="#" class="masterstudy-buy-button__link" <?php echo wp_kses_post( implode( ' ', $attributes ) ); ?>>
	<span class="masterstudy-buy-button__title">
		<?php echo esc_html__( 'Get course', 'masterstudy-lms-learning-management-system' ); ?>
	</span>
	<?php if ( ! empty( $price ) || ! empty( $sale_price ) ) : ?>
		<span class="masterstudy-buy-button__separator"></span>
		<span class="masterstudy-buy-button__price<?php echo $is_sale ? ' has_sale' : ''; ?>">
		<?php if ( $is_sale ) : ?>
			<span class="masterstudy-buy-button__price_sale">
				<?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $sale_price ) ); ?>
			</span>
			<?php
		endif;
		if ( ! empty( $price ) ) :
			?>
			<span class="masterstudy-buy-button__price_regular">
				<?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $price ) ); ?>
			</span>
		<?php endif; ?>
	</span>
	<?php endif; ?>
</a>
