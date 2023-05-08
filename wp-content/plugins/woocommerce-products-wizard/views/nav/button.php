<?php
defined('ABSPATH') || exit;

use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'class' => '',
    'state' => '',
    'action' => '',
    'id' => '',
    'navName' => '',
    'formId' => null,
    'toggleMobileNavOn' => 'sm'
]);

if (!$arguments['toggleMobileNavOn']) {
    return;
}
?>
<button role="tab"
    class="woocommerce-products-wizard-nav-button btn btn-default btn-light btn-block <?php
    echo esc_attr($arguments['class'] . " d-$arguments[toggleMobileNavOn]-none");
    ?>"
    form="<?php echo esc_attr($arguments['formId']); ?>"
    name="<?php echo esc_attr($arguments['action']); ?>"
    value="<?php echo esc_attr($arguments['value']); ?>"
    data-component="wcpw-nav-item"
    data-nav-action="<?php echo esc_attr($arguments['action']); ?>"
    data-nav-id="<?php echo esc_attr($arguments['value']); ?>"<?php
    disabled($arguments['state'], 'disabled');
    ?>><?php
    echo $arguments['thumbnail']
        ? wp_get_attachment_image(
            $arguments['thumbnail'],
            'thumbnail',
            false,
            ['class' => 'woocommerce-products-wizard-nav-button-thumbnail']
        ) . ' '
        : '';
    ?><span class="woocommerce-products-wizard-nav-button-inner"><?php
        echo wp_kses_post($arguments['navName']);
        ?></span></button>
