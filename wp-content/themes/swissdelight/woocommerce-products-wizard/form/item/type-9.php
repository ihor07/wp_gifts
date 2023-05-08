<?php
defined('ABSPATH') || exit;

use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'class' => 'woocommerce-products-wizard-form-item',
    'product' => null,
    'stepId' => null,
    'cartItem' => null,
    'hideChooseElement' => false,
    'enableTitleLink' => false,
    'enableThumbnailLink' => false,
    'severalProducts' => false,
    'showFooterPrice' => false,
    'showFooterChoose' => false
]);

$inputType = $arguments['severalProducts'] ? 'checkbox' : 'radio';
$product = $arguments['product'];

if (!$product instanceof WC_Product) {
    return;
}

$disabled = !($product->is_purchasable() && ($product->is_in_stock() || $product->backorders_allowed()));
?>
<article class="product type-9 <?php
    echo esc_attr($arguments['class']) . ($arguments['cartItem'] ? ' is-in-cart' : '')
        . ($disabled ? ' is-disabled' : '');
    ?>"
    data-component="wcpw-product"
    data-type="<?php echo esc_attr($product->get_type()); ?>"
    data-id="<?php echo esc_attr($product->get_id()); ?>"
    data-step-id="<?php echo esc_attr($arguments['stepId']); ?>"<?php
    echo $arguments['cartItem'] ? (' data-cart-key="' . esc_attr($arguments['cartItem']['key']) . '"') : '';
    ?>>
    <div class="<?php echo esc_attr($arguments['class']); ?>-body">
        <a href="#<?php echo esc_attr("{$arguments['class']}-modal-{$product->get_id()}-{$arguments['stepId']}"); ?>"
            class="<?php echo esc_attr($arguments['class']); ?>-link"
            data-toggle="modal"><?php
            Template::html('form/item/prototype/thumbnail', array_replace($arguments, ['enableThumbnailLink' => false]));
            Template::html('form/item/prototype/title', array_replace($arguments, ['enableTitleLink' => false]));
            ?></a>
        <div class="<?php echo esc_attr($arguments['class']); ?>-check<?php
            echo !$arguments['hideChooseElement'] ? esc_attr(' form-check custom-control custom-' . $inputType) : '';
            ?>">
            <?php
            Template::html('form/item/prototype/choose', $arguments);
            Template::html('form/item/prototype/price', $arguments);
            ?>
        </div>
        <?php Template::html('form/item/prototype/footer', $arguments); ?>
    </div>
    <div class="<?php echo esc_attr($arguments['class']); ?>-modal modal fade" tabindex="-1" role="dialog"
        data-component="wcpw-product-modal"
        id="<?php echo esc_attr("{$arguments['class']}-modal-{$product->get_id()}-{$arguments['stepId']}"); ?>">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <a href="#" role="button" data-dismiss="modal"
                        class="<?php echo esc_attr($arguments['class']); ?>-modal-close close btn-close"
                        aria-label="<?php esc_attr_e('Close', 'woocommerce-products-wizard'); ?>">
                        <span aria-hidden="true" class="visually-hidden">&times;</span>
                    </a>
                    <div class="<?php echo esc_attr($arguments['class']); ?>-content">
                        <div class="<?php echo esc_attr($arguments['class']); ?>-content-thumbnail"><?php
                            Template::html('form/item/prototype/thumbnail', $arguments);
                            Template::html('form/item/prototype/gallery', $arguments);
                            Template::html('form/item/prototype/sku', $arguments);
                            ?></div>
                        <div class="<?php echo esc_attr($arguments['class']); ?>-content-body"><?php
                            Template::html('form/item/prototype/title', $arguments);
                            Template::html('form/item/prototype/description', $arguments);
                            Template::html('form/item/prototype/attributes', $arguments);
                            ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
