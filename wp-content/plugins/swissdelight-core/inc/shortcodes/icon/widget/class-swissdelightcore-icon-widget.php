<?php

if ( ! function_exists( 'swissdelight_core_add_icon_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function swissdelight_core_add_icon_widget( $widgets ) {
		$widgets[] = 'SwissDelightCore_Icon_Widget';

		return $widgets;
	}

	add_filter( 'swissdelight_core_filter_register_widgets', 'swissdelight_core_add_icon_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class SwissDelightCore_Icon_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$widget_mapped = $this->import_shortcode_options(
				array(
					'shortcode_base' => 'swissdelight_core_icon',
				)
			);

			if ( $widget_mapped ) {
				$this->set_base( 'swissdelight_core_icon' );
				$this->set_name( esc_html__( 'SwissDelight Icon', 'swissdelight-core' ) );
				$this->set_description( esc_html__( 'Add a icon element into widget areas', 'swissdelight-core' ) );
			}
		}

		public function render( $atts ) {

			$params = $this->generate_string_params( $atts );

			echo do_shortcode( "[swissdelight_core_icon $params]" ); // XSS OK
		}
	}
}
