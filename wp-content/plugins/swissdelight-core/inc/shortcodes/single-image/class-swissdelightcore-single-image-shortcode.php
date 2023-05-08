<?php

if ( ! function_exists( 'swissdelight_core_add_single_image_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param $shortcodes array
	 *
	 * @return array
	 */
	function swissdelight_core_add_single_image_shortcode( $shortcodes ) {
		$shortcodes[] = 'SwissDelightCore_Single_Image_Shortcode';

		return $shortcodes;
	}

	add_filter( 'swissdelight_core_filter_register_shortcodes', 'swissdelight_core_add_single_image_shortcode' );
}

if ( class_exists( 'SwissDelightCore_Shortcode' ) ) {
	class SwissDelightCore_Single_Image_Shortcode extends SwissDelightCore_Shortcode {

		public function __construct() {
			$this->set_layouts( apply_filters( 'swissdelight_core_filter_single_image_layouts', array() ) );
			$this->set_extra_options( apply_filters( 'swissdelight_core_filter_single_image_extra_options', array() ) );

			parent::__construct();
		}

		public function map_shortcode() {
			$this->set_shortcode_path( SWISSDELIGHT_CORE_SHORTCODES_URL_PATH . '/single-image' );
			$this->set_base( 'swissdelight_core_single_image' );
			$this->set_name( esc_html__( 'Single Image', 'swissdelight-core' ) );
			$this->set_description( esc_html__( 'Shortcode that adds image element', 'swissdelight-core' ) );
			$this->set_category( esc_html__( 'SwissDelight Core', 'swissdelight-core' ) );
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'swissdelight-core' ),
				)
			);

			$options_map = swissdelight_core_get_variations_options_map( $this->get_layouts() );

			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'layout',
					'title'         => esc_html__( 'Layout', 'swissdelight-core' ),
					'options'       => $this->get_layouts(),
					'default_value' => $options_map['default_value'],
					'visibility'    => array( 'map_for_page_builder' => $options_map['visibility'] ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'image',
					'name'       => 'image',
					'title'      => esc_html__( 'Image', 'swissdelight-core' ),
				)
			);

			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'image_size',
					'title'       => esc_html__( 'Image Size', 'swissdelight-core' ),
					'description' => esc_html__( 'For predefined image sizes input thumbnail, medium, large or full. If you wish to set a custom image size, type in the desired image dimensions in pixels (e.g. 400x400).', 'swissdelight-core' ),
				)
			);

			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'image_action',
					'title'      => esc_html__( 'Image Action', 'swissdelight-core' ),
					'options'    => array(
						''            => esc_html__( 'No Action', 'swissdelight-core' ),
						'open-popup'  => esc_html__( 'Open Popup', 'swissdelight-core' ),
						'custom-link' => esc_html__( 'Custom Link', 'swissdelight-core' ),
					),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'link',
					'title'      => esc_html__( 'Custom Link', 'swissdelight-core' ),
					'dependency' => array(
						'show' => array(
							'image_action' => array(
								'values'        => array( 'custom-link' ),
								'default_value' => '',
							),
						),
					),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'target',
					'title'         => esc_html__( 'Custom Link Target', 'swissdelight-core' ),
					'options'       => swissdelight_core_get_select_type_options_pool( 'link_target' ),
					'default_value' => '_self',
					'dependency'    => array(
						'show' => array(
							'image_action' => array(
								'values'        => 'custom-link',
								'default_value' => '',
							),
						),
					),
				)
			);
            $this->set_option(
                array(
                    'field_type'    => 'select',
                    'name'          => 'appear_animation',
                    'title'         => esc_html__( 'Enable Appear Animation', 'swissdelight-core' ),
                    'options'       => swissdelight_core_get_select_type_options_pool( 'yes_no', false ),
                    'default_value' => 'no',
                    'group'         => esc_html__( 'Animation Options', 'swissdelight-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'    => 'select',
                    'name'          => 'appear_animation_direction',
                    'title'         => esc_html__( 'Appear Animation Direction', 'swissdelight-core' ),
                    'options'       => array(
                        'right'  => esc_html__( 'Right - Default', 'swissdelight-core' ),
                        'left'   => esc_html__( 'Left', 'swissdelight-core' ),
                        'top'    => esc_html__( 'Top', 'swissdelight-core' ),
                        'bottom' => esc_html__( 'Bottom', 'swissdelight-core' )
                    ),
                    'default_value' => 'right',
                    'group'         => esc_html__( 'Animation Options', 'swissdelight-core' ),
                    'dependency'    => array(
                        'show' => array(
                            'appear_animation' => array(
                                'values'        => 'yes',
                                'default_value' => 'no',
                            ),
                        ),
                    )
                )
            );
            $this->set_option(
                array(
                    'field_type'    => 'text',
                    'name'          => 'appear_animation_delay',
                    'title'         => esc_html__( 'Appear Animation Delay (ms)', 'swissdelight-core' ),
                    'group'         => esc_html__( 'Animation Options', 'swissdelight-core' ),
                    'dependency'    => array(
                        'show' => array(
                            'appear_animation' => array(
                                'values'        => 'yes',
                                'default_value' => 'no',
                            ),
                        ),
                    )
                )
            );
            $this->set_option(
                array(
                    'field_type'    => 'select',
                    'name'          => 'float_animation',
                    'title'         => esc_html__( 'Enable Float Animation', 'swissdelight-core' ),
                    'options'       => swissdelight_core_get_select_type_options_pool( 'yes_no', false ),
                    'default_value' => 'no',
                    'group'         => esc_html__( 'Animation Options', 'swissdelight-core' )
                )
            );
            $this->set_option(
                array(
                    'field_type'    => 'text',
                    'name'          => 'float_animation_distance',
                    'title'         => esc_html__( 'Float Animation Distance', 'swissdelight-core' ),
                    'description'   => esc_html__( 'Default value is 30'),
                    'group'         => esc_html__( 'Animation Options', 'swissdelight-core' ),
                    'dependency'    => array(
                        'show' => array(
                            'float_animation' => array(
                                'values'        => 'yes',
                                'default_value' => 'no',
                            ),
                        ),
                    )
                )
            );
            $this->set_option(
                array(
                    'field_type'    => 'text',
                    'name'          => 'float_animation_smoothness',
                    'title'         => esc_html__( 'Float Animation Smoothness', 'swissdelight-core' ),
                    'description'   => esc_html__( 'Default value is 60'),
                    'group'         => esc_html__( 'Animation Options', 'swissdelight-core' ),
                    'dependency'    => array(
                        'show' => array(
                            'float_animation' => array(
                                'values'        => 'yes',
                                'default_value' => 'no',
                            ),
                        ),
                    )
                )
            );
            $this->set_option(
                array(
                    'field_type'    => 'select',
                    'name'          => 'hover_animation',
                    'title'         => esc_html__( 'Hover Animation', 'swissdelight-core' ),
                    'options'       => array(
                        'none' => esc_html__( 'None', 'swissdelight-core' ),
                        'move' => esc_html__( 'Move', 'swissdelight-core' )
                    ),
                    'default_value' => 'none',
                    'group'         => esc_html__( 'Animation Options', 'swissdelight-core' )
                )
            );
			$this->map_extra_options();
		}

		public static function call_shortcode( $params ) {
			$html = qode_framework_call_shortcode( 'swissdelight_core_single_image', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['holder_classes'] = $this->get_holder_classes( $atts );
            $atts['data_attrs']     = $this->get_data_attrs( $atts );
			$atts['image_params']   = $this->generate_image_params( $atts );
			$atts['image_styles']   = $this->get_image_styles( $atts );

			return swissdelight_core_get_template_part( 'shortcodes/single-image', 'variations/' . $atts['layout'] . '/templates/' . $atts['layout'], '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-single-image';
			$holder_classes[] = ! empty( $atts['layout'] ) ? 'qodef-layout--' . $atts['layout'] : '';
			$holder_classes[] = ! empty( $atts['image_action'] ) && 'open-popup' === $atts['image_action'] ? 'qodef-magnific-popup qodef-popup-gallery' : '';
			$holder_classes[] = ! empty( $atts['appear_animation'] ) && 'yes' === $atts['appear_animation'] ? 'qodef-appear-animation--enabled' : '';
			$holder_classes[] = ! empty( $atts['appear_animation_direction'] ) ? 'qodef-direction--' . $atts['appear_animation_direction'] : '';
            $holder_classes[] = ! empty( $atts['float_animation'] ) && 'yes' === $atts['float_animation'] ? 'qodef-float-animation--enabled' : '';
			$holder_classes[] = ! empty( $atts['hover_animation'] ) ? 'qodef-hover--' . $atts['hover_animation'] : '';

			return implode( ' ', $holder_classes );
		}

		private function get_data_attrs( $atts ) {
            $data = array();

            if ( ! empty( $atts['float_animation_distance'] ) ) {
                $data['data-float-distance'] = $atts['float_animation_distance'];
            }

            if ( ! empty( $atts['float_animation_smoothness'] ) ) {
                $data['data-float-smoothness'] = $atts['float_animation_smoothness'];
            }

            return $data;
        }

		private function generate_image_params( $atts ) {
			$image = array();

			if ( ! empty( $atts['image'] ) ) {
				$id = $atts['image'];

				$image['image_id'] = intval( $id );
				$image_original    = wp_get_attachment_image_src( $id, 'full' );
				$image['url']      = $image_original[0];
				$image['alt']      = get_post_meta( $id, '_wp_attachment_image_alt', true );

				$image_size = trim( $atts['image_size'] );
				preg_match_all( '/\d+/', $image_size, $matches ); /* check if numeral width and height are entered */
				if ( in_array( $image_size, array( 'thumbnail', 'thumb', 'medium', 'large', 'full' ), true ) ) {
					$image['image_size'] = $image_size;
				} elseif ( ! empty( $matches[0] ) ) {
					$image['image_size'] = array(
						$matches[0][0],
						$matches[0][1],
					);
				} else {
					$image['image_size'] = 'full';
				}
			}

			return $image;
		}

		private function get_image_styles( $atts ) {
            $styles = array();

            if ( '' !== $atts['appear_animation_delay'] ) {
                $styles[] = 'transition-delay: ' . intval( $atts['appear_animation_delay'] ) . 'ms';
            }

            return $styles;
        }
	}
}
