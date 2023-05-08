<?php

if ( ! function_exists( 'swissdelight_core_add_working_hours_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function swissdelight_core_add_working_hours_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope'       => SWISSDELIGHT_CORE_OPTIONS_NAME,
				'type'        => 'admin',
				'slug'        => 'working-hours',
				'icon'        => 'fa fa-book',
				'title'       => esc_html__( 'Working Hours', 'swissdelight-core' ),
				'description' => esc_html__( 'Global Working Hours Options', 'swissdelight-core' ),
			)
		);

		if ( $page ) {
			$page->add_field_element(
				array(
					'field_type'    => 'select',
					'name'          => 'qodef_working_hours_layout',
					'options'       => array(
						'all-days'   => esc_html__( 'All Days', 'swissdelight-core' ),
						'split-days' => esc_html__( 'Split Days', 'swissdelight-core' ),
					),
					'default_value' => 'split-days',
					'title'         => esc_html__( 'Working Hours Layout', 'swissdelight-core' ),
				)
			);
			$page->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_working_hours_monday',
					'title'      => esc_html__( 'Working Hours For Monday', 'swissdelight-core' ),
					'dependency' => array(
						'show' => array(
							'qodef_working_hours_layout' => array(
								'values'        => 'all-days',
								'default_value' => 'split-days',
							),
						),
					),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_working_hours_tuesday',
					'title'      => esc_html__( 'Working Hours For Tuesday', 'swissdelight-core' ),
					'dependency' => array(
						'show' => array(
							'qodef_working_hours_layout' => array(
								'values'        => 'all-days',
								'default_value' => 'split-days',
							),
						),
					),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_working_hours_wednesday',
					'title'      => esc_html__( 'Working Hours For Wednesday', 'swissdelight-core' ),
					'dependency' => array(
						'show' => array(
							'qodef_working_hours_layout' => array(
								'values'        => 'all-days',
								'default_value' => 'split-days',
							),
						),
					),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_working_hours_thursday',
					'title'      => esc_html__( 'Working Hours For Thursday', 'swissdelight-core' ),
					'dependency' => array(
						'show' => array(
							'qodef_working_hours_layout' => array(
								'values'        => 'all-days',
								'default_value' => 'split-days',
							),
						),
					),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_working_hours_friday',
					'title'      => esc_html__( 'Working Hours For Friday', 'swissdelight-core' ),
					'dependency' => array(
						'show' => array(
							'qodef_working_hours_layout' => array(
								'values'        => 'all-days',
								'default_value' => 'split-days',
							),
						),
					),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_working_hours_other',
					'title'      => esc_html__( 'Working Hours For Mon - Fri', 'swissdelight-core' ),
					'dependency' => array(
						'show' => array(
							'qodef_working_hours_layout' => array(
								'values'        => 'split-days',
								'default_value' => 'split-days',
							),
						),
					),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_working_hours_saturday',
					'title'      => esc_html__( 'Working Hours For Saturday', 'swissdelight-core' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_working_hours_sunday',
					'title'      => esc_html__( 'Working Hours For Sunday', 'swissdelight-core' ),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'checkbox',
					'name'       => 'qodef_working_hours_special_days',
					'title'      => esc_html__( 'Special Days', 'swissdelight-core' ),
					'options'    => array(
						'monday'    => esc_html__( 'Monday', 'swissdelight-core' ),
						'tuesday'   => esc_html__( 'Tuesday', 'swissdelight-core' ),
						'wednesday' => esc_html__( 'Wednesday', 'swissdelight-core' ),
						'thursday'  => esc_html__( 'Thursday', 'swissdelight-core' ),
						'friday'    => esc_html__( 'Friday', 'swissdelight-core' ),
						'saturday'  => esc_html__( 'Saturday', 'swissdelight-core' ),
						'sunday'    => esc_html__( 'Sunday', 'swissdelight-core' ),
						'mon - fri' => esc_html__( 'Mon - Fri', 'swissdelight-core' ),
					),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'text',
					'name'       => 'qodef_working_hours_special_text',
					'title'      => esc_html__( 'Featured Text For Special Days', 'swissdelight-core' ),
				)
			);

			// Hook to include additional options after module options
			do_action( 'swissdelight_core_action_after_working_hours_options_map', $page );
		}
	}

	add_action( 'swissdelight_core_action_default_options_init', 'swissdelight_core_add_working_hours_options', swissdelight_core_get_admin_options_map_position( 'working-hours' ) );
}
