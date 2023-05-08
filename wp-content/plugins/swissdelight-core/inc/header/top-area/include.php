<?php

include_once SWISSDELIGHT_CORE_INC_PATH . '/header/top-area/class-swissdelightcore-top-area.php';
include_once SWISSDELIGHT_CORE_INC_PATH . '/header/top-area/helper.php';

foreach ( glob( SWISSDELIGHT_CORE_INC_PATH . '/header/top-area/dashboard/*/*.php' ) as $dashboard ) {
	include_once $dashboard;
}
