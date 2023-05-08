<?php

class SwissDelightCore_Vertical_Header extends SwissDelightCore_Header {
	private static $instance;

	public function __construct() {
		$this->set_layout( 'vertical' );
		$this->set_overriding_whole_header( true );

		parent::__construct();
	}

	/**
	 * @return SwissDelightCore_Vertical_Header
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function enqueue_additional_assets() {
		wp_enqueue_style( 'perfect-scrollbar', SWISSDELIGHT_CORE_URL_PATH . 'assets/plugins/perfect-scrollbar/perfect-scrollbar.css', array() );
		wp_enqueue_script( 'perfect-scrollbar', SWISSDELIGHT_CORE_URL_PATH . 'assets/plugins/perfect-scrollbar/perfect-scrollbar.jquery.min.js', array( 'jquery' ), false, true );
	}

	public function set_nav_menu_header_selector( $selector ) {
		return '.qodef-header--vertical .qodef-header-vertical-navigation';
	}

	public function set_nav_menu_narrow_header_selector( $selector ) {
		return '';
	}
}
