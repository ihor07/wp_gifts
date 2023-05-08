<?php

if ( ! function_exists( 'swissdelight_core_add_social_share_variation_dropdown' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_social_share_variation_dropdown( $variations ) {
		$variations['dropdown'] = esc_html__( 'Dropdown', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_social_share_layouts', 'swissdelight_core_add_social_share_variation_dropdown' );
}
