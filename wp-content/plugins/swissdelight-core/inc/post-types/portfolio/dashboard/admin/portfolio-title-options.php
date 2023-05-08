<?php

if ( ! function_exists( 'swissdelight_core_add_portfolio_title_options' ) ) {
	/**
	 * Function that add title options for portfolio module
	 */
	function swissdelight_core_add_portfolio_title_options( $tab ) {

		if ( $tab ) {
			$tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_enable_portfolio_title',
					'title'       => esc_html__( 'Enable Title on Portfolio Single', 'swissdelight-core' ),
					'description' => esc_html__( 'Use this option to enable/disable portfolio single title', 'swissdelight-core' ),
					'options'     => swissdelight_core_get_select_type_options_pool( 'yes_no' ),
				)
			);

			$tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_enable_portfolio_tags',
					'title'       => esc_html__( 'Enable Tags on Portfolio Single', 'swissdelight-core' ),
					'description' => esc_html__( 'Use this option to enable/disable portfolio single tags', 'swissdelight-core' ),
					'options'     => swissdelight_core_get_select_type_options_pool( 'no_yes', false ),
				)
			);

			$tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_set_portfolio_title_area_in_grid',
					'title'       => esc_html__( 'Portfolio Title in Grid', 'swissdelight-core' ),
					'description' => esc_html__( 'Enabling this option will set portfolio title area to be in grid', 'swissdelight-core' ),
					'options'     => swissdelight_core_get_select_type_options_pool( 'yes_no' ),
				)
			);
		}
	}

	add_action( 'swissdelight_core_action_after_portfolio_options_single', 'swissdelight_core_add_portfolio_title_options' );
}
