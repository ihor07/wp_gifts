<?php

if ( ! function_exists( 'swissdelight_core_include_portfolio_single_post_navigation_template' ) ) {
	/**
	 * Function which includes additional module on single portfolio page
	 */
	function swissdelight_core_include_portfolio_single_post_navigation_template() {
		swissdelight_core_template_part( 'post-types/portfolio', 'templates/single/single-navigation/templates/single-navigation' );
	}

	add_action( 'swissdelight_core_action_after_portfolio_single_item', 'swissdelight_core_include_portfolio_single_post_navigation_template' );
}
