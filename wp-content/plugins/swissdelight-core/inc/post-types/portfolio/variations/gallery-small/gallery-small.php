<?php

if ( ! function_exists( 'swissdelight_core_add_portfolio_single_variation_gallery_small' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_portfolio_single_variation_gallery_small( $variations ) {
		$variations['gallery-small'] = esc_html__( 'Gallery - Small', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_portfolio_single_layout_options', 'swissdelight_core_add_portfolio_single_variation_gallery_small' );
}
