<?php

if ( ! function_exists( 'swissdelight_core_include_working_hours_shortcodes' ) ) {
	/**
	 * Function that includes shortcodes
	 */
	function swissdelight_core_include_working_hours_shortcodes() {
		foreach ( glob( SWISSDELIGHT_CORE_INC_PATH . '/working-hours/shortcodes/*/include.php' ) as $shortcode ) {
			include_once $shortcode;
		}
	}

	add_action( 'qode_framework_action_before_shortcodes_register', 'swissdelight_core_include_working_hours_shortcodes' );
}

if ( ! function_exists( 'swissdelight_core_include_working_hours_widgets' ) ) {
	/**
	 * Function that includes widgets
	 */
	function swissdelight_core_include_working_hours_widgets() {
		foreach ( glob( SWISSDELIGHT_CORE_INC_PATH . '/working-hours/shortcodes/*/widget/include.php' ) as $widget ) {
			include_once $widget;
		}
	}

	add_action( 'qode_framework_action_before_widgets_register', 'swissdelight_core_include_working_hours_widgets' );
}

if ( ! function_exists( 'swissdelight_core_set_working_hours_template_params' ) ) {
	/**
	 * Function that set working hours area content parameters
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	function swissdelight_core_set_working_hours_template_params( $params ) {
		$layout = swissdelight_core_get_option_value( 'admin', 'qodef_working_hours_layout' );

		if ( 'all-days' === $layout ) {
			$days = array(
				'monday'    => esc_html__( 'monday', 'swissdelight-core' ),
				'tuesday'   => esc_html__( 'tuesday', 'swissdelight-core' ),
				'wednesday' => esc_html__( 'wednesday', 'swissdelight-core' ),
				'thursday'  => esc_html__( 'thursday', 'swissdelight-core' ),
				'friday'    => esc_html__( 'friday', 'swissdelight-core' ),
				'saturday'  => esc_html__( 'saturday', 'swissdelight-core' ),
				'sunday'    => esc_html__( 'sunday', 'swissdelight-core' ),
			);
		} else {
			$days = array(
				'other'    => esc_html__( 'mon - fri', 'swissdelight-core' ),
				'saturday' => esc_html__( 'saturday', 'swissdelight-core' ),
				'sunday'   => esc_html__( 'sunday', 'swissdelight-core' ),
			);
		}

		foreach ( $days as $day => $label ) {
			$option = swissdelight_core_get_post_value_through_levels( 'qodef_working_hours_' . $day );

			$params[ $label ] = ! empty( $option ) ? esc_attr( $option ) : '';
		}

		return $params;
	}

	add_filter( 'swissdelight_core_filter_working_hours_template_params', 'swissdelight_core_set_working_hours_template_params' );
}

if ( ! function_exists( 'swissdelight_core_set_working_hours_special_template_params' ) ) {
	/**
	 * Function that set working hours area special content parameters
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	function swissdelight_core_set_working_hours_special_template_params( $params ) {
		$special_days = swissdelight_core_get_post_value_through_levels( 'qodef_working_hours_special_days' );
		$special_text = swissdelight_core_get_post_value_through_levels( 'qodef_working_hours_special_text' );

		if ( ! empty( $special_days ) ) {
			$special_days = array_filter( (array) $special_days, 'strlen' );
		}

		$params['special_days'] = $special_days;
		$params['special_text'] = esc_attr( $special_text );

		return $params;
	}

	add_filter( 'swissdelight_core_filter_working_hours_special_template_params', 'swissdelight_core_set_working_hours_special_template_params' );
}

if ( ! function_exists( 'swissdelight_core_working_hours_set_admin_options_map_position' ) ) {
	/**
	 * Function that set dashboard admin options map position for this module
	 *
	 * @param int $position
	 * @param string $map
	 *
	 * @return int
	 */
	function swissdelight_core_working_hours_set_admin_options_map_position( $position, $map ) {

		if ( 'working-hours' === $map ) {
			$position = 90;
		}

		return $position;
	}

	add_filter( 'swissdelight_core_filter_admin_options_map_position', 'swissdelight_core_working_hours_set_admin_options_map_position', 10, 2 );
}
