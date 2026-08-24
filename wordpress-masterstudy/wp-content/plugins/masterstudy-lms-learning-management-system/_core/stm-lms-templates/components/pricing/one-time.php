<?php
/**
 * @var float $price
 * @var string $price_info
 * @var bool $show_price_info
 *
 */
?>

<div class="masterstudy-pricing-item masterstudy-pricing-one-time">
	<div class="masterstudy-pricing-item__header">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/switcher',
			array(
				'name'  => 'single_sale',
				'class' => 'masterstudy-switcher-toggleable',
				'on'    => ! empty( $price ),
			)
		);
		?>
		<span class="masterstudy-pricing-item__title">
			<?php echo esc_html__( 'One-time purchase', 'masterstudy-lms-learning-management-system' ); ?>
		</span>
	</div>
	<div class="masterstudy-pricing-item__content <?php echo esc_attr( ! empty( $price ) ? 'masterstudy-pricing-item__content_open' : '' ); ?>">
		<div class="masterstudy-pricing-item__block">
			<span class="masterstudy-pricing-item__label">
				<?php
				echo esc_html__( 'Price', 'masterstudy-lms-learning-management-system' );
				echo ' (' . esc_html( STM_LMS_Options::get_option( 'currency_symbol', '$' ) ) . ')';
				?>
			</span>
			<div class="masterstudy-pricing-item__input-wrapper">
				<input
					name="one_time_price"
					type="number"
					class="masterstudy-pricing-item__input masterstudy-pricing-item__input_number"
					placeholder="<?php echo esc_attr__( 'Enter price', 'masterstudy-lms-learning-management-system' ); ?>"
					value="<?php echo isset( $price ) ? esc_attr( $price ) : ''; ?>"
				/>
				<span class="masterstudy-pricing-item__arrow-top"></span>
				<span class="masterstudy-pricing-item__arrow-down"></span>
			</div>
		</div>
		<?php if ( $show_price_info ) { ?>
			<div class="masterstudy-pricing-item__block">
				<span class="masterstudy-pricing-item__label">
					<?php echo esc_html__( 'Price info', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<input
					name="one_time_price_info"
					type="text"
					class="masterstudy-pricing-item__input"
					value="<?php echo isset( $price_info ) ? esc_attr( $price_info ) : ''; ?>"
				/>
			</div>
		<?php } ?>
	</div>
</div>
