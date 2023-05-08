<?php

if ( ! function_exists( 'swissdelight_membership_add_social_login_twitter_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function swissdelight_membership_add_social_login_twitter_options( $page, $social_login_network_section ) {
		
		if ( $social_login_network_section ) {
			$social_login_network_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_enable_twitter_social_login',
					'title'         => esc_html__( 'Enable Twitter Social Login', 'swissdelight-membership' ),
					'description'   => esc_html__( 'Enabling this option will allow login from twitter social network', 'swissdelight-membership' ),
					'default_value' => 'no'
				)
			);
		}
	}
	
	add_action( 'swissdelight_membership_action_after_social_login_options_map', 'swissdelight_membership_add_social_login_twitter_options', 10, 2 );
}