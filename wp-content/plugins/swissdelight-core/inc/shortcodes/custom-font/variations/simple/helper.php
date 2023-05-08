<?php

if ( ! function_exists( 'swissdelight_core_add_custom_font_variation_simple' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_custom_font_variation_simple( $variations ) {
		$variations['simple'] = esc_html__( 'Simple', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_custom_font_layouts', 'swissdelight_core_add_custom_font_variation_simple' );
}
