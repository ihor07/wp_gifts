<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$stepId = isset($stepId) ? $stepId : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Utils;
use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$thumbnailLink = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
$arguments = Template::getHTMLArgs(
    [
        'class' => 'woocommerce-products-wizard-form-item',
        'enableThumbnailLink' => true,
        'thumbnailSize' => Settings::getStep($id, $stepId, 'item_thumbnail_size'),
        'thumbnailLink' => isset($thumbnailLink[0]) ? $thumbnailLink[0] : '',
        'thumbnailAttributes' => ['data-component' => 'wcpw-product-thumbnail-image'],
        'mergeThumbnailWithGallery' => false,
        'showThumbnails' => Settings::getStep($id, $stepId, 'show_item_thumbnails'),
        'product' => null
    ],
    ['recursive' => true]
);

$product = $arguments['product'];

if (!$product instanceof WC_Product || !$arguments['showThumbnails']) {
    return;
}

if (is_string($arguments['thumbnailSize']) && strpos($arguments['thumbnailSize'], ',') !== false) {
    $arguments['thumbnailSize'] = explode(',', $arguments['thumbnailSize']);
}

$dimensions = wc_get_image_size($arguments['thumbnailSize']);
$placeholderAttributes = [
    'src' => wc_placeholder_img_src(),
    'alt' => esc_html__('Placeholder', 'woocommerce'),
    'width' => $dimensions['width'],
    'height' => $dimensions['height']
];

$placeholderAttributes = array_replace($placeholderAttributes, $arguments['thumbnailAttributes']);
$placeholder = '<img ' . Utils::attributesArrayToString($placeholderAttributes) . '/>';
?>
<figure class="<?php echo esc_attr($arguments['class']); ?>-thumbnail thumbnail img-thumbnail"
    data-component="wcpw-product-thumbnail"><?php
    Template::html('form/item/prototype/tags', $arguments);

    if ($arguments['mergeThumbnailWithGallery']) {
        $attachmentIds = [];

        if (has_post_thumbnail($product->get_id())) {
            $attachmentIds[] = get_post_thumbnail_id($product->get_id());
        }

        if (method_exists($product, 'get_gallery_image_ids')) {
            $attachmentIds = array_merge($attachmentIds, (array) $product->get_gallery_image_ids());
        } elseif (method_exists($product, 'get_gallery_attachment_ids')) {
            $attachmentIds = array_merge($attachmentIds, (array) $product->get_gallery_attachment_ids());
        }

        $attachmentIds = array_unique(array_filter($attachmentIds));

        echo '<div class="' . esc_attr($arguments['class']) . '-thumbnail-gallery-wrapper">'
            . '<div class="' . esc_attr($arguments['class']) . '-thumbnail-gallery thumbnail has-items-count-'
            . count($attachmentIds) . '">';

        if (empty($attachmentIds)) {
            echo $placeholder;
            echo '</div>' . $placeholder . '</div>';
        } else {
            foreach ($attachmentIds as $attachmentId) {
                $imageLink = wp_get_attachment_url($attachmentId);
                $imageTitle = get_the_title($attachmentId);
                $itemAttributes = [
                    'class' => esc_attr($arguments['class']) . '-thumbnail-gallery-item zoom',
                    'title' => esc_attr($imageTitle),
                    'rel' => 'lightbox[' . $product->get_id() . ']',
                    'data-rel' => 'prettyPhoto[product-gallery-' . $product->get_id() . ']'
                ];

                if ($arguments['enableThumbnailLink'] && $imageLink) {
                    $itemAttributes['href'] = $imageLink;
                }

                $imageAttributes = [
                    'title' => esc_attr($imageTitle),
                    'alt' => trim(strip_tags(get_post_meta($attachmentId, '_wp_attachment_image_alt', true))),
                    'class' => esc_attr($arguments['class']) . '-thumbnail-gallery-item-image'
                ];

                if (reset($attachmentIds) == $attachmentId) {
                    $itemAttributes['data-component'] = 'wcpw-product-thumbnail-link';
                    $itemAttributes['class'] .= ' is-static';
                    $imageAttributes = array_replace($imageAttributes, $arguments['thumbnailAttributes']);
                    $imageAttributes['class'] .= ' is-static';
                }

                $image = wp_get_attachment_image($attachmentId, $arguments['thumbnailSize'], 0, $imageAttributes);
                $tag = $arguments['enableThumbnailLink'] ? 'a' : 'span';

                echo '<' . $tag . ' ' . Utils::attributesArrayToString($itemAttributes) . '>' . $imageTitle
                    . '</' . $tag . '>'
                    . '<span class="' . esc_attr($arguments['class']) . '-thumbnail-gallery-item-image-wrapper">'
                    . $image . '</span>';
            }

            // output one image under to take the correct size
            echo '</div>' . wp_get_attachment_image(reset($attachmentIds), $arguments['thumbnailSize']) . '</div>';
        }
    } else {
        if ($arguments['thumbnailLink'] && $arguments['enableThumbnailLink']) {
            echo '<a href="' . esc_attr($arguments['thumbnailLink']) . '"
            class="' . esc_attr($arguments['class']) . '-thumbnail-link"
            title="' . esc_attr(get_the_title(get_post_thumbnail_id())) . '"
            data-component="wcpw-product-thumbnail-link"
            data-rel="prettyPhoto[product-gallery-' . esc_attr($product->get_id()) . ']"
            rel="lightbox[' . esc_attr($product->get_id()) . ']">';
        }

        echo $product->get_image($arguments['thumbnailSize'], $arguments['thumbnailAttributes']);

        if ($arguments['thumbnailLink'] && $arguments['enableThumbnailLink']) {
            echo '</a>';
        }
    }
    ?></figure>
