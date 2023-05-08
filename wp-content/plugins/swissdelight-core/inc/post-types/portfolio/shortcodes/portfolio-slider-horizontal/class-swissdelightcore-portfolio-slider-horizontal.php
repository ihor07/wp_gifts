<?php

if ( ! function_exists( 'swissdelight_core_portfolio_slider_horizontal_shortcode' ) ) {
	/**
	 * Function that is adding shortcode into shortcodes list for registration
	 *
	 * @param array $shortcodes - Array of registered shortcodes
	 *
	 * @return array
	 */
	function swissdelight_core_portfolio_slider_horizontal_shortcode( $shortcodes ) {
		$shortcodes[] = 'SwissDelightCorePortfolioSliderHorizontalShortcode';

		return $shortcodes;
	}

	add_filter( 'swissdelight_core_filter_register_shortcodes', 'swissdelight_core_portfolio_slider_horizontal_shortcode' );
}

if ( class_exists( 'SwissDelightCore_List_Shortcode' ) ) {
	class SwissDelightCorePortfolioSliderHorizontalShortcode extends SwissDelightCore_List_Shortcode {

		public function __construct() {
			$this->set_post_type( 'portfolio-item' );
			$this->set_post_type_taxonomy( 'portfolio-category' );
			$this->set_layouts( apply_filters( 'swissdelight_core_filter_portfolio_slider_horizontal_layouts', array() ) );
			$this->set_extra_options( apply_filters( 'swissdelight_core_filter_portfolio_slider_horizontal_extra_options', array() ) );
			$this->set_hover_animation_options( apply_filters( 'swissdelight_core_filter_portfolio_list_hover_animation_options', array() ) );
			parent::__construct();
		}

		public function map_shortcode() {
			$this->set_shortcode_path( SWISSDELIGHT_CORE_CPT_URL_PATH . '/portfolio/shortcodes/portfolio-slider-horizontal' );
			$this->set_base( 'swissdelight_core_portfolio_slider_horizontal' );
			$this->set_name( esc_html__( 'Portfolio Slider Horizontal', 'swissdelight-core' ) );
			$this->set_description( esc_html__( 'Shortcode that displays horizontal portfolio slider', 'swissdelight-core' ) );
			$this->set_category( esc_html__( 'SwissDelight Core', 'swissdelight-core' ) );
			$this->set_option( array(
                   'field_type' => 'text',
                   'name'       => 'custom_class',
                   'title'      => esc_html__( 'Custom Class', 'swissdelight-core' )
               ) );
			$this->set_option( array(
                   'field_type'    => 'select',
                   'name'          => 'behavior',
                   'title'         => esc_html__( 'List Appearance', 'swissdelight-core' ),
                   'options'       => array(
	                   'portfolio-slider-horizontal' => esc_html__( 'Portfolio Slider Horizontal', 'swissdelight-core' )
                   ),
                   'default_value' => 'portfolio-slider-horizontal',
                   'visibility'    => array( 'map_for_page_builder' => false )
               ) );
			$this->set_option( array(
               'field_type'    => 'select',
               'name'          => 'layout',
               'title'         => esc_html__( 'Item Layout', 'swissdelight-core' ),
               'options'       => array(
                   'info-on-hover' => esc_html__( 'Info On Hover', 'swissdelight-core' )
               ),
               'default_value' => 'info-bottom-left',
               'group'         => esc_html__( 'Layout', 'swissdelight-core' ),
               'visibility'    => array( 'map_for_page_builder' => false )
           ) );
			
			$this->map_query_options( array( 'post_type' => $this->get_post_type(), 'posts_per_page_visibility' => false ) );
		}

		public static function call_shortcode( $params ) {
			$html = qode_framework_call_shortcode( 'swissdelight_core_portfolio_slider_horizontal', $params );
			$html = str_replace( "\n", '', $html );

			return $html;
		}

		public function load_assets() {
			parent::load_assets();

			do_action( 'swissdelight_core_action_portfolio_slider_horizontal_load_assets', $this->get_atts() );
		}

		public function render( $options, $content = null ) {
			parent::render( $options );

			$atts = $this->get_atts();

			//default values
			$atts['columns'] = 3;
			$atts['effect'] = 'slide';
			$atts['slider_speed'] = '200000';
			$atts['slider_speed_animation'] = '900';
			$atts['space'] = 'normal';
			$atts['slider_center'] = 'yes';

			$atts['post_type']       = $this->get_post_type();
			$atts['taxonomy_filter'] = $this->get_post_type_taxonomy();

			// Additional query args
			$atts['additional_query_args'] = $this->get_additional_query_args( $atts );

			$atts['query_result']   = new \WP_Query( swissdelight_core_get_query_params( $atts ) );
			$atts['holder_classes'] = $this->get_holder_classes( $atts );
			$atts['slider_attr']    = $this->get_slider_data( $atts );
			$atts['data_attr']      = swissdelight_core_get_pagination_data( SWISSDELIGHT_CORE_REL_PATH, 'post-types/portfolio/shortcodes', 'portfolio-slider-horizontal', 'portfolio', $atts );

			$atts['this_shortcode'] = $this;

			return swissdelight_core_get_template_part( 'post-types/portfolio/shortcodes/portfolio-slider-horizontal', 'templates/content', $atts['behavior'], $atts );
		}

		private function get_holder_classes( $atts ) {
			$holder_classes = $this->init_holder_classes();
			
			$holder_classes[] = 'qodef-swiper-container';
			$holder_classes[] = ! empty( $atts['layout'] ) ? 'qodef-item-layout--' . $atts['layout'] : '';

			$list_classes            = $this->get_list_classes( $atts );
			$hover_animation_classes = $this->get_hover_animation_classes( $atts );
			$holder_classes          = array_merge( $holder_classes, $list_classes, $hover_animation_classes );
			
			return implode( ' ', $holder_classes );
		}
		
		public function get_item_classes( $atts ) {
			$item_classes = $this->init_item_classes();
			
			$list_item_classes = $this->get_list_item_classes( $atts );
			
			$item_classes[] = 'swiper-slide';
			$item_classes = array_merge( $item_classes, $list_item_classes );
			
			return implode( ' ', $item_classes );
		}
	}
}