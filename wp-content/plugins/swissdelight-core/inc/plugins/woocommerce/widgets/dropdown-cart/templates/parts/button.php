<?php
global $woocommerce;
?>
<div class="qodef-m-action">
    <a itemprop="url" href="<?php echo esc_url(wc_get_cart_url()); ?>" class="qodef-m-view-cart qodef-m-action-link"
       data-title="<?php esc_attr_e('View Cart', 'swissdelight-core'); ?>">
        <span class="qodef-m-border--top-left"></span>
        <span class="qodef-m-border--bottom-right"></span>
        <span><?php esc_html_e('Cart', 'swissdelight-core'); ?></span>
    </a>
    <a itemprop="url" href="<?php echo esc_url($woocommerce->cart->get_checkout_url()); ?>" class="qodef-m-checkout qodef-m-action-link"
       data-title="<?php esc_attr_e('Checkout', 'swissdelight-core'); ?>">
        <span><?php esc_html_e('Checkout', 'swissdelight-core'); ?></span>
    </a>
</div>