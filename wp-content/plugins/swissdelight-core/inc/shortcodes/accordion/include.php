<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/accordion/class-swissdelightcore-accordion-shortcode.php';
include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/accordion/class-swissdelightcore-accordion-child-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_SHORTCODES_PATH . '/accordion/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
