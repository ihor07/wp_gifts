<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$stepId = isset($stepId) ? $stepId : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Template;
use WCProductsWizard\Product;
use WCProductsWizard\Settings;

$arguments = Template::getHTMLArgs([
    'class' => 'woocommerce-products-wizard-form-item',
    'showAttributes' => Settings::getStep($id, $stepId, 'show_item_attributes'),
    'attributes' => []
]);

$product = isset($product) ? $product : null;

if (!$product instanceof WC_Product || !$arguments['showAttributes']) {
    return;
}

if (empty($arguments['attributes'])) {
    $arguments['attributes'] = Product::getAttributes($product);
}

if (empty($arguments['attributes'])) {
    return;
}
?>
<table class="<?php echo esc_attr($arguments['class']); ?>-attributes table table-sm">
    <?php foreach ($arguments['attributes'] as $key => $attribute) { ?>
        <tr class="<?php echo esc_attr($arguments['class']); ?>-attributes-item is-<?php echo esc_attr($key); ?>">
            <th class="<?php echo esc_attr($arguments['class']); ?>-attributes-item-name"><?php
                echo wp_kses_post($attribute['label']);
                ?></th>
            <td class="<?php echo esc_attr($arguments['class']); ?>-attributes-item-value"><?php
                echo wp_kses_post($attribute['value']);
                ?></td>
        </tr>
    <?php } ?>
</table>
