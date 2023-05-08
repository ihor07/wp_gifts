<?php

if ( ! function_exists( 'swissdelight_core_add_map_options' ) ) {
	/**
	 * Function that add map options
	 */
	function swissdelight_core_add_map_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => SWISSDELIGHT_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'map',
				'icon'        => 'fa fa-book',
				'title'       => esc_html__( 'Maps', 'swissdelight-core' ),
				'description' => esc_html__( 'Global Maps Options', 'swissdelight-core' ),
			)
		);

		if ( $page ) {
			$page->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_maps_api_key',
					'title'       => esc_html__( 'Maps API Key', 'swissdelight-core' ),
					'description' => esc_html__( 'Enter Google Maps API key', 'swissdelight-core' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type'  => 'textarea',
					'name'        => 'qodef_map_style',
					'title'       => esc_html__( 'Map Style', 'swissdelight-core' ),
					'description' => esc_html__( 'Enter Snazzy Map style JSON code', 'swissdelight-core' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_map_zoom',
					'title'       => esc_html__( 'Map Zoom', 'swissdelight-core' ),
					'description' => esc_html__( 'Input the default zoom value for the map. Note that this value applies in the event that the map contains a single address only. In the event of multiple addresses being shown, Google Map reverts to its own zoom values in order to fit all the addresses on the screen. ', 'swissdelight-core' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_map_scroll',
					'title'         => esc_html__( 'Enable Map Scroll', 'swissdelight-core' ),
					'description'   => esc_html__( 'Use this option to enable map scrolling', 'swissdelight-core' ),
					'default_value' => 'no',
				)
			);

			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_map_drag',
					'title'         => esc_html__( 'Enable Map Dragging', 'swissdelight-core' ),
					'description'   => esc_html__( 'Use this option to enable map dragging', 'swissdelight-core' ),
					'default_value' => 'yes',
				)
			);

			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_map_street_view_control',
					'title'         => esc_html__( 'Enable Map Street View Control', 'swissdelight-core' ),
					'description'   => esc_html__( 'Use this option to enable street view control on map', 'swissdelight-core' ),
					'default_value' => 'yes',
				)
			);

			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_map_zoom_control',
					'title'         => esc_html__( 'Enable Map Zoom Control', 'swissdelight-core' ),
					'description'   => esc_html__( 'Use this option to enable zoom control on map', 'swissdelight-core' ),
					'default_value' => 'yes',
				)
			);

			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_map_type_control',
					'title'         => esc_html__( 'Enable Map Type Control', 'swissdelight-core' ),
					'description'   => esc_html__( 'Use this option to enable type control on map', 'swissdelight-core' ),
					'default_value' => 'yes',
				)
			);

			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_map_full_screen_control',
					'title'         => esc_html__( 'Enable Map Full Screen Control', 'swissdelight-core' ),
					'description'   => esc_html__( 'Use this option to enable full screen control on map', 'swissdelight-core' ),
					'default_value' => 'yes',
				)
			);

			// Hook to include additional options after module options
			do_action( 'swissdelight_core_action_after_map_options_map', $page );
		}
	}

	add_action( 'swissdelight_core_action_default_options_init', 'swissdelight_core_add_map_options', swissdelight_core_get_admin_options_map_position( 'maps' ) );
}
