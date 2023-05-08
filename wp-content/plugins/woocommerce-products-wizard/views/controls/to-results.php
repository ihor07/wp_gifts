<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Settings;
use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'formId' => null,
    'toResultsButtonText' => Settings::getPost($id, 'to_results_button_text'),
    'toResultsButtonClass' => Settings::getPost($id, 'to_results_button_class'),
    'toResultsButtonBehavior' => Settings::getPost($id, 'to_results_button_behavior')
]);
?>
<button class="btn woocommerce-products-wizard-control is-to-results <?php
    echo esc_attr($arguments['toResultsButtonClass']);
    ?>"
    form="<?php echo esc_attr($arguments['formId']); ?>"
    type="submit" name="<?php echo esc_attr($arguments['toResultsButtonBehavior']); ?>"
    data-component="wcpw-to-results wcpw-nav-item"
    data-nav-action="<?php echo esc_attr($arguments['toResultsButtonBehavior']); ?>"
    data-nav-id="result"><span class="woocommerce-products-wizard-control-inner">
        <!--spacer-->
        <?php echo wp_kses_post($arguments['toResultsButtonText']); ?>
        <!--spacer-->
    </span></button>
<!--spacer-->
