<?php

if ( ! function_exists( 'swissdelight_core_include_blog_single_post_navigation_template' ) ) {
	/**
	 * Function which includes additional module on single posts page
	 */
	function swissdelight_core_include_blog_single_post_navigation_template() {
		if ( is_single() ) {
			include_once SWISSDELIGHT_CORE_INC_PATH . '/blog/templates/single/single-post-navigation/templates/single-post-navigation.php';
		}
	}

	add_action( 'swissdelight_action_after_blog_post_item', 'swissdelight_core_include_blog_single_post_navigation_template', 10 ); // permission 20 is set to define template position
}
