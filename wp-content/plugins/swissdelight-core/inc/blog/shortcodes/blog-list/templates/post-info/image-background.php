<?php
$blog_list_image = get_post_meta( get_the_ID(), 'qodef_blog_list_image', true );
$has_image       = ! empty( $blog_list_image ) || has_post_thumbnail();

if ( $has_image ) {
	$image_dimension = isset( $image_dimension ) && ! empty( $image_dimension ) && 'custom' !== $image_dimension ? esc_attr( $image_dimension['size'] ) : 'full';
	$image_url       = swissdelight_core_get_list_shortcode_item_image_url( $image_dimension, $blog_list_image );
	$style           = ! empty( $image_url ) ? 'background-image: url( ' . esc_url( $image_url ) . ')' : '';
	?>
    <div class="qodef-e-media-image-holder">
        <a itemprop="url" class="qodef-e-post-link" href="<?php the_permalink(); ?>">
            <div class="qodef-e-media-image qodef--background" <?php qode_framework_inline_style( $style ); ?>>
                <?php echo swissdelight_core_get_list_shortcode_item_image( $image_dimension, $blog_list_image ); ?>
            </div>
        </a>
    </div>
<?php } ?>
