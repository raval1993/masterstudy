<?php
$course_free_status = masterstudy_lms_course_free_status( $course['id'], $course['price'] );
$is_sale            = ! empty( $course['sale_price'] ) && $course['is_sale_active'];
$is_affiliate       = 'affiliate' === ( $course['pricing_mode'] ?? '' ) && '' !== (string) ( $course['affiliate_course_price'] ?? '' );

if ( $is_affiliate ) { ?>
	<div class="ms_lms_courses_card_item_popup_price">
		<div class="ms_lms_courses_card_item_popup_price_single">
			<span><?php echo esc_html( STM_LMS_Helpers::display_price( $course['affiliate_course_price'] ) ); ?></span>
		</div>
	</div>
<?php } elseif ( $course['single_sale'] && ! $course_free_status['zero_price'] ) { ?>
	<div class="ms_lms_courses_card_item_popup_price">
		<div class="ms_lms_courses_card_item_popup_price_single <?php echo $is_sale ? 'sale' : ''; ?>">
			<span><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $course['price'] ) ); ?></span>
		</div>
		<?php if ( $is_sale ) { ?>
			<div class="ms_lms_courses_card_item_popup_price_sale">
				<span><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $course['sale_price'] ) ); ?></span>
			</div>
		<?php } ?>
	</div>
<?php } elseif ( ! $course['single_sale'] && ! $course['not_in_membership'] ) { ?>
	<div class="ms_lms_courses_card_item_popup_price_single subscription">
		<i class="stmlms-subscription"></i>
		<span><?php esc_html_e( 'Members Only', 'masterstudy-lms-learning-management-system' ); ?></span>
	</div>
	<?php
} elseif ( $course_free_status['is_free'] ) {
	?>
	<div class="ms_lms_courses_card_item_popup_price">
		<div class="ms_lms_courses_card_item_popup_price_single">
			<span><?php echo esc_html__( 'Free', 'masterstudy-lms-learning-management-system' ); ?></span>
		</div>
	</div>
	<?php
}
