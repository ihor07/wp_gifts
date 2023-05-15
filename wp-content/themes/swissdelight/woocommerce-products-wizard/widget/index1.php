<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

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
    'cartTotalPrice' => Cart::getTotalPrice($id),
    'stepId' => null,
    'formId' => null,
    'navItems' => Form::getNavItems($id),
    'hidePrices' => Settings::getPost($id, 'hide_prices'),
    'sidebarPosition' => Settings::getPost($id, 'sidebar_position'),
    'widgetIsExpanded' => Settings::getPost($id, 'widget_is_expanded'),
    'toggleWidgetOn' => Settings::getPost($id, 'toggle_widget_on', 'post', 'md'),
    'stickyWidget' => Settings::getPost($id, 'sticky_widget'),
    'stickyWidgetOffsetTop' => Settings::getPost($id, 'sticky_widget_offset_top'),
    'subtotalString' => Settings::getPost($id, 'subtotal_string'),
    'discountString' => Settings::getPost($id, 'discount_string'),
    'totalString' => Settings::getPost($id, 'total_string'),
    'showStepsInCart' => Settings::getPost($id, 'show_steps_in_cart'),
    'navigateUsingWidgetSteps' => Settings::getPost($id, 'navigate_using_widget_steps'),
    'groupProductsIntoKits' => Settings::getPost($id, 'group_products_into_kits'),
    'kitsType' => Settings::getPost($id, 'kits_type'),
    'kitBasePrice' => Settings::getPost($id, 'kit_base_price'),
    'kitBasePriceString' => Settings::getPost($id, 'kit_base_price_string'),
    'generateThumbnail' => Settings::getPost($id, 'generate_thumbnail')
]);

$outOfStepsCart = Cart::get($arguments['id'], ['excludeSteps' => array_keys($arguments['navItems'])]);
$isExpanded = isset($_COOKIE["#woocommerce-products-wizard-widget-{$arguments['id']}-expanded"])
    ? $_COOKIE["#woocommerce-products-wizard-widget-{$arguments['id']}-expanded"]
    : $arguments['widgetIsExpanded'];
?>
<section class="woocommerce-products-wizard-widget panel panel-default card<?php
    echo esc_attr(' is-position-' . $arguments['sidebarPosition']);
    echo $arguments['toggleWidgetOn'] ? esc_attr(' toggle-' . $arguments['toggleWidgetOn']) : '';
    ?>"
    id="woocommerce-products-wizard-widget-<?php echo esc_attr($arguments['id']); ?>"
    aria-label="<?php esc_html_e('Cart', 'woocommerce-products-wizard'); ?>"
    aria-expanded="<?php echo var_export(filter_var($isExpanded, FILTER_VALIDATE_BOOLEAN), true); ?>"
    data-component="wcpw-widget<?php echo $arguments['stickyWidget'] ? ' wcpw-sticky' : ''; ?>"
    data-sticky-options="<?php
    echo esc_attr(wp_json_encode([
        'offset_top' => $arguments['stickyWidgetOffsetTop'],
        'parent' => '[data-component=wcpw-main-row]'
    ]));
    ?>">
    <?php
    if (empty($arguments['cart']) && $arguments['showStepsInCart'] != 'all') {
        Template::html('messages/cart-is-empty', $arguments);
    } else {
        ?>
        <ul class="woocommerce-products-wizard-widget-body">
            <?php
            if ($arguments['generateThumbnail']) {
                Template::html('widget/thumbnail', $arguments);
            }

            foreach ($outOfStepsCart as $cartItemKey => $cartItem) {
                if ((isset($cartItem['data'])
                    && (!$cartItem['data'] || ($cartItem['data'] instanceof WC_Product && !$cartItem['data']->exists())))
                    || (isset($cartItem['quantity']) && $cartItem['quantity'] <= 0)
                    || (isset($cartItem['value']) && empty($cartItem['value']))
                    || !isset($cartItem['step_id'])
                ) {
                    continue;
                }

                $itemArguments = array_replace(
                    $arguments,
                    [
                        'cartItem' => $cartItem,
                        'cartItemKey' => $cartItemKey
                    ]
                );

                if (isset($cartItem['product_id'], $cartItem['data']) && !is_null($cartItem['data'])) {
                    Template::html('widget/product', $itemArguments);
                } elseif (isset($cartItem['value'], $cartItem['key']) && !empty($cartItem['value'])
                    && !empty($cartItem['key'])
                ) {
                    Template::html('widget/field', $itemArguments);
                }
            }

            foreach ($arguments['navItems'] as $navItem) {
                if ($arguments['showStepsInCart'] == 'all'
                    || ($arguments['showStepsInCart'] == 'selected' && $navItem['selected'])
                ) {
                    Template::html('widget/heading', array_replace($arguments, ['navItem' => $navItem]));
                }

                $loop = 0;

                foreach ($arguments['cart'] as $cartItemKey => $cartItem) {
                    if (!isset($cartItem['step_id']) || $cartItem['step_id'] != $navItem['id']
                        || (isset($cartItem['data']) && (!$cartItem['data']
                            || ($cartItem['data'] instanceof WC_Product && !$cartItem['data']->exists())))
                        || (isset($cartItem['quantity']) && $cartItem['quantity'] <= 0)
                        || (isset($cartItem['value']) && empty($cartItem['value']))
                    ) {
                        continue;
                    }

                    $itemArguments = array_replace(
                        $arguments,
                        [
                            'cartItem' => $cartItem,
                            'cartItemKey' => $cartItemKey,
                            'navItem' => $navItem
                        ]
                    );

                    if (isset($cartItem['product_id'], $cartItem['data']) && !is_null($cartItem['data'])) {
                        $loop++;
                        Template::html('widget/product', $itemArguments);
                    } elseif (isset($cartItem['value'], $cartItem['key']) && !empty($cartItem['value'])
                        && !empty($cartItem['key'])
                    ) {
                        Template::html('widget/field', $itemArguments);
                    }
                }

                $minProductsSelected = Settings::getStep($arguments['id'], $navItem['id'], 'min_products_selected');

                if (Settings::getStep($arguments['id'], $navItem['id'], 'show_min_products_selected_placeholders')
                    && isset($minProductsSelected['value']) && $minProductsSelected['value']
                ) {
                    $diff = Form::checkStepQuantitiesRule($arguments['id'], $minProductsSelected) - $loop;
                    $itemArguments = array_replace($arguments, ['navItemId' => $navItem['id']]);

                    while ($diff-- > 0) {
                        Template::html('widget/product-placeholder', $itemArguments);
                    }
                }
            }
            ?>
        </ul>
        <footer class="woocommerce-products-wizard-widget-footer">
            <?php
            if ($arguments['groupProductsIntoKits'] && $arguments['kitsType'] == 'combined'
                && $arguments['kitBasePrice'] && !$arguments['hidePrices']
            ) {
                ?>
                <dl class="woocommerce-products-wizard-widget-footer-row is-kit-base-price">
                    <dt class="woocommerce-products-wizard-widget-footer-cell is-caption"><?php
                        echo wp_kses_post($arguments['kitBasePriceString']);
                        ?></dt>
                    <dd class="woocommerce-products-wizard-widget-footer-cell is-value"><?php
                        echo wc_price((float) $arguments['kitBasePrice']);
                        ?></dd>
                </dl>
                <?php
            }

            if (!$arguments['hidePrices']) {
                ?>
                <dl class="woocommerce-products-wizard-widget-footer-row is-total">
                    <dt class="woocommerce-products-wizard-widget-footer-cell is-caption"><?php
                        echo wp_kses_post($arguments['totalString']);
                        ?></dt>
                    <dd class="woocommerce-products-wizard-widget-footer-cell is-value"><?php
                        echo wp_kses_post($arguments['cartTotalPrice']);
                        ?></dd>
                </dl>
                <?php
            }
            ?>
        </footer>
        <?php
    }
    ?>
</section>
