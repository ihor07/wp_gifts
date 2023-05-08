<?php

if ( ! function_exists( 'swissdelight_core_add_centered_header_meta' ) ) {
	/**
	 * Function that add additional header layout meta box options
	 *
	 * @param object $page
	 */
	function swissdelight_core_add_centered_header_meta( $page ) {

		$section = $page->add_section_element(
			array(
				'name'       => 'qodef_centered_header_section',
				'title'      => esc_html__( 'Centered Header', 'swissdelight-core' ),
				'dependency' => array(
					'show' => array(
						'qodef_header_layout' => array(
							'values'        => 'centered',
							'default_value' => '',
						),
					),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_centered_header_height',
				'title'       => esc_html__( 'Header Height', 'swissdelight-core' ),
				'description' => esc_html__( 'Enter header height', 'swissdelight-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px', 'swissdelight-core' ),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_centered_header_background_color',
				'title'       => esc_html__( 'Header Background Color', 'swissdelight-core' ),
				'description' => esc_html__( 'Enter header background color', 'swissdelight-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px', 'swissdelight-core' ),
				),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_centered_header_border_color',
				'title'       => esc_html__( 'Header Border Color', 'swissdelight-core' ),
				'description' => esc_html__( 'Enter header border color', 'swissdelight-core' ),
			)
		);

		$section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_centered_header_border_width',
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
				'name'        => 'qodef_centered_header_border_style',
				'title'       => esc_html__( 'Header Border Style', 'swissdelight-core' ),
				'description' => esc_html__( 'Choose header border style', 'swissdelight-core' ),
				'options'     => swissdelight_core_get_select_type_options_pool( 'border_style' ),
			)
		);
	}

	add_action( 'swissdelight_core_action_after_page_header_meta_map', 'swissdelight_core_add_centered_header_meta' );
}
