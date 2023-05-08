<?php

if ( ! function_exists( 'swissdelight_core_add_pricing_table_variation_standard' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_pricing_table_variation_standard( $variations ) {

		$variations['standard'] = esc_html__( 'Standard', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_pricing_table_layouts', 'swissdelight_core_add_pricing_table_variation_standard' );
}
