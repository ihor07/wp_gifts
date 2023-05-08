<?php

if ( ! function_exists( 'swissdelight_core_register_fullscreen_search_layout' ) ) {
	/**
	 * Function that add variation layout into global list
	 *
	 * @param array $search_layouts
	 *
	 * @return array
	 */
	function swissdelight_core_register_fullscreen_search_layout( $search_layouts ) {
		$search_layouts['fullscreen'] = 'SwissDelightCore_Fullscreen_Search';

		return $search_layouts;
	}

	add_filter( 'swissdelight_core_filter_register_search_layouts', 'swissdelight_core_register_fullscreen_search_layout' );
}
