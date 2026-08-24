<?php
/**
 * @var float $points
 * @var string $points_info
 * @var bool $show_points_info
 *
 */
?>

<div class="masterstudy-pricing-item masterstudy-pricing-points">
	<div class="masterstudy-pricing-item__header">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/switcher',
			array(
				'name'  => 'buy_for_points',
				'class' => 'masterstudy-switcher-toggleable',
				'on'    => ! empty( $points ),
			)
		);
		?>
		<span class="masterstudy-pricing-item__title">
			<?php echo esc_html__( 'Buy with points', 'masterstudy-lms-learning-management-system' ); ?>
		</span>
	</div>
	<div class="masterstudy-pricing-item__content <?php echo esc_attr( ! empty( $points ) ? 'masterstudy-pricing-item__content_open' : '' ); ?>">
		<div class="masterstudy-pricing-item__block">
			<span class="masterstudy-pricing-item__label">
				<?php echo esc_html__( 'Points price', 'masterstudy-lms-learning-management-system' ); ?>
			</span>
			<div class="masterstudy-pricing-item__input-wrapper">
				<input
					name="points_price"
					type="number"
					class="masterstudy-pricing-item__input masterstudy-pricing-item__input_number"
					placeholder="<?php echo esc_attr__( 'Enter price', 'masterstudy-lms-learning-management-system' ); ?>"
					value="<?php echo isset( $points ) ? esc_attr( $points ) : ''; ?>"
				/>
				<span class="masterstudy-pricing-item__arrow-top"></span>
				<span class="masterstudy-pricing-item__arrow-down"></span>
			</div>
		</div>
		<?php if ( $show_points_info ) { ?>
			<div class="masterstudy-pricing-item__block">
				<span class="masterstudy-pricing-item__label">
					<?php echo esc_html__( 'Points info', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<input
					name="points_price_info"
					type="text"
					class="masterstudy-pricing-item__input"
					value="<?php echo isset( $points_info ) ? esc_attr( $points_info ) : ''; ?>"
				/>
			</div>
		<?php } ?>
	</div>
</div>
