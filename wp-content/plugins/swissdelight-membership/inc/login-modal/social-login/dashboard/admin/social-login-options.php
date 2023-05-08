<?php

if ( ! function_exists( 'swissdelight_membership_add_social_login_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function swissdelight_membership_add_social_login_options() {
		$qode_framework = qode_framework_get_framework_root();
		
		$page = $qode_framework->add_options_page(
			array(
				'scope'       => SWISSDELIGHT_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'social_login',
				'icon'        => 'fa fa-user',
				'title'       => esc_html__( 'Social Login', 'swissdelight-membership' ),
				'description' => esc_html__( 'Global settings related to social login', 'swissdelight-membership' )
			)
		);
		
		if ( $page ) {
			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_social_login',
					'title'         => esc_html__( 'Enable Social Login', 'swissdelight-membership' ),
					'description'   => esc_html__( 'Enabling this option will allow login from social networks of your choice', 'swissdelight-membership' ),
					'default_value' => 'no'
				)
			);
			
			$social_login_network_section = $page->add_section_element(
				array(
					'name'       => 'qodef_social_login_network_section',
					'title'      => esc_html__( 'Social Networks', 'swissdelight-membership' ),
					'dependency' => array(
						'show' => array(
							'qodef_enable_social_login' => array(
								'values'        => 'yes',
								'default_value' => ''
							)
						)
					)
				)
			);
			
			// Hook to include additional options after module options
			do_action( 'swissdelight_membership_action_after_social_login_options_map', $page, $social_login_network_section );
		}
	}
	
	add_action( 'swissdelight_core_action_default_options_init', 'swissdelight_membership_add_social_login_options', swissdelight_membership_get_admin_options_map_position( 'social-login' ) );
}