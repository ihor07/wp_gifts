<?php

include_once SWISSDELIGHT_CORE_CPT_PATH . '/restaurant-menu/shortcodes/restaurant-menu-list/class-swissdelightcore-restaurant-menu-list-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_CPT_PATH . '/restaurant-menu/shortcodes/restaurant-menu-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
