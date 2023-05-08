<?php

if ( ! function_exists( 'swissdelight_core_add_icon_with_text_variation_before_content' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_icon_with_text_variation_before_content( $variations ) {
		$variations['before-content'] = esc_html__( 'Before Content', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_icon_with_text_layouts', 'swissdelight_core_add_icon_with_text_variation_before_content' );
}
