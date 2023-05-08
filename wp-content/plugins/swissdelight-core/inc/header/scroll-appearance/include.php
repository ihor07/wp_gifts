<?php

include_once SWISSDELIGHT_CORE_INC_PATH . '/header/scroll-appearance/helper.php';

foreach ( glob( SWISSDELIGHT_CORE_INC_PATH . '/header/scroll-appearance/dashboard/*/*.php' ) as $dashboard ) {
	include_once $dashboard;
}

foreach ( glob( SWISSDELIGHT_CORE_INC_PATH . '/header/scroll-appearance/*/include.php' ) as $appearance ) {
	include_once $appearance;
}
