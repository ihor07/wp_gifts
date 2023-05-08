<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/image-with-text/class-swissdelightcore-image-with-text-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_SHORTCODES_PATH . '/image-with-text/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
