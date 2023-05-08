<?php

if ( ! function_exists( 'swissdelight_core_add_portfolio_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function swissdelight_core_add_portfolio_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => SWISSDELIGHT_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'portfolio-item',
				'layout'      => 'tabbed',
				'icon'        => 'fa fa-cog',
				'title'       => esc_html__( 'Portfolio', 'swissdelight-core' ),
				'description' => esc_html__( 'Global settings related to portfolio', 'swissdelight-core' ),
			)
		);

		if ( $page ) {
			$archive_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-archive',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'Portfolio List', 'swissdelight-core' ),
					'description' => esc_html__( 'Settings related to portfolio archive pages', 'swissdelight-core' ),
				)
			);

			// Hook to include additional options after archive module options
			do_action( 'swissdelight_core_action_after_portfolio_options_archive', $archive_tab );

			$single_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-single',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'Portfolio Single', 'swissdelight-core' ),
					'description' => esc_html__( 'Settings related to portfolio single pages', 'swissdelight-core' ),
				)
			);
			$single_tab->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_portfolio_single_layout',
					'title'         => esc_html__( 'Single Layout', 'swissdelight-core' ),
					'description'   => esc_html__( 'Choose default layout for portfolio single', 'swissdelight-core' ),
					'default_value' => apply_filters( 'swissdelight_core_filter_portfolio_single_layout_default_value', '' ),
					'options'       => apply_filters( 'swissdelight_core_filter_portfolio_single_layout_options', array() ),
				)
			);

			$single_tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_portfolio_single_title_tag',
					'title'       => esc_html__( 'Title Tag', 'swissdelight-core' ),
					'description' => esc_html__( 'Choose title tag for portfolio item on portfolio single page', 'swissdelight-core' ),
					'options'     => swissdelight_core_get_select_type_options_pool( 'title_tag' ),
				)
			);

			// Hook to include additional options after single module options
			do_action( 'swissdelight_core_action_after_portfolio_options_single', $single_tab );

			// Hook to include additional options after module options
			do_action( 'swissdelight_core_action_after_portfolio_options_map', $page );
		}
	}

	add_action( 'swissdelight_core_action_default_options_init', 'swissdelight_core_add_portfolio_options', swissdelight_core_get_admin_options_map_position( 'portfolio' ) );
}
