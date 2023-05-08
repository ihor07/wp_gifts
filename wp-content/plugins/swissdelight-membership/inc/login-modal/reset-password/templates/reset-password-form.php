<form id="qodef-membership-reset-password-modal-part" class="qodef-m" method="POST">
	<div class="qodef-m-fields">
		<label><?php esc_html_e( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'swissdelight-membership' ); ?></label>
		<input type="text" class="qodef-m-user-login" name="user_login" placeholder="<?php esc_attr_e( 'Username or email', 'swissdelight-membership' ) ?>" value="" required />
	</div>
	<div class="qodef-m-action">
		<?php
		$reset_button_params = array(
			'custom_class'  => 'qodef-m-action-button',
            'button_layout' => 'outlined',
			'html_type'     => 'submit',
			'show_arrow'    => 'no',
			'text'          => esc_html__( 'Reset Password', 'swissdelight-membership' ),
		);

		echo SwissDelightCore_Button_Shortcode::call_shortcode( $reset_button_params );

		swissdelight_membership_template_part( 'login-modal', 'templates/parts/spinner' );
		?>
	</div>
	<?php swissdelight_membership_template_part( 'login-modal', 'templates/parts/response' ); ?>
	<?php swissdelight_membership_template_part( 'login-modal', 'templates/parts/hidden-fields', '', array( 'response_type' => 'reset-password' ) ); ?>
</form>