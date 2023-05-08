<div class="qodef-m-inner">
	<div class="qodef-m-weather">
		<span class="qodef-m-weather-icon fa qodef--<?php echo esc_attr( $today_description_class ); ?>"></span>
		<?php swissdelight_core_template_part( 'widgets/weather', 'templates/parts/temperature', '', $params ); ?>
	</div>
	<div class="qodef-m-weather-info qodef-e">
		<div class="qodef-e-heading">
			<?php if ( isset( $city ) && ! empty( $city ) ) { ?>
				<h4 class="qodef-e-heading-city"><?php echo esc_html( $city ); ?></h4>
			<?php } ?>
			<?php if ( isset( $today_description ) && ! empty( $today_description ) ) { ?>
				<p class="qodef-e-heading-description"><?php echo esc_html( $today_description ); ?></p>
			<?php } ?>
		</div>
		<p class="qodef-e-humidity">
			<?php echo sprintf( '%s%s%s', esc_html__( 'Humidity:', 'swissdelight-core' ), esc_html( $today_humidity ), esc_html__( '%', 'swissdelight-core' ) ); ?>
		</p>
		<p class="qodef-e-wind">
			<?php echo sprintf( '%s%s%s', esc_html__( 'Wind:', 'swissdelight-core' ), esc_html( $today_wind_speed ), esc_html( $wind_unit ) ); ?>
		</p>
		<p class="qodef-e-temperature qodef--high-low">
			<span class="qodef-e-temperature-low">
				<?php
				echo sprintf(
					'%s %s%s%s',
					esc_html__( 'L', 'swissdelight-core' ),
					esc_html( $today_low ),
					'<sup>°</sup>',
					esc_html( $temp_unit )
				);
				?>
			</span>
			<span class="qodef-e-temperature-high">
				<?php
				echo sprintf(
					'%s%s%s',
					esc_html__( 'H ', 'swissdelight-core' ),
					esc_html( $today_high ),
					'<sup>°</sup>',
					esc_html( $temp_unit )
				);
				?>
			</span>
		</p>
	</div>
</div>
