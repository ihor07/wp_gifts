<?php

class SwissDelightCore_Instagram_List_Shortcode_Elementor extends SwissDelightCore_Elementor_Widget_Base {

	function __construct( array $data = [], $args = null ) {
		$this->set_shortcode_slug( 'swissdelight_core_instagram_list' );

		parent::__construct( $data, $args );
	}
}

if ( qode_framework_is_installed( 'instagram' ) ) {
	swissdelight_core_get_elementor_widgets_manager()->register_widget_type( new SwissDelightCore_Instagram_List_Shortcode_Elementor() );
}
