<?php

foreach ( glob( SWISSDELIGHT_CORE_CPT_PATH . '/restaurant-menu/dashboard/meta-box/*.php' ) as $module ) {
	include_once $module;
}
