<?php
/**
 * @var $field
 * @var $field_name
 * @var $section_name
 * @var $field_label
 */

$field_key = "data['{$section_name}']['fields']['{$field_name}']";

wp_enqueue_style( 'masterstudy-search-select', STM_LMS_URL . '/assets/css/components/search-select.css', array(), MS_LMS_VERSION );
wp_enqueue_script( 'masterstudy-search-select', STM_LMS_URL . 'assets/js/components/search-select.js', array( 'vue.js', 'wpcfto_metaboxes.js' ), MS_LMS_VERSION, true );
wp_localize_script(
	'masterstudy-search-select',
	'searchSelect',
	array(
		'placeholder' => esc_html__( 'Search...', 'masterstudy-lms-learning-management-system' ),
	)
);
?>
<div class="wpcfto_generic_field search-select">
	<div class="wpcfto-field-aside">
		<label class="wpcfto-field-aside__label" v-html="<?php echo esc_attr( $field_key ); ?>['label']"></label>
		<div class="wpcfto-field-description wpcfto-field-description__before description" v-html="<?php echo esc_attr( $field_key ); ?>['description']"></div>
	</div>
	<search-select
		v-on:update-search-select="<?php echo esc_attr( $field_key ); ?>['value'] = $event"
		v-bind:saved_search_select="<?php echo esc_attr( $field_key ); ?>['value']"
		v-bind:field_label="<?php echo esc_attr( $field_key ); ?>['label']"
		:options="<?php echo esc_attr( $field ); ?>['options']"
	>
	</search-select>
</div>
