<?php

$values              = ( ! empty( $_GET['status'] ) ) ? $_GET['status'] : array();
$is_featured_enabled = STM_LMS_Options::get_option( 'enable_featured_courses', true );

$statuses = STM_LMS_Helpers::get_course_statuses();

if ( ! empty( $statuses ) ) : ?>

	<div class="stm_lms_courses__filter stm_lms_courses__search">

		<div class="stm_lms_courses__filter_heading">
			<h3><?php esc_html_e( 'Status', 'masterstudy-lms-learning-management-system' ); ?></h3>
			<div class="toggler"></div>
		</div>

		<div class="stm_lms_courses__filter_content" style="display: none;">

			<?php foreach ( $statuses as $_status ) : ?>
				<div class="stm_lms_courses__filter_category">
					<label class="stm_lms_styled_checkbox">
					<span class="stm_lms_styled_checkbox__inner">
						<input type="checkbox"
								<?php
								if ( in_array( sanitize_text_field( $_status['id'] ), $values, true ) ) {
									echo 'checked="checked"';}
								?>
								value="<?php echo esc_attr( sanitize_text_field( $_status['id'] ) ); ?>"
								name="status[]"/>
						<span><i class="stmlms-check-3"></i> </span>
					</span>
						<span><?php echo esc_html( $_status['label'] ); ?></span>
					</label>
				</div>

			<?php endforeach; ?>

		</div>

	</div>

	<?php
endif;
