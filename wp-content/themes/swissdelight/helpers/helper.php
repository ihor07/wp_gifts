<?php

if ( ! function_exists( 'swissdelight_is_installed' ) ) {
	/**
	 * Function that checks if forward plugin installed
	 *
	 * @param string $plugin - plugin name
	 *
	 * @return bool
	 */
	function swissdelight_is_installed( $plugin ) {

		switch ( $plugin ) {
			case 'framework':
				return class_exists( 'QodeFramework' );
			case 'core':
				return class_exists( 'SwissDelightCore' );
			case 'woocommerce':
				return class_exists( 'WooCommerce' );
			case 'gutenberg-page':
				$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : array();

				return method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor();
			case 'gutenberg-editor':
				return class_exists( 'WP_Block_Type' );
			default:
				return false;
		}
	}
}

if ( ! function_exists( 'swissdelight_include_theme_is_installed' ) ) {
	/**
	 * Function that set case is installed element for framework functionality
	 *
	 * @param bool $installed
	 * @param string $plugin - plugin name
	 *
	 * @return bool
	 */
	function swissdelight_include_theme_is_installed( $installed, $plugin ) {

		if ( 'theme' === $plugin ) {
			return class_exists( 'Swissdelight_Handler' );
		}

		return $installed;
	}

	add_filter( 'qode_framework_filter_is_plugin_installed', 'swissdelight_include_theme_is_installed', 10, 2 );
}

if ( ! function_exists( 'swissdelight_template_part' ) ) {
	/**
	 * Function that echo module template part.
	 *
	 * @param string $module name of the module from inc folder
	 * @param string $template full path of the template to load
	 * @param string $slug
	 * @param array $params array of parameters to pass to template
	 */
	function swissdelight_template_part( $module, $template, $slug = '', $params = array() ) {
		echo swissdelight_get_template_part( $module, $template, $slug, $params ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'swissdelight_get_template_part' ) ) {
	/**
	 * Function that load module template part.
	 *
	 * @param string $module name of the module from inc folder
	 * @param string $template full path of the template to load
	 * @param string $slug
	 * @param array $params array of parameters to pass to template
	 *
	 * @return string - string containing html of template
	 */
	function swissdelight_get_template_part( $module, $template, $slug = '', $params = array() ) {
		//HTML Content from template
		$html          = '';
		$template_path = SWISSDELIGHT_INC_ROOT_DIR . '/' . $module;

		$temp = $template_path . '/' . $template;
		if ( is_array( $params ) && count( $params ) ) {
			extract( $params ); // @codingStandardsIgnoreLine
		}

		$template = '';

		if ( ! empty( $temp ) ) {
			if ( ! empty( $slug ) ) {
				$template = "{$temp}-{$slug}.php";

				if ( ! file_exists( $template ) ) {
					$template = $temp . '.php';
				}
			} else {
				$template = $temp . '.php';
			}
		}

		if ( $template ) {
			ob_start();
			include( $template ); // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			$html = ob_get_clean();
		}

		return $html;
	}
}

if ( ! function_exists( 'swissdelight_get_page_id' ) ) {
	/**
	 * Function that returns current page id
	 * Additional conditional is to check if current page is any wp archive page (archive, category, tag, date etc.) and returns -1
	 *
	 * @return int
	 */
	function swissdelight_get_page_id() {
		$page_id = get_queried_object_id();

		if ( swissdelight_is_wp_template() ) {
			$page_id = - 1;
		}

		return apply_filters( 'swissdelight_filter_page_id', $page_id );
	}
}

if ( ! function_exists( 'swissdelight_is_wp_template' ) ) {
	/**
	 * Function that checks if current page default wp page
	 *
	 * @return bool
	 */
	function swissdelight_is_wp_template() {
		return is_archive() || is_search() || is_404() || ( is_front_page() && is_home() );
	}
}

if ( ! function_exists( 'swissdelight_get_ajax_status' ) ) {
	/**
	 * Function that return status from ajax functions
	 *
	 * @param string $status - success or error
	 * @param string $message - ajax message value
	 * @param string|array $data - returned value
	 * @param string $redirect - url address
	 */
	function swissdelight_get_ajax_status( $status, $message, $data = null, $redirect = '' ) {
		$response = array(
			'status'   => esc_attr( $status ),
			'message'  => esc_html( $message ),
			'data'     => $data,
			'redirect' => ! empty( $redirect ) ? esc_url( $redirect ) : '',
		);

		$output = json_encode( $response );

		exit( $output ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'swissdelight_get_button_element' ) ) {
	/**
	 * Function that returns button with provided params
	 *
	 * @param array $params - array of parameters
	 *
	 * @return string - string representing button html
	 */
	function swissdelight_get_button_element( $params ) {
		if ( class_exists( 'SwissDelightCore_Button_Shortcode' ) ) {
			return SwissDelightCore_Button_Shortcode::call_shortcode( $params );
		} else {
			$link   = isset( $params['link'] ) ? $params['link'] : '#';
			$target = isset( $params['target'] ) ? $params['target'] : '_self';
			$text   = isset( $params['text'] ) ? $params['text'] : '';

			return '<a itemprop="url" class="qodef-theme-button" href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '">' . esc_html( $text ) . '</a>';
		}
	}
}

if ( ! function_exists( 'swissdelight_render_button_element' ) ) {
	/**
	 * Function that render button with provided params
	 *
	 * @param array $params - array of parameters
	 */
	function swissdelight_render_button_element( $params ) {
		echo swissdelight_get_button_element( $params ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'swissdelight_class_attribute' ) ) {
	/**
	 * Function that render class attribute
	 *
	 * @param string|array $class
	 */
	function swissdelight_class_attribute( $class ) {
		echo swissdelight_get_class_attribute( $class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'swissdelight_get_class_attribute' ) ) {
	/**
	 * Function that return class attribute
	 *
	 * @param string|array $class
	 *
	 * @return string|mixed
	 */
	function swissdelight_get_class_attribute( $class ) {
		return swissdelight_is_installed( 'framework' ) ? qode_framework_get_class_attribute( $class ) : '';
	}
}

if ( ! function_exists( 'swissdelight_get_post_value_through_levels' ) ) {
	/**
	 * Function that returns meta value if exists
	 *
	 * @param string $name name of option
	 * @param int $post_id id of
	 *
	 * @return string value of option
	 */
	function swissdelight_get_post_value_through_levels( $name, $post_id = null ) {
		return swissdelight_is_installed( 'framework' ) && swissdelight_is_installed( 'core' ) ? swissdelight_core_get_post_value_through_levels( $name, $post_id ) : '';
	}
}

if ( ! function_exists( 'swissdelight_get_space_value' ) ) {
	/**
	 * Function that returns spacing value based on selected option
	 *
	 * @param string $text_value - textual value of spacing
	 *
	 * @return int
	 */
	function swissdelight_get_space_value( $text_value ) {
		return swissdelight_is_installed( 'core' ) ? swissdelight_core_get_space_value( $text_value ) : 0;
	}
}

if ( ! function_exists( 'swissdelight_wp_kses_html' ) ) {
	/**
	 * Function that does escaping of specific html.
	 * It uses wp_kses function with predefined attributes array.
	 *
	 * @param string $type - type of html element
	 * @param string $content - string to escape
	 *
	 * @return string escaped output
	 * @see wp_kses()
	 *
	 */
	function swissdelight_wp_kses_html( $type, $content ) {
		return swissdelight_is_installed( 'framework' ) ? qode_framework_wp_kses_html( $type, $content ) : $content;
	}
}

if ( ! function_exists( 'swissdelight_render_svg_icon' ) ) {
	/**
	 * Function that print svg html icon
	 *
	 * @param string $name - icon name
	 * @param string $class_name - custom html tag class name
	 */
	function swissdelight_render_svg_icon( $name, $class_name = '' ) {
		echo swissdelight_get_svg_icon( $name, $class_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'swissdelight_get_svg_icon' ) ) {
	/**
	 * Returns svg html
	 *
	 * @param string $name - icon name
	 * @param string $class_name - custom html tag class name
	 *
	 * @return string - string containing svg html
	 */
	function swissdelight_get_svg_icon( $name, $class_name = '' ) {
		$html  = '';
		$class = isset( $class_name ) && ! empty( $class_name ) ? 'class="' . esc_attr( $class_name ) . '"' : '';

		switch ( $name ) {
			case 'menu':
				$html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="64px" height="64px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve"><line x1="12" y1="21" x2="52" y2="21"/><line x1="12" y1="33" x2="52" y2="33"/><line x1="12" y1="45" x2="52" y2="45"/></svg>';
				break;
			case 'search':
				$html = '<svg ' . $class . '  xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16.116px" height="16.251px" viewBox="0 0 16.116 16.251" enable-background="new 0 0 16.116 16.251" xml:space="preserve"><g><path fill="#241C10" d="M15.952,15.105l-0.943,0.943l-4.635-4.635c-1.122,0.902-2.434,1.354-3.938,1.354 c-1.723,0-3.193-0.608-4.409-1.825C0.811,9.726,0.202,8.256,0.202,6.533s0.608-3.192,1.825-4.409 c1.216-1.216,2.687-1.825,4.409-1.825s3.192,0.609,4.409,1.825c1.216,1.217,1.825,2.687,1.825,4.409 c0,1.477-0.451,2.775-1.354,3.896L15.952,15.105z M2.499,10.512c1.066,1.066,2.379,1.6,3.938,1.6c1.531,0,2.844-0.547,3.938-1.641 c1.093-1.094,1.641-2.406,1.641-3.938c0-1.531-0.547-2.844-1.641-3.938C9.28,1.502,7.968,0.955,6.437,0.955 c-1.532,0-2.844,0.547-3.938,1.641C1.405,3.689,0.858,5.002,0.858,6.533C0.858,8.092,1.405,9.418,2.499,10.512z"/></g></svg>';
				break;
			case 'star':
				$html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="32" height="32" viewBox="0 0 32 32"><g><path d="M 20.756,11.768L 15.856,1.84L 10.956,11.768L0,13.36L 7.928,21.088L 6.056,32L 15.856,26.848L 25.656,32L 23.784,21.088L 31.712,13.36 z"></path></g></svg>';
				break;
			case 'menu-arrow-right':
				$html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="7.76px" height="11.75px" viewBox="0 0 7.76 11.75" enable-background="new 0 0 7.76 11.75" xml:space="preserve"><path fill="#63605A" d="M1.618,1.341l0.195-0.196l4.621,4.586v0.403l-4.621,4.586l-0.404-0.405l4.383-4.383L1.408,1.55L1.618,1.341z"/></svg>';
				break;
			case 'slider-arrow-left':
				$html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="42.25px" height="93.625px" viewBox="0 0 42.25 93.625" enable-background="new 0 0 42.25 93.625" xml:space="preserve"><line fill="none" stroke="#241C10" stroke-miterlimit="10" x1="40.483" y1="1.42" x2="1.267" y2="46.83"/><line fill="none" stroke="#241C10" stroke-miterlimit="10" x1="40.483" y1="91.91" x2="1.267" y2="46.5"/></svg>';
				break;
			case 'slider-arrow-right':
				$html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="42.25px" height="93.625px" viewBox="0 0 42.25 93.625" enable-background="new 0 0 42.25 93.625" xml:space="preserve"><line fill="none" stroke="#241C10" stroke-miterlimit="10" x1="1.267" y1="1.42" x2="40.483" y2="46.83"/><line fill="none" stroke="#241C10" stroke-miterlimit="10" x1="1.267" y1="91.91" x2="40.483" y2="46.5"/></svg>';
				break;
			case 'pagination-arrow-left':
				$html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="7.76px" height="11.75px" viewBox="0 0 7.76 11.75" enable-background="new 0 0 7.76 11.75" xml:space="preserve"><path d="M6.831,0.855L1.752,5.933l5.078,5.078L6.362,11.48L1.01,6.167V5.699l5.352-5.313l0.226,0.227L6.831,0.855z"/></svg>';
				break;
			case 'pagination-arrow-right':
				$html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="7.76px" height="11.75px" viewBox="0 0 7.76 11.75" enable-background="new 0 0 7.76 11.75" xml:space="preserve"><path d="M1.253,0.613l0.226-0.227l5.353,5.313v0.468L1.479,11.48l-0.468-0.47l5.078-5.078L1.01,0.855L1.253,0.613z"/></svg>';
				break;
			case 'close':
				$html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="32" height="32" viewBox="0 0 32 32"><g><path d="M 10.050,23.95c 0.39,0.39, 1.024,0.39, 1.414,0L 17,18.414l 5.536,5.536c 0.39,0.39, 1.024,0.39, 1.414,0 c 0.39-0.39, 0.39-1.024,0-1.414L 18.414,17l 5.536-5.536c 0.39-0.39, 0.39-1.024,0-1.414c-0.39-0.39-1.024-0.39-1.414,0 L 17,15.586L 11.464,10.050c-0.39-0.39-1.024-0.39-1.414,0c-0.39,0.39-0.39,1.024,0,1.414L 15.586,17l-5.536,5.536 C 9.66,22.926, 9.66,23.56, 10.050,23.95z"></path></g></svg>';
				break;
			case 'reply':
				$html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="21.083px" height="10px" viewBox="0 0 21.083 10" enable-background="new 0 0 21.083 10"><g><path fill="#241C10" d="M1.04,9.502H0.415v-6.25H19.36l-2.266-2.266l0.469-0.469l2.852,2.813v0.469l-2.852,2.813l-0.508-0.469 l2.305-2.266H1.04V9.502z"/></g></svg>';
				break;
			case 'spinner':
				$html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512"><path d="M304 48c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-48 368c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zm208-208c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zM96 256c0-26.51-21.49-48-48-48S0 229.49 0 256s21.49 48 48 48 48-21.49 48-48zm12.922 99.078c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.491-48-48-48zm294.156 0c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.49-48-48-48zM108.922 60.922c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.491-48-48-48z"></path></svg>';
				break;
			case 'link':
				$html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="32.06999969482422" height="33.58000183105469" viewBox="0 0 32.06999969482422 33.58000183105469"><g><path d="M 7.54,15.77c 1.278,1.278, 3.158,1.726, 4.868,1.216L 2.96,7.54C 2.652,7.232, 2.49,6.786, 2.49,6.254 c0-0.88, 0.46-2.004, 1.070-2.614c 0.8-0.8, 2.97-1.686, 3.98-0.682l 9.446,9.448c 0.138-0.462, 0.208-0.942, 0.208-1.422 c0-1.304-0.506-2.526-1.424-3.446L 9.364,1.134C 7.44-0.79, 3.616-0.068, 1.734,1.814C 0.642,2.906-0.036,4.598-0.036,6.23 c0,1.268, 0.416,2.382, 1.17,3.136L 7.54,15.77zM 24.46,16.23c-1.278-1.278-3.158-1.726-4.868-1.216l 9.448,9.448c 0.308,0.308, 0.47,0.752, 0.47,1.286 c0,0.88-0.46,2.004-1.070,2.614c-0.8,0.8-2.97,1.686-3.98,0.682L 15.014,19.594c-0.138,0.462-0.208,0.942-0.208,1.422 c0,1.304, 0.506,2.526, 1.424,3.446l 6.404,6.404c 1.924,1.924, 5.748,1.202, 7.63-0.68c 1.092-1.092, 1.77-2.784, 1.77-4.416 c0-1.268-0.416-2.382-1.17-3.136L 24.46,16.23zM 9.164,9.162C 8.908,9.416, 8.768,9.756, 8.768,10.116s 0.14,0.698, 0.394,0.952l 11.768,11.77 c 0.526,0.524, 1.38,0.524, 1.906,0c 0.256-0.254, 0.394-0.594, 0.394-0.954s-0.14-0.698-0.394-0.952L 11.068,9.162 C 10.544,8.638, 9.688,8.638, 9.164,9.162z"></path></g></svg>';
				break;
			case 'button-arrow':
				$html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="35.375px" height="11.125px" viewBox="0 0 35.375 12.125" enable-background="new 0 0 35.375 12.125" xml:space="preserve"><g><line x1="0.198" y1="6.078" x2="35.088" y2="6.047"/><line x1="29.823" y1="1" x2="34.745" y2="5.922"/><line x1="29.823" y1="11.125" x2="34.745" y2="6.203"/></g></svg>';
				break;
			case 'predefined-opener':
				$html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg"><circle stroke-miterlimit="10" cx="34.334" cy="34.334" r="33.5"/><circle stroke-miterlimit="10" cx="34.334" cy="34.334" r="33.5"/></svg>';
				break;
			case 'cart-icon':
			    $html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg"><circle stroke-miterlimit="10" cx="18.66" cy="18.66" r="15.5"/><circle stroke-miterlimit="10" cx="18.66" cy="18.66" r="15.5"/></svg>';
			    break;
			case 'edit-icon':
				$html = '<svg ' . $class . ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16.438px" height="16.145px" viewBox="0 0 16.438 16.145" enable-background="new 0 0 16.438 16.145" xml:space="preserve"><g><path fill="#241D12" d="M12.755,14.895V6.77l0.625-0.625v9.375H0.88V3.645h10L10.255,4.27h-8.75v10.625H12.755z M7.677,9.739 l6.68-6.68l0.43,0.43L7.755,10.52h-1.25V9.27l7.031-7.031l0.43,0.43l-6.68,6.68L7.677,9.739z M15.724,1.77 c0.104,0.156,0.156,0.287,0.156,0.391c0,0.104-0.053,0.234-0.156,0.391L15.255,3.02l-0.82-0.82l-0.43-0.43l0.469-0.469 c0.156-0.104,0.286-0.156,0.391-0.156c0.104,0,0.234,0.053,0.391,0.156L15.724,1.77z"/></g></svg>';
				break;
		}

		return apply_filters( 'swissdelight_filter_svg_icon', $html );
	}
}
