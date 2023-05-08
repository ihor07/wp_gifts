<?php

if ( ! function_exists( 'swissdelight_core_search_include_widgets' ) ) {
	/**
	 * Function that includes widgets
	 */
	function swissdelight_core_search_include_widgets() {
		foreach ( glob( SWISSDELIGHT_CORE_INC_PATH . '/search/widgets/*/include.php' ) as $widget ) {
			include_once $widget;
		}
	}

	add_action( 'qode_framework_action_before_widgets_register', 'swissdelight_core_search_include_widgets' );
}

if ( ! function_exists( 'swissdelight_core_search_include_layout' ) ) {
	/**
	 * Function that includes module variations
	 *
	 * @instance SwissDelightCore_Headers
	 */
	function swissdelight_core_search_include_layout() {
		$header_object = SwissDelightCore_Headers::get_instance()->get_header_object();

		if ( ! empty( $header_object ) ) {
			$search_layout = $header_object->get_search_layout();
			$layouts       = apply_filters( 'swissdelight_core_filter_register_search_layouts', $header_layouts_option = array() );

			if ( ! empty( $layouts ) ) {
				foreach ( $layouts as $key => $value ) {
					if ( $search_layout === $key ) {
						$value::get_instance();
					}
				}
			}
		}
	}

	add_action( 'wp', 'swissdelight_core_search_include_layout' );
}

if ( ! function_exists( 'swissdelight_core_set_search_page_page_title' ) ) {
	/**
	 * Function that enable/disable page title area for search page
	 *
	 * @param bool $enable_page_title
	 *
	 * @return bool
	 */
	function swissdelight_core_set_search_page_page_title( $enable_page_title ) {
		$option = 'no' !== swissdelight_core_get_post_value_through_levels( 'qodef_search_page_enable_page_title' );

		if ( is_search() && '' !== $option ) {
			$enable_page_title = $option;
		}

		return $enable_page_title;
	}

	add_filter( 'swissdelight_filter_enable_page_title', 'swissdelight_core_set_search_page_page_title' );
}

if ( ! function_exists( 'swissdelight_core_set_search_page_sidebar_layout' ) ) {
	/**
	 * Function that return sidebar layout
	 *
	 * @param string $layout
	 *
	 * @return string
	 */
	function swissdelight_core_set_search_page_sidebar_layout( $layout ) {

		if ( is_search() ) {
			$option = swissdelight_core_get_post_value_through_levels( 'qodef_search_page_sidebar_layout' );

			if ( ! empty( $option ) ) {
				$layout = $option;
			}
		}

		return $layout;
	}

	add_filter( 'swissdelight_filter_sidebar_layout', 'swissdelight_core_set_search_page_sidebar_layout' );
}

if ( ! function_exists( 'swissdelight_core_set_search_page_custom_sidebar_name' ) ) {
	/**
	 * Function that return sidebar name
	 *
	 * @param string $sidebar_name
	 *
	 * @return string
	 */
	function swissdelight_core_set_search_page_custom_sidebar_name( $sidebar_name ) {

		if ( is_search() ) {
			$option = swissdelight_core_get_post_value_through_levels( 'qodef_search_page_custom_sidebar' );

			if ( ! empty( $option ) ) {
				$sidebar_name = $option;
			}
		}

		return $sidebar_name;
	}

	add_filter( 'swissdelight_filter_sidebar_name', 'swissdelight_core_set_search_page_custom_sidebar_name' );
}

if ( ! function_exists( 'swissdelight_core_set_search_page_sidebar_grid_gutter_classes' ) ) {
	/**
	 * Function that returns grid gutter classes
	 *
	 * @param string $classes
	 *
	 * @return string
	 */
	function swissdelight_core_set_search_page_sidebar_grid_gutter_classes( $classes ) {

		if ( is_search() ) {
			$option = swissdelight_core_get_post_value_through_levels( 'qodef_search_page_sidebar_grid_gutter' );

			if ( ! empty( $option ) ) {
				$classes = 'qodef-gutter--' . esc_attr( $option );
			}
		}

		return $classes;
	}

	add_filter( 'swissdelight_filter_grid_gutter_classes', 'swissdelight_core_set_search_page_sidebar_grid_gutter_classes' );
}

if ( ! function_exists( 'swissdelight_core_set_search_page_post_excerpt_length' ) ) {
	/**
	 * Function that set number of characters for excerpt on blog list page
	 *
	 * @param int $excerpt_length
	 *
	 * @return int
	 */
	function swissdelight_core_set_search_page_post_excerpt_length( $excerpt_length ) {
		$option = swissdelight_core_get_post_value_through_levels( 'qodef_search_page_excerpt_number_of_characters' );

		if ( '' !== $option ) {
			$excerpt_length = $option;
		}

		return $excerpt_length;
	}

	add_filter( 'swissdelight_filter_search_page_excerpt_length', 'swissdelight_core_set_search_page_post_excerpt_length' );
}
