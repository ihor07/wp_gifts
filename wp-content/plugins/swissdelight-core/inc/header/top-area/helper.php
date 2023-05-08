<?php

if ( ! function_exists( 'swissdelight_core_dependency_for_top_area_options' ) ) {
	/**
	 * Function which return dependency values for global module options
	 *
	 * @return array
	 */
	function swissdelight_core_dependency_for_top_area_options() {
		return apply_filters( 'swissdelight_core_filter_top_area_hide_option', $hide_dep_options = array() );
	}
}

if ( ! function_exists( 'swissdelight_core_register_top_area_header_areas' ) ) {
	/**
	 * Function which register widget areas for current module
	 */
	function swissdelight_core_register_top_area_header_areas() {
		register_sidebar(
			array(
				'id'            => 'qodef-top-area-left',
				'name'          => esc_html__( 'Header Top Area - Left', 'swissdelight-core' ),
				'description'   => esc_html__( 'Widgets added here will appear on the left side in top header area', 'swissdelight-core' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s qodef-top-bar-widget">',
				'after_widget'  => '</div>',
			)
		);

		register_sidebar(
			array(
				'id'            => 'qodef-top-area-right',
				'name'          => esc_html__( 'Header Top Area - Right', 'swissdelight-core' ),
				'description'   => esc_html__( 'Widgets added here will appear on the right side in top header area', 'swissdelight-core' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s qodef-top-bar-widget">',
				'after_widget'  => '</div>',
			)
		);
	}

	add_action( 'swissdelight_core_action_additional_header_widgets_area', 'swissdelight_core_register_top_area_header_areas' );
}

if ( ! function_exists( 'swissdelight_core_set_top_area_header_widget_area' ) ) {
	/**
	 * This function add additional header widgets area into global list
	 *
	 * @param array $widget_area_map
	 *
	 * @return array
	 */
	function swissdelight_core_set_top_area_header_widget_area( $widget_area_map ) {

		if ( 'top-area-left' === $widget_area_map['header_layout'] ) {
			$widget_area_map['is_enabled']          = true;
			$widget_area_map['default_widget_area'] = 'qodef-top-area-left';
			$widget_area_map['custom_widget_area']  = '';
		} elseif ( 'top-area-right' === $widget_area_map['header_layout'] ) {
			$widget_area_map['is_enabled']          = true;
			$widget_area_map['default_widget_area'] = 'qodef-top-area-right';
			$widget_area_map['custom_widget_area']  = '';
		}

		return $widget_area_map;
	}

	add_filter( 'swissdelight_core_filter_header_widget_area', 'swissdelight_core_set_top_area_header_widget_area' );
}

if ( ! function_exists( 'swissdelight_core_get_top_area_header_widget_area' ) ) {
	/**
	 * This function return top area header widgets area
	 *
	 * @param string $widget_area
	 */
	function swissdelight_core_get_top_area_header_widget_area( $widget_area = '' ) {
		$page_id = qode_framework_get_page_id();

		if ( ! empty( $widget_area ) ) {
			$parameters = apply_filters(
				'swissdelight_core_filter_top_area_header_widget_area',
				array(
					'page_id'             => $page_id,
					'widget_area'         => $widget_area,
					'default_widget_area' => 'qodef-top-area-' . esc_attr( $widget_area ),
					'custom_widget_area'  => get_post_meta( $page_id, 'qodef_top_area_header_custom_widget_area_' . esc_attr( $widget_area ), true ),
				)
			);

			swissdelight_core_template_part( 'header/templates', 'parts/widgets', '', $parameters );
		}
	}
}
