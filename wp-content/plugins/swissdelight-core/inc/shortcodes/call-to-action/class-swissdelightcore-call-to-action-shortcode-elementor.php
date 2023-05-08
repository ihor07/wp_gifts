<?php

class SwissDelightCore_Call_To_Action_Shortcode_Elementor extends SwissDelightCore_Elementor_Widget_Base {

	function __construct( array $data = [], $args = null ) {
		$this->set_shortcode_slug( 'swissdelight_core_call_to_action' );

		parent::__construct( $data, $args );
	}
}

swissdelight_core_get_elementor_widgets_manager()->register_widget_type( new SwissDelightCore_Call_To_Action_Shortcode_Elementor() );
