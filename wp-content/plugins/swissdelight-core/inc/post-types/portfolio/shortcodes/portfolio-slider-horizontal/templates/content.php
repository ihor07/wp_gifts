<div class="qodef-portfolio-slider-horizontal">
	<div <?php qode_framework_class_attribute( $holder_classes ); ?> <?php qode_framework_inline_attr( $slider_attr, 'data-options' ); ?>>
		<div class="swiper-wrapper">
			<?php
			// Include items
			swissdelight_core_template_part( 'post-types/portfolio/shortcodes/portfolio-slider-horizontal', 'templates/loop', '', $params );
			?>
		</div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
	</div>

</div>