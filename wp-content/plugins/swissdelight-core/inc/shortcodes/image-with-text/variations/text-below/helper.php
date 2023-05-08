<?php

if ( ! function_exists( 'swissdelight_core_add_image_with_text_variation_text_below' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_image_with_text_variation_text_below( $variations ) {
		$variations['text-below'] = esc_html__( 'Text Below', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_image_with_text_layouts', 'swissdelight_core_add_image_with_text_variation_text_below' );
}
