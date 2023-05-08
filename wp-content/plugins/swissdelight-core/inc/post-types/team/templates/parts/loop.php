<?php

if ( have_posts() ) {
	while ( have_posts() ) :
		the_post();

		// Hook to include additional content before post item
		do_action( 'swissdelight_core_action_before_team_item' );

		$item_layout = apply_filters( 'swissdelight_core_filter_team_single_layout', '' );

		// Include post item
		swissdelight_core_template_part( 'post-types/team', 'templates/layouts/' . $item_layout );

		// Hook to include additional content after post item
		do_action( 'swissdelight_core_action_after_team_item' );

	endwhile; // End of the loop.
} else {
	// Include global posts not found
	swissdelight_core_theme_template_part( 'content', 'templates/parts/posts-not-found' );
}

wp_reset_postdata();
