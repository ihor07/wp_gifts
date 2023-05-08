<?php

if ( ! function_exists( 'swissdelight_core_add_team_list_variation_info_on_hover' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_team_list_variation_info_on_hover( $variations ) {
		$variations['info-on-hover'] = esc_html__( 'Info on Hover', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_team_list_layouts', 'swissdelight_core_add_team_list_variation_info_on_hover' );
}
