<?php

if ( ! class_exists( 'Swissdelight_Handler' ) ) {
	/**
	 * Main theme class with configuration
	 */
	class Swissdelight_Handler {
		private static $instance;

		public function __construct() {

			// Include required files
			require_once get_template_directory() . '/constants.php';
			require_once SWISSDELIGHT_ROOT_DIR . '/helpers/helper.php';

			// Include theme's style and inline style
			add_action( 'wp_enqueue_scripts', array( $this, 'include_css_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_inline_style' ) );

			// Include theme's script and localize theme's main js script
			add_action( 'wp_enqueue_scripts', array( $this, 'include_js_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'localize_js_scripts' ) );

			// Include theme's 3rd party plugins styles
			add_action( 'swissdelight_action_before_main_css', array( $this, 'include_plugins_styles' ) );

			// Include theme's 3rd party plugins scripts
			add_action( 'swissdelight_action_before_main_js', array( $this, 'include_plugins_scripts' ) );

			// Add pingback header
			add_action( 'wp_head', array( $this, 'add_pingback_header' ), 1 );

			// Include theme's skip link
			add_action( 'swissdelight_action_after_body_tag_open', array( $this, 'add_skip_link' ), 5 );

			// Include theme's Google fonts
			add_action( 'swissdelight_action_before_main_css', array( $this, 'include_google_fonts' ) );

			// Add theme's supports feature
			add_action( 'after_setup_theme', array( $this, 'set_theme_support' ) );

			// Enqueue supplemental block editor styles
			add_action( 'enqueue_block_editor_assets', array( $this, 'editor_customizer_styles' ) );

			// Add theme's body classes
			add_filter( 'body_class', array( $this, 'add_body_classes' ) );

			// Include modules
			add_action( 'after_setup_theme', array( $this, 'include_modules' ) );
		}

		/**
		 * @return Swissdelight_Handler
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function include_css_scripts() {
			// CSS dependency variable
			$main_css_dependency = apply_filters( 'swissdelight_filter_main_css_dependency', array( 'swiper' ) );

			// Hook to include additional scripts before theme's main style
			do_action( 'swissdelight_action_before_main_css' );

			// Enqueue theme's main style
			wp_enqueue_style( 'swissdelight-main', SWISSDELIGHT_ASSETS_CSS_ROOT . '/main.min.css', $main_css_dependency );

			// Enqueue theme's style
			wp_enqueue_style( 'swissdelight-style', SWISSDELIGHT_ROOT . '/style.css' );

			// Hook to include additional scripts after theme's main style
			do_action( 'swissdelight_action_after_main_css' );
		}

		function add_inline_style() {
			$style = apply_filters( 'swissdelight_filter_add_inline_style', $style = '' );

			if ( ! empty( $style ) ) {
				wp_add_inline_style( 'swissdelight-style', $style );
			}
		}

		function include_js_scripts() {
			// JS dependency variable
			$main_js_dependency = apply_filters( 'swissdelight_filter_main_js_dependency', array( 'jquery' ) );

			// Hook to include additional scripts before theme's main script
			do_action( 'swissdelight_action_before_main_js', $main_js_dependency );

			// Enqueue theme's main script
			wp_enqueue_script( 'swissdelight-main-js', SWISSDELIGHT_ASSETS_JS_ROOT . '/main.min.js', $main_js_dependency, false, true );

			// Hook to include additional scripts after theme's main script
			do_action( 'swissdelight_action_after_main_js' );

			// Include comment reply script
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}
		}

		function localize_js_scripts() {
			$global = apply_filters(
				'swissdelight_filter_localize_main_js',
				array(
					'adminBarHeight' => is_admin_bar_showing() ? 32 : 0,
					'iconArrowLeft'  => swissdelight_get_svg_icon( 'slider-arrow-left' ),
					'iconArrowRight' => swissdelight_get_svg_icon( 'slider-arrow-right' ),
					'iconClose'      => swissdelight_get_svg_icon( 'close' ),
				)
			);

			wp_localize_script(
				'swissdelight-main-js',
				'qodefGlobal',
				array(
					'vars' => $global,
				)
			);
		}

		function include_plugins_styles() {

			// Enqueue 3rd party plugins style
			wp_enqueue_style( 'swiper', SWISSDELIGHT_ASSETS_ROOT . '/plugins/swiper/swiper.min.css' );
			wp_enqueue_style( 'magnific-popup', SWISSDELIGHT_ASSETS_ROOT . '/plugins/magnific-popup/magnific-popup.css' );
		}

		function include_plugins_scripts() {

			// JS dependency variables
			$js_3rd_party_dependency = apply_filters( 'swissdelight_filter_js_3rd_party_dependency', 'jquery' );

			// Enqueue 3rd party plugins script
			wp_enqueue_script( 'jquery-waitforimages', SWISSDELIGHT_ASSETS_ROOT . '/plugins/waitforimages/jquery.waitforimages.js', array( $js_3rd_party_dependency ), false, true );
			wp_enqueue_script( 'jquery-appear', SWISSDELIGHT_ASSETS_ROOT . '/plugins/appear/jquery.appear.js', array( $js_3rd_party_dependency ), false, true );
			wp_enqueue_script( 'swiper', SWISSDELIGHT_ASSETS_ROOT . '/plugins/swiper/swiper.min.js', array( $js_3rd_party_dependency ), false, true );
			wp_enqueue_script( 'jquery-magnific-popup', SWISSDELIGHT_ASSETS_ROOT . '/plugins/magnific-popup/jquery.magnific-popup.min.js', array( $js_3rd_party_dependency ), false, true );
		}

		function add_pingback_header() {
			if ( is_singular() && pings_open( get_queried_object() ) ) { ?>
				<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
				<?php
			}
		}

		function add_skip_link() {
			echo '<a class="skip-link screen-reader-text" href="#qodef-page-content">' . esc_html__( 'Skip to the content', 'swissdelight' ) . '</a>';
		}

		function include_google_fonts() {
			$is_enabled = boolval( apply_filters( 'swissdelight_filter_enable_google_fonts', true ) );
			
			if ( $is_enabled ) {
				$font_subset_array = array(
					'latin-ext',
				);
				
				$font_weight_array = array(
					'300',
					'400',
					'500',
					'600',
					'700',
				);
				
				$default_font_family = array(
					'Cormorant',
					'Heebo',
				);
				
				$font_weight_str = implode( ',', array_unique( apply_filters( 'swissdelight_filter_google_fonts_weight_list', $font_weight_array ) ) );
				$font_subset_str = implode( ',', array_unique( apply_filters( 'swissdelight_filter_google_fonts_subset_list', $font_subset_array ) ) );
				$fonts_array     = apply_filters( 'swissdelight_filter_google_fonts_list', $default_font_family );
				
				if ( ! empty( $fonts_array ) ) {
					$modified_default_font_family = array();
					
					foreach ( $fonts_array as $font ) {
						$modified_default_font_family[] = $font . ':' . $font_weight_str;
					}
					
					$default_font_string = implode( '|', $modified_default_font_family );
					
					$fonts_full_list_args = array(
						'family'  => urlencode( $default_font_string ),
						'subset'  => urlencode( $font_subset_str ),
						'display' => 'swap',
					);
					
					$google_fonts_url = add_query_arg( $fonts_full_list_args, 'https://fonts.googleapis.com/css' );
					wp_enqueue_style( 'swissdelight-google-fonts', esc_url_raw( $google_fonts_url ), array(), '1.0.0' );
				}
			}
		}

		function set_theme_support() {

			// Make theme available for translation
			load_theme_textdomain( 'swissdelight', SWISSDELIGHT_ROOT_DIR . '/languages' );

			// Add support for feed links
			add_theme_support( 'automatic-feed-links' );

			// Add support for title tag
			add_theme_support( 'title-tag' );

			// Add support for post thumbnails
			add_theme_support( 'post-thumbnails' );

			// Add theme support for Custom Logo
			add_theme_support( 'custom-logo' );

			// Add support for full and wide align images.
			add_theme_support( 'align-wide' );

			// Set the default content width
			global $content_width;
			if ( ! isset( $content_width ) ) {
				$content_width = apply_filters( 'swissdelight_filter_set_content_width', 1300 );
			}

			// Add support for post formats
			add_theme_support( 'post-formats', array( 'gallery', 'video', 'audio', 'link', 'quote' ) );

			// Add theme support for editor style
			add_editor_style( SWISSDELIGHT_ASSETS_CSS_ROOT . '/editor-style.min.css' );
		}

		function editor_customizer_styles() {

			// Include theme's Google fonts for Gutenberg editor
			$this->include_google_fonts();

			// Add editor customizer style
			wp_enqueue_style( 'swissdelight-editor-customizer-styles', SWISSDELIGHT_ASSETS_CSS_ROOT . '/editor-customizer-style.css' );

			// Add Gutenberg blocks style
			wp_enqueue_style( 'swissdelight-gutenberg-blocks-style', SWISSDELIGHT_INC_ROOT . '/gutenberg/assets/admin/css/gutenberg-blocks.css' );
		}

		function add_body_classes( $classes ) {
			$current_theme = wp_get_theme();
			$theme_name    = esc_attr( str_replace( ' ', '-', strtolower( $current_theme->get( 'Name' ) ) ) );
			$theme_version = esc_attr( $current_theme->get( 'Version' ) );

			// Check is child theme activated
			if ( $current_theme->parent() ) {

				// Add child theme version
				$classes[] = $theme_name . '-child-' . $theme_version;

				// Get main theme variables
				$current_theme = $current_theme->parent();
				$theme_name    = esc_attr( str_replace( ' ', '-', strtolower( $current_theme->get( 'Name' ) ) ) );
				$theme_version = esc_attr( $current_theme->get( 'Version' ) );
			}

			if ( $current_theme->exists() ) {
				$classes[] = $theme_name . '-' . $theme_version;
			}

			// Set default grid size value
			$classes['grid_size'] = 'qodef-content-grid-1100';

			return apply_filters( 'swissdelight_filter_add_body_classes', $classes );
		}

		function include_modules() {

			// Hook to include additional files before modules inclusion
			do_action( 'swissdelight_action_before_include_modules' );

			foreach ( glob( SWISSDELIGHT_INC_ROOT_DIR . '/*/include.php' ) as $module ) {
				include_once $module; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			}

			// Hook to include additional files after modules inclusion
			do_action( 'swissdelight_action_after_include_modules' );
		}
	}

	Swissdelight_Handler::get_instance();
}
function add_custom_item_data( $cart_item_data, $product_id, $variation_id, $cart_item ) {
    $cart_item_data['custom_data'] = 'Custom Data Value';
    return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'add_custom_item_data', 10, 3 );

function display_custom_item_data( $item_data, $cart_item ) {
    if ( isset( $cart_item['custom_data'] ) ) {
        $item_data[] = array(
            'key'   => 'Custom Data',
            'value' => $cart_item['custom_data']
        );
    }
    return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'display_custom_item_data', 10, 2 );

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
  'key' => 'group_63ee81096fe01',
  'title' => 'Button for cart of product',
  'fields' => array(
    array(
      'key' => 'field_63ee810894a14',
      'label' => 'Delivery',
      'name' => 'delivery-info-btn',
      'aria-label' => '',
      'type' => 'textarea',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => array(
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'default_value' => '',
      'maxlength' => '',
      'rows' => '',
      'placeholder' => '',
      'new_lines' => '',
    ),
    array(
      'key' => 'field_63ee822694a15',
      'label' => 'Pay',
      'name' => 'payment-info-btn',
      'aria-label' => '',
      'type' => 'text',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => array(
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'default_value' => '',
      'maxlength' => '',
      'placeholder' => '',
      'prepend' => '',
      'append' => '',
    ),
  ),
  'location' => array(
    array(
      array(
        'param' => 'post_type',
        'operator' => '==',
        'value' => 'product',
      ),
    ),
  ),
  'menu_order' => 0,
  'position' => 'normal',
  'style' => 'default',
  'label_placement' => 'top',
  'instruction_placement' => 'label',
  'hide_on_screen' => '',
  'active' => true,
  'description' => '',
  'show_in_rest' => 0,
));

endif;
function add_custom_content_to_mini_cart() {
    echo '<p>Custom product</p>';
}
add_action( 'woocommerce_before_mini_cart_contents', 'add_custom_content_to_mini_cart' );
// Add custom price field to product options
function add_custom_price_field() {
  woocommerce_wp_text_input( array(
    'id' => '_custom_price_field',
    'label' => __( 'Custom Price', 'woocommerce' ),
    'desc_tip' => true,
    'description' => __( 'Enter the custom price for this product.', 'woocommerce' ),
    'value' => get_post_meta( get_the_ID(), '_custom_price_field', true ),
    'type' => 'number',
    'custom_attributes' => array(
      'step' => 'any',
      'min' => '0'
    )
  ) );
}
add_action( 'woocommerce_product_options_general_product_data', 'add_custom_price_field' );

// Save custom price field value
function save_custom_price_field( $post_id ) {
  $custom_price = $_POST['_custom_price_field'];
  if ( ! empty( $custom_price ) ) {
    update_post_meta( $post_id, '_custom_price_field', esc_attr( $custom_price ) );
  }
}
add_action( 'woocommerce_process_product_meta', 'save_custom_price_field' );

// Display custom price field in Product Wizard
function display_custom_price_field() {
  $custom_price = get_post_meta( get_the_ID(), '_custom_price_field', true );
  if ( ! empty( $custom_price ) ) {
    echo '<p><strong>' . __( 'Custom Price:', 'woocommerce' ) . '</strong> ' . wc_price( $custom_price ) . '</p>';
  }
}
add_action( 'woocommerce_single_product_summary', 'display_custom_price_field', 11 );
