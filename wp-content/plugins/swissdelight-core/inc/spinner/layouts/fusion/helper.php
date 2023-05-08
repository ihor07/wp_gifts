<?php

if ( ! function_exists( 'swissdelight_core_add_fusion_spinner_layout_option' ) ) {
	/**
	 * Function that set new value into page spinner layout options map
	 *
	 * @param array $layouts - module layouts
	 *
	 * @return array
	 */
	function swissdelight_core_add_fusion_spinner_layout_option( $layouts ) {
		$layouts['fusion'] = esc_html__( 'Fusion', 'swissdelight-core' );

		return $layouts;
	}

	add_filter( 'swissdelight_core_filter_page_spinner_layout_options', 'swissdelight_core_add_fusion_spinner_layout_option' );
}
