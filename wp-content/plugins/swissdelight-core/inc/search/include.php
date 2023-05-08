<?php

include_once SWISSDELIGHT_CORE_INC_PATH . '/search/class-swissdelightcore-search.php';
include_once SWISSDELIGHT_CORE_INC_PATH . '/search/helper.php';
include_once SWISSDELIGHT_CORE_INC_PATH . '/search/dashboard/admin/search-options.php';

foreach ( glob( SWISSDELIGHT_CORE_INC_PATH . '/search/layouts/*/include.php' ) as $layout ) {
	include_once $layout;
}
