<?php
if ( ! function_exists( 'swissdelight_core_add_top_area_meta_options' ) ) {
	/**
	 * Function that add additional header layout meta box options
	 *
	 * @param object $page
	 */
	function swissdelight_core_add_top_area_meta_options( $page ) {
		$top_area_section = $page->add_section_element(
			array(
				'name'       => 'qodef_top_area_section',
				'title'      => esc_html__( 'Top Area', 'swissdelight-core' ),
				'dependency' => array(
					'hide' => array(
						'qodef_header_layout' => array(
							'values'        => swissdelight_core_dependency_for_top_area_options(),
							'default_value' => '',
						),
					),
				),
			)
		);

		$top_area_section->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_top_area_header',
				'title'       => esc_html__( 'Top Area', 'swissdelight-core' ),
				'description' => esc_html__( 'Enable top area', 'swissdelight-core' ),
				'options'     => swissdelight_core_get_select_type_options_pool( 'yes_no' ),
			)
		);

		$top_area_options_section = $top_area_section->add_section_element(
			array(
				'name'        => 'qodef_top_area_options_section',
				'title'       => esc_html__( 'Top Area Options', 'swissdelight-core' ),
				'description' => esc_html__( 'Set desired values for top area', 'swissdelight-core' ),
				'dependency'  => array(
					'show' => array(
						'qodef_top_area_header' => array(
							'values'        => 'yes',
							'default_value' => 'no',
						),
					),
				),
			)
		);

		$top_area_options_section->add_field_element(
			array(
				'field_type'    => 'yesno',
				'name'          => 'qodef_top_area_header_in_grid',
				'title'         => esc_html__( 'Content in Grid', 'swissdelight-core' ),
				'description'   => esc_html__( 'Set content to be in grid', 'swissdelight-core' ),
				'default_value' => 'no',
			)
		);

		$top_area_options_section->add_field_element(
			array(
				'field_type'    => 'select',
				'name'          => 'qodef_top_area_header_skin',
				'title'         => esc_html__( 'Top Area Skin', 'swissdelight-core' ),
				'description'   => esc_html__( 'Choose top area skin', 'swissdelight-core' ),
				'options'       => array(
					''      => esc_html__( 'Default', 'swissdelight-core' ),
					'light' => esc_html__( 'Light', 'swissdelight-core' ),
					'dark'  => esc_html__( 'Dark', 'swissdelight-core' ),
				),
				'default_value' => '',
			)
		);

		$top_area_options_section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_top_area_header_background_color',
				'title'       => esc_html__( 'Top Area Background Color', 'swissdelight-core' ),
				'description' => esc_html__( 'Choose top area background color', 'swissdelight-core' ),
			)
		);

		$top_area_options_section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_top_area_header_height',
				'title'       => esc_html__( 'Top Area Height', 'swissdelight-core' ),
				'description' => esc_html__( 'Enter top area height (default is 30px)', 'swissdelight-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px', 'swissdelight-core' ),
				),
			)
		);

		$top_area_options_section->add_field_element(
			array(
				'field_type' => 'text',
				'name'       => 'qodef_top_area_header_side_padding',
				'title'      => esc_html__( 'Top Area Side Padding', 'swissdelight-core' ),
				'args'       => array(
					'suffix' => esc_html__( 'px or %', 'swissdelight-core' ),
				),
			)
		);

		$custom_sidebars = swissdelight_core_get_custom_sidebars();
		if ( ! empty( $custom_sidebars ) && count( $custom_sidebars ) > 1 ) {
			$top_area_options_section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_top_area_header_custom_widget_area_left',
					'title'       => esc_html__( 'Choose Custom Left Widget Area for Top Area Header', 'swissdelight-core' ),
					'description' => esc_html__( 'Choose custom widget area to display in top area header inside left widget area', 'swissdelight-core' ),
					'options'     => $custom_sidebars,
				)
			);

			$top_area_options_section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_top_area_header_custom_widget_area_right',
					'title'       => esc_html__( 'Choose Custom Right Widget Area for Top Area Header', 'swissdelight-core' ),
					'description' => esc_html__( 'Choose custom widget area to display in top area header inside right widget area', 'swissdelight-core' ),
					'options'     => $custom_sidebars,
				)
			);
		}
	}

	add_action( 'swissdelight_core_action_after_page_header_meta_map', 'swissdelight_core_add_top_area_meta_options', 20 );
}
