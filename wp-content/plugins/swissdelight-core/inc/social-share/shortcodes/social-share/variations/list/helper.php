<?php

if ( ! function_exists( 'swissdelight_core_add_social_share_variation_list' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_social_share_variation_list( $variations ) {
		$variations['list'] = esc_html__( 'List', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_social_share_layouts', 'swissdelight_core_add_social_share_variation_list' );
}
