<?php

if ( ! function_exists( 'swissdelight_core_add_clock_spinner_layout_option' ) ) {
	/**
	 * Function that set new value into page spinner layout options map
	 *
	 * @param array $layouts - module layouts
	 *
	 * @return array
	 */
	function swissdelight_core_add_clock_spinner_layout_option( $layouts ) {
		$layouts['clock'] = esc_html__( 'Clock', 'swissdelight-core' );

		return $layouts;
	}

	add_filter( 'swissdelight_core_filter_page_spinner_layout_options', 'swissdelight_core_add_clock_spinner_layout_option' );
}
