<?php
/**
 * @var string $id
 * @var array $items
 */

$items = wp_parse_args(
	$items ?? array(),
	array(
		'today'      => esc_html__( 'Today', 'masterstudy-lms-learning-management-system' ),
		'yesterday'  => esc_html__( 'Yesterday', 'masterstudy-lms-learning-management-system' ),
		'this_week'  => esc_html__( 'This week', 'masterstudy-lms-learning-management-system' ),
		'last_week'  => esc_html__( 'Last week', 'masterstudy-lms-learning-management-system' ),
		'this_month' => esc_html__( 'This month', 'masterstudy-lms-learning-management-system' ),
		'last_month' => esc_html__( 'Last month', 'masterstudy-lms-learning-management-system' ),
		'this_year'  => esc_html__( 'This year', 'masterstudy-lms-learning-management-system' ),
		'last_year'  => esc_html__( 'Last year', 'masterstudy-lms-learning-management-system' ),
	)
);
?>

<div class="masterstudy-datepicker-modal">
	<div class="masterstudy-datepicker-modal__wrapper">
		<div class="masterstudy-datepicker-modal__single">
			<?php foreach ( $items as $key => $label ) { ?>
				<div id="masterstudy-datepicker-modal-<?php echo esc_attr( $key ); ?>" class="masterstudy-datepicker-modal__single-item">
					<?php echo esc_html( $label ); ?>
				</div>
			<?php } ?>
			<div class="masterstudy-datepicker-modal__actions">
				<span class="masterstudy-datepicker-modal__reset">
					<?php echo esc_html__( 'Reset', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
				<span class="masterstudy-datepicker-modal__close">
					<?php echo esc_html__( 'Close', 'masterstudy-lms-learning-management-system' ); ?>
				</span>
			</div>
		</div>
		<div class="masterstudy-datepicker-modal__calendar">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/analytics/datepicker',
				array(
					'id' => $id,
				)
			);
			?>
		</div>
	</div>
</div>
