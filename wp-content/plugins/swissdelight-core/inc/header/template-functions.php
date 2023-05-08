<?php

if ( ! function_exists( 'swissdelight_core_get_header_logo_image' ) ) {
	/**
	 * This function print header logo image
	 *
	 * @param array $parameters
	 */
	function swissdelight_core_get_header_logo_image( $parameters = array() ) {
		$header_skin     = swissdelight_core_get_post_value_through_levels( 'qodef_header_skin' );
		$logo_height     = swissdelight_core_get_post_value_through_levels( 'qodef_logo_height' );
		$logo_padding    = swissdelight_core_get_post_value_through_levels( 'qodef_logo_padding' );
		$customizer_logo = swissdelight_core_get_customizer_logo();

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

		$parameters = array_merge(
			$parameters,
			array(
				'logo_classes' => implode( ' ', $logo_classes ),
				'logo_styles'  => implode( ';', $logo_styles ),
			)
		);

		$available_logos = apply_filters(
			'swissdelight_core_filter_available_header_logo_images',
			array(
				'main'  => 'main',
				'dark'  => 'dark',
				'light' => 'light',
			),
			$parameters
		);

		$is_enabled = false;
		$logo_html  = array();
		foreach ( $available_logos as $logo_key => $option_value ) {
			$logo_html[ 'logo_' . $logo_key . '_image' ] = '';

			$logo_image_id = swissdelight_core_get_post_value_through_levels( 'qodef_logo_' . $option_value );

			if ( empty( $logo_image_id ) && ! empty( $header_skin ) ) {
				$logo_image_id = swissdelight_core_get_post_value_through_levels( 'qodef_logo_main' );
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

		$parameters['logo_image'] = implode( '', apply_filters( 'swissdelight_core_filter_header_logo_image_html', $logo_html, $parameters ) );

		if ( $is_enabled ) {
			echo apply_filters( 'swissdelight_core_filter_get_header_logo_image', swissdelight_core_get_template_part( 'header/templates', 'parts/logo', '', $parameters ), $parameters );
		}
	}
}

if ( ! function_exists( 'swissdelight_core_get_header_widget_area' ) ) {
	/**
	 * This function return header widgets area
	 *
	 * @param string $header_layout
	 * @param string $widget_area
	 */
	function swissdelight_core_get_header_widget_area( $header_layout = '', $widget_area = 'one' ) {
		$page_id    = qode_framework_get_page_id();
		$is_enabled = 'no' !== get_post_meta( $page_id, 'qodef_show_header_widget_areas', true );

		if ( $is_enabled ) {
			$parameters = apply_filters(
				'swissdelight_core_filter_header_widget_area',
				array(
					'page_id'             => $page_id,
					'header_layout'       => $header_layout,
					'widget_area'         => $widget_area,
					'is_enabled'          => $is_enabled,
					'default_widget_area' => 'qodef-header-widget-area-' . esc_attr( $widget_area ),
					'custom_widget_area'  => get_post_meta( $page_id, 'qodef_header_custom_widget_area_' . esc_attr( $widget_area ), true ),
				)
			);

			swissdelight_core_template_part( 'header/templates', 'parts/widgets', '', $parameters );
		}
	}
}
