<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/icon-with-text/class-swissdelightcore-icon-with-text-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_SHORTCODES_PATH . '/icon-with-text/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
