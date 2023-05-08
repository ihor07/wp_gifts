<?php

if ( ! function_exists( 'swissdelight_register_justified_gallery_scripts' ) ) {
	/**
	 * Function that register module 3rd party scripts
	 */
	function swissdelight_register_justified_gallery_scripts() {
		wp_register_script( 'jquery-justified-gallery', SWISSDELIGHT_INC_ROOT . '/justified-gallery/assets/js/plugins/jquery.justifiedGallery.min.js', array( 'jquery' ), true );
	}

	add_action( 'swissdelight_action_before_main_js', 'swissdelight_register_justified_gallery_scripts' );
}

if ( ! function_exists( 'swissdelight_include_justified_gallery_scripts' ) ) {
	/**
	 * Function that enqueue modules 3rd party scripts
	 *
	 * @param array $atts
	 */
	function swissdelight_include_justified_gallery_scripts( $atts ) {

		if ( isset( $atts['behavior'] ) && 'justified-gallery' === $atts['behavior'] ) {
			wp_enqueue_script( 'jquery-justified-gallery' );
		}
	}

	add_action( 'swissdelight_core_action_list_shortcodes_load_assets', 'swissdelight_include_justified_gallery_scripts' );
}

if ( ! function_exists( 'swissdelight_register_justified_gallery_scripts_for_list_shortcodes' ) ) {
	/**
	 * Function that set module 3rd party scripts for list shortcodes
	 *
	 * @param array $scripts
	 *
	 * @return array
	 */
	function swissdelight_register_justified_gallery_scripts_for_list_shortcodes( $scripts ) {

		$scripts['jquery-justified-gallery'] = array(
			'registered' => true,
		);

		return $scripts;
	}

	add_filter( 'swissdelight_core_filter_register_list_shortcode_scripts', 'swissdelight_register_justified_gallery_scripts_for_list_shortcodes' );
}
