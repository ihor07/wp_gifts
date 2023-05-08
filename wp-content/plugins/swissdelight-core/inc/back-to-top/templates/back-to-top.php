<?php
$custom_icon = swissdelight_core_get_custom_svg_opener_icon_html( 'back_to_top' );
$skin        = swissdelight_get_post_value_through_levels( 'qodef_back_to_top_skin' );
$holder_classes = array();
if ( empty( $custom_icon ) ) {
	$holder_classes[] = 'qodef--predefined';
}
if ( !empty( $skin ) ) {
	$holder_classes[] = 'qodef-skin--' . $skin;
}
?>
<a id="qodef-back-to-top" href="#" <?php qode_framework_class_attribute( $holder_classes ); ?>>
	<?php if ( ! empty( $custom_icon ) ) { ?>
		<span class="qodef-back-to-top-icon">
				<?php echo swissdelight_core_get_custom_svg_opener_icon_html( 'back_to_top' ); ?>
			</span>
	<?php } else { ?>
		<span class="qodef-back-to-top-line"></span>
		<span class="qodef-back-to-top-text"><?php esc_html_e( 'Top', 'swissdelight-core' ); ?></span>
	<?php } ?>
</a>
