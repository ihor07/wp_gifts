<?php

if ( ! function_exists( 'swissdelight_core_add_product_category_list_variation_info_on_image' ) ) {
	/**
	 * Function that add variation layout for this module
	 *
	 * @param array $variations
	 *
	 * @return array
	 */
	function swissdelight_core_add_product_category_list_variation_info_on_image( $variations ) {
		$variations['info-on-image'] = esc_html__( 'Info On Image', 'swissdelight-core' );

		return $variations;
	}

	add_filter( 'swissdelight_core_filter_product_category_list_layouts', 'swissdelight_core_add_product_category_list_variation_info_on_image' );
}
