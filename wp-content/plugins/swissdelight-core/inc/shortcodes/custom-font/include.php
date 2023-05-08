<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/custom-font/class-swissdelightcore-custom-font-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_SHORTCODES_PATH . '/custom-font/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
