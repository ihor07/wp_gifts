<?php

if ( ! function_exists( 'swissdelight_core_add_fonts_options' ) ) {
	/**
	 * Function that add options for this module
	 */
	function swissdelight_core_add_fonts_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => SWISSDELIGHT_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'fonts',
				'title'       => esc_html__( 'Fonts', 'swissdelight-core' ),
				'description' => esc_html__( 'Global Fonts Options', 'swissdelight-core' ),
				'icon'        => 'fa fa-cog',
			)
		);

		if ( $page ) {
			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_google_fonts',
					'title'         => esc_html__( 'Enable Google Fonts', 'swissdelight-core' ),
					'default_value' => 'yes',
					'args'          => array(
						'custom_class' => 'qodef-enable-google-fonts',
					),
				)
			);

			$google_fonts_section = $page->add_section_element(
				array(
					'name'       => 'qodef_google_fonts_section',
					'title'      => esc_html__( 'Google Fonts Options', 'swissdelight-core' ),
					'dependency' => array(
						'show' => array(
							'qodef_enable_google_fonts' => array(
								'values'        => 'yes',
								'default_value' => '',
							),
						),
					),
				)
			);

			$page_repeater = $google_fonts_section->add_repeater_element(
				array(
					'name'        => 'qodef_choose_google_fonts',
					'title'       => esc_html__( 'Google Fonts to Include', 'swissdelight-core' ),
					'description' => esc_html__( 'Choose Google Fonts which you want to use on your website', 'swissdelight-core' ),
					'button_text' => esc_html__( 'Add New Google Font', 'swissdelight-core' ),
				)
			);

			$page_repeater->add_field_element(
				array(
					'field_type'  => 'googlefont',
					'name'        => 'qodef_choose_google_font',
					'title'       => esc_html__( 'Google Font', 'swissdelight-core' ),
					'description' => esc_html__( 'Choose Google Font', 'swissdelight-core' ),
					'args'        => array(
						'include' => 'google-fonts',
					),
				)
			);

			$google_fonts_section->add_field_element(
				array(
					'field_type'  => 'checkbox',
					'name'        => 'qodef_google_fonts_weight',
					'title'       => esc_html__( 'Google Fonts Weight', 'swissdelight-core' ),
					'description' => esc_html__( 'Choose a default Google Fonts weights for your website. Impact on page load time', 'swissdelight-core' ),
					'options'     => array(
						'100'  => esc_html__( '100 Thin', 'swissdelight-core' ),
						'100i' => esc_html__( '100 Thin Italic', 'swissdelight-core' ),
						'200'  => esc_html__( '200 Extra-Light', 'swissdelight-core' ),
						'200i' => esc_html__( '200 Extra-Light Italic', 'swissdelight-core' ),
						'300'  => esc_html__( '300 Light', 'swissdelight-core' ),
						'300i' => esc_html__( '300 Light Italic', 'swissdelight-core' ),
						'400'  => esc_html__( '400 Regular', 'swissdelight-core' ),
						'400i' => esc_html__( '400 Regular Italic', 'swissdelight-core' ),
						'500'  => esc_html__( '500 Medium', 'swissdelight-core' ),
						'500i' => esc_html__( '500 Medium Italic', 'swissdelight-core' ),
						'600'  => esc_html__( '600 Semi-Bold', 'swissdelight-core' ),
						'600i' => esc_html__( '600 Semi-Bold Italic', 'swissdelight-core' ),
						'700'  => esc_html__( '700 Bold', 'swissdelight-core' ),
						'700i' => esc_html__( '700 Bold Italic', 'swissdelight-core' ),
						'800'  => esc_html__( '800 Extra-Bold', 'swissdelight-core' ),
						'800i' => esc_html__( '800 Extra-Bold Italic', 'swissdelight-core' ),
						'900'  => esc_html__( '900 Ultra-Bold', 'swissdelight-core' ),
						'900i' => esc_html__( '900 Ultra-Bold Italic', 'swissdelight-core' ),
					),
				)
			);

			$google_fonts_section->add_field_element(
				array(
					'field_type'  => 'checkbox',
					'name'        => 'qodef_google_fonts_subset',
					'title'       => esc_html__( 'Google Fonts Style', 'swissdelight-core' ),
					'description' => esc_html__( 'Choose a default Google Fonts style for your website. Impact on page load time', 'swissdelight-core' ),
					'options'     => array(
						'latin'        => esc_html__( 'Latin', 'swissdelight-core' ),
						'latin-ext'    => esc_html__( 'Latin Extended', 'swissdelight-core' ),
						'cyrillic'     => esc_html__( 'Cyrillic', 'swissdelight-core' ),
						'cyrillic-ext' => esc_html__( 'Cyrillic Extended', 'swissdelight-core' ),
						'greek'        => esc_html__( 'Greek', 'swissdelight-core' ),
						'greek-ext'    => esc_html__( 'Greek Extended', 'swissdelight-core' ),
						'vietnamese'   => esc_html__( 'Vietnamese', 'swissdelight-core' ),
					),
				)
			);

			$page_repeater = $page->add_repeater_element(
				array(
					'name'        => 'qodef_custom_fonts',
					'title'       => esc_html__( 'Custom Fonts', 'swissdelight-core' ),
					'description' => esc_html__( 'Add custom fonts', 'swissdelight-core' ),
					'button_text' => esc_html__( 'Add New Custom Font', 'swissdelight-core' ),
				)
			);

			$page_repeater->add_field_element(
				array(
					'field_type' => 'file',
					'name'       => 'qodef_custom_font_ttf',
					'title'      => esc_html__( 'Custom Font TTF', 'swissdelight-core' ),
					'args'       => array(
						'allowed_type' => 'application/octet-stream',
					),
				)
			);

			$page_repeater->add_field_element(
				array(
					'field_type' => 'file',
					'name'       => 'qodef_custom_font_otf',
					'title'      => esc_html__( 'Custom Font OTF', 'swissdelight-core' ),
					'args'       => array(
						'allowed_type' => 'application/octet-stream',
					),
				)
			);

			$page_repeater->add_field_element(
				array(
					'field_type' => 'file',
					'name'       => 'qodef_custom_font_woff',
					'title'      => esc_html__( 'Custom Font WOFF', 'swissdelight-core' ),
					'args'       => array(
						'allowed_type' => 'application/octet-stream',
					),
				)
			);

			$page_repeater->add_field_element(
				array(
					'field_type' => 'file',
					'name'       => 'qodef_custom_font_woff2',
					'title'      => esc_html__( 'Custom Font WOFF2', 'swissdelight-core' ),
					'args'       => array(
						'allowed_type' => 'application/octet-stream',
					),
				)
			);

			$page_repeater->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_custom_font_name',
					'title'      => esc_html__( 'Custom Font Name', 'swissdelight-core' ),
				)
			);

			// Hook to include additional options after module options
			do_action( 'swissdelight_core_action_after_page_fonts_options_map', $page );
		}
	}

	add_action( 'swissdelight_core_action_default_options_init', 'swissdelight_core_add_fonts_options', swissdelight_core_get_admin_options_map_position( 'fonts' ) );
}
