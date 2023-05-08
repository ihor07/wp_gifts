<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/single-image/class-swissdelightcore-single-image-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_SHORTCODES_PATH . '/single-image/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
