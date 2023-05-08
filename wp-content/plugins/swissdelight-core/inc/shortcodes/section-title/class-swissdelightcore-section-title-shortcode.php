<?php

if ( ! function_exists( 'swissdelight_core_add_section_title_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function swissdelight_core_add_section_title_shortcode( $shortcodes ) {
		$shortcodes[] = 'SwissDelightCore_Section_Title_Shortcode';

		return $shortcodes;
	}

	add_filter( 'swissdelight_core_filter_register_shortcodes', 'swissdelight_core_add_section_title_shortcode' );
}

if ( class_exists( 'SwissDelightCore_Shortcode' ) ) {
	class SwissDelightCore_Section_Title_Shortcode extends SwissDelightCore_Shortcode {

		public function map_shortcode() {
			$this->set_shortcode_path( SWISSDELIGHT_CORE_SHORTCODES_URL_PATH . '/section-title' );
			$this->set_base( 'swissdelight_core_section_title' );
			$this->set_name( esc_html__( 'Section Title', 'swissdelight-core' ) );
			$this->set_description( esc_html__( 'Shortcode that adds section title element', 'swissdelight-core' ) );
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
					'name'       => 'tagline',
					'title'      => esc_html__( 'Tagline', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'title',
					'title'      => esc_html__( 'Title', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'subtitle',
					'title'      => esc_html__( 'Subtitle', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'text',
					'name'          => 'line_break_positions',
					'title'         => esc_html__( 'Positions of Line Break', 'swissdelight-core' ),
					'description'   => esc_html__( 'Enter the positions of the words after which you would like to create a line break. Separate the positions with commas (e.g. if you would like the first, third, and fourth word to have a line break, you would enter "1,3,4")', 'swissdelight-core' ),
					'group'         => esc_html__( 'Title Style', 'swissdelight-core' ),
					'default_value' => '-1',
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'disable_title_break_words',
					'title'         => esc_html__( 'Disable Title Line Break', 'swissdelight-core' ),
					'description'   => esc_html__( 'Enabling this option will disable title line breaks for screen size 1024 and lower', 'swissdelight-core' ),
					'options'       => swissdelight_core_get_select_type_options_pool( 'no_yes', false ),
					'default_value' => 'no',
					'group'         => esc_html__( 'Title Style', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'title_tag',
					'title'         => esc_html__( 'Title Tag', 'swissdelight-core' ),
					'options'       => swissdelight_core_get_select_type_options_pool( 'title_tag', '', '', array( 'span' => 'span' ) ),
					'default_value' => 'h2',
					'group'         => esc_html__( 'Title Style', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'title_color',
					'title'      => esc_html__( 'Title Color', 'swissdelight-core' ),
					'group'      => esc_html__( 'Title Style', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'tagline_color',
					'title'      => esc_html__( 'Tagline Color', 'swissdelight-core' ),
					'group'      => esc_html__( 'Title Style', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'subtitle_color',
					'title'      => esc_html__( 'Subtitle Color', 'swissdelight-core' ),
					'group'      => esc_html__( 'Title Style', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'link',
					'title'      => esc_html__( 'Title Custom Link', 'swissdelight-core' ),
					'group'      => esc_html__( 'Title Style', 'swissdelight-core' ),
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
					'group'         => esc_html__( 'Title Style', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'textarea',
					'name'       => 'text_title',
					'title'      => esc_html__( 'Text', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'color',
					'name'       => 'text_color',
					'title'      => esc_html__( 'Text Color', 'swissdelight-core' ),
					'group'      => esc_html__( 'Text Style', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'text_margin_top',
					'title'      => esc_html__( 'Text Margin Top', 'swissdelight-core' ),
					'group'      => esc_html__( 'Text Style', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'text_alignment',
					'title'      => esc_html__( 'Text Alignment', 'swissdelight-core' ),
					'options'    => array(
						''       => esc_html__( 'Default', 'swissdelight-core' ),
						'left'   => esc_html__( 'Left', 'swissdelight-core' ),
						'center' => esc_html__( 'Center', 'swissdelight-core' ),
						'right'  => esc_html__( 'Right', 'swissdelight-core' ),
					),
					'group'      => esc_html__( 'Text Style', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'content_alignment',
					'title'      => esc_html__( 'Content Alignment', 'swissdelight-core' ),
					'options'    => array(
						''       => esc_html__( 'Default', 'swissdelight-core' ),
						'left'   => esc_html__( 'Left', 'swissdelight-core' ),
						'center' => esc_html__( 'Center', 'swissdelight-core' ),
						'right'  => esc_html__( 'Right', 'swissdelight-core' ),
					),
				)
			);
            $this->set_option(
                array(
                    'field_type'    => 'select',
                    'name'          => 'appear_animation',
                    'title'         => esc_html__( 'Enable Appear Animation', 'swissdelight-core' ),
                    'options'       => swissdelight_core_get_select_type_options_pool( 'yes_no', false ),
                    'default_value' => 'yes',
                    'group'         => esc_html__( 'Animation Options', 'swissdelight-core' )
                )
            );
			$this->import_shortcode_options(
				array(
					'shortcode_base'    => 'swissdelight_core_button',
					'exclude'           => array( 'custom_class' ),
					'additional_params' => array(
						'group' => esc_html__( 'Button', 'swissdelight-core' ),
					),
				)
			);
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['holder_classes']  = $this->get_holder_classes( $atts );
			$atts['title']           = $this->get_modified_title( $atts );
			$atts['title_styles']    = $this->get_title_styles( $atts );
			$atts['tagline_styles']  = $this->get_tagline_styles( $atts );
			$atts['subtitle_styles'] = $this->get_subtitle_styles( $atts );
			$atts['text_styles']     = $this->get_text_styles( $atts );
			$atts['button_params']   = $this->generate_button_params( $atts );


			return swissdelight_core_get_template_part( 'shortcodes/section-title', 'templates/section-title', '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-section-title';
			$holder_classes[] = ! empty( $atts['content_alignment'] ) ? 'qodef-alignment--' . $atts['content_alignment'] : 'qodef-alignment--left';
			$holder_classes[] = 'yes' === $atts['disable_title_break_words'] ? 'qodef-title-break--disabled' : '';
			$holder_classes[] = 'yes' === $atts['appear_animation'] ? 'qodef-appear-animation--enabled' : '';

			return implode( ' ', $holder_classes );
		}

		private function get_modified_title( $atts ) {
			$title = $atts['title'];

			if ( ! empty( $title ) && ! empty( $atts['line_break_positions'] ) ) {
				$split_title          = explode( ' ', $title );
				$line_break_positions = explode( ',', str_replace( ' ', '', $atts['line_break_positions'] ) );

				foreach ( $line_break_positions as $position ) {
					$position = intval( $position );
					if ( isset( $split_title[ $position - 1 ] ) && ! empty( $split_title[ $position - 1 ] ) ) {
						$split_title[ $position - 1 ] = $split_title[ $position - 1 ] . '<br />';
					}
				}

				$title = implode( ' ', $split_title );
			} if ( ! empty( $atts['subtitle'] ) && count( explode( ' ', $title ) ) - 1 !== intval( $atts['line_break_positions'] ) ) {
				$split_title   = explode( ' ', $title );
				$subtitle_html = '<span class="qodef-m-subtitle" ' . qode_framework_get_inline_attr( $this->get_subtitle_styles( $atts ), 'style', ';' ) . '>' . qode_framework_wp_kses_html( 'content', $atts['subtitle'] ) . '</span>';

				$split_title[ count( $split_title ) - 1 ] = '<span class="qodef-subtitle-wrapper">' . $split_title[ count( $split_title ) - 1 ] . $subtitle_html . '</span>';

				$title = implode( ' ', $split_title );
			}

			return $title;
		}

		private function get_title_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['title_color'] ) ) {
				$styles[] = 'color: ' . $atts['title_color'];
			}

			return $styles;
		}

		private function get_tagline_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['tagline_color'] ) ) {
				$styles[] = 'color: ' . $atts['tagline_color'];
			}

			return $styles;
		}

		private function get_subtitle_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['subtitle_color'] ) ) {
				$styles[] = 'color: ' . $atts['subtitle_color'];
			}

			return $styles;
		}

		private function get_text_styles( $atts ) {
			$styles = array();

			if ( '' !== $atts['text_margin_top'] ) {
				if ( qode_framework_string_ends_with_space_units( $atts['text_margin_top'] ) ) {
					$styles[] = 'margin-top: ' . $atts['text_margin_top'];
				} else {
					$styles[] = 'margin-top: ' . intval( $atts['text_margin_top'] ) . 'px';
				}
			}

			if ( ! empty( $atts['text_color'] ) ) {
				$styles[] = 'color: ' . $atts['text_color'];
			}

			if ( ! empty( $atts['text_alignment'] ) ) {
				$styles[] = 'text-align: ' . $atts['text_alignment'];
			}

			return $styles;
		}

		private function generate_button_params( $atts ) {
			$params = $this->populate_imported_shortcode_atts(
				array(
					'shortcode_base' => 'swissdelight_core_button',
					'exclude'        => array( 'custom_class' ),
					'atts'           => $atts,
				)
			);

			return $params;
		}
	}
}
