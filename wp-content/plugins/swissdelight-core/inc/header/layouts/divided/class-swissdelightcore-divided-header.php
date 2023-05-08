<?php

class SwissDelightCore_Divided_Header extends SwissDelightCore_Header {
	private static $instance;

	public function __construct() {
		$this->set_layout( 'divided' );
		$this->set_search_layout( 'covers-header' );
		$this->default_header_height = 90;

		parent::__construct();
	}

	/**
	 * @return SwissDelightCore_Divided_Header
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
