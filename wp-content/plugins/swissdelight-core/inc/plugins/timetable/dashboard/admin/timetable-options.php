<?php

if ( ! function_exists( 'swissdelight_core_add_timetable_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function swissdelight_core_add_timetable_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => SWISSDELIGHT_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'timetable',
				'icon'        => 'fa fa-book',
				'title'       => esc_html__( 'Timetable', 'swissdelight-core' ),
				'description' => esc_html__( 'Global Timetable Options', 'swissdelight-core' ),
			)
		);

		if ( $page ) {

			$page->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_enable_timetable_predefined_style',
					'title'         => esc_html__( 'Enable Predefined Style', 'swissdelight-core' ),
					'description'   => esc_html__( 'Enabling this option will set predefined style for timetable plugin', 'swissdelight-core' ),
					'options'       => swissdelight_core_get_select_type_options_pool( 'no_yes', false ),
					'default_value' => 'no',
				)
			);

			// Hook to include additional options after module options
			do_action( 'swissdelight_core_action_after_timetable_options_map', $page );
		}
	}

	add_action( 'swissdelight_core_action_default_options_init', 'swissdelight_core_add_timetable_options', swissdelight_core_get_admin_options_map_position( 'timetable' ) );
}
