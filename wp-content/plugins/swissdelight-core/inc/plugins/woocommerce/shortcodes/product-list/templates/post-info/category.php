<?php

$categories = swissdelight_core_woo_get_product_categories();

if ( ! empty( $categories ) && isset( $enable_category ) && 'no' !== $enable_category ) { ?>
	<div class="qodef-woo-product-categories"><?php echo wp_kses_post( $categories ); ?></div>
<?php } ?>
