<?php
if ( 5 == $days_to_show && isset( $today_params['rest'] ) && ! empty( $today_params['rest'] ) ) {
	foreach ( $today_params['rest'] as $key => $value ) {
		// Add surrounding div for days after today
		if ( 0 === $key ) {
			?>
			<div class="qodef-m-other-days">
			<?php
		}
		$new_params              = $value;
		$new_params['temp_unit'] = $today_params['temp_unit'];

		swissdelight_core_template_part( 'widgets/weather', 'templates/parts/day', '', $new_params );

		if ( intval( $days_to_show ) - 1 === $key ) {
			?>
			</div>
			<?php
			break;
		}
	}
}
?>
