<?php

if ( ! function_exists( 'swissdelight_core_add_button_variation_outlined' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_button_variation_outlined( $variations ) {
		$variations['outlined'] = esc_html__( 'Outlined', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_button_layouts', 'swissdelight_core_add_button_variation_outlined' );
}
