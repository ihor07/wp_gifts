<?php

if ( ! function_exists( 'swissdelight_core_add_performance_customizer_options' ) ) {
	/**
	 * Function that add customizer options for this module
	 */
	function swissdelight_core_add_performance_customizer_options() {
		$qode_framework = qode_framework_get_framework_root();

		$page = $qode_framework->add_options_page(
			array(
				'type' => 'customizer',
			)
		);

		if ( $page ) {

			$page->add_field_element(
				array(
					'field_type' => 'panel',
					'name'       => 'swissdelight_core_performance_panel',
					'priority'   => 250,
					'title'      => esc_html__( 'Qode Performance', 'swissdelight-core' ),
				)
			);

			// Hook to include additional options after module options
			do_action( 'swissdelight_core_action_performance_customizer_options', $page );
		}
	}

	add_action( 'qode_framework_action_customizer_swissdelight_core_performance_panel', 'swissdelight_core_add_performance_customizer_options' );
}
