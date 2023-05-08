<?php

if ( ! function_exists( 'swissdelight_core_add_portfolio_single_variation_gallery_big' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_portfolio_single_variation_gallery_big( $variations ) {
		$variations['gallery-big'] = esc_html__( 'Gallery - Big', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_portfolio_single_layout_options', 'swissdelight_core_add_portfolio_single_variation_gallery_big' );
}
