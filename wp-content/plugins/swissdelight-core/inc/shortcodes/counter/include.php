<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/counter/class-swissdelightcore-counter-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_SHORTCODES_PATH . '/counter/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
