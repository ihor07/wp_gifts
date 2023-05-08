<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/nutritional-values-list/class-swissdelightcore-nutritional-values-list-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_SHORTCODES_PATH . '/nutritional-values-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
