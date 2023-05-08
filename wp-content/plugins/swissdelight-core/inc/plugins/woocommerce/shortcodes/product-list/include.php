<?php

include_once SWISSDELIGHT_CORE_PLUGINS_PATH . '/woocommerce/shortcodes/product-list/class-swissdelightcore-product-list-shortcode.php';
include_once SWISSDELIGHT_CORE_PLUGINS_PATH . '/woocommerce/shortcodes/product-list/helper.php';

foreach ( glob( SWISSDELIGHT_CORE_PLUGINS_PATH . '/woocommerce/shortcodes/product-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
