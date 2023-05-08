<?php

if ( ! function_exists( 'swissdelight_core_add_pulse_spinner_layout_option' ) ) {
	/**
	 * Function that set new value into page spinner layout options map
	 *
	 * @param array $layouts - module layouts
	 *
	 * @return array
	 */
	function swissdelight_core_add_pulse_spinner_layout_option( $layouts ) {
		$layouts['pulse'] = esc_html__( 'Pulse', 'swissdelight-core' );

		return $layouts;
	}

	add_filter( 'swissdelight_core_filter_page_spinner_layout_options', 'swissdelight_core_add_pulse_spinner_layout_option' );
}

if ( ! function_exists( 'swissdelight_core_set_pulse_spinner_layout_as_default_option' ) ) {
	/**
	 * Function that set default value for page spinner layout options map
	 *
	 * @param string $default_value
	 *
	 * @return string
	 */
	function swissdelight_core_set_pulse_spinner_layout_as_default_option( $default_value ) {
		return 'pulse';
	}

	add_filter( 'swissdelight_core_filter_page_spinner_default_layout_option', 'swissdelight_core_set_pulse_spinner_layout_as_default_option' );
}
