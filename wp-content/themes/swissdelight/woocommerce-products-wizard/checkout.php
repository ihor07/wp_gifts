<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$mode = isset($mode) ? $mode : 'step-by-step';
$notices = WCProductsWizard\Instance()->form->getNotices($mode == 'single-step' ? 'checkout' : null);

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Cart;
use WCProductsWizard\Form;
use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$arguments = Template::getHTMLArgs([
    'id' => $id,
    'cart' => Cart::get($id),
    'checkoutStepDescription' => Settings::getPost($id, 'checkout_step_description'),
    'notices' => $notices
]);

$class = ['woocommerce-products-wizard-step', 'woocommerce-products-wizard-checkout', 'is-step-checkout'];

if (Form::getActiveStepId($arguments['id']) == 'checkout') {
    $class[] = 'is-active';
}

echo '<article class="' . esc_attr(implode(' ', $class)) . '" data-component="wcpw-form-step">';

do_action('wcpw_before_checkout_output', $arguments);

if (!empty($arguments['notices'])) {
    foreach ($arguments['notices'] as $notice) {
        Template::html("messages/{$notice['view']}", array_replace($arguments, $notice));
    }
}

if (empty($arguments['cart']) || empty(WC()->cart->get_cart_contents())) {
    Template::html('messages/cart-is-empty', $arguments);

    echo '</article>';

    return;
}

echo '<div class="woocommerce-products-wizard-checkout-description">'
    . apply_filters('the_content', $arguments['checkoutStepDescription']) . '</div>';

do_action('wcpw_after_checkout_output', $arguments);

echo '</article>';
