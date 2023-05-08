<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    return trigger_error('Empty wizard id', E_USER_WARNING);
}

if (!get_post_status($id)) {
    return trigger_error('Wizard does not exists', E_USER_WARNING);
}

use WCProductsWizard\Template;
use WCProductsWizard\Settings;
use WCProductsWizard\Utils;

$arguments = Template::getHTMLArgs([
    'id' => $id,
    'formId' => "wcpw-form-{$id}",
    'attachedProduct' => null,
    'mode' => Settings::getPost($id, 'mode'),
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'enableCheckoutStep' => Settings::getPost($id, 'enable_checkout_step'),
    'scrollingTopOnUpdate' => Settings::getPost($id, 'scrolling_top_on_update'),
    'scrollingUpGap' => Settings::getPost($id, 'scrolling_up_gap'),
    'reflectInMainCart' => Settings::getPost($id, 'reflect_in_main_cart'),
    'currentPageURL' => Utils::getCurrentURL(),
    'editCartItem' => isset($_GET['wcpwEditCartItem']) && $_GET['wcpwEditCartItem']
        ? wc_clean($_GET['wcpwEditCartItem'])
        : ''
]);

do_action('wcpw_before', $arguments);
?>
<section class="woocommerce-products-wizard <?php echo 'is-id-' . esc_attr($arguments['id']); ?>"
    data-component="wcpw" data-id="<?php echo esc_attr($arguments['id']); ?>"
    data-options="<?php echo esc_attr(wp_json_encode($arguments)); ?>"><?php
    Template::html('router', $arguments);
    ?></section>
<?php do_action('wcpw_after', $arguments); ?>
