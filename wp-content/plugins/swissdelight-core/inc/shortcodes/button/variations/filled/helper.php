<?php

if ( ! function_exists( 'swissdelight_core_add_button_variation_filled' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_button_variation_filled( $variations ) {
		$variations['filled'] = esc_html__( 'Filled', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_button_layouts', 'swissdelight_core_add_button_variation_filled' );
}
