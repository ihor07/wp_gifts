<?php

if ( ! function_exists( 'swissdelight_core_get_age_verification' ) ) {
	/**
	 * Loads age-verification HTML
	 */
	function swissdelight_core_get_age_verification() {
		if ( 'yes' === swissdelight_core_get_post_value_through_levels( 'qodef_enable_age_verification' ) ) {
			swissdelight_core_load_age_verification_template();
		}
	}

	// Get age-verification HTML
	add_action( 'swissdelight_action_before_page_header', 'swissdelight_core_get_age_verification' );
}

if ( ! function_exists( 'swissdelight_core_body_class_age_verification' ) ) {
	/**
	 * Function that add additional class name into global class list for body tag
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function swissdelight_core_body_class_age_verification( $classes ) {
		if ( ! isset( $_COOKIE['disabledAgeVerification'] ) ) {
			$classes[] = 'qodef-age-verification--opened';
		}

		return $classes;
	}

	// Add plugin's body classes
	add_filter( 'body_class', 'swissdelight_core_body_class_age_verification' );
}

if ( ! function_exists( 'swissdelight_core_age_verification_set_admin_options_map_position' ) ) {
	/**
	 * Function that set dashboard admin options map position for this module
	 *
	 * @param int $position
	 * @param string $map
	 *
	 * @return int
	 */
	function swissdelight_core_age_verification_set_admin_options_map_position( $position, $map ) {

		if ( 'age-verification' === $map ) {
			$position = 98;
		}

		return $position;
	}

	add_filter( 'swissdelight_core_filter_admin_options_map_position', 'swissdelight_core_age_verification_set_admin_options_map_position', 10, 2 );
}

if ( ! function_exists( 'swissdelight_core_load_age_verification_template' ) ) {
	/**
	 * Loads HTML template with params
	 */
	function swissdelight_core_load_age_verification_template() {
		$logo_image = swissdelight_core_get_option_value( 'admin', 'qodef_age_verification_logo_image' );

		$params                     = array();
		$params['logo_image']       = ! empty( $logo_image ) ? $logo_image : swissdelight_core_get_post_value_through_levels( 'qodef_logo_main' );
		$params['title']            = swissdelight_core_get_option_value( 'admin', 'qodef_age_verification_title' );
		$params['subtitle']         = swissdelight_core_get_option_value( 'admin', 'qodef_age_verification_subtitle' );
		$params['note']             = swissdelight_core_get_option_value( 'admin', 'qodef_age_verification_note' );
		$params['link']             = swissdelight_core_get_option_value( 'admin', 'qodef_age_verification_link' );
		$background_image           = swissdelight_core_get_option_value( 'admin', 'qodef_age_verification_background_image' );
		$params['content_style']    = ! empty( $background_image ) ? 'background-image: url(' . esc_url( wp_get_attachment_url( $background_image ) ) . ')' : '';
		$params['prevent_behavior'] = swissdelight_core_get_option_value( 'admin', 'qodef_age_verification_prevent_behavior' );

		$holder_classes           = array();
		$holder_classes[]         = ! empty( $params['prevent_behavior'] ) ? 'qodef-prevent--' . $params['prevent_behavior'] : 'qodef-prevent--cookies';
		$params['holder_classes'] = implode( ' ', $holder_classes );

		echo swissdelight_core_get_template_part( 'age-verification', 'templates/age-verification', '', $params );
	}
}
