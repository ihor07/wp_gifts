<?php

if ( ! function_exists( 'swissdelight_core_add_nutritional_values_list_shortcode' ) ) {
	/**
	 * Function that add shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes
	 *
	 * @return array
	 */
	function swissdelight_core_add_nutritional_values_list_shortcode( $shortcodes ) {
		$shortcodes[] = 'SwissDelightCore_Nutritional_Values_List_Shortcode';

		return $shortcodes;
	}

	add_filter( 'swissdelight_core_filter_register_shortcodes', 'swissdelight_core_add_nutritional_values_list_shortcode' );
}

if ( class_exists( 'SwissDelightCore_Shortcode' ) ) {
	class SwissDelightCore_Nutritional_Values_List_Shortcode extends SwissDelightCore_Shortcode {

		public function __construct() {
			$this->set_layouts( apply_filters( 'swissdelight_core_filter_nutritional_values_list_layouts', array() ) );

			parent::__construct();
		}

		public function map_shortcode() {
			$this->set_shortcode_path( SWISSDELIGHT_CORE_SHORTCODES_URL_PATH . '/item-showcase' );
			$this->set_base( 'swissdelight_core_nutritional_values_list' );
			$this->set_name( esc_html__( 'Nutritional Values List', 'swissdelight-core' ) );
			$this->set_description( esc_html__( 'Shortcode that adds nutritional values list holder', 'swissdelight-core' ) );
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
					'field_type' => 'repeater',
					'name'       => 'children',
					'title'      => esc_html__( 'Child elements', 'swissdelight-core' ),
					'items'      => array(
						array(
							'field_type' => 'text',
							'name'       => 'item_title',
							'title'      => esc_html__( 'Title', 'swissdelight-core' ),
						),
						array(
							'field_type'    => 'select',
							'name'          => 'item_title_tag',
							'title'         => esc_html__( 'Title Tag', 'swissdelight-core' ),
							'options'       => swissdelight_core_get_select_type_options_pool( 'title_tag', false ),
							'default_value' => 'h5',
						),
						array(
							'field_type' => 'color',
							'name'       => 'item_title_color',
							'title'      => esc_html__( 'Title Color', 'swissdelight-core' ),
						),
						array(
							'field_type' => 'text',
							'name'       => 'item_value',
							'title'      => esc_html__( 'Value', 'swissdelight-core' ),
						),
						array(
							'field_type' => 'color',
							'name'       => 'item_value_color',
							'title'      => esc_html__( 'Value Color', 'swissdelight-core' ),
						),
					)
				)
			);
		}

		public function render( $options, $content = null ) {
			parent::render( $options );
			$atts = $this->get_atts();

			$atts['holder_classes'] = $this->get_holder_classes( $atts );
			$atts['items']          = $this->parse_repeater_items( $atts['children'] );
			$atts['this_shortcode'] = $this;

			return swissdelight_core_get_template_part( 'shortcodes/nutritional-values-list', 'variations/' . $atts['layout'] . '/templates/' . $atts['layout'], '', $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();

			$holder_classes[] = 'qodef-nutritional-values-list';
			$holder_classes[] = ! empty( $atts['layout'] ) ? 'qodef-layout--' . $atts['layout'] : '';

			return implode( ' ', $holder_classes );
		}

		public function get_title_styles( $item ) {
			$styles = array();

			if ( ! empty( $item['item_title_color'] ) ) {
				$styles[] = 'color: ' . $item['item_title_color'];
			}

			return $styles;
		}

		public function get_value_styles( $item ) {
			$styles = array();

			if ( ! empty( $item['item_value_color'] ) ) {
				$styles[] = 'color: ' . $item['item_value_color'];
			}

			return $styles;
		}
	}
}
