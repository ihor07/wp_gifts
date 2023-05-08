<?php

if ( ! function_exists( 'swissdelight_core_add_logo_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function swissdelight_core_add_logo_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => SWISSDELIGHT_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'logo',
				'icon'        => 'fa fa-cog',
				'title'       => esc_html__( 'Logo', 'swissdelight-core' ),
				'description' => esc_html__( 'Global Logo Options', 'swissdelight-core' ),
				'layout'      => 'tabbed',
			)
		);

		if ( $page ) {

			$header_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-header',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'Header Logo Options', 'swissdelight-core' ),
					'description' => esc_html__( 'Set options for initial headers', 'swissdelight-core' ),
				)
			);

			$header_tab->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_logo_height',
					'title'       => esc_html__( 'Logo Height', 'swissdelight-core' ),
					'description' => esc_html__( 'Enter logo height', 'swissdelight-core' ),
					'args'        => array(
						'suffix' => esc_html__( 'px', 'swissdelight-core' ),
					),
				)
			);

			$header_tab->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_logo_padding',
					'title'       => esc_html__( 'Logo Padding', 'swissdelight-core' ),
					'description' => esc_html__( 'Enter logo padding value (top right bottom left)', 'swissdelight-core' ),
				)
			);

			$header_tab->add_field_element(
				array(
					'field_type'    => 'image',
					'name'          => 'qodef_logo_main',
					'title'         => esc_html__( 'Logo - Main', 'swissdelight-core' ),
					'description'   => esc_html__( 'Choose main logo image', 'swissdelight-core' ),
					'default_value' => defined( 'SWISSDELIGHT_ASSETS_ROOT' ) ? SWISSDELIGHT_ASSETS_ROOT . '/img/logo.png' : '',
					'multiple'      => 'no',
				)
			);

			$header_tab->add_field_element(
				array(
					'field_type'  => 'image',
					'name'        => 'qodef_logo_dark',
					'title'       => esc_html__( 'Logo - Dark', 'swissdelight-core' ),
					'description' => esc_html__( 'Choose dark logo image', 'swissdelight-core' ),
					'multiple'    => 'no',
				)
			);

			$header_tab->add_field_element(
				array(
					'field_type'  => 'image',
					'name'        => 'qodef_logo_light',
					'title'       => esc_html__( 'Logo - Light', 'swissdelight-core' ),
					'description' => esc_html__( 'Choose light logo image', 'swissdelight-core' ),
					'multiple'    => 'no',
				)
			);

			// Hook to include additional options after module options
			do_action( 'swissdelight_core_action_after_header_logo_options_map', $page, $header_tab );
		}
	}

	add_action( 'swissdelight_core_action_default_options_init', 'swissdelight_core_add_logo_options', swissdelight_core_get_admin_options_map_position( 'logo' ) );
}
