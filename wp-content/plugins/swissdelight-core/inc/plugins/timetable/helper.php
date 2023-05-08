<?php

if ( ! function_exists( 'swissdelight_core_include_timetable_plugin_is_installed' ) ) {
	/**
	 * Function that set case is installed element for framework functionality
	 *
	 * @param bool $installed
	 * @param string $plugin - plugin name
	 *
	 * @return bool
	 */
	function swissdelight_core_include_timetable_plugin_is_installed( $installed, $plugin ) {
		if ( 'timetable' === $plugin ) {
			return function_exists( 'timetable_init' );
		}

		return $installed;
	}

	add_filter( 'qode_framework_filter_is_plugin_installed', 'swissdelight_core_include_timetable_plugin_is_installed', 10, 2 );
}
