<?php

include_once SWISSDELIGHT_CORE_SHORTCODES_PATH . '/info-section/class-swissdelightcore-info-section-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_SHORTCODES_PATH . '/info-section/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
