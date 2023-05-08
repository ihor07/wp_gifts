<?php if ( class_exists( 'SwissDelightCore_Social_Share_Shortcode' ) ) { ?>
	<div class="qodef-e qodef-info--social-share">
		<?php
		$params = array(
			'title'  => esc_html__( 'Share :', 'swissdelight-core' ),
			'layout' => 'list',
		);

		echo SwissDelightCore_Social_Share_Shortcode::call_shortcode( $params );
		?>
	</div>
<?php } ?>
