<?php
/**
 * @var $field
 * @var $field_name
 * @var $section_name
 *
 */

$field_key = "data['{$section_name}']['fields']['{$field_name}']";

require STM_LMS_PATH . '/settings/payments/components_js/payments.php';
wp_enqueue_style( 'stm-payments-hidden-css', STM_LMS_URL . 'settings/payments/components_css/payments.css', null, get_bloginfo( 'version' ), 'all' );

?>

<stm-payments v-on:update-payments="<?php echo esc_attr( $field_key ); ?>['value'] = $event" v-bind:saved_payments="<?php echo esc_attr( $field_key ); ?>['value']">
</stm-payments>
