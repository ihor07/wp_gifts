<?php

if ( ! function_exists( 'swissdelight_core_get_mobile_header_logo_image' ) ) {
	/**
	 * Function that return logo image html for current module
	 *
	 * @return string that contains html content
	 */
	function swissdelight_core_get_mobile_header_logo_image() {
		$logo_height_mobile        = swissdelight_core_get_post_value_through_levels( 'qodef_mobile_logo_height' );
		$logo_height               = ! empty( $logo_height_mobile ) ? $logo_height_mobile : swissdelight_core_get_post_value_through_levels( 'qodef_logo_height' );
		$logo_padding_mobile       = swissdelight_core_get_post_value_through_levels( 'qodef_mobile_logo_padding' );
		$logo_padding              = ! empty( $logo_padding_mobile ) ? $logo_padding_mobile : swissdelight_core_get_post_value_through_levels( 'qodef_logo_padding' );
		$mobile_logo_main_image_id = swissdelight_core_get_post_value_through_levels( 'qodef_mobile_logo_main' );
		$customizer_logo           = swissdelight_core_get_customizer_logo();

		$logo_classes = array();
		$logo_styles  = array();
		if ( ! empty( $logo_height ) ) {
			$logo_classes[] = 'qodef-height--set';
			$logo_styles[]  = 'height:' . intval( $logo_height ) . 'px';
		} else {
			$logo_classes[] = 'qodef-height--not-set';
		}

		if ( ! empty( $logo_padding ) ) {
			$logo_styles[] = 'padding:' . esc_attr( $logo_padding );
		}

		$parameters = array(
			'logo_classes' => implode( ' ', $logo_classes ),
			'logo_styles'  => implode( ';', $logo_styles ),
		);

		$available_logos = apply_filters(
			'swissdelight_core_filter_available_mobile_header_logo_images',
			array(
				'main' => 'main',
			),
			$parameters
		);

		$is_enabled = false;
		$logo_html  = array();
		foreach ( $available_logos as $logo_key => $option_value ) {
			$logo_html[ 'logo_' . $logo_key . '_image' ] = '';

			$logo_image_id = swissdelight_core_get_post_value_through_levels( 'qodef_logo_' . $option_value );

			if ( ! empty( $mobile_logo_main_image_id ) && ( 'main' === $logo_key || empty( $logo_image_id ) ) ) {
				$logo_image_id = $mobile_logo_main_image_id;
			}

			if ( ! empty( $logo_image_id ) ) {
				$logo_image_attr = array(
					'class'    => 'qodef-header-logo-image qodef--' . str_replace( '_', '-', $logo_key ),
					'itemprop' => 'image',
					'alt'      => sprintf( esc_attr__( 'logo %s', 'swissdelight-core' ), str_replace( '_', ' ', $logo_key ) ),
				);

				$image      = wp_get_attachment_image( $logo_image_id, 'full', false, $logo_image_attr );
				$image_html = ! empty( $image ) ? $image : qode_framework_get_image_html_from_src( $logo_image_id, $logo_image_attr );

				$logo_html[ 'logo_' . $logo_key . '_image' ] = $image_html;

				$is_enabled = true;
			}
		}

		if ( ! empty( $customizer_logo ) ) {
			$logo_html['logo_main_image'] = $customizer_logo;
		}

		$parameters['logo_image'] = implode( '', apply_filters( 'swissdelight_core_filter_mobile_header_logo_image_html', $logo_html, $parameters ) );

		if ( $is_enabled ) {
			echo apply_filters( 'swissdelight_core_filter_get_mobile_header_logo_image', swissdelight_core_get_template_part( 'mobile-header/templates', 'parts/mobile-logo', '', $parameters ), $parameters, $logo_html );
		}
	}
}
