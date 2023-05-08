<?php

include_once SWISSDELIGHT_CORE_CPT_PATH . '/clients/helper.php';

foreach ( glob( SWISSDELIGHT_CORE_CPT_PATH . '/clients/dashboard/meta-box/*.php' ) as $module ) {
	include_once $module;
}
