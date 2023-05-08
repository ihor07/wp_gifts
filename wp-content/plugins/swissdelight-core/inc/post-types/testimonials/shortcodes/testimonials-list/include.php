<?php

include_once SWISSDELIGHT_CORE_CPT_PATH . '/testimonials/shortcodes/testimonials-list/class-swissdelightcore-testimonials-list-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_CPT_PATH . '/testimonials/shortcodes/testimonials-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
