<?php
/**
 * @var $field
 *
 */

wp_enqueue_style( 'masterstudy-switcher' );
wp_enqueue_style( 'masterstudy-taxes', STM_LMS_URL . 'assets/css/components/taxes.css', array(), MS_LMS_VERSION );
wp_enqueue_script( 'masterstudy-taxes', STM_LMS_URL . 'assets/js/components/taxes.js', array( 'vue.js', 'vue2-color.js', 'wpcfto_metaboxes.js' ), MS_LMS_VERSION, true );
wp_localize_script(
	'masterstudy-taxes',
	'masterstudyTaxes',
	array(
		'countries'         => masterstudy_lms_get_countries( false ),
		'regions'           => array( 'US' => masterstudy_lms_get_us_states( false ) ),
		'taxRegions'        => esc_html__( 'Tax Rate by Country', 'masterstudy-lms-learning-management-system' ),
		'country'           => esc_html__( 'Country', 'masterstudy-lms-learning-management-system' ),
		'addNewCountry'     => esc_html__( 'Add New Country', 'masterstudy-lms-learning-management-system' ),
		'taxRate'           => esc_html__( 'Tax Rate', 'masterstudy-lms-learning-management-system' ),
		'allRegions'        => esc_html__( 'All regions', 'masterstudy-lms-learning-management-system' ),
		'add'               => esc_html__( 'Add', 'masterstudy-lms-learning-management-system' ),
		'selectCountry'     => esc_html__( 'Select Country', 'masterstudy-lms-learning-management-system' ),
		'searchCountry'     => esc_html__( 'Search for a country…', 'masterstudy-lms-learning-management-system' ),
		'noTaxRates'        => esc_html__( 'No tax rates set up yet. Click ‘Add’ to create the first one.', 'masterstudy-lms-learning-management-system' ),
		'enterTaxRate'      => esc_html__( 'Enter tax rate (0–100%)', 'masterstudy-lms-learning-management-system' ),
		'configureRatesFor' => esc_html__( 'Tax rates for', 'masterstudy-lms-learning-management-system' ),
		'applyToAllRegions' => esc_html__( 'Use the same % for all regions', 'masterstudy-lms-learning-management-system' ),
		'apply'             => esc_html__( 'Apply', 'masterstudy-lms-learning-management-system' ),
		'save'              => esc_html__( 'Save', 'masterstudy-lms-learning-management-system' ),
		'edit'              => esc_html__( 'edit', 'masterstudy-lms-learning-management-system' ),
	),
);
?>

<stm-taxes :field="<?php echo esc_attr( $field ); ?>"></stm-taxes>
