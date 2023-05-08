<div class="qodef-m-inner">
	<?php
	swissdelight_core_template_part( 'widgets/weather', 'templates/parts/temperature', '', $params );

	if ( isset( $day_of_week ) && ! empty( $day_of_week ) ) { ?>
		<div class="qodef-m-day"><?php echo esc_html( $day_of_week ); ?></div>
	<?php } ?>
</div>
