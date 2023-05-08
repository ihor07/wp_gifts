<?php

if ( ! function_exists( 'swissdelight_core_add_banner_variation_link_button' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_banner_variation_link_button( $variations ) {
		$variations['link-button'] = esc_html__( 'Link Button', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_banner_layouts', 'swissdelight_core_add_banner_variation_link_button' );
}
