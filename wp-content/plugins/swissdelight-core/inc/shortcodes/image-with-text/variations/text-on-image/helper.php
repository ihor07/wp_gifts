<?php

if ( ! function_exists( 'swissdelight_core_add_image_with_text_variation_text_on_image' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_image_with_text_variation_text_on_image( $variations ) {
		$variations['text-on-image'] = esc_html__( 'Text On Image', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_image_with_text_layouts', 'swissdelight_core_add_image_with_text_variation_text_on_image' );
}
