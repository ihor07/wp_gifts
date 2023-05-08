<?php

require_once SWISSDELIGHT_MEMBERSHIP_INC_PATH . '/general/register-template.php';
include_once SWISSDELIGHT_MEMBERSHIP_INC_PATH . '/general/helper.php';

foreach ( glob( SWISSDELIGHT_MEMBERSHIP_INC_PATH . '/general/dashboard/admin/*.php' ) as $module ) {
	include_once $module;
}

require_once SWISSDELIGHT_MEMBERSHIP_INC_PATH . '/general/register-template.php';
include_once SWISSDELIGHT_MEMBERSHIP_INC_PATH . '/general/helper.php';
