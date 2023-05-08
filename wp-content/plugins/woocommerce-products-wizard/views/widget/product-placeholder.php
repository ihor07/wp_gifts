<?php
defined('ABSPATH') || exit;

use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'stepId' => null,
    'navItemId' => null
]);
?>
<li class="woocommerce-products-wizard-widget-body-item is-product-placeholder <?php
    echo esc_attr("is-step-{$arguments['navItemId']}");
    echo $arguments['stepId'] == $arguments['navItemId'] ? ' is-current-step' : '';
    ?>"><div class="woocommerce-products-wizard-widget-item is-product-placeholder"></div></li>
