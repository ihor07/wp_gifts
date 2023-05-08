<?php

if ( ! function_exists( 'swissdelight_core_add_product_list_shortcode' ) ) {
	/**
	 * Function that is adding shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes - Array of registered shortcodes
	 *
	 * @return array
	 */
	function swissdelight_core_add_product_list_shortcode( $shortcodes ) {
		$shortcodes[] = 'SwissDelightCore_Product_List_Shortcode';

		return $shortcodes;
	}

	add_filter( 'swissdelight_core_filter_register_shortcodes', 'swissdelight_core_add_product_list_shortcode' );
}

if ( class_exists( 'SwissDelightCore_List_Shortcode' ) ) {
	class SwissDelightCore_Product_List_Shortcode extends SwissDelightCore_List_Shortcode {

		public function __construct() {
			$this->set_post_type( 'product' );
			$this->set_post_type_taxonomy( 'product_cat' );
			$this->set_post_type_additional_taxonomies( array( 'product_tag', 'product_type' ) );
			$this->set_layouts( apply_filters( 'swissdelight_core_filter_product_list_layouts', array() ) );
			$this->set_extra_options( apply_filters( 'swissdelight_core_filter_product_list_extra_options', array() ) );

			parent::__construct();
		}

		public function map_shortcode() {
			$this->set_shortcode_path( SWISSDELIGHT_CORE_PLUGINS_URL_PATH . '/woocommerce/shortcodes/product-list' );
			$this->set_base( 'swissdelight_core_product_list' );
			$this->set_name( esc_html__( 'Product List', 'swissdelight-core' ) );
			$this->set_description( esc_html__( 'Shortcode that displays list of products', 'swissdelight-core' ) );
			$this->set_category( esc_html__( 'SwissDelight Core', 'swissdelight-core' ) );
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'custom_class',
					'title'      => esc_html__( 'Custom Class', 'swissdelight-core' ),
				)
			);
			$this->map_list_options();
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'uneven_layout',
					'title'      => esc_html__( 'Uneven Layout', 'swissdelight-core' ),
					'options'       => swissdelight_core_get_select_type_options_pool( 'no_yes' ),
					'default_value' => '',
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'enable_line',
					'title'      => esc_html__( 'Enable Line', 'swissdelight-core' ),
					'options'       => swissdelight_core_get_select_type_options_pool( 'no_yes' ),
					'default_value' => '',
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'add_to_cart_position',
					'title'         => esc_html__( 'Add to Cart Position', 'swissdelight-core' ),
					'options'       => array(
						'center' => esc_html__( 'Center', 'swissdelight-core' ),
						'bottom' => esc_html__( 'Bottom', 'swissdelight-core' ),
					),
					'default_value' => 'center',
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'skin',
					'title'         => esc_html__( 'Skin', 'swissdelight-core' ),
					'options'       => array(
						''      => esc_html__( 'Default', 'swissdelight-core' ),
						'light' => esc_html__( 'Light', 'swissdelight-core' ),
						'dark'  => esc_html__( 'Dark', 'swissdelight-core' ),
					),
					'default_value' => '',
				)
			);
			$this->map_query_options( array( 'post_type' => $this->get_post_type() ) );
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'product_list_enable_filter_order_by',
					'title'      => esc_html__( 'Enable Order By Filter', 'swissdelight-core' ),
					'options'    => swissdelight_core_get_select_type_options_pool( 'yes_no' ),
					'group'      => esc_html__( 'Additional', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'select',
					'name'       => 'product_list_enable_filter_category',
					'title'      => esc_html__( 'Enable Category Filter', 'swissdelight-core' ),
					'options'    => swissdelight_core_get_select_type_options_pool( 'yes_no' ),
					'group'      => esc_html__( 'Additional', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'  => 'text',
					'name'        => 'filter_tax__in',
					'title'       => esc_html__( 'Filter Taxonomy IDs', 'swissdelight-core' ),
					'description' => esc_html__( 'Separate filter taxonomy IDs with commas', 'swissdelight-core' ),
					'group'      => esc_html__( 'Additional', 'swissdelight-core' ),
					'dependency'  => array(
						'show' => array(
							'product_list_enable_filter_category' => array(
								'values'        => 'yes',
							),
						),
					),
				)
			);
			$this->map_layout_options( array( 'layouts' => $this->get_layouts() ) );
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'enable_excerpt',
					'title'         => esc_html__( 'Enable Excerpt', 'swissdelight-core' ),
					'options'       => swissdelight_core_get_select_type_options_pool('no_yes'),
					'default_value' => '',
					'group'         => esc_html__( 'Layout', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'excerpt_length',
					'title'      => esc_html__( 'Excerpt Length', 'swissdelight-core' ),
					'group'      => esc_html__( 'Layout', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type'    => 'select',
					'name'          => 'enable_category',
					'title'         => esc_html__( 'Enable Category', 'swissdelight-core' ),
					'options'       => swissdelight_core_get_select_type_options_pool( 'yes_no' ),
					'default_value' => '',
					'group'         => esc_html__( 'Layout', 'swissdelight-core' ),
				)
			);
			$this->set_option(
				array(
					'field_type' => 'text',
					'name'       => 'content_margin',
					'title'      => esc_html__( 'Content Margin', 'swissdelight-core' ),
					'group'      => esc_html__( 'Layout', 'swissdelight-core' ),
				)
			);
            $this->set_option(
                array(
                    'field_type'    => 'select',
                    'name'          => 'appear_animation',
                    'title'         => esc_html__( 'Enable Appear Animation', 'swissdelight-core' ),
                    'options'       => swissdelight_core_get_select_type_options_pool( 'yes_no', false ),
                    'default_value' => 'yes',
                    'group'         => esc_html__( 'Animation Options', 'swissdelight-core' ),
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
			$this->map_additional_options( array( 'exclude_filter' => true ) );
			$this->map_extra_options();
		}

		public static function call_shortcode( $params ) {
			$html = qode_framework_call_shortcode( 'swissdelight_core_product_list', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function render( $options, $content = null ) {
			parent::render( $options );

			$atts = $this->get_atts();

			$atts['post_type']       = $this->get_post_type();
			$atts['taxonomy_filter'] = $this->get_post_type_taxonomy();

			// Additional query args
			$atts['additional_query_args'] = $this->get_additional_query_args( $atts );

			$atts['holder_classes'] = $this->get_holder_classes( $atts );
			$atts['content_style']  = $this->get_content_styles( $atts );

			$atts['query_result'] = new \WP_Query( swissdelight_core_get_query_params( $atts ) );
			$atts['slider_attr']  = $this->get_slider_data( $atts );
			$atts['data_attr']    = swissdelight_core_get_pagination_data( SWISSDELIGHT_CORE_REL_PATH, 'plugins/woocommerce/shortcodes', 'product-list', 'product', $atts );

			$atts['this_shortcode'] = $this;

			return swissdelight_core_get_template_part( 'plugins/woocommerce/shortcodes/product-list', 'templates/content', $atts['behavior'], $atts );
		}

		public function get_additional_query_args( $atts ) {
			$args = parent::get_additional_query_args( $atts );

			return $args;
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-woo-shortcode';
			$holder_classes[] = 'qodef-woo-product-list';
			$holder_classes[] = ! empty( $atts['layout'] ) ? 'qodef-item-layout--' . $atts['layout'] : '';
			$holder_classes[] = ! empty( $atts['skin'] ) ? 'qodef-item-skin--' . $atts['skin'] : '';
			$holder_classes[] = ! empty( $atts['add_to_cart_position'] ) ? 'qodef-button-position--' . $atts['add_to_cart_position'] : '';
			$holder_classes[] = 'yes' === $atts['uneven_layout'] ? 'qodef-uneven-layout' : '';
			$holder_classes[] = 'yes' === $atts['enable_line'] ? 'qodef-line-separator' : '';
            $holder_classes[] = 'yes' === $atts['appear_animation'] ? 'qodef-appear-animation--enabled' : '';
            $holder_classes[] = ! empty( $atts['hover_animation'] ) ? 'qodef-hover--' . $atts['hover_animation'] : '';

			$list_classes   = $this->get_list_classes( $atts );
			$holder_classes = array_merge( $holder_classes, $list_classes );

			return implode( ' ', $holder_classes );
		}

		private function get_content_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['content_margin'] ) ) {
				if ( qode_framework_string_ends_with_space_units( $atts['content_margin'] ) ) {
					$styles[] = 'margin: ' . $atts['content_margin'];
				} else {
					$styles[] = 'margin: ' . $atts['content_margin'] . 'px';
				}
			}

			return $styles;
		}

		public function get_item_classes( $atts ) {
			$item_classes      = $this->init_item_classes();
			$list_item_classes = $this->get_list_item_classes( $atts );

			$item_classes = array_merge( $item_classes, $list_item_classes );

			return implode( ' ', $item_classes );
		}

		public function get_title_styles( $atts ) {
			$styles = array();

			if ( ! empty( $atts['text_transform'] ) ) {
				$styles[] = 'text-transform: ' . $atts['text_transform'];
			}

			return $styles;
		}

	}
}
