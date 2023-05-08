<?php

if ( ! function_exists( 'swissdelight_membership_add_general_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function swissdelight_membership_add_general_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => SWISSDELIGHT_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'membership',
				'icon'        => 'fa fa-envelope',
				'title'       => esc_html__( 'Membership', 'swissdelight-membership' ),
				'description' => esc_html__( 'Membership Settings', 'swissdelight-membership' ),
			)
		);

		if ( $page ) {

			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_membership_privacy_policy_enable',
					'title'         => esc_html__( 'Enable Privacy Policy Text', 'swissdelight-membership' ),
					'description'   => esc_html__( 'Enable privacy policy text for registration modal form', 'swissdelight-membership' ),
					'default_value' => 'yes',
				)
			);

			$page->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_membership_privacy_policy_text',
					'title'       => esc_html__( 'Privacy Policy Text', 'swissdelight-membership' ),
					'description' => esc_html__( 'Enter privacy policy text for registration modal form', 'swissdelight-membership' ),
					'dependency'    => array(
						'show' => array(
							'qodef_membership_privacy_policy_enable' => array(
								'values'        => 'yes',
								'default_value' => 'yes',
							),
						),
					),
				)
			);

			$page->add_field_element(
				array(
					'field_type'  => 'select',
					'name'        => 'qodef_membership_privacy_policy_link',
					'title'       => esc_html__( 'Privacy Policy Link', 'swissdelight-membership' ),
					'description' => esc_html__( 'Choose "Privacy Policy Link" page to link from registration modal form', 'swissdelight-membership' ),
					'options'     => qode_framework_get_pages( true ),
					'dependency'    => array(
						'show' => array(
							'qodef_membership_privacy_policy_enable' => array(
								'values'        => 'yes',
								'default_value' => 'yes',
							),
						),
					),
				)
			);

			$page->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_membership_privacy_policy_link_text',
					'title'       => esc_html__( 'Privacy Policy Link Text', 'swissdelight-membership' ),
					'description' => esc_html__( 'Enter privacy policy link text for registration modal form. Default value is "privacy policy"', 'swissdelight-membership' ),
					'dependency'    => array(
						'show' => array(
							'qodef_membership_privacy_policy_enable' => array(
								'values'        => 'yes',
								'default_value' => 'yes',
							),
						),
					),
				)
			);

			// Hook to include additional options after module options
			do_action( 'swissdelight_membership_action_after_membership_options_map', $page );
		}
	}

	add_action( 'swissdelight_core_action_default_options_init', 'swissdelight_membership_add_general_options', 70 );
}
