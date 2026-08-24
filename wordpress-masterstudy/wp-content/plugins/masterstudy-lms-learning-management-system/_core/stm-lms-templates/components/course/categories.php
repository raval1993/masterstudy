<?php
/**
 * @var array $term_ids
 * @var boolean $only_one
 * @var boolean $inline
 */

use MasterStudy\Lms\Plugin\Taxonomy;

$only_one      = isset( $only_one ) ? $only_one : false;
$inline        = isset( $inline ) ? $inline : false;
$separator     = is_rtl() ? '⸲' : ',';
$terms_content = '';

if ( ! empty( $term_ids ) ) {
	?>
	<div class="masterstudy-single-course-categories <?php echo $only_one ? 'masterstudy-single-course-categories_only-one' : ''; ?>">
		<div class="masterstudy-single-course-categories__wrapper">
			<div class="masterstudy-single-course-categories__container">
				<?php if ( $only_one ) { ?>
					<span class="masterstudy-single-course-categories__icon"></span>
				<?php } ?>
				<div class="masterstudy-single-course-categories__list">
					<?php
					if ( $only_one ) {
						$course_term = get_term_by( 'id', $term_ids[0], Taxonomy::COURSE_CATEGORY );
						?>
						<span class="masterstudy-single-course-categories__title">
							<?php
							echo esc_html__( 'Category', 'masterstudy-lms-learning-management-system' );
							if ( $inline ) {
								echo ':';
							}
							?>
						</span>
						<div class="masterstudy-single-course-categories__item-wrapper">
							<a
								class="masterstudy-single-course-categories__item"
								href="<?php echo esc_url( STM_LMS_Course::courses_page_url() . '?terms[]=' . $term_ids[0] . '&category[]=' . $term_ids[0] ); ?>"
								target="_blank"
							>
								<?php echo esc_html( $course_term->name ); ?>
							</a>
							<?php
							if ( count( $term_ids ) > 1 ) {
								foreach ( $term_ids as $index => $term_id ) {
									if ( 0 === $index ) {
										continue;
									}

									$course_term    = get_term_by( 'id', $term_id, Taxonomy::COURSE_CATEGORY );
									$terms_content .= '<a class="masterstudy-single-course-categories__link" href="' . esc_url( STM_LMS_Course::courses_page_url() . '?terms[]=' . $term_id . '&category[]=' . $term_id ) . '" target="_blank">' . esc_html( $course_term->name ) . ( array_key_last( $term_ids ) !== $index ? $separator . ' ' : '' ) . '</a>';
								}
								STM_LMS_Templates::show_lms_template(
									'components/hint',
									array(
										'content'   => $terms_content,
										'side'      => 'left',
										'dark_mode' => false,
									)
								);
							}
							?>
						</div>
						<?php
					} else {
						foreach ( $term_ids as $index => $term_id ) {
							$course_term = get_term_by( 'id', $term_id, Taxonomy::COURSE_CATEGORY );
							?>
							<a
								class="masterstudy-single-course-categories__item"
								href="<?php echo esc_url( STM_LMS_Course::courses_page_url() . '?terms[]=' . $term_id . '&category[]=' . $term_id ); ?>"
								target="_blank"
							>
								<?php
								echo esc_html( $course_term->name );
								if ( array_key_last( $term_ids ) !== $index ) {
									echo esc_html( $separator );
								}
								?>
							</a>
							<?php
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
