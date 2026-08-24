<?php
/**
 * @var object $course
 */

$faq = ( new \MasterStudy\Lms\Repositories\FaqRepository() )->find_for_course( $course->id );

if ( ! empty( $faq ) ) { ?>
	<div class="masterstudy-single-course-faq">
		<?php
		foreach ( $faq as $index => $item ) {
			if ( empty( $item['answer'] ) || empty( $item['question'] ) ) {
				continue;
			}
			?>
			<div class="masterstudy-single-course-faq__item" data-id="<?php echo esc_attr( $index ); ?>">
				<div class="masterstudy-single-course-faq__container">
					<div class="masterstudy-single-course-faq__container-wrapper">
						<div class="masterstudy-single-course-faq__question">
							<?php echo esc_html( $item['question'] ); ?>
						</div>
						<span class="masterstudy-single-course-faq__answer-toggler"></span>
					</div>
					<div class="masterstudy-single-course-faq__answer">
						<div class="masterstudy-single-course-faq__answer-wrapper">
							<?php echo nl2br( esc_html( $item['answer'] ) ); ?>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
	<?php
}
