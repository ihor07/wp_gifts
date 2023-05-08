<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/item-showcase/class-swissdelightcore-item-showcase-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_SHORTCODES_PATH . '/item-showcase/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
