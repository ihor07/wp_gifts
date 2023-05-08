<?php

if ( ! function_exists( 'swissdelight_core_add_general_page_meta_box' ) ) {
	/**
	 * Function that add general meta box options for this module
	 *
	 * @param object $page
	 */
	function swissdelight_core_add_general_page_meta_box( $page ) {

		$general_tab = $page->add_tab_element(
			array(
				'name'        => 'tab-page',
				'icon'        => 'fa fa-cog',
				'title'       => esc_html__( 'Page Settings', 'swissdelight-core' ),
				'description' => esc_html__( 'General page layout settings', 'swissdelight-core' ),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_page_background_color',
				'title'       => esc_html__( 'Page Background Color', 'swissdelight-core' ),
				'description' => esc_html__( 'Set background color', 'swissdelight-core' ),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'image',
				'name'        => 'qodef_page_background_image',
				'title'       => esc_html__( 'Page Background Image', 'swissdelight-core' ),
				'description' => esc_html__( 'Set background image', 'swissdelight-core' ),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_page_background_repeat',
				'title'       => esc_html__( 'Page Background Image Repeat', 'swissdelight-core' ),
				'description' => esc_html__( 'Set background image repeat', 'swissdelight-core' ),
				'options'     => array(
					''          => esc_html__( 'Default', 'swissdelight-core' ),
					'no-repeat' => esc_html__( 'No Repeat', 'swissdelight-core' ),
					'repeat'    => esc_html__( 'Repeat', 'swissdelight-core' ),
					'repeat-x'  => esc_html__( 'Repeat-x', 'swissdelight-core' ),
					'repeat-y'  => esc_html__( 'Repeat-y', 'swissdelight-core' ),
				),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_page_background_size',
				'title'       => esc_html__( 'Page Background Image Size', 'swissdelight-core' ),
				'description' => esc_html__( 'Set background image size', 'swissdelight-core' ),
				'options'     => array(
					''        => esc_html__( 'Default', 'swissdelight-core' ),
					'contain' => esc_html__( 'Contain', 'swissdelight-core' ),
					'cover'   => esc_html__( 'Cover', 'swissdelight-core' ),
				),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_page_background_attachment',
				'title'       => esc_html__( 'Page Background Image Attachment', 'swissdelight-core' ),
				'description' => esc_html__( 'Set background image attachment', 'swissdelight-core' ),
				'options'     => array(
					''       => esc_html__( 'Default', 'swissdelight-core' ),
					'fixed'  => esc_html__( 'Fixed', 'swissdelight-core' ),
					'scroll' => esc_html__( 'Scroll', 'swissdelight-core' ),
				),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_page_content_padding',
				'title'       => esc_html__( 'Page Content Padding', 'swissdelight-core' ),
				'description' => esc_html__( 'Set padding that will be applied for page content in format: top right bottom left (e.g. 10px 5px 10px 5px)', 'swissdelight-core' ),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_page_content_padding_mobile',
				'title'       => esc_html__( 'Page Content Padding Mobile', 'swissdelight-core' ),
				'description' => esc_html__( 'Set padding that will be applied for page content on mobile screens (1024px and below) in format: top right bottom left (e.g. 10px 5px 10px 5px)', 'swissdelight-core' ),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'    => 'select',
				'name'          => 'qodef_boxed',
				'title'         => esc_html__( 'Boxed Layout', 'swissdelight-core' ),
				'description'   => esc_html__( 'Set boxed layout', 'swissdelight-core' ),
				'default_value' => '',
				'options'       => swissdelight_core_get_select_type_options_pool( 'yes_no' ),
			)
		);

		$boxed_section = $general_tab->add_section_element(
			array(
				'name'       => 'qodef_boxed_section',
				'title'      => esc_html__( 'Boxed Layout Section', 'swissdelight-core' ),
				'dependency' => array(
					'hide' => array(
						'qodef_boxed' => array(
							'values'        => 'no',
							'default_value' => '',
						),
					),
				),
			)
		);

		$boxed_section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_boxed_background_color',
				'title'       => esc_html__( 'Boxed Background Color', 'swissdelight-core' ),
				'description' => esc_html__( 'Set boxed background color', 'swissdelight-core' ),
			)
		);

		$boxed_section->add_field_element(
			array(
				'field_type'  => 'image',
				'name'        => 'qodef_boxed_background_pattern',
				'title'       => esc_html__( 'Boxed Background Pattern', 'swissdelight-core' ),
				'description' => esc_html__( 'Set boxed background pattern', 'swissdelight-core' ),
			)
		);

		$boxed_section->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_boxed_background_pattern_behavior',
				'title'       => esc_html__( 'Boxed Background Pattern Behavior', 'swissdelight-core' ),
				'description' => esc_html__( 'Set boxed background pattern behavior', 'swissdelight-core' ),
				'options'     => array(
					''       => esc_html__( 'Default', 'swissdelight-core' ),
					'fixed'  => esc_html__( 'Fixed', 'swissdelight-core' ),
					'scroll' => esc_html__( 'Scroll', 'swissdelight-core' ),
				),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'    => 'select',
				'name'          => 'qodef_passepartout',
				'title'         => esc_html__( 'Passepartout', 'swissdelight-core' ),
				'description'   => esc_html__( 'Enabling this option will display a passepartout around website content', 'swissdelight-core' ),
				'default_value' => '',
				'options'       => swissdelight_core_get_select_type_options_pool( 'yes_no' ),
			)
		);

		$passepartout_section = $general_tab->add_section_element(
			array(
				'name'       => 'qodef_passepartout_section',
				'dependency' => array(
					'hide' => array(
						'qodef_passepartout' => array(
							'values'        => 'no',
							'default_value' => '',
						),
					),
				),
			)
		);

		$passepartout_section->add_field_element(
			array(
				'field_type'  => 'color',
				'name'        => 'qodef_passepartout_color',
				'title'       => esc_html__( 'Passepartout Color', 'swissdelight-core' ),
				'description' => esc_html__( 'Choose background color for passepartout', 'swissdelight-core' ),
			)
		);

		$passepartout_section->add_field_element(
			array(
				'field_type'  => 'image',
				'name'        => 'qodef_passepartout_image',
				'title'       => esc_html__( 'Passepartout Background Image', 'swissdelight-core' ),
				'description' => esc_html__( 'Set background image for passepartout', 'swissdelight-core' ),
			)
		);

		$passepartout_section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_passepartout_size',
				'title'       => esc_html__( 'Passepartout Size', 'swissdelight-core' ),
				'description' => esc_html__( 'Enter size amount for passepartout', 'swissdelight-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px or %', 'swissdelight-core' ),
				),
			)
		);

		$passepartout_section->add_field_element(
			array(
				'field_type'  => 'text',
				'name'        => 'qodef_passepartout_size_responsive',
				'title'       => esc_html__( 'Passepartout Responsive Size', 'swissdelight-core' ),
				'description' => esc_html__( 'Enter size amount for passepartout for smaller screens (1024px and below)', 'swissdelight-core' ),
				'args'        => array(
					'suffix' => esc_html__( 'px or %', 'swissdelight-core' ),
				),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'  => 'select',
				'name'        => 'qodef_content_width',
				'title'       => esc_html__( 'Initial Width of Content', 'swissdelight-core' ),
				'description' => esc_html__( 'Choose the initial width of content which is in grid (applies to pages set to "Default Template" and rows set to "In Grid")', 'swissdelight-core' ),
				'options'     => swissdelight_core_get_select_type_options_pool( 'content_width' ),
			)
		);

		$general_tab->add_field_element(
			array(
				'field_type'    => 'yesno',
				'default_value' => 'no',
				'name'          => 'qodef_content_behind_header',
				'title'         => esc_html__( 'Always put content behind header', 'swissdelight-core' ),
				'description'   => esc_html__( 'Enabling this option will put page content behind page header', 'swissdelight-core' ),
			)
		);

		// Hook to include additional options after module options
		do_action( 'swissdelight_core_action_after_general_page_meta_box_map', $general_tab );
	}

	add_action( 'swissdelight_core_action_after_general_meta_box_map', 'swissdelight_core_add_general_page_meta_box', 9 );
}

if ( ! function_exists( 'swissdelight_core_add_general_page_meta_box_callback' ) ) {
	/**
	 * Function that set current meta box callback as general callback functions
	 *
	 * @param array $callbacks
	 *
	 * @return array
	 */
	function swissdelight_core_add_general_page_meta_box_callback( $callbacks ) {
		$callbacks['page'] = 'swissdelight_core_add_general_page_meta_box';

		return $callbacks;
	}

	add_filter( 'swissdelight_core_filter_general_meta_box_callbacks', 'swissdelight_core_add_general_page_meta_box_callback' );
}
