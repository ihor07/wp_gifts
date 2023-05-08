<?php

if ( ! function_exists( 'swissdelight_core_add_restaurant_menu_list_variation_info_standard' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_restaurant_menu_list_variation_info_standard( $variations ) {
		$variations['info-standard'] = esc_html__( 'Info Standard', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_restaurant_menu_list_layouts', 'swissdelight_core_add_restaurant_menu_list_variation_info_standard' );
}
