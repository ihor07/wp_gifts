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
    'descriptionSource' => Settings::getStep($id, $stepId, 'item_description_source'),
    'showDescriptions' => Settings::getStep($id, $stepId, 'show_item_descriptions'),
    'product' => null
]);

if (!$arguments['showDescriptions']) {
    return;
}

$product = $arguments['product'];

if (!$product instanceof WC_Product) {
    return;
}

switch ($arguments['descriptionSource']) {
    default:
    case 'content':
        $description = $product->get_description();
        break;

    case 'excerpt':
        $description = $product->get_short_description();
        break;

    case 'none':
        $description = '';
}

$description = do_shortcode(wpautop($description));
?>
<div class="<?php echo esc_attr($arguments['class']); ?>-description"
    data-component="wcpw-product-description"
    data-default="<?php echo esc_attr($description); ?>"><?php echo $description; ?></div>
