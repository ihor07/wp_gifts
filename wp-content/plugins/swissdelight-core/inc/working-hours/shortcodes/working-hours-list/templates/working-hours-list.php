<div <?php qode_framework_class_attribute( $holder_classes ); ?>>
	<?php foreach ( $params['working_hours_params'] as $day => $time ) : ?>
		<div class="qodef-working-hours-item qodef-e">
			<?php if ( ! empty( $day ) ) : ?>
				<h5 class="qodef-e-day"><?php echo sprintf( esc_html__( '%s', 'swissdelight-core' ), str_replace( '_', '', $day ) ); ?>
					<?php
					foreach ( $params['working_hours_special_params']['special_days'] as $special ) {
						if ( $day === $special ) {
							esc_html_e( '*', 'swissdelight-core' );
						}
					}
					?>
				</h5>
			<?php endif; ?>
			<?php if ( ! empty( $time ) ) { ?>
				<span class="qodef-e-time"><?php echo esc_html( $time ); ?></span>
			<?php } else { ?>
				<span class="qodef-e-time qodef--closed"><?php esc_html_e( 'Closed', 'swissdelight-core' ); ?></span>
			<?php } ?>
		</div>
	<?php endforeach; ?>
	<div class="qodef-m-footer">
		<?php if ( isset( $params['working_hours_special_params']['special_text'] ) && ! empty( $params['working_hours_special_params']['special_text'] ) ) : ?>
			<span><?php esc_html_e( '* ', 'swissdelight-core' ); ?> </span>
			<span class="qodef-m-footer-label"><?php echo esc_html( $params['working_hours_special_params']['special_text'] ); ?></span>
		<?php endif; ?>
	</div>
</div>
