<?php

if ( ! function_exists( 'swissdelight_load_page_mobile_header' ) ) {
	/**
	 * Function which loads page template module
	 */
	function swissdelight_load_page_mobile_header() {
		// Include mobile header template
		echo apply_filters( 'swissdelight_filter_mobile_header_template', swissdelight_get_template_part( 'mobile-header', 'templates/mobile-header' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	add_action( 'swissdelight_action_page_header_template', 'swissdelight_load_page_mobile_header' );
}

if ( ! function_exists( 'swissdelight_register_mobile_navigation_menus' ) ) {
	/**
	 * Function which registers navigation menus
	 */
	function swissdelight_register_mobile_navigation_menus() {
		$navigation_menus = apply_filters( 'swissdelight_filter_register_mobile_navigation_menus', array( 'mobile-navigation' => esc_html__( 'Mobile Navigation', 'swissdelight' ) ) );

		if ( ! empty( $navigation_menus ) ) {
			register_nav_menus( $navigation_menus );
		}
	}

	add_action( 'swissdelight_action_after_include_modules', 'swissdelight_register_mobile_navigation_menus' );
}
