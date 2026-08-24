<?php

/**
 * @var $model
 * @var $value
 * @var $label
 */

$preview_url = 'https://masterstudy.stylemixthemes.com/lms-plugin/courses-page/basics-of-masterstudy/';

if ( 'default' !== $value ) {
	$preview_url .= "?course_style=$value";
}
?>

<label class="masterstudy-wizard-course-radio-image" :class="{ 'masterstudy-wizard-course-radio-image_active': <?php echo esc_html( $model ); ?> === '<?php echo esc_html( $value ); ?>' }">
	<div class="masterstudy-wizard-course-radio-image__wrapper">
		<div class="masterstudy-wizard-course-radio-image__container">
			<img src="<?php echo esc_url( STM_LMS_URL . "assets/img/course/{$value}.png" ); ?>" />
			<a href="<?php echo esc_url( $preview_url ); ?>" class="masterstudy-wizard-course-radio-image__preview" target="_blank">
				<?php echo esc_html__( 'Demo preview', 'masterstudy-lms-learning-management-system' ); ?>
			</a>
		</div>
		<div class="masterstudy-wizard-course-radio-image__label">
			<input type="radio" name="course_style" v-model="<?php echo esc_html( $model ); ?>" value="<?php echo esc_html( $value ); ?>"/>
			<span class="masterstudy-wizard-course-radio-image__alt">
				<?php echo esc_html( $label ); ?>
			</span>
		</div>
	</div>
</label>
