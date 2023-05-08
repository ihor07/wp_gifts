<?php

if ( ! function_exists( 'swissdelight_core_add_banner_variation_link_overlay' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_banner_variation_link_overlay( $variations ) {
		$variations['link-overlay'] = esc_html__( 'Link Overlay', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_banner_layouts', 'swissdelight_core_add_banner_variation_link_overlay' );
}
