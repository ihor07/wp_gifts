<?php

include_once SWISSDELIGHT_MEMBERSHIP_LOGIN_MODAL_PATH . '/helper.php';

foreach ( glob( SWISSDELIGHT_MEMBERSHIP_LOGIN_MODAL_PATH . '/*/include.php' ) as $module ) {
	include_once $module;
}