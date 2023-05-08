<?php if ( 'no' !== $slider_navigation ) {
	$nav_next_classes = '';
	$nav_prev_classes = '';

	if ( isset( $unique ) && ! empty( $unique ) ) {
		$nav_next_classes = 'swiper-button-outside swiper-button-next-' . esc_attr( $unique );
		$nav_prev_classes = 'swiper-button-outside swiper-button-prev-' . esc_attr( $unique );
	}
	?>
	<div class="swiper-button-prev <?php echo esc_attr( $nav_prev_classes ); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px" height="63px" viewBox="0 0 28 63" enable-background="new 0 0 28 63" xml:space="preserve"><line fill="none" stroke="#241C10" stroke-miterlimit="10" x1="27.48" y1="0" x2="0" y2="31.485"/><line fill="none" stroke="#241C10" stroke-miterlimit="10" x1="27.48" y1="63" x2="0" y2="31.515"/></svg>
	</div>
	<div class="swiper-button-next <?php echo esc_attr( $nav_next_classes ); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px" height="63px" viewBox="0 0 28 63" enable-background="new 0 0 28 63" xml:space="preserve"><line fill="none" stroke="#241C10" stroke-miterlimit="10" x1="0" y1="0" x2="27.48" y2="31.485"/><line fill="none" stroke="#241C10" stroke-miterlimit="10" x1="0" y1="63" x2="27.48" y2="31.515"/></svg>
	</div>
<?php } ?>
