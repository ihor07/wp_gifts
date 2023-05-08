<?php

include_once SWISSDELIGHT_CORE_CPT_PATH . '/portfolio/shortcodes/portfolio-category-list/class-swissdelightcore-portfolio-category-list-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_CPT_PATH . '/portfolio/shortcodes/portfolio-category-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
