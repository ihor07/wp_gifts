<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/pricing-table/class-swissdelightcore-pricing-table-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_SHORTCODES_PATH . '/pricing-table/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
