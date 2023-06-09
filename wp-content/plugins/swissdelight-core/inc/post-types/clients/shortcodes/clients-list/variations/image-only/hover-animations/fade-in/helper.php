<?php

if ( ! function_exists( 'swissdelight_core_filter_clients_list_image_only_fade_in' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_filter_clients_list_image_only_fade_in( $variations ) {
		$variations['fade-in'] = esc_html__( 'Fade In', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_clients_list_image_only_animation_options', 'swissdelight_core_filter_clients_list_image_only_fade_in' );
}
