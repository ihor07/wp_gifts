<?php

if ( ! function_exists( 'swissdelight_core_add_weather_widget' ) ) {
	/**
	 * Function that add widget into widgets list for registration
	 *
	 * @param array $widgets
	 *
	 * @return array
	 */
	function swissdelight_core_add_weather_widget( $widgets ) {
		$widgets[] = 'SwissDelightCore_Weather_Widget';

		return $widgets;
	}

	add_filter( 'swissdelight_core_filter_register_widgets', 'swissdelight_core_add_weather_widget' );
}

if ( class_exists( 'QodeFrameworkWidget' ) ) {
	class SwissDelightCore_Weather_Widget extends QodeFrameworkWidget {

		public function map_widget() {
			$this->set_base( 'swissdelight_core_weather' );
			$this->set_name( esc_html__( 'SwissDelight Weather', 'swissdelight-core' ) );
			$this->set_description( esc_html__( 'Displays weather forecast', 'swissdelight-core' ) );

			$this->set_widget_option(
				array(
					'field_type' => 'text',
					'name'       => 'widget_title',
					'title'      => esc_html__( 'Widget Title', 'swissdelight-core' ),
				)
			);

			$this->set_widget_option(
				array(
					'field_type' => 'select',
					'name'       => 'layout',
					'title'      => esc_html__( 'Layout', 'swissdelight-core' ),
					'options'    => array(
						'standard' => esc_html__( 'Standard', 'swissdelight-core' ),
						'simple'   => esc_html__( 'Simple', 'swissdelight-core' ),
					),
				)
			);

			$this->set_widget_option(
				array(
					'field_type'  => 'text',
					'name'        => 'api_key',
					'title'       => esc_html__( 'API Key', 'swissdelight-core' ),
					'description' => sprintf(
						'%s%s%s',
						'<a href="https://openweathermap.org/appid#get" target="_blank">',
						esc_html__( 'How to get API key', 'swissdelight-core' ),
						'</a>'
					),
				)
			);

			$this->set_widget_option(
				array(
					'field_type'  => 'text',
					'name'        => 'location',
					'title'       => esc_html__( 'Location', 'swissdelight-core' ),
					'description' => sprintf(
						'%s%s%s',
						'<a href="https://openweathermap.org/find" target="_blank">',
						esc_html__( 'Find Your Location (i.e: London, UK or New York City)', 'swissdelight-core' ),
						'</a>'
					),
				)
			);

			$this->set_widget_option(
				array(
					'field_type' => 'select',
					'name'       => 'units',
					'title'      => esc_html__( 'Temperature Unit', 'swissdelight-core' ),
					'options'    => array(
						'metric'   => esc_html__( 'Metric', 'swissdelight-core' ),
						'imperial' => esc_html__( 'Imperial', 'swissdelight-core' ),
					),
				)
			);

			$this->set_widget_option(
				array(
					'field_type' => 'select',
					'name'       => 'time_zone',
					'title'      => esc_html__( 'Time Zone', 'swissdelight-core' ),
					'options'    => array(
						'0' => esc_html__( 'UTC', 'swissdelight-core' ),
						'1' => esc_html__( 'GMT', 'swissdelight-core' ),
					),
				)
			);

			$this->set_widget_option(
				array(
					'field_type' => 'select',
					'name'       => 'days_to_show',
					'title'      => esc_html__( 'Days to Show', 'swissdelight-core' ),
					'options'    => array(
						'1' => esc_html__( 'Current Day', 'swissdelight-core' ),
						'5' => esc_html__( '5 Days', 'swissdelight-core' ),
					),
				)
			);
		}

		public function render( $atts ) {
			$atts['holder_classes'] = $this->get_holder_classes( $atts );
			$atts['weather_data']   = swissdelight_core_weather_widget_logic( $atts );
			$atts['today_params']   = $this->get_layout_params( $atts );

			swissdelight_core_template_part( 'widgets/weather', 'templates/holder', '', $atts );
		}

		public function get_holder_classes( $atts ) {
			$classes = array();

			$classes[] = 'qodef-weather-widget qodef-m';
			$classes[] = ! empty( $atts['layout'] ) ? 'qodef-layout--' . $atts['layout'] : '';
			$classes[] = ! empty( $atts['days_to_show'] ) ? 'qodef-show--' . $atts['days_to_show'] : '';

			return implode( ' ', $classes );
		}

		private function get_layout_params( $atts ) {
			$today_params = array();

			if ( 'metric' === $atts['units'] ) {
				$today_params['temp_unit'] = esc_html__( 'C', 'swissdelight-core' );
				$today_params['wind_unit'] = esc_html__( 'm/s', 'swissdelight-core' );
			} else {
				$today_params['temp_unit'] = esc_html__( 'F', 'swissdelight-core' );
				$today_params['wind_unit'] = esc_html__( 'fps', 'swissdelight-core' );
			}

			$today_params['dt_today'] = gmdate( get_option( 'date_format' ), current_time( 'timestamp', $atts['time_zone'] ) );

			// todays temps
			$today = $atts['weather_data']['now'];

			if ( empty( $today ) ) {
				return array();
			}

			$today_params['today_temp']              = round( $today->main->temp );
			$today_params['today_high']              = round( $today->main->temp_max );
			$today_params['today_low']               = round( $today->main->temp_min );
			$today_params['today_description']       = $today->weather[0]->description;
			$today_params['today_description_class'] = strtolower( sanitize_title( $today->weather[0]->main ) );
			$today_params['today_humidity']          = $today->main->humidity;
			$today_params['today_wind_speed']        = $today->wind->speed;
			$today_params['city']                    = $today->name;
			$today_params['day_number']              = 1;

			if ( 5 == $atts['days_to_show'] ) {
				$forecast = $atts['weather_data']['forecast'];

				$days_of_week = array(
					esc_html__( 'Sun', 'swissdelight-core' ),
					esc_html__( 'Mon', 'swissdelight-core' ),
					esc_html__( 'Tue', 'swissdelight-core' ),
					esc_html__( 'Wed', 'swissdelight-core' ),
					esc_html__( 'Thu', 'swissdelight-core' ),
					esc_html__( 'Fri', 'swissdelight-core' ),
					esc_html__( 'Sat', 'swissdelight-core' )
				);

				foreach ( (array) $forecast->list as $forecast ) {
					$rest = array();

					$rest['today_temp']              = round( $forecast->temp->day );
					$rest['day_of_week']             = $days_of_week[ gmdate( 'w', $forecast->dt ) ];
					$rest['today_description_class'] = strtolower( sanitize_title( $forecast->weather[0]->main ) );
					$today_params['rest'][]          = $rest;
				}
			}

			return $today_params;
		}
	}
}
