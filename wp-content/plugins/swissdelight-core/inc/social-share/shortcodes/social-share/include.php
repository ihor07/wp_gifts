<?php

include_once SWISSDELIGHT_CORE_INC_PATH . '/social-share/shortcodes/social-share/class-swissdelightcore-social-share-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_INC_PATH . '/social-share/shortcodes/social-share/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
