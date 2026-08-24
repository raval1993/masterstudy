<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function masterstudy_lms_get_countries( $filter_by_tax_settings = true ) {
	$settings          = get_option( 'stm_lms_settings' );
	$choosen_countries = ! empty( $settings['taxes'] ) ? $settings['taxes'] : array();

	$allowed_country_codes = array_map(
		function( $entry ) {
			return isset( $entry['country'] ) ? strtoupper( $entry['country'] ) : '';
		},
		$choosen_countries
	);

	$allowed_country_codes = array_filter( $allowed_country_codes );

	$all_countries = array(
		array(
			'code' => 'US',
			'name' => esc_html__( 'United States', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'DE',
			'name' => esc_html__( 'Germany', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'FR',
			'name' => esc_html__( 'France', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'IT',
			'name' => esc_html__( 'Italy', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'ES',
			'name' => esc_html__( 'Spain', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GB',
			'name' => esc_html__( 'United Kingdom', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'RU',
			'name' => esc_html__( 'Russian Federation', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CN',
			'name' => esc_html__( 'China', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'JP',
			'name' => esc_html__( 'Japan', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'IN',
			'name' => esc_html__( 'India', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BR',
			'name' => esc_html__( 'Brazil', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CA',
			'name' => esc_html__( 'Canada', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AU',
			'name' => esc_html__( 'Australia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'UA',
			'name' => esc_html__( 'Ukraine', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PL',
			'name' => esc_html__( 'Poland', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NL',
			'name' => esc_html__( 'Netherlands', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SE',
			'name' => esc_html__( 'Sweden', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CH',
			'name' => esc_html__( 'Switzerland', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TR',
			'name' => esc_html__( 'Türkiye', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BE',
			'name' => esc_html__( 'Belgium', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AT',
			'name' => esc_html__( 'Austria', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NO',
			'name' => esc_html__( 'Norway', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'FI',
			'name' => esc_html__( 'Finland', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'DK',
			'name' => esc_html__( 'Denmark', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'IE',
			'name' => esc_html__( 'Ireland', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PT',
			'name' => esc_html__( 'Portugal', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GR',
			'name' => esc_html__( 'Greece', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CZ',
			'name' => esc_html__( 'Czech Republic', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'HU',
			'name' => esc_html__( 'Hungary', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'RO',
			'name' => esc_html__( 'Romania', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SK',
			'name' => esc_html__( 'Slovakia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BG',
			'name' => esc_html__( 'Bulgaria', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'HR',
			'name' => esc_html__( 'Croatia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SI',
			'name' => esc_html__( 'Slovenia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'EE',
			'name' => esc_html__( 'Estonia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'LV',
			'name' => esc_html__( 'Latvia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'LT',
			'name' => esc_html__( 'Lithuania', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'LU',
			'name' => esc_html__( 'Luxembourg', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'IS',
			'name' => esc_html__( 'Iceland', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'LI',
			'name' => esc_html__( 'Liechtenstein', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MC',
			'name' => esc_html__( 'Monaco', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SM',
			'name' => esc_html__( 'San Marino', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'VA',
			'name' => esc_html__( 'Holy See (Vatican City State)', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AD',
			'name' => esc_html__( 'Andorra', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AE',
			'name' => esc_html__( 'United Arab Emirates', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AF',
			'name' => esc_html__( 'Afghanistan', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AG',
			'name' => esc_html__( 'Antigua and Barbuda', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AI',
			'name' => esc_html__( 'Anguilla', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AL',
			'name' => esc_html__( 'Albania', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AM',
			'name' => esc_html__( 'Armenia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CW',
			'name' => esc_html__( 'Curacao', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AO',
			'name' => esc_html__( 'Angola', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AQ',
			'name' => esc_html__( 'Antarctica', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AR',
			'name' => esc_html__( 'Argentina', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AS',
			'name' => esc_html__( 'American Samoa', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AW',
			'name' => esc_html__( 'Aruba', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AZ',
			'name' => esc_html__( 'Azerbaijan', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BA',
			'name' => esc_html__( 'Bosnia and Herzegovina', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BB',
			'name' => esc_html__( 'Barbados', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BD',
			'name' => esc_html__( 'Bangladesh', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BF',
			'name' => esc_html__( 'Burkina Faso', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BH',
			'name' => esc_html__( 'Bahrain', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BI',
			'name' => esc_html__( 'Burundi', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BJ',
			'name' => esc_html__( 'Benin', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BM',
			'name' => esc_html__( 'Bermuda', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BN',
			'name' => esc_html__( 'Brunei Darussalam', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BO',
			'name' => esc_html__( 'Bolivia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BS',
			'name' => esc_html__( 'Bahamas', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BT',
			'name' => esc_html__( 'Bhutan', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BV',
			'name' => esc_html__( 'Bouvet Island', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BW',
			'name' => esc_html__( 'Botswana', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BY',
			'name' => esc_html__( 'Belarus', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BZ',
			'name' => esc_html__( 'Belize', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CC',
			'name' => esc_html__( 'Cocos (Keeling) Islands', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CD',
			'name' => esc_html__( 'Congo, The Democratic Republic of the', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CF',
			'name' => esc_html__( 'Central African Republic', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CG',
			'name' => esc_html__( 'Congo', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CI',
			'name' => wp_kses_post( __( "Cote D'Ivoire", 'masterstudy-lms-learning-management-system' ) ),
		),
		array(
			'code' => 'CK',
			'name' => esc_html__( 'Cook Islands', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CL',
			'name' => esc_html__( 'Chile', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CM',
			'name' => esc_html__( 'Cameroon', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CO',
			'name' => esc_html__( 'Colombia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CR',
			'name' => esc_html__( 'Costa Rica', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CU',
			'name' => esc_html__( 'Cuba', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CV',
			'name' => esc_html__( 'Cape Verde', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CX',
			'name' => esc_html__( 'Christmas Island', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CY',
			'name' => esc_html__( 'Cyprus', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'DJ',
			'name' => esc_html__( 'Djibouti', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'DM',
			'name' => esc_html__( 'Dominica', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'DO',
			'name' => esc_html__( 'Dominican Republic', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'DZ',
			'name' => esc_html__( 'Algeria', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'EC',
			'name' => esc_html__( 'Ecuador', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'EG',
			'name' => esc_html__( 'Egypt', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'EH',
			'name' => esc_html__( 'Western Sahara', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'ER',
			'name' => esc_html__( 'Eritrea', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'ET',
			'name' => esc_html__( 'Ethiopia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'FJ',
			'name' => esc_html__( 'Fiji', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'FK',
			'name' => esc_html__( 'Falkland Islands (Malvinas)', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'FM',
			'name' => esc_html__( 'Micronesia, Federated States of', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'FO',
			'name' => esc_html__( 'Faroe Islands', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SX',
			'name' => esc_html__( 'Sint Maarten (Dutch part)', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GA',
			'name' => esc_html__( 'Gabon', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GD',
			'name' => esc_html__( 'Grenada', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GE',
			'name' => esc_html__( 'Georgia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GF',
			'name' => esc_html__( 'French Guiana', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GH',
			'name' => esc_html__( 'Ghana', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GI',
			'name' => esc_html__( 'Gibraltar', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GL',
			'name' => esc_html__( 'Greenland', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GM',
			'name' => esc_html__( 'Gambia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GN',
			'name' => esc_html__( 'Guinea', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GP',
			'name' => esc_html__( 'Guadeloupe', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GQ',
			'name' => esc_html__( 'Equatorial Guinea', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GS',
			'name' => esc_html__( 'South Georgia and the South Sandwich Islands', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GT',
			'name' => esc_html__( 'Guatemala', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GU',
			'name' => esc_html__( 'Guam', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GW',
			'name' => esc_html__( 'Guinea-Bissau', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GY',
			'name' => esc_html__( 'Guyana', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'HK',
			'name' => esc_html__( 'Hong Kong', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'HM',
			'name' => esc_html__( 'Heard Island and McDonald Islands', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'HN',
			'name' => esc_html__( 'Honduras', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'HT',
			'name' => esc_html__( 'Haiti', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'ID',
			'name' => esc_html__( 'Indonesia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'IL',
			'name' => esc_html__( 'Israel', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'IO',
			'name' => esc_html__( 'British Indian Ocean Territory', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'IQ',
			'name' => esc_html__( 'Iraq', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'IR',
			'name' => esc_html__( 'Iran, Islamic Republic of', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'JM',
			'name' => esc_html__( 'Jamaica', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'JO',
			'name' => esc_html__( 'Jordan', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'KE',
			'name' => esc_html__( 'Kenya', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'KG',
			'name' => esc_html__( 'Kyrgyzstan', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'KH',
			'name' => esc_html__( 'Cambodia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'KI',
			'name' => esc_html__( 'Kiribati', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'KM',
			'name' => esc_html__( 'Comoros', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'KN',
			'name' => esc_html__( 'Saint Kitts and Nevis', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'KP',
			'name' => wp_kses_post( __( "Korea, Democratic People's Republic", 'masterstudy-lms-learning-management-system' ) ),
		),
		array(
			'code' => 'KR',
			'name' => esc_html__( 'Korea, Republic of', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'KW',
			'name' => esc_html__( 'Kuwait', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'KY',
			'name' => esc_html__( 'Cayman Islands', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'KZ',
			'name' => esc_html__( 'Kazakhstan', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'LA',
			'name' => wp_kses_post( __( "Lao People's Democratic Republic", 'masterstudy-lms-learning-management-system' ) ),
		),
		array(
			'code' => 'LB',
			'name' => esc_html__( 'Lebanon', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'LC',
			'name' => esc_html__( 'Saint Lucia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'LK',
			'name' => esc_html__( 'Sri Lanka', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'LR',
			'name' => esc_html__( 'Liberia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'LS',
			'name' => esc_html__( 'Lesotho', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'LY',
			'name' => esc_html__( 'Libya', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MA',
			'name' => esc_html__( 'Morocco', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MD',
			'name' => esc_html__( 'Moldova, Republic of', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MG',
			'name' => esc_html__( 'Madagascar', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MH',
			'name' => esc_html__( 'Marshall Islands', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MK',
			'name' => esc_html__( 'Macedonia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'ML',
			'name' => esc_html__( 'Mali', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MM',
			'name' => esc_html__( 'Myanmar', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MN',
			'name' => esc_html__( 'Mongolia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MO',
			'name' => esc_html__( 'Macau', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MP',
			'name' => esc_html__( 'Northern Mariana Islands', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MQ',
			'name' => esc_html__( 'Martinique', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MR',
			'name' => esc_html__( 'Mauritania', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MS',
			'name' => esc_html__( 'Montserrat', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MT',
			'name' => esc_html__( 'Malta', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MU',
			'name' => esc_html__( 'Mauritius', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MV',
			'name' => esc_html__( 'Maldives', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MW',
			'name' => esc_html__( 'Malawi', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MX',
			'name' => esc_html__( 'Mexico', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MY',
			'name' => esc_html__( 'Malaysia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MZ',
			'name' => esc_html__( 'Mozambique', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NA',
			'name' => esc_html__( 'Namibia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NC',
			'name' => esc_html__( 'New Caledonia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NE',
			'name' => esc_html__( 'Niger', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NF',
			'name' => esc_html__( 'Norfolk Island', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NG',
			'name' => esc_html__( 'Nigeria', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NI',
			'name' => esc_html__( 'Nicaragua', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NP',
			'name' => esc_html__( 'Nepal', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NR',
			'name' => esc_html__( 'Nauru', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NU',
			'name' => esc_html__( 'Niue', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NZ',
			'name' => esc_html__( 'New Zealand', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'OM',
			'name' => esc_html__( 'Oman', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PA',
			'name' => esc_html__( 'Panama', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PE',
			'name' => esc_html__( 'Peru', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PF',
			'name' => esc_html__( 'French Polynesia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PG',
			'name' => esc_html__( 'Papua New Guinea', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PH',
			'name' => esc_html__( 'Philippines', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PK',
			'name' => esc_html__( 'Pakistan', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PM',
			'name' => esc_html__( 'Saint Pierre and Miquelon', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PN',
			'name' => esc_html__( 'Pitcairn Islands', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PR',
			'name' => esc_html__( 'Puerto Rico', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PS',
			'name' => esc_html__( 'Palestinian Territory', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PW',
			'name' => esc_html__( 'Palau', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PY',
			'name' => esc_html__( 'Paraguay', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'QA',
			'name' => esc_html__( 'Qatar', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'RE',
			'name' => esc_html__( 'Reunion', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'RW',
			'name' => esc_html__( 'Rwanda', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SA',
			'name' => esc_html__( 'Saudi Arabia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SB',
			'name' => esc_html__( 'Solomon Islands', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SC',
			'name' => esc_html__( 'Seychelles', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SD',
			'name' => esc_html__( 'Sudan', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SG',
			'name' => esc_html__( 'Singapore', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SH',
			'name' => esc_html__( 'Saint Helena', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SJ',
			'name' => esc_html__( 'Svalbard and Jan Mayen', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SL',
			'name' => esc_html__( 'Sierra Leone', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SN',
			'name' => esc_html__( 'Senegal', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SO',
			'name' => esc_html__( 'Somalia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SR',
			'name' => esc_html__( 'Suriname', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'ST',
			'name' => esc_html__( 'Sao Tome and Principe', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SV',
			'name' => esc_html__( 'El Salvador', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SY',
			'name' => esc_html__( 'Syrian Arab Republic', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SZ',
			'name' => esc_html__( 'Eswatini', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TC',
			'name' => esc_html__( 'Turks and Caicos Islands', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TD',
			'name' => esc_html__( 'Chad', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TF',
			'name' => esc_html__( 'French Southern Territories', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TG',
			'name' => esc_html__( 'Togo', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TH',
			'name' => esc_html__( 'Thailand', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TJ',
			'name' => esc_html__( 'Tajikistan', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TK',
			'name' => esc_html__( 'Tokelau', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TM',
			'name' => esc_html__( 'Turkmenistan', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TN',
			'name' => esc_html__( 'Tunisia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TO',
			'name' => esc_html__( 'Tonga', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TL',
			'name' => esc_html__( 'Timor-Leste', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TT',
			'name' => esc_html__( 'Trinidad and Tobago', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TV',
			'name' => esc_html__( 'Tuvalu', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TW',
			'name' => esc_html__( 'Taiwan', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TZ',
			'name' => esc_html__( 'Tanzania, United Republic of', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'UG',
			'name' => esc_html__( 'Uganda', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'UM',
			'name' => esc_html__( 'United States Minor Outlying Islands', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'UY',
			'name' => esc_html__( 'Uruguay', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'UZ',
			'name' => esc_html__( 'Uzbekistan', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'VC',
			'name' => esc_html__( 'Saint Vincent and the Grenadines', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'VE',
			'name' => esc_html__( 'Venezuela', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'VG',
			'name' => esc_html__( 'Virgin Islands, British', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'VI',
			'name' => esc_html__( 'Virgin Islands, U.S.', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'VN',
			'name' => esc_html__( 'Vietnam', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'VU',
			'name' => esc_html__( 'Vanuatu', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'WF',
			'name' => esc_html__( 'Wallis and Futuna', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'WS',
			'name' => esc_html__( 'Samoa', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'YE',
			'name' => esc_html__( 'Yemen', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'YT',
			'name' => esc_html__( 'Mayotte', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'RS',
			'name' => esc_html__( 'Serbia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'ZA',
			'name' => esc_html__( 'South Africa', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'ZM',
			'name' => esc_html__( 'Zambia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'ME',
			'name' => esc_html__( 'Montenegro', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'ZW',
			'name' => esc_html__( 'Zimbabwe', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AX',
			'name' => esc_html__( 'Aland Islands', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GG',
			'name' => esc_html__( 'Guernsey', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'IM',
			'name' => esc_html__( 'Isle of Man', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'JE',
			'name' => esc_html__( 'Jersey', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BL',
			'name' => esc_html__( 'Saint Barthelemy', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MF',
			'name' => esc_html__( 'Saint Martin', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'BQ',
			'name' => esc_html__( 'Bonaire, Saint Eustatius and Saba', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SS',
			'name' => esc_html__( 'South Sudan', 'masterstudy-lms-learning-management-system' ),
		),
	);

	if ( $filter_by_tax_settings && ! empty( $allowed_country_codes ) ) {
		$all_countries = array_filter(
			$all_countries,
			function( $country ) use ( $allowed_country_codes ) {
				return in_array( strtoupper( $country['code'] ), $allowed_country_codes, true );
			}
		);
	}

	return array_values( $all_countries );
}

function masterstudy_lms_get_us_states( $filter_by_tax_settings = true ) {
	$all = masterstudy_lms_all_us_states();

	if ( ! $filter_by_tax_settings ) {
		return $all;
	}

	$all_index = array();

	foreach ( $all as $st ) {
		$code = strtoupper( $st['code'] ?? '' );

		if ( $code ) {
			$all_index[ $code ] = $st;
		}
	}

	$settings = get_option( 'stm_lms_settings' );
	$taxes    = ( is_array( $settings ) && isset( $settings['taxes'] ) && is_array( $settings['taxes'] ) )
		? $settings['taxes']
		: array();

	$allowed_codes = array();

	foreach ( $taxes as $entry ) {
		if ( ! is_array( $entry ) ) {
			continue;
		}

		$country = strtoupper( (string) ( $entry['country'] ?? '' ) );

		if ( 'US' !== $country ) {
			continue;
		}

		$region = strtoupper( trim( (string) ( $entry['region'] ?? '' ) ) );

		if ( $region && isset( $all_index[ $region ] ) ) {
			$allowed_codes[ $region ] = true;
		}
	}

	if ( empty( $allowed_codes ) ) {
		return $all;
	}

	$out = array();

	foreach ( $all as $st ) {
		if ( isset( $allowed_codes[ strtoupper( $st['code'] ) ] ) ) {
			$out[] = $st;
		}
	}

	return $out;
}

function masterstudy_lms_all_us_states() {
	return array(
		array(
			'code' => 'AL',
			'name' => esc_html__( 'Alabama', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AK',
			'name' => esc_html__( 'Alaska', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AZ',
			'name' => esc_html__( 'Arizona', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'AR',
			'name' => esc_html__( 'Arkansas', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CA',
			'name' => esc_html__( 'California', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CO',
			'name' => esc_html__( 'Colorado', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'CT',
			'name' => esc_html__( 'Connecticut', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'DE',
			'name' => esc_html__( 'Delaware', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'FL',
			'name' => esc_html__( 'Florida', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'GA',
			'name' => esc_html__( 'Georgia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'HI',
			'name' => esc_html__( 'Hawaii', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'ID',
			'name' => esc_html__( 'Idaho', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'IL',
			'name' => esc_html__( 'Illinois', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'IN',
			'name' => esc_html__( 'Indiana', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'IA',
			'name' => esc_html__( 'Iowa', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'KS',
			'name' => esc_html__( 'Kansas', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'KY',
			'name' => esc_html__( 'Kentucky', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'LA',
			'name' => esc_html__( 'Louisiana', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'ME',
			'name' => esc_html__( 'Maine', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MD',
			'name' => esc_html__( 'Maryland', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MA',
			'name' => esc_html__( 'Massachusetts', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MI',
			'name' => esc_html__( 'Michigan', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MN',
			'name' => esc_html__( 'Minnesota', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MS',
			'name' => esc_html__( 'Mississippi', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MO',
			'name' => esc_html__( 'Missouri', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'MT',
			'name' => esc_html__( 'Montana', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NE',
			'name' => esc_html__( 'Nebraska', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NV',
			'name' => esc_html__( 'Nevada', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NH',
			'name' => esc_html__( 'New Hampshire', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NJ',
			'name' => esc_html__( 'New Jersey', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NM',
			'name' => esc_html__( 'New Mexico', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NY',
			'name' => esc_html__( 'New York', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'NC',
			'name' => esc_html__( 'North Carolina', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'ND',
			'name' => esc_html__( 'North Dakota', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'OH',
			'name' => esc_html__( 'Ohio', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'OK',
			'name' => esc_html__( 'Oklahoma', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'OR',
			'name' => esc_html__( 'Oregon', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'PA',
			'name' => esc_html__( 'Pennsylvania', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'RI',
			'name' => esc_html__( 'Rhode Island', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SC',
			'name' => esc_html__( 'South Carolina', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'SD',
			'name' => esc_html__( 'South Dakota', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TN',
			'name' => esc_html__( 'Tennessee', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'TX',
			'name' => esc_html__( 'Texas', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'UT',
			'name' => esc_html__( 'Utah', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'VT',
			'name' => esc_html__( 'Vermont', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'VA',
			'name' => esc_html__( 'Virginia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'WA',
			'name' => esc_html__( 'Washington', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'WV',
			'name' => esc_html__( 'West Virginia', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'WI',
			'name' => esc_html__( 'Wisconsin', 'masterstudy-lms-learning-management-system' ),
		),
		array(
			'code' => 'WY',
			'name' => esc_html__( 'Wyoming', 'masterstudy-lms-learning-management-system' ),
		),
	);
}
