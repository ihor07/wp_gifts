<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$stepId = isset($stepId) ? $stepId : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$arguments = Template::getHTMLArgs([
    'class' => 'woocommerce-products-wizard-form-item',
    'showSku' => Settings::getStep($id, $stepId, 'show_item_sku'),
    'product' => null
]);

$product = $arguments['product'];

if (!$product instanceof WC_Product || !wc_product_sku_enabled()
    || !($product->get_sku() || $product->is_type('variable')) || !$arguments['showSku']
) {
    return;
}

$sku = $product->get_sku();
$sku = $sku ? $sku : esc_html__('N/A', 'woocommerce');
?>
<dl class="<?php echo esc_attr($arguments['class']); ?>-sku">
    <dt class="<?php echo esc_attr($arguments['class']); ?>-sku-name"><?php
        esc_html_e('SKU:', 'woocommerce');
        ?></dt>
    <dd class="<?php echo esc_attr($arguments['class']); ?>-sku-value" data-component="wcpw-product-sku"
        data-default="<?php echo esc_attr($sku); ?>"><?php echo wp_kses_post($sku); ?></dd>
</dl>
