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
    'stepId' => $stepId,
    'productsPerPage' => Form::getStepProductsPerPageValue($stepId),
    'productsPerPageItems' => Settings::getStep($id, $stepId, 'products_per_page_items')
]);

if (empty(array_filter($arguments['productsPerPageItems']))) {
    return;
}
?>
<form action="#" method="get" class="woocommerce-products-wizard-form-products-per-page form-inline"
    data-component="wcpw-form-products-per-page wcpw-submit-on-change"
    data-step-id="<?php echo esc_attr($arguments['stepId']); ?>">
    <label for="woocommerce-products-wizard-form-products-per-page-<?php echo esc_attr($arguments['id']); ?>"
        class="woocommerce-products-wizard-form-products-per-page-label"><?php
        esc_html_e('Products per page', 'woocommerce-products-wizard');
        ?></label>
    <select name="wcpwProductsPerPage[<?php echo esc_attr($arguments['stepId']); ?>]"
        id="woocommerce-products-wizard-form-products-per-page-<?php echo esc_attr($arguments['id']); ?>"
        class="woocommerce-products-wizard-form-products-per-page-input form-select form-control"><?php
        foreach (array_filter($arguments['productsPerPageItems']) as $item) {
            echo '<option value="' . esc_html($item) . '" '
                . selected($item, $arguments['productsPerPage'])
                . '>' . esc_html($item) . '</option>';
        }
        ?></select>
    <noscript>
        <button type="submit"
            class="woocommerce-products-wizard-form-products-per-page-submit btn btn-secondary"><?php
            esc_html_e('Apply products per page', 'woocommerce-products-wizard');
            ?></button>
    </noscript>
    <?php
    // no-js version forms values binding
    foreach (['wcpwFilter', 'wcpwOrderBy'] as $key) {
        if (isset($_GET[$key]) && !empty($_GET[$key])) {
            echo '<input type="hidden" name="wcpwFilter" value="'
                . esc_attr(http_build_query((array) $_GET[$key]))  . '">';
        }
    }
    ?>
</form>
