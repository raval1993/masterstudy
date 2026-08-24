<?php

class MS_LMS_Email_Template_Helpers {

	public static function render( $template, $vars ) {
		$replacements = array();
		foreach ( $vars as $key => $value ) {
			$replacements[ '{{' . $key . '}}' ] = $value;
		}

		return strtr( $template, $replacements );
	}


	public static function link( $url ) {
		return sprintf(
			'<a href="%s" target="_blank">%s</a>',
			esc_url( $url ),
			esc_html( $url )
		);
	}
}
