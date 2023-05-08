<?php

if ( ! function_exists( 'swissdelight_core_add_icon_with_text_variation_before_title' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_icon_with_text_variation_before_title( $variations ) {
		$variations['before-title'] = esc_html__( 'Before Title', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_icon_with_text_layouts', 'swissdelight_core_add_icon_with_text_variation_before_title' );
}
