<?php stm_lms_register_style( 'admin/shortcodes' ); ?>
<div class="stm_lms_shortcode_list">
	<div>
		<label><?php echo esc_html__( 'Search box', 'masterstudy-lms-learning-management-system' ); ?></label>
		<input type="text" disabled value='[stm_courses_searchbox]'>
		<ul class="params">
			<li>
				<?php echo esc_html__( 'style', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'style_1', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'style_2', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
		</ul>
	</div>
	<div>
		<label><?php echo esc_html__( 'Courses Carousel', 'masterstudy-lms-learning-management-system' ); ?></label>
		<input type="text" value='[stm_lms_courses_carousel]' disabled/>
		<ul class="params">
			<li><?php echo esc_html__( 'title (enter the module title)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'title_color (change the color of the title, example #fafafa)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li>
				<?php echo esc_html__( 'query (sorting options — Sort by, set by default as "none")', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'none', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'popular', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'free', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'rating', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li>
				<?php echo esc_html__( 'prev_next (enable or disable Previous/Next buttons, set by default as “enable”)', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'enable', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'disable', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li>
				<?php echo esc_html__( 'remove_border (enable or disable border removing, set by default "disable")', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'enable', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'disable', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li>
				<?php echo esc_html__( 'show_categories (enable/disable display of categories, set by default as "disable")', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'enable', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'disable', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li>
				<?php echo esc_html__( 'pagination (disable or enable paginations, set by default as "disable")', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'enable', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'disable', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li><?php echo esc_html__( 'per_row (specify the number of courses per row, by default — 6)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'taxonomy (term ID of stm_lms_course_taxonomy taxonomy, only if show_categories is "enable". example "233,255,321")', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'image_size (image size, (Ex.: thumbnail))', 'masterstudy-lms-learning-management-system' ); ?></li>
		</ul>
	</div>
	<div>
		<label><?php echo esc_html__( 'Courses Categories', 'masterstudy-lms-learning-management-system' ); ?></label>
		<input type="text" value='[stm_lms_courses_categories]' disabled />
		<ul class="params">
			<li>
				<?php echo esc_html__( 'style', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'style_1', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'style_2', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'style_3', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'style_4', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li><?php echo esc_html__( 'taxonomy (term ID of stm_lms_course_taxonomy taxonomy. example "233,255,321")', 'masterstudy-lms-learning-management-system' ); ?></li>
		</ul>
	</div>
	<div>
		<label><?php echo esc_html__( 'Courses Grid', 'masterstudy-lms-learning-management-system' ); ?></label>
		<input type="text" value='[stm_lms_courses_grid]' disabled />
		<ul class="params">
			<li>
				<?php echo esc_html__( 'hide_top_bar (hide/show the top bar, by default — "showing")', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'hidden', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'showing', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li><?php echo esc_html__( 'title (module title)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li>
				<?php echo esc_html__( 'hide_load_more (hide/show the button Load More, by default — "showing")', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'hidden', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'showing', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li>
				<?php echo esc_html__( 'hide_sort (hide/show sorting option, by default — "showing")', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'hidden', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'showing', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li><?php echo esc_html__( 'per_row (the number of Courses Per Row, by default 6)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'image_size (image size, (Ex.: thumbnail))', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'posts_per_page (number of courses to show on the page)', 'masterstudy-lms-learning-management-system' ); ?></li>
		</ul>
	</div>
	<div>
		<label><?php echo esc_html__( 'Featured Teacher', 'masterstudy-lms-learning-management-system' ); ?></label>
		<input type="text" value='[stm_lms_featured_teacher]' disabled />
		<ul class="params">
			<li><?php echo esc_html__( 'instructor (Instructor ID)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'position (Instructor Position)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'bio (Instructor Bio)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'image (enter image ID)', 'masterstudy-lms-learning-management-system' ); ?></li>
		</ul>
	</div>
	<div>
		<label><?php echo esc_html__( 'Instructors Carousel', 'masterstudy-lms-learning-management-system' ); ?></label>
		<input type="text" value='[stm_lms_instructors_carousel]' disabled />
		<ul class="params">
			<li><?php echo esc_html__( 'title (module title)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'title_color (changes the color of the title)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'per_row (number of Instructors per row, by default 6)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'per_row_md (number of Instructors per row on Notebook, by default 4)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'per_row_sm (number of Instructors per row on Tablet, by default 2)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'per_row_xs (number of Instructors per row on Mobile, by default 1)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li>
				<?php echo esc_html__( 'style (change the display style, by default "style_1")', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'style_1', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'style_2', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li>
				<?php echo esc_html__( 'sort (the option Sort By)', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'default', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'rating', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li>
				<?php echo esc_html__( 'prev_next (Enable or Disable Previous and Next Buttons, by default "enable")', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'default', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'rating', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li>
				<?php echo esc_html__( 'pagination (Enable or Disable Previous and Next Buttons, by default "disable")', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'default', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'rating', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
		</ul>
	</div>
	<div>
		<label><?php echo esc_html__( 'Recent Courses', 'masterstudy-lms-learning-management-system' ); ?></label>
		<input type="text" value='[stm_lms_recent_courses]' disabled />
		<ul class="params">
			<li><?php echo esc_html__( 'posts_per_page (Number of courses to show on the page)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'image_size (Image size (Ex.: thumbnail))', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'per_row (the number of courses per row)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li>
				<?php echo esc_html__( 'style (Default "style_1")', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'style_1', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'style_2', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
		</ul>
	</div>
	<div>
		<label><?php echo esc_html__( 'Single Course Carousel', 'masterstudy-lms-learning-management-system' ); ?></label>
		<input type="text" value='[stm_lms_single_course_carousel]' disabled/>
		<ul class="params">
			<li>
				<?php echo esc_html__( 'query (Sorting options “Sort by”, by default "none")', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'none', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'popular', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'free', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'rating', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li>
				<?php echo esc_html__( 'prev_next (Enable or Disable Previous/Next Buttons, by default "enable")', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'enable', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'disable', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li>
				<?php echo esc_html__( 'pagination (Enable or Disable pagination, by default "disable")', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li><?php echo esc_html__( 'enable', 'masterstudy-lms-learning-management-system' ); ?></li>
					<li><?php echo esc_html__( 'disable', 'masterstudy-lms-learning-management-system' ); ?></li>
				</ul>
			</li>
			<li>
				<?php echo esc_html__( 'taxonomy (term ID of stm_lms_course_taxonomy taxonomy, example "233,255,321")', 'masterstudy-lms-learning-management-system' ); ?>
			</li>
		</ul>
	</div>
	<div>
		<label><?php echo esc_html__( 'Certificate checker (pro version)', 'masterstudy-lms-learning-management-system' ); ?></label>
		<input type="text" value='[stm_lms_certificate_checker]' disabled />
		<ul class="params">
			<li>
				<?php echo esc_html__( 'title (module title)', 'masterstudy-lms-learning-management-system' ); ?>
			</li>
		</ul>
	</div>
	<div>
		<label><?php echo esc_html__( 'Course Bundles (pro version)', 'masterstudy-lms-learning-management-system' ); ?></label>
		<input type="text" value='[stm_lms_course_bundles]' disabled />
		<ul class="params">
			<li><?php echo esc_html__( 'title (module title)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li>
				<?php echo esc_html__( 'columns (number of columns, by default 3)', 'masterstudy-lms-learning-management-system' ); ?>
				<ul>
					<li>2</li>
					<li>3</li>
				</ul>
			</li>
			<li><?php echo esc_html__( 'posts_per_page (number of posts per page)', 'masterstudy-lms-learning-management-system' ); ?></li>
		</ul>
	</div>
	<div>
		<label><?php echo esc_html__( 'Google Classrooms grid view (pro version)', 'masterstudy-lms-learning-management-system' ); ?></label>
		<input type="text" value='[stm_lms_google_classroom]' disabled />
		<ul class="params">
			<li><?php echo esc_html__( 'title (module title)', 'masterstudy-lms-learning-management-system' ); ?></li>
			<li><?php echo esc_html__( 'number (number of posts on the page)', 'masterstudy-lms-learning-management-system' ); ?></li>
		</ul>
	</div>
	<div>
		<label><?php echo esc_html__( 'Memberships Plans (pro version)', 'masterstudy-lms-learning-management-system' ); ?></label>
		<input type="text" value='[masterstudy_membership_pricing]' disabled />
		<ul class="params">
			<li><?php echo esc_html__( 'This shortcode is available only when the Membership feature is enabled', 'masterstudy-lms-learning-management-system' ); ?></li>
		</ul>
	</div>
	<div class="stm_lms_guide">
		<label><?php echo esc_html__( 'Online Testing (pro version)', 'masterstudy-lms-learning-management-system' ); ?></label>
		<input type="text" value='[stm_lms_quiz_online id=QUIZ_ID_HERE]' disabled />
		<ul class="params">
			<li><?php esc_html_e( 'This shortcode is available only when the Online Testing addon is enabled' ); ?></li>
			<li><?php esc_html_e( 'Create quiz and insert shortcode with quiz id on a page' ); ?></li>
		</ul>
	</div>
</div>
