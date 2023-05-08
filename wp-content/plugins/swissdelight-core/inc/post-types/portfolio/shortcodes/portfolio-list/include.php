<?php

include_once SWISSDELIGHT_CORE_CPT_PATH . '/portfolio/shortcodes/portfolio-list/class-swissdelightcore-portfolio-list-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_CPT_PATH . '/portfolio/shortcodes/portfolio-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
