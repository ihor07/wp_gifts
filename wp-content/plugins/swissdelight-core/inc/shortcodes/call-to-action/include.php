<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/call-to-action/class-swissdelightcore-call-to-action-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_SHORTCODES_PATH . '/call-to-action/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
