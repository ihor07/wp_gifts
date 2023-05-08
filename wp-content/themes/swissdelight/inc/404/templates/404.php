<?php

// Hook to include additional content before 404 page content
do_action( 'swissdelight_action_before_404_page_content' );

// Include module content template
echo apply_filters( 'swissdelight_filter_404_page_content_template', swissdelight_get_template_part( '404', 'templates/404-content', '', swissdelight_get_404_page_parameters() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

// Hook to include additional content after 404 page content
do_action( 'swissdelight_action_after_404_page_content' );
