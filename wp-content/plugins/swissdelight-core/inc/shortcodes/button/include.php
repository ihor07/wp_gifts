<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/button/class-swissdelightcore-button-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_SHORTCODES_PATH . '/button/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
