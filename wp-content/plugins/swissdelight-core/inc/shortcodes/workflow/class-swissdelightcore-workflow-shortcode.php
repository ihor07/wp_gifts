<?php

if ( ! function_exists( 'swissdelight_core_add_workflow_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function swissdelight_core_add_workflow_shortcode( $shortcodes ) {
		$shortcodes[] = 'SwissDelightCore_Workflow_Shortcode';

		return $shortcodes;
	}

	add_filter( 'swissdelight_core_filter_register_shortcodes', 'swissdelight_core_add_workflow_shortcode' );
}

if ( class_exists( 'SwissDelightCore_Shortcode' ) ) {
	class SwissDelightCore_Workflow_Shortcode extends SwissDelightCore_Shortcode {

		public function map_shortcode() {
			$this->set_shortcode_path( SWISSDELIGHT_CORE_SHORTCODES_URL_PATH . '/workflow' );
			$this->set_base( 'swissdelight_core_workflow' );
			$this->set_name( esc_html__( 'Workflow', 'swissdelight-core' ) );
			$this->set_description( esc_html__( 'Shortcode that adds workflow element', 'swissdelight-core' ) );
			$this->set_category( esc_html__( 'SwissDelight Core', 'swissdelight-core' ) );
			$this->set_option( array(
				'field_type' => 'text',
				'name'       => 'custom_class',
				'title'      => esc_html__( 'Custom Class', 'swissdelight-core' ),
			) );
			$this->set_option( array(
				'field_type' => 'color',
				'name'       => 'play_button_color',
				'title'      => esc_html__( 'Workflow line color', 'swissdelight-core' )
			) );
			$this->set_option( array(
				'field_type' => 'select',
				'name'       => 'animate',
				'title'      => esc_html__( 'Animate Workflow', 'swissdelight-core' ),
				'options'    => swissdelight_core_get_select_type_options_pool( 'yes_no' ),
			) );
			$this->set_option( array(
				'field_type' => 'repeater',
				'name'       => 'children',
				'title'      => esc_html__( 'Child elements', 'swissdelight-core' ),
				'items'   => array(
                    array(
                        'field_type' => 'text',
                        'name'       => 'year',
                        'title'      => esc_html__( 'Year', 'swissdelight-core' )
                    ),
                    array(
                        'field_type' => 'text',
                        'name'       => 'text_padding',
                        'title'      => esc_html__( 'Text Padding', 'swissdelight-core' )
                    ),
                    array(
                        'field_type' => 'text',
                        'name'       => 'caption',
                        'title'      => esc_html__( 'Caption', 'swissdelight-core' )
                    ),
					array(
                        'field_type' => 'text',
                        'name'       => 'title',
                        'title'      => esc_html__( 'Title', 'swissdelight-core' )
                    ),
					array(
						'field_type' => 'textarea',
						'name'       => 'text',
						'title'      => esc_html__( 'Text', 'swissdelight-core' )
					),
					array(
						'field_type' => 'image',
						'name'       => 'image',
						'title'      => esc_html__( 'Image', 'swissdelight-core' ),
					),
				)
			) );
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['holder_classes']		= $this->get_holder_classes( $atts );
			$atts['line_styles'] = $this->get_line_styles( $atts );
            $atts['items']       = $this->parse_repeater_items( $atts['children'] );
			$atts['text_styles'] = $this->get_text_styles( $atts['items'] );

			return swissdelight_core_get_template_part( 'shortcodes/workflow', 'templates/workflow', '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-workflow';
			$holder_classes[] = $atts['animate'] === 'yes' ? 'qodef-workflow-animate' : '';

			return implode( ' ', $holder_classes );
		}

		private function get_line_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['line_color'] ) ) {
				$styles[] = 'background-color: ' . $atts['line_color'];
			}

			return implode( ';', $styles );
		}

        private function get_text_styles( $atts ) {
            $styles = array();

            if ( ! empty( $atts[0]['text_padding'] ) ) {
                $styles[] = 'padding: ' . $atts[0]['text_padding'];
            }

            return implode( ';', $styles );
        }
	}
}