<?php

$values = ( ! empty( $_GET['category'] ) ) ? $_GET['category'] : array( $category ?? '' );

$terms_args = array(
	'taxonomy' => 'stm_lms_course_taxonomy',
	'orderby'  => 'count',
	'order'    => 'DESC',
	'parent'   => false,
);

if ( false !== STM_LMS_Options::get_option( 'course_categories_sort_alpha', false ) ) {
	$terms_args = array_merge(
		$terms_args,
		array(
			'orderby' => 'name',
			'order'   => 'ASC',
			'parent'  => false,
		)
	);
}

$terms = get_terms( $terms_args );

$parents = array();

if ( ! empty( $terms ) ) : ?>

	<div class="stm_lms_courses__filter stm_lms_courses__category">

		<div class="stm_lms_courses__filter_heading">
			<h3><?php esc_html_e( 'Category', 'masterstudy-lms-learning-management-system' ); ?></h3>
			<div class="toggler"></div>
		</div>

		<div class="stm_lms_courses__filter_content" style="display: none;">

			<?php
			foreach ( $terms as $term ) :
				$parents[] = $term->term_id;
				?>

				<div class="stm_lms_courses__filter_category">
					<label class="stm_lms_styled_checkbox">
					<span class="stm_lms_styled_checkbox__inner">
						<input type="checkbox"
							<?php
							if ( in_array( intval( $term->term_id ), $values, true ) ) {
								echo 'checked="checked"';}
							?>
							value="<?php echo intval( $term->term_id ); ?>"
							name="category[]"/>
						<span><i class="stmlms-check-3"></i> </span>
					</span>
						<span><?php echo esc_html( $term->name ); ?></span>
					</label>
				</div>

			<?php endforeach; ?>

		</div>

	</div>

	<?php
	set_transient( 'stm_lms_parent_categories', $parents );
endif;
