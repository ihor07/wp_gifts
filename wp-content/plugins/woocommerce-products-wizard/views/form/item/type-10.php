<?php
defined('ABSPATH') || exit;

use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'class' => 'woocommerce-products-wizard-form-item',
    'product' => null,
    'stepId' => null,
    'cartItem' => null,
    'hideChooseElement' => false,
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
<article class="product type-10 <?php
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
        <?php
        Template::html('form/item/prototype/thumbnail', $arguments);
        Template::html('form/item/prototype/gallery', $arguments);
        ?>
        <div class="<?php echo esc_attr($arguments['class']); ?>-check<?php
            echo !$arguments['hideChooseElement']
                ? esc_attr(' d-flex form-check custom-control custom-' . $inputType)
                : '';
            ?>">
            <?php
            Template::html('form/item/prototype/choose', $arguments);
            Template::html('form/item/prototype/title', $arguments);
            ?>
        </div>
        <?php
        Template::html('form/item/prototype/price', $arguments);
        Template::html('form/item/prototype/sku', $arguments);
        Template::html('form/item/prototype/description', $arguments);
        Template::html('form/item/prototype/attributes', $arguments);
        Template::html('form/item/prototype/footer', $arguments);
        ?>
    </div>
</article>
