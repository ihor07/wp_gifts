<?php

if ( ! function_exists( 'swissdelight_core_add_single_image_variation_default' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_single_image_variation_default( $variations ) {
		$variations['default'] = esc_html__( 'Default', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_single_image_layouts', 'swissdelight_core_add_single_image_variation_default' );
}
