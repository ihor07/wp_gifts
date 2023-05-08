<?php

if ( ! function_exists( 'swissdelight_core_add_fixed_header_option' ) ) {
	/**
	 * This function set header scrolling appearance value for global header option map
	 */
	function swissdelight_core_add_fixed_header_option( $options ) {
		$options['fixed'] = esc_html__( 'Fixed', 'swissdelight-core' );

		return $options;
	}

	add_filter( 'swissdelight_core_filter_header_scroll_appearance_option', 'swissdelight_core_add_fixed_header_option' );
}
