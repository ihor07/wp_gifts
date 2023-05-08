<?php

if ( ! function_exists( 'swissdelight_core_add_twitter_list_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function swissdelight_core_add_twitter_list_widget( $widgets ) {
		if ( qode_framework_is_installed( 'twitter' ) ) {
			$widgets[] = 'SwissDelightCore_Twitter_List_Widget';
		}

		return $widgets;
	}

	add_filter( 'swissdelight_core_filter_register_widgets', 'swissdelight_core_add_twitter_list_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class SwissDelightCore_Twitter_List_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$this->set_widget_option(
				array(
					'name'       => 'widget_title',
					'field_type' => 'text',
					'title'      => esc_html__( 'Title', 'swissdelight-core' ),
				)
			);
			$widget_mapped = $this->import_shortcode_options(
				array(
					'shortcode_base' => 'swissdelight_core_twitter_list',
				)
			);
			if ( $widget_mapped ) {
				$this->set_base( 'swissdelight_core_twitter_list' );
				$this->set_name( esc_html__( 'SwissDelight Twitter List', 'swissdelight-core' ) );
				$this->set_description( esc_html__( 'Add a twitter list element into widget areas', 'swissdelight-core' ) );
			}
		}

		public function render( $atts ) {
			$params = $this->generate_string_params( $atts );

			echo do_shortcode( "[swissdelight_core_twitter_list $params]" ); // XSS OK
		}
	}
}
