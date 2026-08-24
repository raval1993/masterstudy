<?php
/**
 * @var $price
 * @var $sale_price
 */

$course_id          = get_the_ID();
$single_sale        = get_post_meta( $course_id, 'single_sale', true );
$pricing_mode       = get_post_meta( $course_id, 'pricing_mode', true );
$affiliate_price    = get_post_meta( $course_id, 'affiliate_course_price', true );
$is_udemy_course    = get_post_meta( $course_id, 'udemy_course_id', true );
$not_in_membership  = get_post_meta( $course_id, 'not_in_membership', true );
$members_only       = ! $single_sale && STM_LMS_Subscriptions::subscription_enabled() && ! $not_in_membership;
$course_free_status = masterstudy_lms_course_free_status( $course_id, $price );

if ( 'affiliate' === $pricing_mode && '' !== (string) $affiliate_price ) { ?>
	<div class="stm_lms_courses__single--price heading_font">
		<strong><?php echo esc_html( STM_LMS_Helpers::display_price( $affiliate_price ) ); ?></strong>
	</div>
<?php } elseif ( $members_only ) { ?>
	<div class="stm_lms_courses__single--price heading_font stm_lms_courses__single--price-membership">
		<strong><?php echo esc_html__( 'Members only', 'masterstudy-lms-learning-management-system' ); ?></strong>
	</div>
<?php } elseif ( $is_udemy_course && ! $course_free_status['zero_price'] ) { ?>
	<div class="stm_lms_courses__single--price heading_font">
		<strong><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $price ) ); ?></strong>
	</div>
	<?php
} elseif ( $single_sale && ! $course_free_status['zero_price'] ) {
	?>
	<div class="stm_lms_courses__single--price heading_font">
		<?php if ( ! empty( $sale_price ) ) { ?>
			<span><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $price ) ); ?></span>
			<strong><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $sale_price ) ); ?></strong>
			<?php
		} else {
			?>
			<strong><?php echo esc_html( STM_LMS_Helpers::display_price_with_taxes( $price ) ); ?></strong>
			<?php
		}
		?>
	</div>
	<?php
} elseif ( $course_free_status['is_free'] ) {
	?>
	<div class="stm_lms_courses__single--price heading_font">
		<strong><?php echo esc_html__( 'Free', 'masterstudy-lms-learning-management-system' ); ?></strong>
	</div>
	<?php
}
