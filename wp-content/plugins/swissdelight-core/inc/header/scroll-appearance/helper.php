<?php

if ( ! function_exists( 'swissdelight_core_dependency_for_scroll_appearance_options' ) ) {
	/**
	 * Function which return dependency values for global module options
	 *
	 * @return array
	 */
	function swissdelight_core_dependency_for_scroll_appearance_options() {
		return apply_filters( 'swissdelight_core_filter_header_scroll_appearance_hide_option', $hide_dep_options = array() );
	}
}
