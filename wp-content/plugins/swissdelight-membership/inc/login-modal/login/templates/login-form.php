<form id="qodef-membership-login-modal-part" class="qodef-m" method="GET">
	<div class="qodef-m-fields">
		<input type="text" class="qodef-m-user-name" name="user_name" placeholder="<?php esc_attr_e( 'Username', 'swissdelight-membership' ) ?>" value="" required pattern=".{3,}" autocomplete="username"/>
		<input type="password" class="qodef-m-user-password" name="user_password" placeholder="<?php esc_attr_e( 'Password', 'swissdelight-membership' ) ?>" required autocomplete="current-password" />
	</div>
	<div class="qodef-m-links">
		<div class="qodef-m-links-remember-me">
			<input type="checkbox" id="qodef-m-links-remember" class="qodef-m-links-remember" name="remember" value="forever" />
			<label for="qodef-m-links-remember" class="qodef-m-links-remember-label"><?php esc_html_e( 'Remember me', 'swissdelight-membership' ) ?></label>
		</div>
	</div>
	<div class="qodef-m-action">
        <?php
        $reset_button_params = array(
            'custom_class'  => 'qodef-m-links-reset-password',
            'button_layout' => 'textual',
            'link'          => '#',
            'show_arrow'   => 'no',
            'text'          => esc_html__( 'Lost Your password?', 'swissdelight-membership' )
        );

        echo SwissDelightCore_Button_Shortcode::call_shortcode( $reset_button_params ); ?>
		<?php
		$login_button_params = array(
			'custom_class'  => 'qodef-m-action-button',
            'button_layout' => 'outlined',
			'html_type'     => 'submit',
            'show_arrow'    => 'no',
			'text'          => esc_html__( 'Login', 'swissdelight-membership' )
		);
		
		echo SwissDelightCore_Button_Shortcode::call_shortcode( $login_button_params );
		
		swissdelight_membership_template_part( 'login-modal', 'templates/parts/spinner' ); ?>
	</div>
	<?php
	/**
	 * Hook to include additional form content
	 */
	do_action( 'swissdelight_membership_action_login_form_template' );
	
	swissdelight_membership_template_part( 'login-modal', 'templates/parts/response' );
	swissdelight_membership_template_part( 'login-modal', 'templates/parts/hidden-fields', '', array( 'response_type' => 'login' ) ); ?>
</form>