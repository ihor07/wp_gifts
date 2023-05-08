<?php

if ( ! function_exists( 'swissdelight_core_add_sticky_sidebar_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function swissdelight_core_add_sticky_sidebar_widget( $widgets ) {
		$widgets[] = 'SwissDelightCore_Sticky_Sidebar_Widget';

		return $widgets;
	}

	add_filter( 'swissdelight_core_filter_register_widgets', 'swissdelight_core_add_sticky_sidebar_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class SwissDelightCore_Sticky_Sidebar_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$this->set_base( 'swissdelight_core_sticky_sidebar' );
			$this->set_name( esc_html__( 'SwissDelight Sticky Sidebar', 'swissdelight-core' ) );
			$this->set_description( esc_html__( 'Use this widget to make the sidebar sticky. Drag it into the sidebar above the widget which you want to be the first element in the sticky sidebar', 'swissdelight-core' ) );
		}

		public function render( $atts ) {
		}
	}
}
