<?php

include_once SWISSDELIGHT_CORE_INC_PATH . '/blog/shortcodes/blog-list/class-swissdelightcore-blog-list-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_INC_PATH . '/blog/shortcodes/blog-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
