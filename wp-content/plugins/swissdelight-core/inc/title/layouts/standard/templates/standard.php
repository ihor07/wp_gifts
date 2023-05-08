<?php
// Load title image template
swissdelight_core_get_page_title_image();
$cursive = swissdelight_core_get_post_value_through_levels( 'qodef_page_title_cursive' );
?>
<div class="qodef-m-content <?php echo esc_attr( swissdelight_core_get_page_title_content_classes() ); ?>">
	<?php
	// Load subtitle template
	swissdelight_core_template_part( 'title/layouts/standard', 'templates/parts/subtitle', '', swissdelight_core_get_standard_title_layout_subtitle_text() );
	?>
	<<?php echo esc_attr( $title_tag ); ?> class="qodef-m-title entry-title">
		<?php
		if ( qode_framework_is_installed( 'theme' ) ) {
			echo esc_html( swissdelight_get_page_title_text() );
		} else {
			echo get_option( 'blogname' );
		}
		?>
		<?php if ( ! empty( $cursive ) ) { ?>
			<span class="qodef-m-cursive"><?php echo esc_html( $cursive ); ?></span>
		<?php } ?>
	</<?php echo esc_attr( $title_tag ); ?>>
</div>
