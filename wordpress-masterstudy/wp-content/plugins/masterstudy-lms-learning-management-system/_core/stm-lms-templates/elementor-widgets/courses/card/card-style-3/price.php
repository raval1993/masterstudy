<?php
$course_free_status = masterstudy_lms_course_free_status( $course['id'], $course['price'] );
$is_sale            = ! empty( $course['sale_price'] ) && $course['is_sale_active'];
$is_affiliate       = 'affiliate' === ( $course['pricing_mode'] ?? '' ) && '' !== (string) ( $course['affiliate_course_price'] ?? '' );
?>

<div class="ms_lms_courses_card_item_info_price">
	<a href="<?php echo esc_url( $course['url'] ); ?>" class="ms_lms_courses_card_item_info_price_preview">
		<span><?php esc_html_e( 'Preview this course', 'masterstudy-lms-learning-management-system' ); ?></span>
		<?php if ( 'on' === $course['is_trial'] ) : ?>
		<small><?php esc_html_e( 'Free Lesson(s) Offer', 'masterstudy-lms-learning-management-system' ); ?></small>
		<?php endif; ?>
	</a>
	<?php if ( $is_affiliate ) { ?>
		<div class="ms_lms_courses_card_item_info_price_single">
			<span><?php echo esc_html( STM_LMS_Helpers::display_price( $course['affiliate_course_price'] ) ); ?></span>
		</div>
	<?php } elseif ( $course['single_sale'] && ! $course_free_status['zero_price'] ) { ?>
		<div class="ms_lms_courses_card_item_info_price_single <?php echo $is_sale ? 'sale' : ''; ?>">
			<span><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $course['price'] ) ); ?></span>
		</div>
		<?php if ( $is_sale ) { ?>
			<div class="ms_lms_courses_card_item_info_price_sale">
				<span><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $course['sale_price'] ) ); ?></span>
			</div>
		<?php } ?>
	<?php } elseif ( ! $course['single_sale'] && ! $course['not_in_membership'] ) { ?>
		<div class="ms_lms_courses_card_item_info_price_single subscription">
			<i class="stmlms-subscription"></i>
			<span><?php esc_html_e( 'Members Only', 'masterstudy-lms-learning-management-system' ); ?></span>
		</div>
	<?php } elseif ( $course_free_status['is_free'] ) { ?>
		<div class="ms_lms_courses_card_item_info_price_single">
			<span><?php echo esc_html__( 'Free', 'masterstudy-lms-learning-management-system' ); ?></span>
		</div>
	<?php } ?>
</div>
