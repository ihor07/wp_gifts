<?php

if ( ! function_exists( 'swissdelight_core_add_stamp_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function swissdelight_core_add_stamp_shortcode( $shortcodes ) {
		$shortcodes[] = 'SwissDelightCore_Stamp_Shortcode';

		return $shortcodes;
	}

	add_filter( 'swissdelight_core_filter_register_shortcodes', 'swissdelight_core_add_stamp_shortcode' );
}

if ( class_exists( 'SwissDelightCore_Shortcode' ) ) {
	class SwissDelightCore_Stamp_Shortcode extends SwissDelightCore_Shortcode {

		public function map_shortcode() {
			$this->set_shortcode_path( SWISSDELIGHT_CORE_SHORTCODES_URL_PATH . '/stamp' );
			$this->set_base( 'swissdelight_core_stamp' );
			$this->set_name( esc_html__( 'Stamp', 'swissdelight-core' ) );
			$this->set_description( esc_html__( 'Shortcode that adds stamp element', 'swissdelight-core' ) );
			$this->set_category( esc_html__( 'SwissDelight Core', 'swissdelight-core' ) );
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'textfield',
					'name'       => 'text',
					'title'      => esc_html__( 'Stamp Text', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'text_color',
					'title'      => esc_html__( 'Text Color', 'swissdelight-core' ),
					'dependency' => array(
						'hide' => array(
							'text' => array(
								'values' => '',
							),
						),
					),
					'group'      => esc_html__( 'Text Settings', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'textfield',
					'name'       => 'text_font_size',
					'title'      => esc_html__( 'Text Font Size (px)', 'swissdelight-core' ),
					'dependency' => array(
						'hide' => array(
							'text' => array(
								'values' => '',
							),
						),
					),
					'group'      => esc_html__( 'Text Settings', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'textfield',
					'name'       => 'centered_text',
					'title'      => esc_html__( 'Centered Text', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'vertical_line',
					'title'      => esc_html__( 'Enable Vertical Line', 'swissdelight-core' ),
					'options'    => swissdelight_core_get_select_type_options_pool( 'yes_no', false ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'centered_text_color',
					'title'      => esc_html__( 'Centered Text Color', 'swissdelight-core' ),
					'dependency' => array(
						'hide' => array(
							'centered_text' => array(
								'values' => '',
							),
						),
					),
					'group'      => esc_html__( 'Text Settings', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'textfield',
					'name'       => 'centered_text_font_size',
					'title'      => esc_html__( 'Centered Text Font Size (px)', 'swissdelight-core' ),
					'dependency' => array(
						'hide' => array(
							'centered_text' => array(
								'values' => '',
							),
						),
					),
					'group'      => esc_html__( 'Text Settings', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'textfield',
					'name'        => 'stamp_size',
					'title'       => esc_html__( 'Stamp Size (px)', 'swissdelight-core' ),
					'description' => esc_html__( 'Default value is 114', 'swissdelight-core' ),
					'dependency'  => array(
						'hide' => array(
							'text' => array(
								'values' => '',
							),
						),
					),
					'group'       => esc_html__( 'Text Settings', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'disable_stamp',
					'title'      => esc_html__( 'Disable Stamp', 'swissdelight-core' ),
					'group'      => esc_html__( 'Visibility', 'swissdelight-core' ),
					'options'    => array(
						''     => esc_html__( 'Never', 'swissdelight-core' ),
						'1440' => esc_html__( 'Below 1440px', 'swissdelight-core' ),
						'1280' => esc_html__( 'Below 1280px', 'swissdelight-core' ),
						'1024' => esc_html__( 'Below 1024px', 'swissdelight-core' ),
						'768'  => esc_html__( 'Below 768px', 'swissdelight-core' ),
						'680'  => esc_html__( 'Below 680px', 'swissdelight-core' ),
						'480'  => esc_html__( 'Below 480px', 'swissdelight-core' ),
					),
					'dependency' => array(
						'hide' => array(
							'text' => array(
								'values' => '',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'textfield',
					'name'        => 'appearing_delay',
					'title'       => esc_html__( 'Appearing Delay (ms)', 'swissdelight-core' ),
					'description' => esc_html__( 'Default value is 0', 'swissdelight-core' ),
					'dependency'  => array(
						'hide' => array(
							'text' => array(
								'values' => '',
							),
						),
					),
					'group'       => esc_html__( 'Visibility', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'absolute_position',
					'title'      => esc_html__( 'Enable Absolute Position', 'swissdelight-core' ),
					'options'    => swissdelight_core_get_select_type_options_pool( 'no_yes', false ),
					'dependency' => array(
						'hide' => array(
							'text' => array(
								'values' => '',
							),
						),
					),
					'group'      => esc_html__( 'Visibility', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'textfield',
					'name'       => 'top_position',
					'title'      => esc_html__( 'Top Position (px or %)', 'swissdelight-core' ),
					'dependency' => array(
						'show' => array(
							'absolute_position' => array(
								'values'        => 'yes',
								'default_value' => 'no',
							),
						),
					),
					'group'      => esc_html__( 'Visibility', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'textfield',
					'name'       => 'bottom_position',
					'title'      => esc_html__( 'Bottom Position (px or %)', 'swissdelight-core' ),
					'dependency' => array(
						'show' => array(
							'absolute_position' => array(
								'values'        => 'yes',
								'default_value' => 'no',
							),
						),
					),
					'group'      => esc_html__( 'Visibility', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'textfield',
					'name'       => 'left_position',
					'title'      => esc_html__( 'Left Position (px or %)', 'swissdelight-core' ),
					'dependency' => array(
						'show' => array(
							'absolute_position' => array(
								'values'        => 'yes',
								'default_value' => 'no',
							),
						),
					),
					'group'      => esc_html__( 'Visibility', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'textfield',
					'name'       => 'right_position',
					'title'      => esc_html__( 'Right Position (px or %)', 'swissdelight-core' ),
					'dependency' => array(
						'show' => array(
							'absolute_position' => array(
								'values'        => 'yes',
								'default_value' => 'no',
							),
						),
					),
					'group'      => esc_html__( 'Visibility', 'swissdelight-core' ),
				)
			);
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['holder_classes']       = $this->getHolderClasses( $atts );
			$atts['holder_styles']        = $this->getHolderStyles( $atts );
			$atts['centered_text_styles'] = $this->getCenteredTextStyles( $atts );
			$atts['holder_data']          = $this->getHolderData( $atts );
			$atts['text_data']            = $this->getModifiedText( $atts );

			return swissdelight_core_get_template_part( 'shortcodes/stamp', 'templates/stamp', '', $atts );
		}

		private function getHolderClasses( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-stamp';
			$holder_classes[] = ! empty( $atts['disable_stamp'] ) ? 'qodef-hide-on--' . $atts['disable_stamp'] : '';
			$holder_classes[] = 'yes' === $atts['absolute_position'] ? 'qodef--abs' : '';
			$holder_classes[] = 'yes' === $atts['vertical_line'] ? 'qodef--vertical-line' : '';

			return implode( ' ', $holder_classes );
		}

		private function getHolderStyles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['text_font_size'] ) ) {
				$styles[] = 'font-size: ' . intval( $atts['text_font_size'] ) . 'px';
			}
			if ( ! empty( $atts['text_color'] ) ) {
				$styles[] = 'color: ' . $atts['text_color'];
			}

			if ( ! empty( $atts['stamp_size'] ) ) {
				$styles[] = 'width: ' . intval( $atts['stamp_size'] ) . 'px';
				$styles[] = 'height: ' . intval( $atts['stamp_size'] ) . 'px';
			}

			if ( '' !== $atts['top_position'] && ! empty( $atts['top_position'] ) ) {
				$styles[] = 'top: ' . $atts['top_position'];
			}

			if ( '' !== $atts['bottom_position'] && ! empty( $atts['bottom_position'] ) ) {
				$styles[] = 'bottom: ' . $atts['bottom_position'];
			}

			if ( '' !== $atts['left_position'] && ! empty( $atts['left_position'] ) ) {
				$styles[] = 'left: ' . $atts['left_position'];
			}

			if ( '' !== $atts['right_position'] && ! empty( $atts['right_position'] ) ) {
				$styles[] = 'right: ' . $atts['right_position'];
			}

			return implode( ';', $styles );
		}

		private function getCenteredtextStyles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['centered_text_font_size'] ) ) {
				$styles[] = 'font-size: ' . intval( $atts['centered_text_font_size'] ) . 'px';
			}
			if ( ! empty( $atts['centered_text_color'] ) ) {
				$styles[] = 'color: ' . $atts['centered_text_color'];
			}

			return implode( ';', $styles );
		}

		private function getHolderData( $atts ) {
			$slider_data = array();

			$slider_data['data-appearing-delay'] = ! empty( $atts['appearing_delay'] ) ? intval( $atts['appearing_delay'] ) : 0;

			return $slider_data;
		}

		private function getModifiedText( $atts ) {
			$text = $atts['text'];
			$data = array(
				'text'  => $this->get_split_text( $text ),
				'count' => count( $this->str_split_unicode( $text ) ),
			);

			return $data;
		}

		private function str_split_unicode( $str ) {
			return preg_split( '~~u', $str, - 1, PREG_SPLIT_NO_EMPTY );
		}

		private function get_split_text( $text ) {
			if ( ! empty( $text ) ) {
				$split_text = $this->str_split_unicode( $text );

				foreach ( $split_text as $key => $value ) {
					$split_text[ $key ] = '<span class="qodef-m-character">' . $value . '</span>';
				}

				return implode( ' ', $split_text );
			}

			return $text;
		}
	}
}
