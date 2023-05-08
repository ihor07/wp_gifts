<?php

if ( ! function_exists( 'swissdelight_core_add_back_to_top_meta_box' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function swissdelight_core_add_back_to_top_meta_box( $general_tab ) {

		if ( $general_tab ) {
			$general_tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_back_to_top',
					'title'       => esc_html__( 'Enable Back to Top', 'swissdelight-core' ),
					'description' => esc_html__( 'Enable Back to Top element', 'swissdelight-core' ),
					'options'     => swissdelight_core_get_select_type_options_pool( 'yes_no' ),
				)
			);

			$general_tab->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_back_to_top_skin',
					'title'         => esc_html__( 'Back to Top Skin', 'swissdelight-core' ),
					'options'       => array(
						''      => esc_html__( 'Default', 'swissdelight-core' ),
						'light' => esc_html__( 'Light', 'swissdelight-core' ),
						'dark'  => esc_html__( 'Dark', 'swissdelight-core' ),
					),
					'default_value' => '',
					'dependency'    => array(
						'show' => array(
							'qodef_back_to_top' => array(
								'values'        => array( 'yes', '' ),
								'default_value' => 'yes',
							),
						),
					),
				)
			);
		}
	}

	add_action( 'swissdelight_core_action_after_general_page_meta_box_map', 'swissdelight_core_add_back_to_top_meta_box' );
}
