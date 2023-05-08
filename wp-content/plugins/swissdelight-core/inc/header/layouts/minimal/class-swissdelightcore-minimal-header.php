<?php

class SwissDelightCore_Minimal_Header extends SwissDelightCore_Header {
	private static $instance;

	public function __construct() {
		$this->set_layout( 'minimal' );
		$this->set_search_layout( 'fullscreen' );
		$this->default_header_height = 100;

		add_action( 'swissdelight_action_before_wrapper_close_tag', array( $this, 'fullscreen_menu_template' ) );

		parent::__construct();
	}

	/**
	 * @return SwissDelightCore_Minimal_Header
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	function fullscreen_menu_template() {
		$parameters = array(
			'fullscreen_menu_in_grid' => 'yes' === swissdelight_core_get_post_value_through_levels( 'qodef_fullscreen_menu_in_grid' ),
		);

		swissdelight_core_template_part( 'fullscreen-menu', 'templates/full-screen-menu', '', $parameters );
	}
}
