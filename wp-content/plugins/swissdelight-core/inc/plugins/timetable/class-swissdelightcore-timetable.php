<?php

if ( ! class_exists( 'SwissDelightCore_Timetable' ) ) {
	class SwissDelightCore_Timetable {
		private static $instance;

		public function __construct() {
			// Include helper functions
			include_once SWISSDELIGHT_CORE_PLUGINS_PATH . '/timetable/helper.php';

			if ( qode_framework_is_installed( 'timetable' ) ) {
				// Init
				$this->init();

				// Add module body classes
				add_filter( 'body_class', array( $this, 'add_body_classes' ) );
			}
		}

		/**
		 * @return SwissDelightCore_Timetable
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function init() {
			// Set dashboard admin options map position
			add_filter( 'swissdelight_core_filter_admin_options_map_position', array( $this, 'set_map_position' ), 10, 2 );

			// Include options
			include_once SWISSDELIGHT_CORE_PLUGINS_PATH . '/timetable/dashboard/admin/timetable-options.php';
		}

		function set_map_position( $position, $map ) {

			if ( 'timetable' === $map ) {
				$position = 56;
			}

			return $position;
		}

		function add_body_classes( $classes ) {
			$predefined_style = swissdelight_core_get_option_value( 'admin', 'qodef_enable_timetable_predefined_style' );

			if ( 'yes' === $predefined_style ) {
				$classes[] = 'qodef-timetable--predefined';
			}

			return $classes;
		}
	}

	SwissDelightCore_Timetable::get_instance();
}
