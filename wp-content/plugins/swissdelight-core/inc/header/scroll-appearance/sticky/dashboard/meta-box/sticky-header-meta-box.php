<?php

if ( ! function_exists( 'swissdelight_core_add_sticky_header_meta_options' ) ) {
	/**
	 * Function that add additional meta box options for current module
	 *
	 * @param object $section
	 * @param array $custom_sidebars
	 */
	function swissdelight_core_add_sticky_header_meta_options( $section, $custom_sidebars ) {

		if ( $section ) {

			$sticky_section = $section->add_section_element(
				array(
					'name'       => 'qodef_sticky_header_section',
					'dependency' => array(
						'show' => array(
							'qodef_header_scroll_appearance' => array(
								'values'        => 'sticky',
								'default_value' => '',
							),
						),
					),
				)
			);

			$sticky_section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_sticky_header_appearance',
					'title'       => esc_html__( 'Sticky Header Appearance', 'swissdelight-core' ),
					'description' => esc_html__( 'Select the appearance of sticky header when you scrolling the page', 'swissdelight-core' ),
					'options'     => array(
						''     => esc_html__( 'Default', 'swissdelight-core' ),
						'down' => esc_html__( 'Show Sticky on Scroll Down/Up', 'swissdelight-core' ),
						'up'   => esc_html__( 'Show Sticky on Scroll Up', 'swissdelight-core' ),
					),
				)
			);

			$sticky_section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_sticky_header_skin',
					'title'       => esc_html__( 'Sticky Header Skin', 'swissdelight-core' ),
					'description' => esc_html__( 'Choose a predefined sticky header style for header elements', 'swissdelight-core' ),
					'options'     => array(
						''      => esc_html__( 'Default', 'swissdelight-core' ),
						'none'  => esc_html__( 'None', 'swissdelight-core' ),
						'light' => esc_html__( 'Light', 'swissdelight-core' ),
						'dark'  => esc_html__( 'Dark', 'swissdelight-core' ),
					),
				)
			);

			$sticky_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_sticky_header_scroll_amount',
					'title'       => esc_html__( 'Sticky Scroll Amount', 'swissdelight-core' ),
					'description' => esc_html__( 'Enter scroll amount for sticky header to appear', 'swissdelight-core' ),
					'args'        => array(
						'suffix' => esc_html__( 'px', 'swissdelight-core' ),
					),
				)
			);

			$sticky_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_sticky_header_side_padding',
					'title'       => esc_html__( 'Sticky Header Side Padding', 'swissdelight-core' ),
					'description' => esc_html__( 'Enter side padding for sticky header area', 'swissdelight-core' ),
					'args'        => array(
						'suffix' => esc_html__( 'px or %', 'swissdelight-core' ),
					),
				)
			);

			$sticky_section->add_field_element(
				array(
					'field_type'  => 'color',
					'name'        => 'qodef_sticky_header_background_color',
					'title'       => esc_html__( 'Sticky Header Background Color', 'swissdelight-core' ),
					'description' => esc_html__( 'Enter sticky header background color', 'swissdelight-core' ),
				)
			);

			$sticky_section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_sticky_header_custom_widget_area_one',
					'title'       => esc_html__( 'Choose Custom Sticky Header Widget Area One', 'swissdelight-core' ),
					'description' => esc_html__( 'Choose custom widget area to display in sticky header widget area one', 'swissdelight-core' ),
					'options'     => $custom_sidebars,
				)
			);

			$sticky_section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_sticky_header_custom_widget_area_two',
					'title'       => esc_html__( 'Choose Custom Sticky Header Widget Area Two', 'swissdelight-core' ),
					'description' => esc_html__( 'Choose custom widget area to display in sticky header widget area two', 'swissdelight-core' ),
					'options'     => $custom_sidebars,
				)
			);
		}
	}

	add_action( 'swissdelight_core_action_after_header_scroll_appearance_meta_options_map', 'swissdelight_core_add_sticky_header_meta_options', 10, 2 );
}

if ( ! function_exists( 'swissdelight_core_add_sticky_header_logo_meta_options' ) ) {
	/**
	 * Function that add additional header logo meta box options
	 *
	 * @param object $logo_tab
	 * @param array $header_logo_section
	 */
	function swissdelight_core_add_sticky_header_logo_meta_options( $logo_tab, $header_logo_section ) {

		if ( $header_logo_section ) {

			$header_logo_section->add_field_element(
				array(
					'field_type'  => 'image',
					'name'        => 'qodef_logo_sticky',
					'title'       => esc_html__( 'Logo - Sticky', 'swissdelight-core' ),
					'description' => esc_html__( 'Choose sticky logo image', 'swissdelight-core' ),
					'multiple'    => 'no',
				)
			);
		}
	}

	add_action( 'swissdelight_core_action_after_page_logo_meta_map', 'swissdelight_core_add_sticky_header_logo_meta_options', 10, 2 );
}
