<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/tabs/class-swissdelightcore-tab-shortcode.php';
include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/tabs/class-swissdelightcore-tab-child-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_SHORTCODES_PATH . '/tabs/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
