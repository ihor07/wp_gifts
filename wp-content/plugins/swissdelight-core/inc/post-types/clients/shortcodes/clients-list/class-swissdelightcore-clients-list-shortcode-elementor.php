<?php

class SwissDelightCore_Clients_List_Shortcode_Elementor extends SwissDelightCore_Elementor_Widget_Base {

	function __construct( array $data = [], $args = null ) {
		$this->set_shortcode_slug( 'swissdelight_core_clients_list' );

		parent::__construct( $data, $args );
	}
}

swissdelight_core_get_elementor_widgets_manager()->register_widget_type( new SwissDelightCore_Clients_List_Shortcode_Elementor() );