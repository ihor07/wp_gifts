<div <?php wc_product_class( $item_classes ); ?>>
	<div class="qodef-woo-product-inner">
		<?php if ( has_post_thumbnail() ) { ?>
			<div class="qodef-woo-product-image">
				<?php swissdelight_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/mark' ); ?>
				<?php swissdelight_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/image', '', $params ); ?>
				<div class="qodef-woo-product-image-inner">
					<?php
					swissdelight_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/add-to-cart' );

					// Hook to include additional content inside product list item image
					do_action( 'swissdelight_core_action_product_list_item_additional_image_content' );
					?>
				</div>
			</div>
		<?php }
		
		$content_style = ( isset( $content_style ) && ! empty( $content_style ) ) ?  $content_style : '';
		?>
		<div class="qodef-woo-product-content" <?php qode_framework_inline_style( $content_style ); ?>>
			<?php swissdelight_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/title', '', $params ); ?>
			<?php swissdelight_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/category', '', $params ); ?>
			<?php swissdelight_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/excerpt', '', $params ); ?>
			<?php swissdelight_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/price', '', $params ); ?>
			<?php
			// Hook to include additional content inside product list item content
			do_action( 'swissdelight_core_action_product_list_item_additional_content' );
			?>
		</div>
		<?php swissdelight_core_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/post-info/link' ); ?>
	</div>
</div>
