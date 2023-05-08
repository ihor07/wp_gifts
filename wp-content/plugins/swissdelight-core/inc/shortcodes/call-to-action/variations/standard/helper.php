<?php

if ( ! function_exists( 'swissdelight_core_add_call_to_action_variation_standard' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_call_to_action_variation_standard( $variations ) {
		$variations['standard'] = esc_html__( 'Standard', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_call_to_action_layouts', 'swissdelight_core_add_call_to_action_variation_standard' );
}
