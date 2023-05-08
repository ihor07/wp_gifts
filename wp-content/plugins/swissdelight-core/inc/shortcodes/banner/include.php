<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/banner/class-swissdelightcore-banner-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_INC_PATH . '/shortcodes/banner/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
