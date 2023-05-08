<?php if ( 'custom-icon' === $icon_type && ( ! empty( $custom_icon || $svg_code ) ) ) { ?>
	<?php if ( ! empty( $link ) ) : ?>
		<a itemprop="url" href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $target ); ?>">
	<?php endif; ?>
	<?php if ( empty( $svg_code ) ) : ?>
		<?php echo wp_get_attachment_image( $custom_icon, 'full' ); ?>
	<?php else : ?>
		<?php echo qode_framework_wp_kses_html( 'html', $svg_code ); ?>
	<?php endif; ?>
	<?php if ( ! empty( $link ) ) : ?>
		</a>
	<?php endif; ?>
<?php } ?>
