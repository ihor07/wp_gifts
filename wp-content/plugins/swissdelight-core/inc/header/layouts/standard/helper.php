<?php

if ( ! function_exists( 'swissdelight_core_add_standard_header_global_option' ) ) {
	/**
	 * This function set header type value for global header option map
	 */
	function swissdelight_core_add_standard_header_global_option( $header_layout_options ) {
		$header_layout_options['standard'] = array(
			'image' => SWISSDELIGHT_CORE_HEADER_LAYOUTS_URL_PATH . '/standard/assets/img/standard-header.png',
			'label' => esc_html__( 'Standard', 'swissdelight-core' ),
		);

		return $header_layout_options;
	}

	add_filter( 'swissdelight_core_filter_header_layout_option', 'swissdelight_core_add_standard_header_global_option' );
}

if ( ! function_exists( 'swissdelight_core_set_standard_header_as_default_global_option' ) ) {
	/**
	 * This function set header type as default option value for global header option map
	 */
	function swissdelight_core_set_standard_header_as_default_global_option() {
		return 'standard';
	}

	add_filter( 'swissdelight_core_filter_header_layout_default_option_value', 'swissdelight_core_set_standard_header_as_default_global_option' );
}

if ( ! function_exists( 'swissdelight_core_register_standard_header_layout' ) ) {
	/**
	 * Function which add header layout into global list
	 *
	 * @param array $header_layouts
	 *
	 * @return array
	 */
	function swissdelight_core_register_standard_header_layout( $header_layouts ) {
		$header_layout = array(
			'standard' => 'SwissDelightCore_Standard_Header',
		);

		$header_layouts = array_merge( $header_layouts, $header_layout );

		return $header_layouts;
	}

	add_filter( 'swissdelight_core_filter_register_header_layouts', 'swissdelight_core_register_standard_header_layout' );
}
