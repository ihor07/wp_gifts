<?php

if ( ! function_exists( 'swissdelight_core_add_page_title_meta_box' ) ) {
	/**
	 * Function that add general meta box options for this module
	 *
	 * @param object $page
	 */
	function swissdelight_core_add_page_title_meta_box( $page ) {

		if ( $page ) {

			$title_tab = $page->add_tab_element(
				array(
					'name'        => 'tab-title',
					'icon'        => 'fa fa-cog',
					'title'       => esc_html__( 'Title Settings', 'swissdelight-core' ),
					'description' => esc_html__( 'Title layout settings', 'swissdelight-core' ),
				)
			);

			$title_tab->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_enable_page_title',
					'title'       => esc_html__( 'Enable Page Title', 'swissdelight-core' ),
					'description' => esc_html__( 'Use this option to enable/disable page title', 'swissdelight-core' ),
					'options'     => swissdelight_core_get_select_type_options_pool( 'no_yes' ),
				)
			);

			$page_title_section = $title_tab->add_section_element(
				array(
					'name'       => 'qodef_page_title_section',
					'title'      => esc_html__( 'Title Area', 'swissdelight-core' ),
					'dependency' => array(
						'hide' => array(
							'qodef_enable_page_title' => array(
								'values'        => 'no',
								'default_value' => '',
							),
						),
					),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_title_layout',
					'title'       => esc_html__( 'Title Layout', 'swissdelight-core' ),
					'description' => esc_html__( 'Choose a title layout', 'swissdelight-core' ),
					'options'     => apply_filters( 'swissdelight_core_filter_title_layout_options', $layouts = array( '' => esc_html__( 'Default', 'swissdelight-core' ) ) ),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_set_page_title_area_in_grid',
					'title'       => esc_html__( 'Page Title In Grid', 'swissdelight-core' ),
					'description' => esc_html__( 'Enabling this option will set page title area to be in grid', 'swissdelight-core' ),
					'options'     => swissdelight_core_get_select_type_options_pool( 'no_yes' ),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_page_title_height',
					'title'       => esc_html__( 'Height', 'swissdelight-core' ),
					'description' => esc_html__( 'Enter title height', 'swissdelight-core' ),
					'args'        => array(
						'suffix' => esc_html__( 'px', 'swissdelight-core' ),
					),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_page_title_height_on_smaller_screens',
					'title'       => esc_html__( 'Height on Smaller Screens', 'swissdelight-core' ),
					'description' => esc_html__( 'Enter title height to be displayed on smaller screens with active mobile header', 'swissdelight-core' ),
					'args'        => array(
						'suffix' => esc_html__( 'px', 'swissdelight-core' ),
					),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'  => 'color',
					'name'        => 'qodef_page_title_background_color',
					'title'       => esc_html__( 'Background Color', 'swissdelight-core' ),
					'description' => esc_html__( 'Enter page title area background color', 'swissdelight-core' ),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'  => 'image',
					'name'        => 'qodef_page_title_background_image',
					'title'       => esc_html__( 'Background Image', 'swissdelight-core' ),
					'description' => esc_html__( 'Enter page title area background image', 'swissdelight-core' ),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef_page_title_background_image_behavior',
					'title'      => esc_html__( 'Background Image Behavior', 'swissdelight-core' ),
					'options'    => array(
						''           => esc_html__( 'Default', 'swissdelight-core' ),
						'responsive' => esc_html__( 'Set Responsive Image', 'swissdelight-core' ),
						'parallax'   => esc_html__( 'Set Parallax Image', 'swissdelight-core' ),
					),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type' => 'color',
					'name'       => 'qodef_page_title_color',
					'title'      => esc_html__( 'Title Color', 'swissdelight-core' ),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_page_title_tag',
					'title'         => esc_html__( 'Title Tag', 'swissdelight-core' ),
					'description'   => esc_html__( 'Enabling this option will set title tag', 'swissdelight-core' ),
					'options'       => swissdelight_core_get_select_type_options_pool( 'title_tag' ),
					'default_value' => '',
					'dependency'    => array(
						'show' => array(
							'qodef_title_layout' => array(
								'values'        => array( 'standard-with-breadcrumbs', 'standard' ),
								'default_value' => '',
							),
						),
					),
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_page_title_text_alignment',
					'title'         => esc_html__( 'Text Alignment', 'swissdelight-core' ),
					'options'       => array(
						''       => esc_html__( 'Default', 'swissdelight-core' ),
						'left'   => esc_html__( 'Left', 'swissdelight-core' ),
						'center' => esc_html__( 'Center', 'swissdelight-core' ),
						'right'  => esc_html__( 'Right', 'swissdelight-core' ),
					),
					'default_value' => '',
				)
			);

			$page_title_section->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_page_title_vertical_text_alignment',
					'title'         => esc_html__( 'Vertical Text Alignment', 'swissdelight-core' ),
					'options'       => array(
						''              => esc_html__( 'Default', 'swissdelight-core' ),
						'header-bottom' => esc_html__( 'From Bottom of Header', 'swissdelight-core' ),
						'window-top'    => esc_html__( 'From Window Top', 'swissdelight-core' ),
					),
					'default_value' => '',
				)
			);

			// Hook to include additional options after module options
			do_action( 'swissdelight_core_action_after_page_title_meta_box_map', $page_title_section );
		}
	}

	add_action( 'swissdelight_core_action_after_general_meta_box_map', 'swissdelight_core_add_page_title_meta_box' );
}

if ( ! function_exists( 'swissdelight_core_add_general_page_title_meta_box_callback' ) ) {
	/**
	 * Function that set current meta box callback as general callback functions
	 *
	 * @param array $callbacks
	 *
	 * @return array
	 */
	function swissdelight_core_add_general_page_title_meta_box_callback( $callbacks ) {
		$callbacks['page-title'] = 'swissdelight_core_add_page_title_meta_box';

		return $callbacks;
	}

	add_filter( 'swissdelight_core_filter_general_meta_box_callbacks', 'swissdelight_core_add_general_page_title_meta_box_callback' );
}
