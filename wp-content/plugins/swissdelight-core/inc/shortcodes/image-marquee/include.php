<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/image-marquee/class-swissdelightcore-image-marquee-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_INC_PATH . '/shortcodes/image-marquee/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
