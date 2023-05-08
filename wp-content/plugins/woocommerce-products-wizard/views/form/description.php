<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'description' => '',
    'descriptionSubClass' => '',
]);

if (!$arguments['description']) {
    return;
}
?>
<div class="woocommerce-products-wizard-form-description <?php
    echo esc_attr($arguments['descriptionSubClass']);
    ?>"><?php echo $arguments['description']; ?></div>
