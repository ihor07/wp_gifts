<?php if ( ! empty( $tagline ) ) { ?>
	<span class="qodef-m-tagline" <?php qode_framework_inline_style( $tagline_styles ); ?>><?php echo qode_framework_wp_kses_html( 'content', $tagline ); ?></span>
<?php } ?>
<?php if ( ! empty( $title ) ) { ?>
	<<?php echo esc_attr( $title_tag ); ?> class="qodef-m-title" <?php qode_framework_inline_style( $title_styles ); ?>>
			<?php echo swissdelight_core_get_html_returned( $title ); ?>
	</<?php echo esc_attr( $title_tag ); ?>>
<?php } ?>
