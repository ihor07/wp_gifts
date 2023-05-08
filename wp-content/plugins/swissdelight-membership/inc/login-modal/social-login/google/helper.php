<?php

if ( ! function_exists( 'swissdelight_membership_is_google_social_login_enabled' ) ) {
	/**
	 * Function that check is module enabled
	 *
	 * @return bool
	 */
	function swissdelight_membership_is_google_social_login_enabled() {
		return swissdelight_core_get_option_value( 'admin', 'qodef_enable_google_social_login' ) === 'yes';
	}
}

if ( ! function_exists( 'swissdelight_membership_include_google_login_template' ) ) {
	/**
	 * Render form for google login
	 */
	function swissdelight_membership_include_google_login_template() {
		if ( swissdelight_membership_is_google_social_login_enabled() ) {
			swissdelight_membership_template_part( 'login-modal/social-login', 'google/templates/button' );
		}
	}
	
	add_action( 'swissdelight_membership_action_social_login_content', 'swissdelight_membership_include_google_login_template', 10 );
}

if ( ! function_exists( 'swissdelight_membership_localize_main_script_with_google_app_id' ) ) {
	/**
	 * Render form for google login
	 *
	 * @param array $global
	 *
	 * @return array
	 */
	function swissdelight_membership_localize_main_script_with_google_app_id( $global ) {
		$app_id = swissdelight_core_get_option_value( 'admin', 'qodef_google_social_login_api_id' );
		
		if ( swissdelight_membership_is_google_social_login_enabled() && ! empty( $app_id ) ) {
			$global['googleAppId'] = esc_attr( $app_id );
		}
		
		return $global;
	}
	
	add_filter( 'swissdelight_membership_filter_localize_main_js', 'swissdelight_membership_localize_main_script_with_google_app_id' );
}

if ( ! function_exists( 'swissdelight_membership_include_required_scripts_for_google_social_login' ) ) {
	/**
	 * Function that include additional js script before main plugin script
	 */
	function swissdelight_membership_include_required_scripts_for_google_social_login() {
		if ( swissdelight_membership_is_google_social_login_enabled() ) {
			wp_enqueue_script( 'swissdelight-membership-google-plus-api', 'https://apis.google.com/js/platform.js', array(), null, false );
		}
	}
	
	add_action( 'swissdelight_membership_action_before_main_js', 'swissdelight_membership_include_required_scripts_for_google_social_login' );
}

if ( ! function_exists( 'swissdelight_membership_init_rest_api_google_login' ) ) {
	/**
	 * Main login modal function that is triggered through social login modal ajax
	 */
	function swissdelight_membership_init_rest_api_google_login() {
		
		if ( isset( $_GET ) && ! empty( $_GET ) && isset( $_GET['options']['social_response'] ) && ! empty( $_GET['options']['social_response'] ) ) {
			$user_data = $_GET['options']['social_response'];
			$user_email = isset( $user_data['email'] ) && is_email( $user_data['email'] ) ? sanitize_email( $user_data['email'] ) : '';
		
			if ( ! empty ( $user_email ) ) {
				if ( email_exists( $user_email ) ) {
					//User already exist, log in user
					swissdelight_membership_login_current_user_by_meta( $user_email );
				} else {
					// Register new user
					$user_meta = array(
						'user_login'            => str_replace( '-', '_', sanitize_title( $user_data['name'] ) ) . '_google',
						'user_email'            => $user_email,
						'user_password'         => $user_data['id'],
						'user_confirm_password' => $user_data['id'],
						'social_login'          => 'google'
					);
					
					swissdelight_membership_init_rest_api_register( $user_meta );
				}
			} else {
				qode_framework_get_ajax_status( 'error', esc_html__( 'Email address is invalid.', 'swissdelight-membership' ) );
			}
		}
	}
}