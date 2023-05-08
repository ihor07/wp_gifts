<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$stepId = isset($stepId) ? $stepId : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Form;
use WCProductsWizard\Settings;
use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'id' => $id,
    'enableOrderByDropdown' => Settings::getStep($id, $stepId, 'enable_order_by_dropdown'),
    'orderBy' => Form::getStepOrderByValue($stepId),
    'orderByItems' => apply_filters(
        'woocommerce_catalog_orderby',
        [
            'menu_order' => esc_html__('Default sorting', 'woocommerce'),
            'popularity' => esc_html__('Sort by popularity', 'woocommerce'),
            'rating' => esc_html__('Sort by average rating', 'woocommerce'),
            'date' => esc_html__('Sort by latest', 'woocommerce'),
            'price' => esc_html__('Sort by price: low to high', 'woocommerce'),
            'price-desc' => esc_html__('Sort by price: high to low', 'woocommerce')
        ]
    )
]);

if (!$arguments['enableOrderByDropdown']) {
    return;
}
?>
<form action="#" method="get" class="woocommerce-products-wizard-form-order-by form-inline"
    data-component="wcpw-form-order-by wcpw-submit-on-change"
    data-step-id="<?php echo esc_attr($arguments['stepId']); ?>">
    <label for="woocommerce-products-wizard-form-order-by-<?php echo esc_attr($arguments['id']); ?>"
        class="woocommerce-products-wizard-form-order-by-label"><?php
        esc_html_e('Shop order', 'woocommerce');
        ?></label>
    <select name="wcpwOrderBy[<?php echo esc_attr($arguments['stepId']); ?>]"
        id="woocommerce-products-wizard-form-order-by-<?php echo esc_attr($arguments['id']); ?>"
        class="woocommerce-products-wizard-form-order-by-input form-select form-control"><?php
        foreach ($arguments['orderByItems'] as $key => $name) {
            echo '<option value="' . esc_html($key) . '" ' . selected($key, $arguments['orderBy']) . '>'
                . esc_html($name) . '</option>';
        }
        ?></select>
    <noscript>
        <button type="submit"
            class="woocommerce-products-wizard-form-order-by-submit btn btn-secondary"><?php
            esc_html_e('Apply order', 'woocommerce-products-wizard');
            ?></button>
    </noscript>
    <?php
    // no-js version forms values binding
    foreach (['wcpwFilter', 'wcpwProductsPerPage'] as $key) {
        if (isset($_GET[$key]) && !empty($_GET[$key])) {
            echo '<input type="hidden" name="wcpwFilter" value="'
                . esc_attr(http_build_query((array) $_GET[$key])) . '">';
        }
    }
    ?>
</form>
