<div class="qodef-divided-header-left-wrapper">
	<?php
	// Include widget area two
	swissdelight_core_get_header_widget_area( '', 'two' );

	// Include divided left navigation
	swissdelight_core_template_part( 'header/layouts/divided', 'templates/parts/left-navigation' );
	?>
</div>
<?php
// Include logo
swissdelight_core_get_header_logo_image();
?>
<div class="qodef-divided-header-right-wrapper">
	<?php
	// Include divided right navigation
	swissdelight_core_template_part( 'header/layouts/divided', 'templates/parts/right-navigation' );

	// Include widget area one
	swissdelight_core_get_header_widget_area();
	?>
</div>
