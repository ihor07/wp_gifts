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
    'showAvailabilities' => Settings::getStep($id, $stepId, 'show_item_availabilities'),
    'product' => null
]);

$product = $arguments['product'];
$stockHtml = '';

if (!$product instanceof WC_Product || !$arguments['showAvailabilities']) {
    return;
}

if (function_exists('wc_get_stock_html')) {
    $stockHtml = wc_get_stock_html($product);
} elseif (method_exists($product, 'get_availability')) {
    $availability = $product->get_availability();
    $availabilityHtml = empty($availability['availability'])
        ? ''
        : '<p class="stock ' . esc_attr($availability['class']) . '">'
            . esc_html($availability['availability']) . '</p>';

    $stockHtml = apply_filters('woocommerce_stock_html', $availabilityHtml, $availability['availability'], $product);
}
?>
<div class="<?php echo esc_attr($arguments['class']); ?>-availability"
    data-component="wcpw-product-availability"
    data-default="<?php echo esc_attr($stockHtml); ?>"><?php echo wp_kses_post($stockHtml); ?></div>
