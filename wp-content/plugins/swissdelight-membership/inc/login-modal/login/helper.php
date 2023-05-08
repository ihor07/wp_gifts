<?php

if ( ! function_exists( 'swissdelight_membership_include_login_navigation_template' ) ) {
	/**
	 * Loads modal template
	 */
	function swissdelight_membership_include_login_navigation_template() {
		$params = array(
			'item_class' => 'qodef--login',
			'item_label' => esc_attr__( 'Login', 'swissdelight-membership' ),
			'item_link'  => '#qodef-membership-login-modal-part'
		);
		
		swissdelight_membership_template_part( 'login-modal', 'templates/parts/navigation-item', '', $params );
	}
	
	add_action( 'swissdelight_membership_action_login_modal_navigation_item', 'swissdelight_membership_include_login_navigation_template', 10 );
}

if ( ! function_exists( 'swissdelight_membership_include_login_template' ) ) {
	/**
	 * Loads modal template
	 */
	function swissdelight_membership_include_login_template() {
		swissdelight_membership_template_part( 'login-modal/login', 'templates/login-form' );
	}
	
	add_action( 'swissdelight_membership_action_login_modal_content', 'swissdelight_membership_include_login_template', 10 );
}

if ( ! function_exists( 'swissdelight_membership_init_rest_api_login' ) ) {
	/**
	 * Main login modal function that is triggered through login modal ajax
	 */
	function swissdelight_membership_init_rest_api_login( $options ) {
		
		if ( ! empty( $options ) ) {
			$credentials                  = array();
			$credentials['user_login']    = sanitize_user( $options['user_login'] );
			$credentials['user_password'] = wp_unslash( $options['user_password'] );
			$credentials['remember']      = isset( $options['remember'] ) && ! empty( $options['remember'] );
			
			// Hook to add additional check before logged in
			do_action( 'swissdelight_membership_action_before_rest_api_login', $options );
			
			// On multisite, ensure user exists on current site, if not add them before allowing login.
			if ( is_multisite() ) {
				$user_data = get_user_by( is_email( $credentials['user_login'] ) ? 'email' : 'login', $credentials['user_login'] );
				
				if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
					add_user_to_blog( get_current_blog_id(), $user_data->ID, get_option( 'default_role' ) );
				}
			}
			
			// Perform the login.
			$user = wp_signon( $credentials, is_ssl() );
			
			if ( is_wp_error( $user ) ) {
				qode_framework_get_ajax_status( 'error', esc_html__( 'Username or password is invalid.', 'swissdelight-membership' ) );
			} else {
				$redirect_uri = swissdelight_membership_get_membership_redirect_url( isset( $options['redirect'] ) ? $options['redirect'] : '' );
				
				qode_framework_get_ajax_status( 'success', esc_html__( 'Login successful, redirecting...', 'swissdelight-membership' ), null, $redirect_uri );
			}
		} else {
			qode_framework_get_ajax_status( 'error', esc_html__( 'Options are invalid.', 'swissdelight-membership' ) );
		}
	}
}