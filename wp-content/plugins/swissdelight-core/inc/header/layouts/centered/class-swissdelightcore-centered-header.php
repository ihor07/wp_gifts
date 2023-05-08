<?php

class SwissDelightCore_Centered_Header extends SwissDelightCore_Header {
	private static $instance;

	public function __construct() {
		$this->set_layout( 'centered' );
		$this->default_header_height = 150;

		parent::__construct();
	}

	/**
	 * @return SwissDelightCore_Centered_Header
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
