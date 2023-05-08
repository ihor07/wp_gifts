<?php

if ( ! function_exists( 'swissdelight_core_add_social_icons_group_widget' ) ) {
	/**
	 * function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function swissdelight_core_add_social_icons_group_widget( $widgets ) {
		$widgets[] = 'SwissDelightCore_Social_Icons_Group_Widget';

		return $widgets;
	}

	add_filter( 'swissdelight_core_filter_register_widgets', 'swissdelight_core_add_social_icons_group_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class SwissDelightCore_Social_Icons_Group_Widget extends QodeFrameworkWidget {
		public $no_of_icons = 5;

		public function map_widget() {
			$this->set_base( 'swissdelight_core_social_icons_group' );
			$this->set_name( esc_html__( 'SwissDelight Social Icons Group', 'swissdelight-core' ) );
			$this->set_description( sprintf( esc_html__( 'Use this widget to add a group of up to %s social icons to a widget area.', 'swissdelight-core' ), $this->no_of_icons ) );
			$this->set_widget_option(
				array(
					'field_type' => 'text',
					'name'       => 'widget_title',
					'title'      => esc_html__( 'Title', 'swissdelight-core' ),
				)
			);
			$this->set_widget_option(
				array(
					'field_type'    => 'select',
					'name'          => 'icon_layout',
					'title'         => esc_html__( 'Layout', 'swissdelight-core' ),
					'options'       => array(
						'normal' => esc_html__( 'Normal', 'swissdelight-core' ),
						'circle' => esc_html__( 'Circle', 'swissdelight-core' ),
						'square' => esc_html__( 'Square', 'swissdelight-core' ),
					),
					'default_value' => 'normal',
				)
			);
			for ( $i = 1; $i <= $this->no_of_icons; $i ++ ) {
				$this->set_widget_option(
					array(
						'field_type' => 'iconpack',
						'name'       => 'main_icon_' . $i,
						'title'      => sprintf( esc_html__( 'Icon %s', 'swissdelight-core' ), $i ),
					)
				);
				$this->set_widget_option(
					array(
						'field_type' => 'text',
						'name'       => 'link_' . $i,
						'title'      => sprintf( esc_html__( 'Link %s', 'swissdelight-core' ), $i ),
					)
				);
				$this->set_widget_option(
					array(
						'field_type'    => 'select',
						'name'          => 'target_' . $i,
						'title'         => sprintf( esc_html__( 'Link %s Target', 'swissdelight-core' ), $i ),
						'options'       => swissdelight_core_get_select_type_options_pool( 'link_target', false ),
						'default_value' => '_blank',
					)
				);
				$this->set_widget_option(
					array(
						'field_type' => 'text',
						'name'       => 'custom_size_' . $i,
						'title'      => sprintf( esc_html__( 'Icon %s Size', 'swissdelight-core' ), $i ),
					)
				);
				$this->set_widget_option(
					array(
						'field_type' => 'text',
						'name'       => 'margin_' . $i,
						'title'      => sprintf( esc_html__( 'Icon %s Margin', 'swissdelight-core' ), $i ),
					)
				);
				$this->set_widget_option(
					array(
						'field_type' => 'color',
						'name'       => 'icon_color_' . $i,
						'title'      => sprintf( esc_html__( 'Icon %s Color', 'swissdelight-core' ), $i ),
					)
				);
				$this->set_widget_option(
					array(
						'field_type' => 'color',
						'name'       => 'icon_background_color_' . $i,
						'title'      => sprintf( esc_html__( 'Icon %s Background Color', 'swissdelight-core' ), $i ),
					)
				);
				$this->set_widget_option(
					array(
						'field_type' => 'color',
						'name'       => 'icon_hover_color_' . $i,
						'title'      => sprintf( esc_html__( 'Icon %s Hover Color', 'swissdelight-core' ), $i ),
					)
				);
				$this->set_widget_option(
					array(
						'field_type' => 'color',
						'name'       => 'icon_hover_background_color_' . $i,
						'title'      => sprintf( esc_html__( 'Icon %s Hover Background Color', 'swissdelight-core' ), $i ),
					)
				);
			}
		}

		public function render( $atts ) { ?>
			<div class="qodef-social-icons-group">
				<?php
				for ( $i = 1; $i <= $this->no_of_icons; $i ++ ) {
					$selected_icon_pack = str_replace( '-', '_', $atts[ 'main_icon_' . $i ] );

					if ( ! empty( $atts[ 'main_icon_' . $i . '_' . $selected_icon_pack ] ) ) {
						$params = array(
							'main_icon'                        => $atts[ 'main_icon_' . $i ],
							'main_icon_' . $selected_icon_pack => $atts[ 'main_icon_' . $i . '_' . $selected_icon_pack ],
							'link'                             => $atts[ 'link_' . $i ],
							'target'                           => $atts[ 'target_' . $i ],
							'custom_size'                      => $atts[ 'custom_size_' . $i ],
							'margin'                           => $atts[ 'margin_' . $i ],
							'background_color'                 => $atts[ 'icon_background_color_' . $i ],
							'color'                            => $atts[ 'icon_color_' . $i ],
							'hover_background_color'           => $atts[ 'icon_hover_background_color_' . $i ],
							'hover_color'                      => $atts[ 'icon_hover_color_' . $i ],
							'icon_layout'                      => $atts['icon_layout'],
						);

						$params = $this->generate_string_params( $params );

						echo do_shortcode( "[swissdelight_core_icon $params]" ); // XSS OK
					}
				}
				?>
			</div>
			<?php
		}
	}
}
