<?php

include_once SWISSDELIGHT_CORE_CPT_PATH . '/team/shortcodes/team-list/class-swissdelightcore-team-list-shortcode.php';

foreach ( glob( SWISSDELIGHT_CORE_CPT_PATH . '/team/shortcodes/team-list/variations/*/include.php' ) as $variation ) {
	include_once $variation;
}
