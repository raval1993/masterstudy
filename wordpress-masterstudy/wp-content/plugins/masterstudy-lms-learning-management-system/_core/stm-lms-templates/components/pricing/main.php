<?php
/**
 * @var bool $points_show
 * @var float $points
 * @var string $points_info
 * @var bool $show_points_info
 * @var float $price
 * @var string $price_info
 * @var bool $show_price_info
 * @var int $bundle_id
 */

wp_enqueue_style( 'masterstudy-pricing' );
wp_enqueue_script( 'masterstudy-pricing' );

$points_show      = isset( $points_show ) ? $points_show : true;
$show_price_info  = isset( $show_price_info ) ? $show_price_info : false;
$show_points_info = isset( $show_points_info ) ? $show_points_info : false;
?>

<div class="masterstudy-pricing">
	<?php
	STM_LMS_Templates::show_lms_template(
		'components/pricing/one-time',
		array(
			'price'           => $price,
			'price_info'      => $price_info,
			'show_price_info' => $show_price_info,
		)
	);
	if ( is_ms_lms_addon_enabled( 'point_system' ) && $points_show ) {
		STM_LMS_Templates::show_lms_template(
			'components/pricing/points',
			array(
				'points'           => $points,
				'points_info'      => $points_info,
				'show_points_info' => $show_points_info,
			)
		);
	}
	?>
</div>
