<?php
// Hook to include additional content before page content holder
do_action( 'swissdelight_action_before_page_content_holder' );
?>
<main id="qodef-page-content" class="qodef-grid qodef-layout--template <?php echo esc_attr( swissdelight_get_grid_gutter_classes() ); ?>">
	<div class="qodef-grid-inner clear">
		<?php
		// Include page content loop
		swissdelight_template_part( 'content', 'templates/parts/loop' );

		// Include page content sidebar
		swissdelight_template_part( 'sidebar', 'templates/sidebar' );
		?>
	</div>
</main>
<?php
// Hook to include additional content after main page content holder
do_action( 'swissdelight_action_after_page_content_holder' );
?>
