<?php

if ( ! function_exists( 'swissdelight_core_add_nav_menu_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function swissdelight_core_add_nav_menu_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'scope' => array( 'nav_menu_item' ),
				'type'  => 'nav-menu',
			)
		);

		if ( $page ) {
			$page->add_field_element(
				array(
					'field_type' => 'checkbox',
					'name'       => 'qodef-enable-mega-menu',
					'title'      => esc_html__( 'Enable Mega Menu', 'swissdelight-core' ),
					'options'    => array(
						'enable' => esc_html__( 'Enable', 'swissdelight-core' ),
					),
					'args'       => array(
						'depth' => 0,
					),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'checkbox',
					'name'       => 'qodef-enable-anchor-link',
					'title'      => esc_html__( 'Enable Anchor Link', 'swissdelight-core' ),
					'options'    => array(
						'enable' => esc_html__( 'Enable', 'swissdelight-core' ),
					),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'select',
					'name'       => 'qodef-menu-item-appearance',
					'title'      => esc_html__( 'Menu Item Appearance', 'swissdelight-core' ),
					'options'    => array(
						'none'       => esc_html__( 'None', 'swissdelight-core' ),
						'hide-item'  => esc_html__( 'Hide Item', 'swissdelight-core' ),
						'hide-link'  => esc_html__( 'Hide Link', 'swissdelight-core' ),
						'hide-label' => esc_html__( 'Hide Label', 'swissdelight-core' ),
					),
				)
			);

			$page->add_field_element(
				array(
					'field_type' => 'iconpack',
					'name'       => 'qodef-menu-item-icon-pack',
					'title'      => esc_html__( 'Icon Pack', 'swissdelight-core' ),
					'args'       => array(
						'width' => 'thin',
					),
				)
			);

			$custom_sidebars = swissdelight_core_get_custom_sidebars();
			if ( ! empty( $custom_sidebars ) && count( $custom_sidebars ) > 1 ) {
				$page->add_field_element(
					array(
						'field_type'  => 'select',
						'name'        => 'qodef-enable-mega-menu-widget',
						'title'       => esc_html__( 'Custom Sidebar', 'swissdelight-core' ),
						'description' => esc_html__( 'Choose a custom sidebar to display on wide menu', 'swissdelight-core' ),
						'options'     => $custom_sidebars,
						'args'        => array(
							'depth' => 1,
						),
					)
				);
			}
		}
	}

	add_action( 'qode_framework_action_custom_nav_menu_fields', 'swissdelight_core_add_nav_menu_options' );
}
