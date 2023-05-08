<?php

if ( ! function_exists( 'swissdelight_core_add_reservation_form_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function swissdelight_core_add_reservation_form_shortcode( $shortcodes ) {
		$shortcodes[] = 'SwissDelightCore_Reservation_Form_Shortcode';

		return $shortcodes;
	}

	add_filter( 'swissdelight_core_filter_register_shortcodes', 'swissdelight_core_add_reservation_form_shortcode', 9 );
}

if ( class_exists( 'SwissDelightCore_Shortcode' ) ) {
	class SwissDelightCore_Reservation_Form_Shortcode extends SwissDelightCore_Shortcode {

		public function map_shortcode() {
			$this->set_shortcode_path( SWISSDELIGHT_CORE_CPT_URL_PATH . '/restaurant-menu/shortcodes/reservation-form' );
			$this->set_base( 'swissdelight_core_reservation_form' );
			$this->set_name( esc_html__( 'Reservation Form', 'swissdelight-core' ) );
			$this->set_description( esc_html__( 'Shortcode that displays reservation form with provided parameters', 'swissdelight-core' ) );
			$this->set_category( esc_html__( 'SwissDelight Core', 'swissdelight-core' ) );
			$this->set_scripts(
				array(
					'datepicker' => array(
						'registered' => false,
						'url'        => SWISSDELIGHT_CORE_CPT_URL_PATH . '/restaurant-menu/shortcodes/reservation-form/assets/js/plugins/datepicker.min.js',
						'dependency' => array( 'jquery' ),
					),
				)
			);
			$this->set_necessary_styles(
				array(
					'datepicker' => array(
						'registered' => false,
						'url'        => SWISSDELIGHT_CORE_CPT_URL_PATH . '/restaurant-menu/shortcodes/reservation-form/assets/css/plugins/jQueryDatepicker.css',
					),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'open_table_id',
					'title'      => esc_html__( 'OpenTable ID', 'swissdelight-core' ),
				)
			);
		}

		public function load_assets() {
			wp_enqueue_script( 'datepicker' );
			wp_enqueue_style( 'datepicker' );
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['holder_classes'] = $this->get_holder_classes( $atts );

			return swissdelight_core_get_template_part( 'post-types/restaurant-menu/shortcodes/reservation-form', 'templates/reservation-form', '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-reservation-form';

			return implode( ' ', $holder_classes );
		}
	}
}
