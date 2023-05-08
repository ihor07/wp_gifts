<?php

if ( ! function_exists( 'swissdelight_core_add_video_button_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function swissdelight_core_add_video_button_shortcode( $shortcodes ) {
		$shortcodes[] = 'SwissDelightCore_Video_Button_Shortcode';

		return $shortcodes;
	}

	add_filter( 'swissdelight_core_filter_register_shortcodes', 'swissdelight_core_add_video_button_shortcode' );
}

if ( class_exists( 'SwissDelightCore_Shortcode' ) ) {
	class SwissDelightCore_Video_Button_Shortcode extends SwissDelightCore_Shortcode {

		public function map_shortcode() {
			$this->set_shortcode_path( SWISSDELIGHT_CORE_SHORTCODES_URL_PATH . '/video-button' );
			$this->set_base( 'swissdelight_core_video_button' );
			$this->set_name( esc_html__( 'Video Button', 'swissdelight-core' ) );
			$this->set_description( esc_html__( 'Shortcode that adds video button element', 'swissdelight-core' ) );
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
					'field_type' => 'text',
					'name'       => 'video_link',
					'title'      => esc_html__( 'Video Link', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'image',
					'name'        => 'video_image',
					'title'       => esc_html__( 'Image', 'swissdelight-core' ),
					'description' => esc_html__( 'Select image from media library', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'play_button_color',
					'title'      => esc_html__( 'Play Button Color', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'play_button_size',
					'title'      => esc_html__( 'Play Button Size (px)', 'swissdelight-core' ),
				)
			);

            $this->set_option(
                array(
                    'field_type' => 'textfield',
                    'name'       => 'text',
                    'title'      => esc_html__( 'Stamp Text', 'swissdelight-core' ),
                )
            );
		}

		public static function call_shortcode( $params ) {
			$html = qode_framework_call_shortcode( 'swissdelight_core_video_button', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['holder_classes']     = $this->get_holder_classes( $atts );
			$atts['play_button_styles'] = $this->get_play_button_styles( $atts );
            $atts['holder_data']        = $this->getHolderData( $atts );
            $atts['text_data']          = $this->getModifiedText( $atts );

			return swissdelight_core_get_template_part( 'shortcodes/video-button', 'templates/video-button', '', $atts );
		}

        private function getModifiedText( $atts ) {
            $text = $atts['text'];
            $data = array(
                'text'  => $this->get_split_text( $text ),
                'count' => count( $this->str_split_unicode( $text ) )
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

        private function getHolderData( $atts ) {
            $slider_data = array();

            $slider_data['data-appearing-delay'] = ! empty( $atts['appearing_delay'] ) ? intval( $atts['appearing_delay'] ) : 0;

            return $slider_data;
        }

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-video-button qodef-hover-animation--follow';
			$holder_classes[] = ! empty( $atts['video_image'] ) ? 'qodef--has-img' : '';
			$holder_classes[] = ! empty( $atts['text'] ) ? 'qodef-stamped' : '';

			return implode( ' ', $holder_classes );
		}

		private function get_play_button_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['play_button_color'] ) ) {
				$styles[] = 'color: ' . $atts['play_button_color'];
			}

			if ( ! empty( $atts['play_button_size'] ) ) {
				if ( qode_framework_string_ends_with_typography_units( $atts['play_button_size'] ) ) {
					$styles[] = 'font-size: ' . $atts['play_button_size'];
				} else {
					$styles[] = 'font-size: ' . intval( $atts['play_button_size'] ) . 'px';
				}
			}

			return implode( ';', $styles );
		}
	}
}
