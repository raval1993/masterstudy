<?php
/**
 * @var string $type
 * @var string $value
 * @var string $color
 *
 * available colors: success, warning, danger
 */

wp_enqueue_style( 'masterstudy-statistics-block' );

$titles = array(
	'completed_courses' => esc_html__( 'Courses completed', 'masterstudy-lms-learning-management-system' ),
	'groups'            => esc_html__( 'Groups', 'masterstudy-lms-learning-management-system' ),
	'bundles'           => esc_html__( 'Bundles', 'masterstudy-lms-learning-management-system' ),
	'certificates'      => esc_html__( 'Certificates', 'masterstudy-lms-learning-management-system' ),
	'quizzes'           => esc_html__( 'Quizzes', 'masterstudy-lms-learning-management-system' ),
	'points'            => esc_html__( 'Points', 'masterstudy-lms-learning-management-system' ),
	'assignments'       => esc_html__( 'Assignments', 'masterstudy-lms-learning-management-system' ),
);

$value = $value ?? '';
?>

<div class="masterstudy-statistics-block masterstudy-statistics-block_<?php echo esc_attr( $type ); ?> <?php echo ! empty( $color ) ? esc_attr( $color ) : ''; ?>">
	<?php STM_LMS_Templates::show_lms_template( 'components/stats-loader' ); ?>
	<span class="masterstudy-statistics-block__icon"></span>
	<div class="masterstudy-statistics-block__content">
		<div class="masterstudy-statistics-block__title">
			<?php echo esc_html( $titles[ $type ] ); ?>
		</div>
		<div class="masterstudy-statistics-block__value">
			<?php echo esc_html( $value ); ?>
		</div>
	</div>
</div>
