<div class="qodef-grid-item <?php echo esc_attr( swissdelight_core_get_page_content_sidebar_classes() ); ?>">
	<?php
	$queried_tax = get_queried_object();
	$tax         = ! empty( $queried_tax->taxonomy ) ? $queried_tax->taxonomy : '';
	$tax_slug    = ! empty( $queried_tax->slug ) ? $queried_tax->slug : '';

	swissdelight_core_generate_team_archive_with_shortcode( $tax, $tax_slug );
	?>
</div>
