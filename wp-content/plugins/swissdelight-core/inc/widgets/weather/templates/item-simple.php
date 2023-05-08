<div class="qodef-m-inner">
	<div class="qodef-m-date"><?php echo esc_html( $dt_today ); ?></div>
	<?php if ( isset( $city ) && ! empty( $city ) ) { ?>
		<div class="qodef-m-city"><?php echo esc_html( $city ); ?></div>
	<?php } ?>
	<?php swissdelight_core_template_part( 'widgets/weather', 'templates/parts/temperature', '', $params ); ?>
</div>
