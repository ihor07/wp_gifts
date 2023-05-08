<?php
$excerpt = swissdelight_core_get_custom_post_type_excerpt( isset( $excerpt_length ) ? $excerpt_length : '' );

if ( ! empty( $excerpt ) && isset( $enable_excerpt ) && 'yes' === $enable_excerpt ) { ?>
	<p itemprop="description" class="qodef-woo-product-excerpt"><?php echo esc_html( $excerpt ); ?></p>
<?php } ?>
