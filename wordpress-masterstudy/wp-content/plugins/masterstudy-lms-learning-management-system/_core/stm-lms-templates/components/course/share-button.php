<?php
/**
 * @var object $course
 * @var string $style
 */

$course_url = STM_LMS_Course::courses_page_url() . $course->slug;
$style      = isset( $style ) ? $style : '';

wp_localize_script(
	'masterstudy-single-course-components',
	'share_data',
	array(
		'copy_text' => __( 'Copied to clipboard!', 'masterstudy-lms-learning-management-system' ),
	)
);

if ( 'row' === $style ) {
	?>
	<div class="masterstudy-single-course-share-button masterstudy-single-course-share-button_row">
		<span class="masterstudy-single-course-share-button__title">
			<?php echo esc_html__( 'Share this course', 'masterstudy-lms-learning-management-system' ); ?>
		</span>
		<div class="masterstudy-single-course-share-button__content">
			<div class="masterstudy-single-course-share-button__link-wrapper">
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( $course_url ); ?>" target="_blank" class="masterstudy-single-course-share-button__link masterstudy-single-course-share-button__link_facebook"></a>
			</div>
			<div class="masterstudy-single-course-share-button__link-wrapper">
				<a href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( $course_url ); ?>&text=<?php echo rawurlencode( $course->title ); ?>" target="_blank" class="masterstudy-single-course-share-button__link masterstudy-single-course-share-button__link_twitter"></a>
			</div>
			<div class="masterstudy-single-course-share-button__link-wrapper">
			<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo rawurlencode( $course_url ); ?>&title=<?php echo rawurlencode( $course->title ); ?>&summary=<?php echo rawurlencode( $course->excerpt ); ?>&source=<?php echo rawurlencode( $course_url ); ?>" target="_blank" class="masterstudy-single-course-share-button__link masterstudy-single-course-share-button__link_linkedin"></a>
			</div>
			<div class="masterstudy-single-course-share-button__link-wrapper">
				<a href="https://t.me/share/url?url=<?php echo rawurlencode( $course_url ); ?>&amp;text=<?php echo rawurlencode( $course->title ); ?>" target="_blank" class="masterstudy-single-course-share-button__link masterstudy-single-course-share-button__link_telegram"></a>
			</div>
			<div class="masterstudy-single-course-share-button__link-wrapper">
				<a href="#" data-url="<?php echo esc_url( $course_url ); ?>" class="masterstudy-single-course-share-button__link masterstudy-single-course-share-button__link_copy"></a>
			</div>
		</div>
	</div>
	<?php
} else {
	?>
	<div class="masterstudy-single-course-share-button">
		<span class="masterstudy-single-course-share-button__title">
			<?php echo esc_html__( 'Share', 'masterstudy-lms-learning-management-system' ); ?>
		</span>
	</div>
	<div class="masterstudy-single-course-share-button-modal" style="display:none">
		<div class="masterstudy-single-course-share-button-modal__wrapper">
			<div class="masterstudy-single-course-share-button-modal__container">
				<div class="masterstudy-single-course-share-button-modal__header">
					<span class="masterstudy-single-course-share-button-modal__header-title">
						<?php echo esc_html__( 'Share', 'masterstudy-lms-learning-management-system' ); ?>
						<?php echo esc_html( '"' . $course->title . '"' ); ?>
					</span>
					<div class="masterstudy-single-course-share-button-modal__close"></div>
				</div>
				<div class="masterstudy-single-course-share-button-modal__content">
					<div class="masterstudy-single-course-share-button-modal__link-wrapper">
						<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( $course_url ); ?>" target="_blank" class="masterstudy-single-course-share-button-modal__link masterstudy-single-course-share-button-modal__link_facebook">
							<?php echo esc_html__( 'Facebook', 'masterstudy-lms-learning-management-system' ); ?>
						</a>
					</div>
					<div class="masterstudy-single-course-share-button-modal__link-wrapper">
						<a href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( $course_url ); ?>&text=<?php echo rawurlencode( $course->title ); ?>" target="_blank" class="masterstudy-single-course-share-button-modal__link masterstudy-single-course-share-button-modal__link_twitter">
							<?php echo esc_html__( 'Twitter', 'masterstudy-lms-learning-management-system' ); ?>
						</a>
					</div>
					<div class="masterstudy-single-course-share-button-modal__link-wrapper">
					<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo rawurlencode( $course_url ); ?>&title=<?php echo rawurlencode( $course->title ); ?>&summary=<?php echo rawurlencode( $course->excerpt ); ?>&source=<?php echo rawurlencode( $course_url ); ?>" target="_blank" class="masterstudy-single-course-share-button-modal__link masterstudy-single-course-share-button-modal__link_linkedin">
							<?php echo esc_html__( 'Linkedin', 'masterstudy-lms-learning-management-system' ); ?>
						</a>
					</div>
					<div class="masterstudy-single-course-share-button-modal__link-wrapper">
						<a href="https://t.me/share/url?url=<?php echo rawurlencode( $course_url ); ?>&amp;text=<?php echo rawurlencode( $course->title ); ?>" target="_blank" class="masterstudy-single-course-share-button-modal__link masterstudy-single-course-share-button-modal__link_telegram">
							<?php echo esc_html__( 'Telegram', 'masterstudy-lms-learning-management-system' ); ?>
						</a>
					</div>
					<div class="masterstudy-single-course-share-button-modal__link-wrapper">
						<a href="https://api.whatsapp.com/send?text=<?php echo rawurlencode( $course->title . "\n" . $course_url ); ?>" target="_blank" class="masterstudy-single-course-share-button-modal__link masterstudy-single-course-share-button-modal__link_whatsapp">
							<?php echo esc_html__( 'WhatsApp', 'masterstudy-lms-learning-management-system' ); ?>
						</a>
					</div>
					<div class="masterstudy-single-course-share-button-modal__link-wrapper">
						<a href="#" data-url="<?php echo esc_url( $course_url ); ?>" class="masterstudy-single-course-share-button-modal__link masterstudy-single-course-share-button-modal__link_copy">
							<?php echo esc_html__( 'Copy link', 'masterstudy-lms-learning-management-system' ); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
