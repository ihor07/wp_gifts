<?php
defined('ABSPATH') || exit;

use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'stepId' => null,
    'cartItem' => null
]);

$value = isset($arguments['cartItem']['display_value'])
    ? $arguments['cartItem']['display_value']
    : (isset($arguments['cartItem']['value']) ? $arguments['cartItem']['value'] : null);

if (is_null($value)) {
    return;
}
?>
<li class="woocommerce-products-wizard-widget-body-item is-field <?php
    echo esc_attr("is-step-{$arguments['cartItem']['step_id']}");
    echo $arguments['stepId'] == $arguments['cartItem']['step_id'] ? ' is-current-step' : '';
    ?>">
    <dl class="woocommerce-products-wizard-widget-item is-field">
        <dt class="woocommerce-products-wizard-widget-item-name"><?php
            echo wp_kses_post($arguments['cartItem']['key']);
            ?></dt>
        <dd class="woocommerce-products-wizard-widget-item-value"><?php echo wp_kses_post($value); ?></dd>
    </dl>
</li>
