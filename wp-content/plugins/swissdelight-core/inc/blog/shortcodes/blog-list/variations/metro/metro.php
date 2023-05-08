<?php

if ( ! function_exists( 'swissdelight_core_add_blog_list_variation_metro' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_blog_list_variation_metro( $variations ) {
		$variations['metro'] = esc_html__( 'Metro', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_blog_list_layouts', 'swissdelight_core_add_blog_list_variation_metro' );
}
