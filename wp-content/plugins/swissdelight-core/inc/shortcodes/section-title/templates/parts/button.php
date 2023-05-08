<?php if ( ! empty( $button_params['text'] ) || 'yes' === $button_params['show_arrow'] && class_exists( 'SwissDelightCore_Button_Shortcode' ) ) { ?>
	<div class="qodef-m-button">
		<?php echo SwissDelightCore_Button_Shortcode::call_shortcode( $button_params ); ?>
	</div>
<?php } ?>
