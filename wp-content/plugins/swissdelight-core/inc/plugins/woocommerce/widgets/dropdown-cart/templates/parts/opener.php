<a itemprop="url" class="qodef-m-opener" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
	<span class="qodef-m-opener-label"><?php esc_html_e( 'cart', 'swissdelight-core' ); ?></span>
	<span class="qodef-m-opener-count">
		<?php echo WC()->cart->cart_contents_count; ?>
		<?php swissdelight_render_svg_icon( 'cart-icon', 'qodef-m-svg-icon' ); ?>
	</span>
</a>
