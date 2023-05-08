<div class="qodef-header-sticky qodef-custom-header-layout <?php echo implode( ' ', apply_filters( 'swissdelight_core_filter_sticky_header_class', array() ) ); ?>">
	<div class="qodef-header-sticky-inner <?php echo implode( ' ', apply_filters( 'swissdelight_filter_header_inner_class', array(), 'sticky' ) ); ?>">
		<?php
		// Include logo
		swissdelight_core_get_header_logo_image( array( 'sticky_logo' => true ) );

		// Include main navigation
		swissdelight_core_template_part( 'header', 'templates/parts/navigation', '', array( 'menu_id' => 'qodef-sticky-navigation-menu' ) );

		// Include widget area one
		swissdelight_core_get_header_widget_area( 'sticky' );

		do_action( 'swissdelight_core_action_after_sticky_header' );
		?>
	</div>
</div>
