<div <?php qode_framework_class_attribute( $holder_classes ); ?> <?php qode_framework_inline_attr( $slider_attr, 'data-options' ); ?>>
	<div class="swiper-wrapper">
		<?php
		// Include items
		swissdelight_core_template_part( 'blog/shortcodes/blog-list', 'templates/loop', '', $params );
		?>
	</div>
	<?php swissdelight_core_template_part( 'content', 'templates/swiper-nav', '', $params ); ?>
	<?php swissdelight_core_template_part( 'content', 'templates/swiper-pag', '', $params ); ?>
</div>
