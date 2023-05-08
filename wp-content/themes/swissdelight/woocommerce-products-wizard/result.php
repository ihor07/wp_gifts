<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$mode = isset($mode) ? $mode : 'step-by-step';
$notices = WCProductsWizard\Instance()->form->getNotices($mode == 'single-step' ? 'result' : null);

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
    'steps' => Form::getSteps($id),
    'notices' => $notices,
    'hidePrices' => Settings::getPost($id, 'hide_prices'),
    'enableRemoveButton' => Settings::getPost($id, 'enable_remove_button'),
    'removeButtonClass' => Settings::getPost($id, 'remove_button_class'),
    'removeButtonText' => Settings::getPost($id, 'remove_button_text'),
    'enableEditButton' => Settings::getPost($id, 'enable_edit_button'),
    'editButtonText' => Settings::getPost($id, 'edit_button_text'),
    'editButtonClass' => Settings::getPost($id, 'edit_button_class'),
    'showStepsInCart' => Settings::getPost($id, 'show_steps_in_cart'),
    'showResultsStepTable' => Settings::getPost($id, 'show_results_step_table'),
    'showResultsRemoveColumn' => Settings::getPost($id, 'show_results_table_remove_column', 'post', true),
    'showResultsThumbnailColumn' => Settings::getPost($id, 'show_results_table_thumbnail_column', 'post', true),
    'showResultsDataColumn' => Settings::getPost($id, 'show_results_table_data_column', 'post', true),
    'showResultsPriceColumn' => Settings::getPost($id, 'show_results_table_price_column', 'post', true),
    'showResultsQuantityColumn' => Settings::getPost($id, 'show_results_table_quantity_column', 'post', true),
    'showResultsSubtotalColumn' => Settings::getPost($id, 'show_results_table_subtotal_column', 'post', true),
    'resultsStepContactForm' => Settings::getPost($id, 'results_step_contact_form'),
    'resultsStepDescription' => Settings::getPost($id, 'results_step_description'),
    'resultsRemoveString' => Settings::getPost($id, 'results_remove_string'),
    'resultsPriceString' => Settings::getPost($id, 'results_price_string'),
    'resultsThumbnailString' => Settings::getPost($id, 'results_thumbnail_string'),
    'resultsDataString' => Settings::getPost($id, 'results_data_string'),
    'resultsQuantityString' => Settings::getPost($id, 'results_quantity_string'),
    'subtotalString' => Settings::getPost($id, 'subtotal_string'),
    'discountString' => Settings::getPost($id, 'discount_string'),
    'totalString' => Settings::getPost($id, 'total_string'),
    'groupProductsIntoKits' => Settings::getPost($id, 'group_products_into_kits'),
    'kitsType' => Settings::getPost($id, 'kits_type'),
    'kitBasePrice' => Settings::getPost($id, 'kit_base_price'),
    'kitBasePriceString' => Settings::getPost($id, 'kit_base_price_string')
]);

$previousStep = null;
$showProductsHeader = true;
$colspanLeft = $colspanRight = $columnsNumber =
    (int) ($arguments['showResultsRemoveColumn'] && $arguments['enableRemoveButton'])
    + (int) ($arguments['showResultsThumbnailColumn'])
    + (int) ($arguments['showResultsDataColumn'])
    + (int) ($arguments['showResultsPriceColumn'] && !$arguments['hidePrices'])
    + (int) ($arguments['showResultsQuantityColumn'])
    + (int) ($arguments['showResultsSubtotalColumn'] && !$arguments['hidePrices']);

if ($arguments['showResultsDataColumn'] && $arguments['showResultsSubtotalColumn']) {
    $colspanLeft = ceil($columnsNumber / 2);
    $colspanRight = floor($columnsNumber / 2);
}

$class = ['woocommerce-products-wizard-step', 'woocommerce-products-wizard-results', 'is-step-result'];

if (Form::getActiveStepId($arguments['id']) == 'result') {
    $class[] = 'is-active';
}

echo '<article class="' . esc_attr(implode(' ', $class)) . '" data-component="wcpw-form-step">';

if (!empty($arguments['notices'])) {
    foreach ($arguments['notices'] as $notice) {
        Template::html("messages/{$notice['view']}", array_replace($arguments, $notice));
    }
}

if (empty($arguments['cart'])) {
    Template::html('messages/cart-is-empty', $arguments);

    echo '</article>';

    return;
}

if ($arguments['resultsStepDescription']) {
    echo '<div class="woocommerce-products-wizard-results-description">'
        . do_shortcode(wpautop($arguments['resultsStepDescription']))
        . '</div>';
}

if ($arguments['showResultsStepTable']) {
    ?>
    <table class="woocommerce-products-wizard-results-table table table-hover wcpw-table-responsive"
        data-component="wcpw-results-table">
        <?php
        if ($arguments['groupProductsIntoKits'] && $arguments['kitsType'] == 'combined'
            && $arguments['kitBasePrice'] && !$arguments['hidePrices']
        ) {
            ?>
            <thead class="woocommerce-products-wizard-results-table-header">
                <tr class="woocommerce-products-wizard-results-table-header-row is-kit-base-price">
                    <?php
                    if ($arguments['showResultsDataColumn']) {
                        ?>
                        <th class="woocommerce-products-wizard-results-table-header-cell is-caption"
                            colspan="<?php echo esc_attr($colspanLeft); ?>"><?php
                            echo wp_kses_post($arguments['kitBasePriceString']);
                            ?></th>
                        <?php
                    }

                    if ($arguments['showResultsSubtotalColumn']) {
                        ?>
                        <td class="woocommerce-products-wizard-results-table-header-cell is-value"
                            colspan="<?php echo esc_attr($colspanRight); ?>"
                            data-th="<?php echo esc_attr($arguments['kitBasePriceString']); ?>"><?php
                            echo wc_price((float) $arguments['kitBasePrice']);
                            ?></td>
                        <?php
                    }
                    ?>
                </tr>
            </thead>
            <?php
        }
        ?>
        <tbody class="woocommerce-products-wizard-results-table-body">
            <?php
            foreach ($arguments['cart'] as $cartItemKey => $cartItem) {
                if ((isset($cartItem['data'])
                    && (!$cartItem['data'] || ($cartItem['data'] instanceof WC_Product && !$cartItem['data']->exists())))
                    || (isset($cartItem['quantity']) && $cartItem['quantity'] <= 0)
                    || (isset($cartItem['value']) && empty($cartItem['value']))
                    || !isset($cartItem['step_id'])
                ) {
                    continue;
                }

                if ($showProductsHeader && isset($cartItem['product_id'], $cartItem['data']) && $cartItem['data']) {
                    ?>
                    <tr class="woocommerce-products-wizard-results-table-body-row is-products wcpw-table-responsive-hidden">
                        <?php
                        if ($arguments['showResultsRemoveColumn'] && $arguments['enableRemoveButton']) {
                            ?>
                            <th class="woocommerce-products-wizard-results-table-header-cell is-remove">
                                <span class="sr-only visually-hidden"><?php
                                    echo wp_kses_post($arguments['resultsRemoveString']);
                                    ?></span>
                            </th>
                            <?php
                        }

                        if ($arguments['showResultsThumbnailColumn']) {
                            ?>
                            <th class="woocommerce-products-wizard-results-table-header-cell is-thumbnail">
                                <span class="sr-only visually-hidden"><?php
                                    echo wp_kses_post($arguments['resultsThumbnailString']);
                                    ?></span>
                            </th>
                            <?php
                        }

                        if ($arguments['showResultsDataColumn']) {
                            ?>
                            <th class="woocommerce-products-wizard-results-table-header-cell is-data"><?php
                                echo wp_kses_post($arguments['resultsDataString']);
                                ?></th>
                            <?php
                        }

                        if ($arguments['showResultsPriceColumn'] && !$arguments['hidePrices']) {
                            ?>
                            <th class="woocommerce-products-wizard-results-table-header-cell is-price"><?php
                                echo wp_kses_post($arguments['resultsPriceString']);
                                ?></th>
                            <?php
                        }

                        if ($arguments['showResultsQuantityColumn']) {
                            ?>
                            <th class="woocommerce-products-wizard-results-table-header-cell is-quantity"><?php
                                echo wp_kses_post($arguments['resultsQuantityString']);
                                ?></th>
                            <?php
                        }

                        if ($arguments['showResultsSubtotalColumn'] && !$arguments['hidePrices']) {
                            ?>
                            <th class="woocommerce-products-wizard-results-table-header-cell is-subtotal"><?php
                                echo wp_kses_post($arguments['subtotalString']);
                                ?></th>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                    $showProductsHeader = false;
                }

                if ($arguments['showStepsInCart'] != 'never' && $previousStep != $cartItem['step_id']
                    && isset($arguments['steps'][$cartItem['step_id']]) && $arguments['steps'][$cartItem['step_id']]
                ) {
                    $previousStep = $cartItem['step_id'];
                    ?>
                    <tr class="woocommerce-products-wizard-results-table-body-row is-heading <?php
                        echo esc_attr("is-step-{$cartItem['step_id']}");
                        ?>">
                        <td class="woocommerce-products-wizard-results-table-body-cell"
                            colspan="<?php echo esc_attr($columnsNumber); ?>">
                            <?php
                            if ($arguments['steps'][$cartItem['step_id']]['thumbnail']) {
                                echo wp_get_attachment_image(
                                    $arguments['steps'][$cartItem['step_id']]['thumbnail'],
                                    'thumbnail',
                                    false,
                                    ['class' => 'woocommerce-products-wizard-results-step-thumbnail']
                                );
                            }
                            ?>
                            <span class="woocommerce-products-wizard-results-step-name"><?php
                                echo wp_kses_post($arguments['steps'][$cartItem['step_id']]['name'])
                                ?></span>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr class="woocommerce-products-wizard-results-table-body-row is-item <?php
                    echo esc_attr("is-step-{$cartItem['step_id']}");

                    if (isset($cartItem['product_id'])) {
                        echo esc_attr(" is-product-{$cartItem['product_id']}");
                    }
                    ?>">
                    <?php
                    if (isset($cartItem['product_id'], $cartItem['data']) && $cartItem['data']) {
                        $product = $cartItem['data'];

                        if (!$product instanceof \WC_Product) {
                            continue;
                        }

                        $price = Cart::getItemPrice($cartItem);

                        if ($arguments['showResultsRemoveColumn'] && $arguments['enableRemoveButton']) {
                            ?>
                            <td class="woocommerce-products-wizard-results-table-body-cell is-remove"
                                data-th="<?php echo esc_attr($arguments['resultsRemoveString']); ?>">
                                <?php
                                if (!Settings::getStep($arguments['id'], $cartItem['step_id'], 'hide_remove_button')) {
                                    ?>
                                    <button class="woocommerce-products-wizard-results-item-remove woocommerce-products-wizard-control <?php
                                        echo esc_attr($arguments['removeButtonClass']);
                                        ?> btn is-remove-from-cart"
                                        form="<?php echo esc_attr($arguments['formId']); ?>"
                                        name="remove-cart-product"
                                        value="<?php echo esc_attr($cartItemKey); ?>"
                                        title="<?php echo esc_attr($arguments['removeButtonText']); ?>"
                                        data-component="wcpw-remove-cart-product"
                                        data-remove-cart-product-options="<?php
                                        echo esc_attr(wp_json_encode([
                                            'lazy' => Settings::getStep($arguments['id'], $cartItem['step_id'], 'buttons_nonblocking_requests')
                                        ]));
                                        ?>">
                                        <!--spacer-->
                                        <span class="woocommerce-products-wizard-control-inner"><?php
                                            echo wp_kses_post($arguments['removeButtonText']);
                                            ?></span>
                                        <!--spacer-->
                                    </button>
                                    <?php
                                }
                                ?>
                            </td>
                            <?php
                        }

                        if ($arguments['showResultsThumbnailColumn']) {
                            ?>
                            <td class="woocommerce-products-wizard-results-table-body-cell is-thumbnail"
                                data-th="<?php echo esc_attr($arguments['resultsThumbnailString']); ?>">
                                <?php if (Settings::getStep($arguments['id'], $cartItem['step_id'], 'show_item_thumbnails')) { ?>
                                    <figure class="woocommerce-products-wizard-results-item-thumbnail"><?php
                                        // phpcs:disable
                                        $href = wp_get_attachment_image_src($product->get_image_id(), 'full');
                                        $thumbnail = $product->get_image('shop_thumbnail', ['class' => 'img-thumbnail']);
                                        $thumbnail = apply_filters('wcpw_result_item_thumbnail', $thumbnail, $cartItem, $cartItemKey);

                                        echo isset($href[0])
                                            ? "<a href=\"{$href[0]}\" data-rel=\"prettyPhoto\" rel=\"lightbox\">{$thumbnail}</a>"
                                            : $thumbnail;
                                        // phpcs:enable
                                        ?></figure>
                                <?php } ?>
                            </td>
                            <?php
                        }

                        if ($arguments['showResultsDataColumn']) {
                            ?>
                            <td class="woocommerce-products-wizard-results-table-body-cell is-data"
                                data-th="<?php echo esc_attr($arguments['resultsDataString']); ?>">
                                <div class="woocommerce-products-wizard-results-item-title-container">
                                    <div class="woocommerce-products-wizard-results-item-title"><?php
                                        if (method_exists($product, 'get_name')) {
                                            echo $product->get_name();
                                        }
                                        ?></div>
                                    <?php
                                    if ($arguments['enableEditButton']
                                        && !Settings::getStep($arguments['id'], $cartItem['step_id'], 'hide_edit_button')
                                    ) {
                                        $stepId = $cartItem['step_id'];

                                        if (isset($arguments['navItem']['merged_with_step'])
                                            && $arguments['navItem']['merged_with_step']
                                        ) {
                                            $stepId = $arguments['navItem']['merged_with_step'];
                                        }
                                        ?>
                                        <button class="woocommerce-products-wizard-results-item-edit woocommerce-products-wizard-control <?php
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
                                    <div class="woocommerce-products-wizard-results-item-data"><?php
                                        echo Cart::getProductMeta($cartItem);

                                        // Backorder notification
                                        if ($product->backorders_require_notification()
                                            && $product->is_on_backorder($cartItem['quantity'])
                                        ) {
                                            echo '<p class="backorder_notification">'
                                                . esc_html__('Available on backorder', 'woocommerce') . '</p>';
                                        }
                                        ?></div>
                                </div>
                            </td>
                            <?php
                        }

                        if ($arguments['showResultsPriceColumn'] && !$arguments['hidePrices']) {
                            ?>
                            <td class="woocommerce-products-wizard-results-table-body-cell is-price"
                                data-th="<?php echo esc_attr($arguments['resultsPriceString']); ?>">
                                <span class="woocommerce-products-wizard-results-item-price<?php
                                    echo $price == 0 ? ' is-zero-price ' : '';
                                    ?>"><?php
                                    if (Settings::getStep($arguments['id'], $cartItem['step_id'], 'show_item_price')) {
                                        // apply the filter for Subscriptions support
                                        echo apply_filters('woocommerce_cart_product_price', wc_price($price), $product);
                                    }
                                    ?></span>
                            </td>
                            <?php
                        }

                        if ($arguments['showResultsQuantityColumn']) {
                            ?>
                            <td class="woocommerce-products-wizard-results-table-body-cell is-quantity"
                                data-th="<?php echo esc_attr($arguments['resultsQuantityString']); ?>">
                                <span class="woocommerce-products-wizard-results-item-quantity"><?php
                                echo $cartItem['quantity'];
                                ?></span>
                            </td>
                            <?php
                        }

                        if ($arguments['showResultsSubtotalColumn'] && !$arguments['hidePrices']) {
                            ?>
                            <td class="woocommerce-products-wizard-results-table-body-cell is-subtotal"
                                data-th="<?php echo esc_attr($arguments['subtotalString']); ?>">
                                <span class="woocommerce-products-wizard-results-item-subtotal<?php
                                    echo $price == 0 ? ' is-zero-price ' : '';
                                    ?>"><?php
                                    if (Settings::getStep($arguments['id'], $cartItem['step_id'], 'show_item_price')) {
                                        echo wc_price($price * $cartItem['quantity']);
                                    }
                                    ?></span>
                            </td>
                            <?php
                        }
                    } elseif (isset($cartItem['value'], $cartItem['key']) && !empty($cartItem['value'])
                        && !empty($cartItem['key'])
                    ) {
                        $showProductsHeader = true;

                        if ($arguments['showResultsDataColumn']) {
                            ?>
                            <th class="woocommerce-products-wizard-results-table-body-cell is-name"
                                colspan="<?php echo esc_attr($colspanLeft); ?>">
                                <span class="woocommerce-products-wizard-results-item-name"><?php
                                    echo wp_kses_post($cartItem['key']);
                                    ?></span>
                            </th>
                            <?php
                        }

                        if ($arguments['showResultsSubtotalColumn']) {
                            ?>
                            <td class="woocommerce-products-wizard-results-table-body-cell is-value"
                                colspan="<?php echo esc_attr($colspanRight); ?>"
                                data-th="<?php echo esc_attr($cartItem['key']); ?>">
                                <span class="woocommerce-products-wizard-results-item-value"><?php
                                    echo wp_kses_post($cartItem['display_value']);
                                    ?></span>
                            </td>
                            <?php
                        }
                    }
                    ?>
                </tr>
                <?php
            }
            ?>
        </tbody>
        <?php if (!$arguments['hidePrices']) { ?>
            <tfoot class="woocommerce-products-wizard-results-table-footer">
                <tr class="woocommerce-products-wizard-results-table-footer-row is-total">
                    <?php
                    if ($arguments['showResultsDataColumn']) {
                        ?>
                        <th class="woocommerce-products-wizard-results-table-footer-cell is-caption"
                            colspan="<?php echo esc_attr($colspanLeft); ?>"><?php
                            echo wp_kses_post($arguments['totalString']);
                            ?></th>
                        <?php
                    }

                    if ($arguments['showResultsSubtotalColumn']) {
                        ?>
                        <td class="woocommerce-products-wizard-results-table-footer-cell is-value"
                            colspan="<?php echo esc_attr($colspanRight); ?>"
                            data-th="<?php echo esc_attr($arguments['totalString']); ?>"><?php
                            echo wp_kses_post($arguments['cartTotalPrice']);
                            ?></td>
                        <?php
                    }
                    ?>
                </tr>
            </tfoot>
        <?php } ?>
    </table>
    <?php
}

if ($arguments['resultsStepContactForm']) {
    echo '<div class="woocommerce-products-wizard-results-form">'
        . do_shortcode(
            '[contact-form-7 title="' . $arguments['resultsStepContactForm']
            . '" html_name="wcpw-result-' . $arguments['id'] . '"]'
        )
        . '</div>';
}

echo '</article>';
