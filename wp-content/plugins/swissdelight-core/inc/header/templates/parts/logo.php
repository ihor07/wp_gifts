<a itemprop="url" class="qodef-header-logo-link <?php echo esc_attr( $logo_classes ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" <?php qode_framework_inline_style( $logo_styles ); ?> rel="home">
	<?php
	// Hooks that allows you to add additional content before logo image
	do_action( 'swissdelight_core_before_header_logo_image' );

	// Include header logo image html
	echo qode_framework_wp_kses_html( 'img', $logo_image );

	// Hooks that allows you to add additional content after logo image
	do_action( 'swissdelight_core_after_header_logo_image' );
	?>
</a>
