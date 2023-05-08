<div class="qodef-header-wrapper">
	<div class="qodef-header-logo">
		<?php
		// Include logo
		swissdelight_core_get_header_logo_image();
		?>
	</div>
	<?php
	// Include main navigation
	swissdelight_core_template_part( 'header', 'templates/parts/navigation' );

	// Include widget area one
	swissdelight_core_get_header_widget_area();
	?>
</div>
