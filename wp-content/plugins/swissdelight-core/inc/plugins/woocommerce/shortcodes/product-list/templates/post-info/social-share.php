<?php if ( class_exists( 'SwissDelightCore_Social_Share_Shortcode' ) ) { ?>
	<div class="qodef-woo-product-social-share">
		<?php
		$params          = array();
		$params['title'] = esc_html__( 'Share', 'swissdelight-core' );

		echo SwissDelightCore_Social_Share_Shortcode::call_shortcode( $params );
		?>
	</div>
<?php } ?>
