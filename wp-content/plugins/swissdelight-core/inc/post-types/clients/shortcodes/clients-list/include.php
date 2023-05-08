<?php

include_once SWISSDELIGHT_CORE_CPT_PATH . '/clients/shortcodes/clients-list/class-swissdelightcore-clients-list-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_CPT_PATH . '/clients/shortcodes/clients-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
