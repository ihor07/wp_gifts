<?php

if ( ! function_exists( 'swissdelight_core_add_product_list_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function swissdelight_core_add_product_list_widget( $widgets ) {
		$widgets[] = 'SwissDelightCore_Product_List_Widget';

		return $widgets;
	}

	add_filter( 'swissdelight_core_filter_register_widgets', 'swissdelight_core_add_product_list_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class SwissDelightCore_Product_List_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$this->set_widget_option(
				array(
					'field_type' => 'text',
					'name'       => 'widget_title',
					'title'      => esc_html__( 'Title', 'swissdelight-core' ),
				)
			);
			$widget_mapped = $this->import_shortcode_options(
				array(
					'shortcode_base' => 'swissdelight_core_product_list',
				)
			);

			if ( $widget_mapped ) {
				$this->set_base( 'swissdelight_core_product_list' );
				$this->set_name( esc_html__( 'SwissDelight Product List', 'swissdelight-core' ) );
				$this->set_description( esc_html__( 'Display a list of products', 'swissdelight-core' ) );
			}
		}

		public function render( $atts ) {
			$params = $this->generate_string_params( $atts );

			echo do_shortcode( "[swissdelight_core_product_list $params]" ); // XSS OK
		}
	}
}
