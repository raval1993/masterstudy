<?php
$course_free_status = masterstudy_lms_course_free_status( $course['id'], $course['price'] );
$has_price_info     = $course['single_sale'] && ! $course_free_status['zero_price'];
$members_only       = ! $course['single_sale'] && ! $course['not_in_membership'];
$has_free_info      = $course_free_status['is_free'];
$is_sale            = ! empty( $course['sale_price'] ) && $course['is_sale_active'];
$is_affiliate       = 'affiliate' === ( $course['pricing_mode'] ?? '' ) && '' !== (string) ( $course['affiliate_course_price'] ?? '' );

$preview_class = ( ! $is_affiliate && ! $has_price_info && ! $members_only && ! $has_free_info )
	? 'ms_lms_courses_card_item_info_price_preview_open'
	: '';
?>

<div class="ms_lms_courses_card_item_info_price">
	<a href="<?php echo esc_url( $course['url'] ); ?>" class="ms_lms_courses_card_item_info_price_preview <?php echo esc_attr( $preview_class ); ?>">
		<span><?php esc_html_e( 'Preview this course', 'masterstudy-lms-learning-management-system' ); ?></span>
		<?php if ( 'on' === $course['is_trial'] ) : ?>
		<small><?php esc_html_e( 'Free Lesson(s) Offer', 'masterstudy-lms-learning-management-system' ); ?></small>
		<?php endif; ?>
	</a>
	<?php if ( $is_affiliate ) { ?>
		<div class="ms_lms_courses_card_item_info_price_single">
			<span><?php echo esc_html( STM_LMS_Helpers::display_price( $course['affiliate_course_price'] ) ); ?></span>
		</div>
	<?php } elseif ( $has_price_info ) { ?>
		<div class="ms_lms_courses_card_item_info_price_single <?php echo $is_sale ? 'sale' : ''; ?>">
			<span><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $course['price'] ) ); ?></span>
		</div>
		<?php if ( $is_sale ) { ?>
			<div class="ms_lms_courses_card_item_info_price_sale">
				<span><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $course['sale_price'] ) ); ?></span>
			</div>
		<?php } ?>
	<?php } elseif ( $members_only ) { ?>
		<div class="ms_lms_courses_card_item_info_price_single subscription">
			<i class="stmlms-subscription"></i>
			<span><?php esc_html_e( 'Members Only', 'masterstudy-lms-learning-management-system' ); ?></span>
		</div>
	<?php } elseif ( $has_free_info ) { ?>
		<div class="ms_lms_courses_card_item_info_price_single">
			<span><?php echo esc_html__( 'Free', 'masterstudy-lms-learning-management-system' ); ?></span>
		</div>
	<?php } ?>
</div>
