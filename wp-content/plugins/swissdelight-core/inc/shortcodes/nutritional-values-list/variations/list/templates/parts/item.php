<div class="qodef-m-item qodef-e">
	<div class="qodef-m-item-info">
	<?php if ( ! empty( $item['item_title'] ) ) { ?>
		<<?php echo esc_attr( $item['item_title_tag'] ); ?> class="qodef-e-title" <?php qode_framework_inline_style( $this_shortcode->get_title_styles( $item ) ); ?>>
		<?php echo qode_framework_wp_kses_html( 'title', $item['item_title'] ); ?>
		</<?php echo esc_attr( $item['item_title_tag'] ); ?>>
	<?php } ?>
	<?php if ( ! empty( $item['item_value'] ) ) { ?>
		<span class="qodef-e-text" <?php qode_framework_inline_style( $this_shortcode->get_value_styles( $item ) ); ?>><?php echo qode_framework_wp_kses_html( 'content', $item['item_value'] ); ?></span>
	<?php } ?>
	</div>
	<span class="qodef-e-line"></span>
</div>
