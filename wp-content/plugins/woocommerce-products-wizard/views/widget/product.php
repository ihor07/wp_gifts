<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Cart;
use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$arguments = Template::getHTMLArgs([
    'stepId' => null,
    'formId' => null,
    'cartItem' => null,
    'cartItemKey' => null,
    'navItem' => null,
    'hidePrices' => Settings::getPost($id, 'hide_prices'),
    'enableRemoveButton' => Settings::getPost($id, 'enable_remove_button'),
    'removeButtonText' => Settings::getPost($id, 'remove_button_text'),
    'removeButtonClass' => Settings::getPost($id, 'remove_button_class'),
    'enableEditButton' => Settings::getPost($id, 'enable_edit_button'),
    'editButtonText' => Settings::getPost($id, 'edit_button_text'),
    'editButtonClass' => Settings::getPost($id, 'edit_button_class')
]);

$product = $arguments['cartItem']['data'];

if (!$product instanceof \WC_Product) {
    return;
}
?>
<h1>lorem ipsum some text</h1>
<li class="woocommerce-products-wizard-widget-body-item is-product <?php
    echo esc_attr("is-step-{$arguments['cartItem']['step_id']} is-product-{$arguments['cartItem']['product_id']}");
    echo $arguments['stepId'] == $arguments['cartItem']['step_id'] ? ' is-current-step' : '';
    ?>">
    <article class="woocommerce-products-wizard-widget-item is-product">
        <?php if (Settings::getStep($arguments['id'], $arguments['cartItem']['step_id'], 'show_item_thumbnails')) { ?>
            <figure class="woocommerce-products-wizard-widget-item-thumbnail">
                <?php
                $href = wp_get_attachment_image_src($product->get_image_id(), 'large');
                $thumbnail = $product->get_image('shop_thumbnail', ['class' => 'img-thumbnail']);
                $thumbnail = apply_filters(
                    'wcpw_widget_item_thumbnail',
                    $thumbnail,
                    $arguments['cartItem'],
                    $arguments['cartItemKey']
                );

                echo isset($href[0])
                    ? "<a href=\"{$href[0]}\" data-rel=\"prettyPhoto\" rel=\"lightbox\">{$thumbnail}</a>"
                    : $thumbnail;
                ?>
            </figure>
        <?php } ?>
        <div class="woocommerce-products-wizard-widget-item-inner">
            <header class="woocommerce-products-wizard-widget-item-header">
                <h4 class="woocommerce-products-wizard-widget-item-title"><?php
                    if (method_exists($product, 'get_name')) {
                        echo $product->get_name();
                    }

                    if (($arguments['hidePrices']
                            || !Settings::getStep($arguments['id'], $arguments['cartItem']['step_id'], 'show_item_price'))
                        && (!isset($arguments['cartItem']['sold_individually']) || !$arguments['cartItem']['sold_individually'])
                    ) {
                        ?>
                        <bdi class="woocommerce-products-wizard-widget-item-times">x</bdi>
                        <span class="woocommerce-products-wizard-widget-item-quantity"><?php
                            echo $arguments['cartItem']['quantity'];
                            ?></span>
                        <?php
                    }
                    ?></h4>
                <?php
                if ($arguments['enableEditButton']
                    && !Settings::getStep($arguments['id'], $arguments['cartItem']['step_id'], 'hide_edit_button')
                ) {
                    $stepId = $arguments['cartItem']['step_id'];

                    if (isset($arguments['navItem']['merged_with_step']) && $arguments['navItem']['merged_with_step']) {
                        $stepId = $arguments['navItem']['merged_with_step'];
                    }
                    ?>
                    <button class="woocommerce-products-wizard-widget-item-control woocommerce-products-wizard-control <?php
                        echo esc_attr($arguments['editButtonClass']);
                        ?> btn is-edit-in-cart"
                        form="<?php echo esc_attr($arguments['formId']); ?>"
                        name="get-step"
                        value="<?php echo esc_attr($stepId); ?>"
                        title="<?php echo esc_attr($arguments['editButtonText']); ?>"
                        data-component="wcpw-product-edit-in-cart wcpw-nav-item"
                        data-nav-action="get-step"
                        data-nav-id="<?php echo esc_attr($stepId); ?>">
                        <!--spacer-->
                        <span class="woocommerce-products-wizard-control-inner"><?php
                            echo wp_kses_post($arguments['editButtonText']);
                            ?></span>
                        <!--spacer-->
                    </button>
                    <?php
                }
                ?>
            </header>
            <div class="woocommerce-products-wizard-widget-item-data"><?php
                echo Cart::getProductMeta($arguments['cartItem']);
                ?></div>
            <footer class="woocommerce-products-wizard-widget-item-footer">
                <?php
                if (!$arguments['hidePrices']
                    && Settings::getStep($arguments['id'], $arguments['cartItem']['step_id'], 'show_item_price')
                ) {
                    $price = Cart::getItemPrice($arguments['cartItem']);
                    ?>
                    <span class="woocommerce-products-wizard-widget-item-price<?php
                        echo $price == 0 ? ' is-zero-price ' : '';
                        ?>"><?php
                        // apply the filter for Subscriptions support
                        echo apply_filters('woocommerce_cart_product_price', wc_price($price), $product);

                        if (!isset($arguments['cartItem']['sold_individually'])
                            || !$arguments['cartItem']['sold_individually']
                        ) {
                            ?>
                            <bdi class="woocommerce-products-wizard-widget-item-price-times">x</bdi>
                            <span class="woocommerce-products-wizard-widget-item-price-quantity"><?php
                                echo $arguments['cartItem']['quantity'];
                                ?></span>
                            <?php
                        }
                        ?></span>
                    <?php
                }

                    ?>
                <h1>Hello World!!!</h1>
                    <button class="woocommerce-products-wizard-widget-item-control woocommerce-products-wizard-control <?php
                        echo esc_attr($arguments['removeButtonClass']);
                        ?> btn is-remove-from-cart"
                        form="<?php echo esc_attr($arguments['formId']); ?>"
                        name="remove-cart-product"
                        value="<?php echo esc_attr($arguments['cartItemKey']); ?>"
                        title="<?php echo esc_attr($arguments['removeButtonText']); ?>"
                        data-component="wcpw-remove-cart-product"
                        data-remove-cart-product-options="<?php
                        echo esc_attr(wp_json_encode([
                            'lazy' => Settings::getStep(
                                $arguments['id'],
                                $arguments['cartItem']['step_id'],
                                'buttons_nonblocking_requests'
                            )
                        ]));
                        ?>">
                        <!--spacer-->
                        <span class="woocommerce-products-wizard-control-inner"><?php
                            echo wp_kses_post($arguments['removeButtonText']);
                            ?></span>
                        <!--spacer-->
                    </button>

            </footer>
        </div>
    </article>
</li>
