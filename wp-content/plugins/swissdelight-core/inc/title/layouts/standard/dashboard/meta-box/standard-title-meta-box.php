<?php

if ( ! function_exists( 'swissdelight_core_add_standard_title_layout_meta_box' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function swissdelight_core_add_standard_title_layout_meta_box( $page ) {

		if ( $page ) {
			$section = $page->add_section_element(
				array(
					'name'       => 'qodef_standard_title_section',
					'title'      => esc_html__( 'Standard Title', 'swissdelight-core' ),
					'dependency' => array(
						'show' => array(
							'qodef_title_layout' => array(
								'values'        => array( '', 'standard' ),
								'default_value' => '',
							),
						),
					),
				)
			);

			$section->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_page_title_subtitle',
					'title'      => esc_html__( 'Subtitle', 'swissdelight-core' ),
				)
			);

			$section->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qodef_page_title_subtitle_color',
					'title'      => esc_html__( 'Subtitle Color', 'swissdelight-core' ),
				)
			);

			$section->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_page_title_subtitle_bottom_margin',
					'title'      => esc_html__( 'Subtitle Bottom Margin', 'swissdelight-core' ),
					'args'       => array(
						'suffix' => esc_html__( 'px', 'swissdelight-core' ),
					),
				)
			);

			$section->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_page_title_cursive',
					'title'      => esc_html__( 'Cursive Text', 'swissdelight-core' ),
				)
			);

			$section->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qodef_page_title_cursive_color',
					'title'      => esc_html__( 'Cursive Color', 'swissdelight-core' ),
				)
			);

			// Hook to include additional options after module options
			do_action( 'swissdelight_core_action_after_standard_title_layout_meta_box_map', $section );
		}
	}

	add_action( 'swissdelight_core_action_after_page_title_meta_box_map', 'swissdelight_core_add_standard_title_layout_meta_box' );
}
