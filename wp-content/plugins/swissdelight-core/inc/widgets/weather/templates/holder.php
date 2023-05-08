<div <?php qode_framework_class_attribute( $holder_classes ); ?>>
	<?php
	if ( ! empty( $today_params ) ) {
		swissdelight_core_template_part( 'widgets/weather', 'templates/item', $layout, $today_params );
		swissdelight_core_template_part( 'widgets/weather', 'templates/parts/five-days', '', $params );
	}
	?>
</div>
