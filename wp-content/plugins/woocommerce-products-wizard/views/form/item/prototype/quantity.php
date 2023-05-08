<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$stepId = isset($stepId) ? $stepId : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Product;
use WCProductsWizard\Settings;
use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'id' => $id,
    'stepId' => $stepId,
    'class' => 'woocommerce-products-wizard-form-item',
    'soldIndividually' => false,
    'defaultQuantity' => Settings::getStep($id, $stepId, 'default_product_quantity'),
    'cartItem' => null,
    'product' => null,
    'formId' => null
]);

$product = $arguments['product'];

if (!$product instanceof WC_Product || $product->is_sold_individually() || $arguments['soldIndividually']) {
    return;
}

$min = Product::getMinQuantity($arguments['id'], $arguments['stepId']);
$max = Product::getMaxQuantity($arguments['id'], $arguments['stepId'], $product);
$value = $arguments['cartItem'] ? $arguments['cartItem']['quantity'] : $arguments['defaultQuantity'];
$value = $min ? max($value, $min) : $value;
$value = $max ? min($value, $max) : $value;
$disabled = !($product->is_purchasable() && ($product->is_in_stock() || $product->backorders_allowed()));
$inputId = "woocommerce-products-wizard-{$arguments['id']}-form-{$arguments['stepId']}-"
    . "item-{$product->get_id()}-quantity";

$input = woocommerce_quantity_input(
    [
        'input_id' => $inputId,
        'min_value' => $min,
        'max_value' => $max,
        'input_value' => $value,
        'input_name' => "productsToAdd[{$arguments['stepId']}-{$product->get_id()}][quantity]"
    ],
    $product,
    false
);

$replacements = [
    '<input' => '<input data-component="wcpw-product-quantity-input" form="' . $arguments['formId'] . '"'
        . disabled($disabled, true, false),
    'class="input-text ' => 'class="input-text form-control input-sm form-control-sm '
];

$input = str_replace(array_keys($replacements), array_values($replacements), $input);
?>
<div class="<?php echo esc_attr($arguments['class']); ?>-quantity" data-component="wcpw-product-quantity"><?php
    echo $input;
    ?></div>
