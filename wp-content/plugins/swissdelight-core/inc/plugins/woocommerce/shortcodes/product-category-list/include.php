<?php

include_once SWISSDELIGHT_CORE_PLUGINS_PATH . '/woocommerce/shortcodes/product-category-list/media-custom-fields.php';
include_once SWISSDELIGHT_CORE_PLUGINS_PATH . '/woocommerce/shortcodes/product-category-list/class-swissdelightcore-product-category-list-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_PLUGINS_PATH . '/woocommerce/shortcodes/product-category-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
