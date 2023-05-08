<?php

if ( ! function_exists( 'swissdelight_core_add_clients_list_variation_image_only' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_clients_list_variation_image_only( $variations ) {
		$variations['image-only'] = esc_html__( 'Image Only', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_clients_list_layouts', 'swissdelight_core_add_clients_list_variation_image_only' );
}
