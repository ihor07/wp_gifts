<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Cart;
use WCProductsWizard\Thumbnail;
use WCProductsWizard\Template;
use WCProductsWizard\Utils;

$arguments = Template::getHTMLArgs([
    'id' => $id,
    'cart' => Cart::get($id)
]);

$generatedThumbnail = Thumbnail::generate($arguments['id'], $arguments['cart']);

if (empty($generatedThumbnail)) {
    return;
}
?>
<li class="woocommerce-products-wizard-widget-body-item is-thumbnail">
    <figure class="woocommerce-products-wizard-widget-generated-thumbnail">
        <?php
        $attributes = [
            'src' => $generatedThumbnail['url'],
            'alt' => ''
        ];

        $attributes = apply_filters(
            'wcpw_widget_generated_thumbnail_attributes',
            $attributes,
            $arguments['id'],
            $arguments['cart']
        );

        $thumbnail = '<img ' . Utils::attributesArrayToString($attributes) . '>';

        echo "<a href=\"{$generatedThumbnail['url']}\" "
            . "data-rel=\"prettyPhoto\" rel=\"lightbox\">{$thumbnail}</a>";
        ?>
    </figure>
</li>
