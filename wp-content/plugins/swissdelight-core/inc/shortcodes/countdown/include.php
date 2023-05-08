<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/countdown/class-swissdelightcore-countdown-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_SHORTCODES_PATH . '/countdown/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
