<?php

if ( ! function_exists( 'swissdelight_core_filter_clients_list_image_only_no_hover' ) ) {
    /**
     * Function that add variation layout for this module
     *
     * @param array $variations
     *
     * @return array
     */
    function swissdelight_core_filter_clients_list_image_only_no_hover( $variations ) {
        $variations['no-hover'] = esc_html__( 'No Hover', 'swissdelight-core' );

        return $variations;
    }

    add_filter( 'swissdelight_core_filter_clients_list_image_only_animation_options', 'swissdelight_core_filter_clients_list_image_only_no_hover' );
}