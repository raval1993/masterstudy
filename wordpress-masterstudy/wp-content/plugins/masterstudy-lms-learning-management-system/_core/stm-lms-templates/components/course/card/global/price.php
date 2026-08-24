<?php
/**
 * @var $course
 */

$course_free_status = masterstudy_lms_course_free_status( $course['id'], $course['price'] );
$members_only       = ! $course['single_sale'] && STM_LMS_Subscriptions::subscription_enabled() && ! $course['not_in_membership'];
$is_sale            = ! empty( $course['sale_price'] ) && $course['is_sale_active'];
?>

<div class="masterstudy-course-card__price <?php echo esc_attr( $members_only ? 'masterstudy-course-card__price_subscription' : '' ); ?>">
	<?php if ( $course['single_sale'] && ! $course_free_status['zero_price'] ) { ?>
		<div class="masterstudy-course-card__price-single <?php echo $is_sale ? 'masterstudy-course-card__price-single_sale' : ''; ?>">
			<span><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $course['price'] ) ); ?></span>
		</div>
		<?php if ( $is_sale ) { ?>
			<div class="masterstudy-course-card__price-sale">
				<span><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $course['sale_price'] ) ); ?></span>
			</div>
		<?php } ?>
	<?php } elseif ( $members_only ) { ?>
		<div class="masterstudy-course-card__price-single masterstudy-course-card__price-single_subscription">
			<i class="stmlms-subscription"></i>
			<span><?php esc_html_e( 'Members Only', 'masterstudy-lms-learning-management-system' ); ?></span>
		</div>
	<?php } elseif ( $course_free_status['is_free'] ) { ?>
		<div class="masterstudy-course-card__price-single">
			<span><?php echo esc_html__( 'Free', 'masterstudy-lms-learning-management-system' ); ?></span>
		</div>
	<?php } ?>
</div>
