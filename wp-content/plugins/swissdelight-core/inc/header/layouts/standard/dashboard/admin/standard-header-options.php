<?php

if ( ! function_exists( 'swissdelight_core_add_standard_header_options' ) ) {
	/**
	 * Function that add additional header layout options
	 *
	 * @param object $page
	 * @param array $general_header_tab
	 */
	function swissdelight_core_add_standard_header_options( $page, $general_header_tab ) {

		$section = $general_header_tab->add_section_element(
			array(
				'name'        => 'qodef_standard_header_section',
				'title'       => esc_html__( 'Standard Header', 'swissdelight-core' ),
				'description' => esc_html__( 'Standard header settings', 'swissdelight-core' ),
				'dependency'  => array(
					'show' => array(
						'qodef_header_layout' => array(
							'values'        => 'standard',
							'default_value' => '',
						),
					),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'    => 'yesno',
				'name'          => 'qodef_standard_header_in_grid',
				'title'         => esc_html__( 'Content in Grid', 'swissdelight-core' ),
				'description'   => esc_html__( 'Set content to be in grid', 'swissdelight-core' ),
				'default_value' => 'no',
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_standard_header_height',
				'title'       => esc_html__( 'Header Height', 'swissdelight-core' ),
				'description' => esc_html__( 'Enter header height', 'swissdelight-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px', 'swissdelight-core' ),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_standard_header_side_padding',
				'title'       => esc_html__( 'Header Side Padding', 'swissdelight-core' ),
				'description' => esc_html__( 'Enter side padding for header area', 'swissdelight-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px or %', 'swissdelight-core' ),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_standard_header_background_color',
				'title'       => esc_html__( 'Header Background Color', 'swissdelight-core' ),
				'description' => esc_html__( 'Enter header background color', 'swissdelight-core' ),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_standard_header_border_color',
				'title'       => esc_html__( 'Header Border Color', 'swissdelight-core' ),
				'description' => esc_html__( 'Enter header border color', 'swissdelight-core' ),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_standard_header_border_width',
				'title'       => esc_html__( 'Header Border Width', 'swissdelight-core' ),
				'description' => esc_html__( 'Enter header border width size', 'swissdelight-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px', 'swissdelight-core' ),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_standard_header_border_style',
				'title'       => esc_html__( 'Header Border Style', 'swissdelight-core' ),
				'description' => esc_html__( 'Choose header border style', 'swissdelight-core' ),
				'options'     => swissdelight_core_get_select_type_options_pool( 'border_style' ),
			)
		);

		$section->add_field_element(
			array(
				'field_type'    => 'select',
				'name'          => 'qodef_standard_header_menu_position',
				'title'         => esc_html__( 'Menu position', 'swissdelight-core' ),
				'default_value' => 'right',
				'options'       => array(
					'left'   => esc_html__( 'Left', 'swissdelight-core' ),
					'center' => esc_html__( 'Center', 'swissdelight-core' ),
					'right'  => esc_html__( 'Right', 'swissdelight-core' ),
				),
			)
		);
	}

	add_action( 'swissdelight_core_action_after_header_options_map', 'swissdelight_core_add_standard_header_options', 10, 2 );
}
