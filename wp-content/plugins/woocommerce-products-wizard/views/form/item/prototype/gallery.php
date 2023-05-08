<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$stepId = isset($stepId) ? $stepId : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Settings;
use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'class' => 'woocommerce-products-wizard-form-item',
    'mergeThumbnailWithGallery' => false,
    'product' => null,
    'galleryGrid' => Settings::getStep($id, $stepId, 'item_gallery_column'),
    'showGalleries' => Settings::getStep($id, $stepId, 'show_item_gallery')
]);

$product = $arguments['product'];

if (!$product instanceof WC_Product || $arguments['mergeThumbnailWithGallery'] || !$arguments['showGalleries']) {
    return;
}

$colClasses = [];
$attachmentIds = [];

if (method_exists($product, 'get_gallery_image_ids')) {
    $attachmentIds = $product->get_gallery_image_ids();
} elseif (method_exists($product, 'get_gallery_attachment_ids')) {
    $attachmentIds = $product->get_gallery_attachment_ids();
}

if (!$attachmentIds || empty($attachmentIds)) {
    return;
}

if (!isset($arguments['galleryGrid']['xxs'])) {
    $arguments['galleryGrid']['xxs'] = 12;
}

$colClasses[] = "col-{$arguments['galleryGrid']['xxs']}";

unset($arguments['galleryGrid']['xxs']);

foreach ($arguments['galleryGrid'] as $col => $value) {
    $colClasses[] = "col-{$col}-{$value}";
}

$colClass = implode(' ', $colClasses);
?>
<section class="<?php echo esc_attr($arguments['class']); ?>-gallery row" data-component="wcpw-product-gallery"
    aria-label="<?php esc_html_e('Gallery', 'woocommerce-products-wizard'); ?>">
    <?php
    foreach ($attachmentIds as $attachmentId) {
        $imageLink = wp_get_attachment_url($attachmentId);

        if (!$imageLink) {
            continue;
        }

        $sizeInfo = wc_get_image_size('gallery_thumbnail');
        $size = apply_filters('woocommerce_gallery_image_size', [$sizeInfo['width'], $sizeInfo['height']]);
        $fullSize = apply_filters('woocommerce_gallery_full_size', apply_filters('woocommerce_product_thumbnails_large_size', 'full'));
        $fullSrc = wp_get_attachment_image_src($attachmentId, $fullSize);
        $image = wp_get_attachment_image(
            $attachmentId,
            $size,
            false,
            apply_filters(
                'woocommerce_gallery_image_html_attachment_image_params',
                [
                    'title' => _wp_specialchars(get_post_field('post_title', $attachmentId), ENT_QUOTES, 'UTF-8', true),
                    'data-caption' => _wp_specialchars(get_post_field('post_excerpt', $attachmentId), ENT_QUOTES, 'UTF-8', true),
                    'data-src' => esc_url($fullSrc[0]),
                    'data-large_image' => esc_url($fullSrc[0]),
                    'data-large_image_width' => esc_attr($fullSrc[1]),
                    'data-large_image_height' => esc_attr($fullSrc[2]),
                    'class' => esc_attr($arguments['class']) . '-gallery-item-image img-thumbnail',
                    'alt' => trim(wp_strip_all_tags(get_post_meta($attachmentId, '_wp_attachment_image_alt', true)))
                ],
                $attachmentId,
                $size,
                false
            )
        );
        ?>
        <div class="<?php echo esc_attr($colClass); ?>">
            <a href="<?php echo esc_attr($imageLink); ?>"
                class="<?php echo esc_attr($arguments['class']) . '-gallery-item thumbnail zoom'; ?>"
                title="<?php echo esc_attr(get_post_field('post_excerpt', $attachmentId)); ?>"
                rel="lightbox[<?php echo esc_attr($product->get_id()); ?>]"
                data-rel="prettyPhoto[product-gallery-<?php echo esc_attr($product->get_id()); ?>]"><?php
                echo $image;
                ?></a>
        </div>
        <?php
    }
    ?>
</section>
