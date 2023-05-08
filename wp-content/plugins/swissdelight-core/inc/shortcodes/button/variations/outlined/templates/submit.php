<button type="submit" <?php qode_framework_class_attribute( $holder_classes ); ?> <?php qode_framework_inline_attrs( $data_attrs ); ?> <?php qode_framework_inline_style( $styles ); ?>>
    <span class="qodef-m-border--top-left" <?php qode_framework_inline_style( $outline_styles ); ?>></span>
    <span class="qodef-m-border--bottom-right" <?php qode_framework_inline_style( $outline_styles ); ?>></span>
    <span class="qodef-btn-text"><?php echo esc_html( $text ); ?>
		<?php if('yes' === $show_arrow) {?>
            <?php swissdelight_render_svg_icon( 'button-arrow', 'qodef-m-svg-icon' ); ?>
		<?php }?>
	</span>
</button>
