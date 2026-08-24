<?php
/**
 * Block render callback.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content Block default content.
 * @param WP_Block $block The block instance.
 */

$block_wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'lms-course-price-accent',
	)
);
$teacher_id               = '';
$course_per_page          = '';
$categories               = array();
$sorting_option           = '';
$show_category            = true;
$show_price               = true;
$show_rating              = true;
$show_divider             = true;
$show_popup_instructor    = true;
$show_popup_price         = true;
$show_popup_wishlist      = true;
$select_dataslot1         = 'lectures';
$select_dataslot2         = 'duration';
$select_popup_dataslot1   = 'level';
$select_popup_dataslot2   = 'lectures';
$select_popup_dataslot3   = 'duration';

if ( isset( $block->context['masterstudy/teacherId'] ) ) {
	$teacher_id = $block->context['masterstudy/teacherId'];
}

if ( isset( $block->context['masterstudy/coursesPerPage'] ) ) {
	$course_per_page = $block->context['masterstudy/coursesPerPage'];
}

if ( isset( $block->context['masterstudy/coursesCategory'] ) ) {
	$categories = $block->context['masterstudy/coursesCategory'];
}

if ( isset( $block->context['masterstudy/coursesOrderBy'] ) ) {
	$sorting_option = $block->context['masterstudy/coursesOrderBy'];
}

if ( isset( $block->context['masterstudy/showPopup'] ) ) {
	$show_popup = $block->context['masterstudy/showPopup'];
}

if ( isset( $block->context['masterstudy/showCategory'] ) ) {
	$show_category = $block->context['masterstudy/showCategory'];
}

if ( isset( $block->context['masterstudy/showPrice'] ) ) {
	$show_price = $block->context['masterstudy/showPrice'];
}
if ( isset( $block->context['masterstudy/showRating'] ) ) {
	$show_rating = $block->context['masterstudy/showRating'];
}

if ( isset( $block->context['masterstudy/showDivider'] ) ) {
	$show_divider = $block->context['masterstudy/showDivider'];
}

if ( isset( $block->context['masterstudy/showPopupInstructor'] ) ) {
	$show_popup_instructor = $block->context['masterstudy/showPopupInstructor'];
}

if ( isset( $block->context['masterstudy/showPopupPrice'] ) ) {
	$show_popup_price = $block->context['masterstudy/showPopupPrice'];
}

if ( isset( $block->context['masterstudy/showPopupWishlist'] ) ) {
	$show_popup_wishlist = $block->context['masterstudy/showPopupWishlist'];
}

if ( isset( $block->context['masterstudy/selectDataslot1'] ) ) {
	$select_dataslot1 = $block->context['masterstudy/selectDataslot1'];
}

if ( isset( $block->context['masterstudy/selectDataslot2'] ) ) {
	$select_dataslot2 = $block->context['masterstudy/selectDataslot2'];
}

if ( isset( $block->context['masterstudy/selectPopupDataslot1'] ) ) {
	$select_popup_dataslot1 = $block->context['masterstudy/selectPopupDataslot1'];
}

if ( isset( $block->context['masterstudy/selectPopupDataslot2'] ) ) {
	$select_popup_dataslot2 = $block->context['masterstudy/selectPopupDataslot2'];
}

if ( isset( $block->context['masterstudy/selectPopupDataslot3'] ) ) {
	$select_popup_dataslot3 = $block->context['masterstudy/selectPopupDataslot3'];
}
?>
<div <?php echo wp_kses_data( $block_wrapper_attributes ); ?>>
	<div class="lms-course-preloader">
		<div class="lms-course-preloader-item"></div>
	</div>
	<div class="lms-course__list lms-course-price-accent__list"></div>
</div>

<input type="hidden" class="lms-course-list-item-data" data-teacher="<?php echo esc_attr( $teacher_id ); ?>" data-sort="<?php echo esc_attr( $sorting_option ); ?>" data-per-page="<?php echo esc_attr( $course_per_page ); ?>" data-categories="<?php echo esc_attr( implode( ',', $categories ) ); ?>">
<input type="hidden" class="lms-course-list-item-blocks" data-show-popup="<?php echo esc_attr( $show_popup ); ?>" data-show-category="<?php echo esc_attr( $show_category ); ?>" data-show-price="<?php echo esc_attr( $show_price ); ?>" data-show-rating="<?php echo esc_attr( $show_rating ); ?>" data-show-divider="<?php echo esc_attr( $show_divider ); ?>" data-show-popup-instructor="<?php echo esc_attr( $show_popup_instructor ); ?>" data-show-popup-price="<?php echo esc_attr( $show_popup_price ); ?>" data-show-popup-wishlist="<?php echo esc_attr( $show_popup_wishlist ); ?>">
<input type="hidden" class="lms-course-list-item-slots" data-dataslot1="<?php echo esc_attr( $select_dataslot1 ); ?>" data-dataslot2="<?php echo esc_attr( $select_dataslot2 ); ?>" data-popup-dataslot1="<?php echo esc_attr( $select_popup_dataslot1 ); ?>" data-popup-dataslot2="<?php echo esc_attr( $select_popup_dataslot2 ); ?>" data-popup-dataslot3="<?php echo esc_attr( $select_popup_dataslot3 ); ?>">
<input type="hidden" class="lms-course-list-item-countdown" data-days="" data-hours="" data-minutes="" data-seconds="">
