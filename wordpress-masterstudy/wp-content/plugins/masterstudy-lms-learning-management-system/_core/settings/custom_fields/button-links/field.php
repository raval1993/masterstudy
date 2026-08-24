<?php
/**
 * @var $field
 * @var $field_name
 * @var $section_name
 * @var $field_value
 * @var $field_label
 */

$field_key = "data['{$section_name}']['fields']['{$field_name}']";

wp_enqueue_style( 'masterstudy-button-links-css', STM_LMS_URL . '/assets/css/components/button-links.css', array(), MS_LMS_VERSION );
wp_enqueue_script( 'masterstudy-button-links', STM_LMS_URL . 'assets/js/components/button-links.js', array( 'vue.js', 'wpcfto_metaboxes.js' ), MS_LMS_VERSION, true );

?>
<div class="wpcfto_generic_field" field_data="[object Object]">
	<div class="masterstudy-meet-link-buttons">
		<div class="wpcfto-field-aside">
			<label class="wpcfto-field-aside__label" v-html="<?php echo esc_attr( $field_key ); ?>['label']"></label>
			<div class="wpcfto-field-description wpcfto-field-description__before description" v-html="<?php echo esc_attr( $field_key ); ?>['description']"></div>
		</div>
		<a :href="<?php echo esc_attr( $field_key ); ?>['button_url']"
			:target="<?php echo esc_attr( $field_key ); ?>['blank'] ? '_blank' : ''"
			class="masterstudy-button-links"
			v-html="<?php echo esc_attr( $field_key ); ?>['button_text']">
		</a>
	</div>
</div>
