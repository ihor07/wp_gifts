<?php

if ( ! function_exists( 'swissdelight_core_add_portfolio_category_list_variation_gallery' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_portfolio_category_list_variation_gallery( $variations ) {
		$variations['gallery'] = esc_html__( 'Gallery', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_portfolio_category_list_layouts', 'swissdelight_core_add_portfolio_category_list_variation_gallery' );
}
