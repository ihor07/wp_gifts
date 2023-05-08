<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$stepId = isset($stepId) ? $stepId : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Cart;
use WCProductsWizard\Settings;
use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'shareButtonText' => Settings::getPost($id, 'share_button_text'),
    'shareButtonClass' => Settings::getPost($id, 'share_button_class'),
    'shareButtonSuccessMessage' => Settings::getPost($id, 'share_button_success_message'),
    'currentPageURL' => '',
    'shareButtonLink' => "wcpwShare&wcpwId={$id}&wcpwStep={$stepId}&wcpwCart="
        . urlencode(json_encode(Cart::getCompressed($id)))
]);

$link = strpos($arguments['currentPageURL'], '?') !== false
    ? ($arguments['currentPageURL'] . '&' . $arguments['shareButtonLink'])
    : ($arguments['currentPageURL'] . '?' . $arguments['shareButtonLink'])
?>
<a class="btn woocommerce-products-wizard-control is-share <?php echo esc_attr($arguments['shareButtonClass']); ?>"
    href="<?php echo esc_attr($link); ?>" target="_blank" role="button"
    data-share-success-message="<?php echo esc_attr($arguments['shareButtonSuccessMessage']); ?>"
    data-component="wcpw-share"><span class="woocommerce-products-wizard-control-inner">
    <!--spacer-->
    <?php echo wp_kses_post($arguments['shareButtonText']); ?>
    <!--spacer-->
</span></a>
<!--spacer-->
