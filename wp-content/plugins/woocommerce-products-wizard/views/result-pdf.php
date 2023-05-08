<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Cart;
use WCProductsWizard\Form;
use WCProductsWizard\PDF;
use WCProductsWizard\Utils;
use WCProductsWizard\Settings;
use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs(
    [
        'cart' => Cart::get($id),
        'cartTotalPrice' => Cart::getTotalPrice($id),
        'steps' => Form::getSteps($id),
        'showStepsInCart' => Settings::getPost($id, 'show_steps_in_cart'),
        'resultPDFHeaderContent' => Settings::getPost($id, 'result_pdf_header_content'),
        'resultPDFHeaderHeight' => Settings::getPost($id, 'result_pdf_header_height'),
        'resultPDFFooterContent' => Settings::getPost($id, 'result_pdf_footer_content'),
        'resultPDFFooterHeight' => Settings::getPost($id, 'result_pdf_footer_height'),
        'resultPDFTopDescription' => Settings::getPost($id, 'result_pdf_top_description'),
        'resultPDFBottomDescription' => Settings::getPost($id, 'result_pdf_bottom_description'),
        'resultPDFAdditionalCSS' => Settings::getPost($id, 'result_pdf_additional_css'),
        'hidePrices' => Settings::getPost($id, 'hide_prices'),
        'resultsPriceString' => Settings::getPost($id, 'results_price_string'),
        'resultsDataString' => Settings::getPost($id, 'results_data_string'),
        'resultsQuantityString' => Settings::getPost($id, 'results_quantity_string'),
        'subtotalString' => Settings::getPost($id, 'subtotal_string'),
        'discountString' => Settings::getPost($id, 'discount_string'),
        'totalString' => Settings::getPost($id, 'total_string'),
        'groupProductsIntoKits' => Settings::getPost($id, 'group_products_into_kits'),
        'kitsType' => Settings::getPost($id, 'kits_type'),
        'kitBasePrice' => Settings::getPost($id, 'kit_base_price'),
        'kitBasePriceString' => Settings::getPost($id, 'kit_base_price_string'),
        'pageSubClass' => 'is-default',
        'formData' => []
    ],
    ['recursive' => true]
);

if (empty($arguments['cart'])) {
    return;
}

$previousStep = null;
$showProductsHeader = true;
?>
<style>
    @page {
        size: A4;
        margin: <?php
        echo ($arguments['resultPDFHeaderHeight'] + 1) . 'cm 1.5cm ' . ($arguments['resultPDFFooterHeight'] + 1) . 'cm';
        ?>;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
    }

    .header,
    .footer {
        position: fixed;
        left: -1.5cm;
        right: -1.5cm;
        width: 21cm;
    }

    .header {
        top: <?php echo '-' . ($arguments['resultPDFHeaderHeight'] + 1) . 'cm'; ?>;
        height: <?php echo "{$arguments['resultPDFHeaderHeight']}cm"; ?>;
    }

    .footer {
        bottom: <?php echo '-' . ($arguments['resultPDFFooterHeight'] + 1) . 'cm'; ?>;
        height: <?php echo "{$arguments['resultPDFFooterHeight']}cm"; ?>;
    }

    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    th.table-header-cell,
    th.table-footer-cell {
        background-color: #f5f5f5;
    }

    .table-header-cell,
    .table-body-cell,
    .table-footer-cell {
        padding: 0.33em 0.5em;
        border: 1px solid #ccc;
        border-left: 0;
    }

    .table-header-cell:first-child,
    .table-body-cell:first-child,
    .table-footer-cell:first-child {
        border-left: 1px solid #ccc;
    }

    .table-footer-cell.is-value,
    .table-body-cell.is-price,
    .table-body-cell.is-subtotal {
        white-space: nowrap;
    }

    .table-body-cell.is-thumbnail img {
        max-width: 80px;
        height: auto;
    }

    .item-data {
        margin-top: 0.5cm;
    }

    .item-data:empty {
        display: none;
    }

    .item-value img {
        display: block;
        max-width: 8cm;
        width: auto;
        height: auto;
    }

    .step-thumbnail {
        max-width: 0.75cm;
        height: auto;
        margin-right: 0.25cm;
        vertical-align: middle;
    }

    .step-name {
        vertical-align: middle;
    }

    .new-page {
        page-break-after: always;
    }

    .page-number::after {
        content: counter(page);
    }

    td,
    th {
        vertical-align: top;
    }

    <?php echo $arguments['resultPDFAdditionalCSS']; ?>
</style>
<div class="header <?php echo esc_attr($arguments['pageSubClass']); ?>"><div class="header-inner"><?php
    echo PDF::prepareContent($arguments['resultPDFHeaderContent'], $arguments['formData']);
    ?></div></div>
<div class="footer <?php echo esc_attr($arguments['pageSubClass']); ?>"><div class="footer-inner"><?php
    echo PDF::prepareContent($arguments['resultPDFFooterContent'], $arguments['formData']);
    ?></div></div>
<div class="description is-top <?php echo esc_attr($arguments['pageSubClass']); ?>"><?php
    echo PDF::prepareContent($arguments['resultPDFTopDescription'], $arguments['formData']);
    ?></div>
<table class="table <?php echo esc_attr($arguments['pageSubClass']); ?>">
    <?php
    if ($arguments['groupProductsIntoKits'] && $arguments['kitsType'] == 'combined'
        && $arguments['kitBasePrice'] && !$arguments['hidePrices']
    ) {
        ?>
        <thead class="table-header">
            <tr class="table-header-row is-kit-base-price">
                <th class="table-header-cell is-caption" align="left" colspan="4"><?php
                    echo wp_kses_post($arguments['kitBasePriceString']);
                    ?></th>
                <td class="table-header-cell is-value" align="center"><?php
                    echo wc_price((float) $arguments['kitBasePrice']);
                    ?></td>
            </tr>
        </thead>
        <?php
    }
    ?>
    <tbody class="table-body">
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
                <tr class="table-body-row is-products">
                    <th class="table-header-cell is-thumbnail"></th>
                    <th class="table-header-cell is-data" align="left"><?php
                        echo wp_kses_post($arguments['resultsDataString']);
                        ?></th>
                    <?php if (!$arguments['hidePrices']) { ?>
                        <th class="table-header-cell is-price"><?php
                            echo wp_kses_post($arguments['resultsPriceString']);
                            ?></th>
                    <?php } ?>
                    <th class="table-header-cell is-quantity"><?php
                        echo wp_kses_post($arguments['resultsQuantityString']);
                        ?></th>
                    <?php if (!$arguments['hidePrices']) { ?>
                        <th class="table-header-cell is-subtotal"><?php
                            echo wp_kses_post($arguments['subtotalString']);
                            ?></th>
                    <?php } ?>
                </tr>
                <?php
                $showProductsHeader = false;
            }

            if ($arguments['showStepsInCart'] != 'never' && $previousStep != $cartItem['step_id']
                && isset($arguments['steps'][$cartItem['step_id']]) && $arguments['steps'][$cartItem['step_id']]
            ) {
                $previousStep = $cartItem['step_id'];
                ?>
                <tr class="table-body-row is-heading <?php
                    echo esc_attr("is-step-{$cartItem['step_id']}");
                    ?>">
                    <th class="table-body-cell" colspan="<?php echo $arguments['hidePrices'] ? 3 : 5; ?>">
                        <?php
                        if ($arguments['steps'][$cartItem['step_id']]['thumbnail']) {
                            echo Utils::replaceImagesToBase64InHtml(wp_get_attachment_image(
                                $arguments['steps'][$cartItem['step_id']]['thumbnail'],
                                'thumbnail',
                                false,
                                ['class' => 'step-thumbnail']
                            ));
                        }
                        ?>
                        <span class="step-name"><?php
                            echo wp_kses_post($arguments['steps'][$cartItem['step_id']]['name'])
                            ?></span>
                    </th>
                </tr>
                <?php
            }
            ?>
            <tr class="table-body-row is-item <?php
                echo esc_attr("is-step-{$cartItem['step_id']}");

                if (isset($cartItem['product_id'])) {
                    echo esc_attr(" is-product is-product-{$cartItem['product_id']}");
                } elseif (isset($cartItem['key'])) {
                    echo esc_attr(' is-field is-field-' . str_replace(' ', '-', $cartItem['key']));
                }
                ?>">
                <?php
                if (isset($cartItem['product_id'], $cartItem['data']) && $cartItem['data']) {
                    $product = $cartItem['data'];

                    if (!$product instanceof \WC_Product) {
                        continue;
                    }

                    $price = Cart::getItemPrice($cartItem);
                    ?>
                    <td class="table-body-cell is-thumbnail" align="center" width="60">
                        <?php
                        if (Settings::getStep($arguments['id'], $cartItem['step_id'], 'show_item_thumbnails')
                            && $product->get_image_id()
                        ) {
                            echo Utils::replaceImagesToBase64InHtml(wp_get_attachment_image(
                                $product->get_image_id(),
                                'shop_thumbnail',
                                false,
                                [
                                    'class' => 'item-thumbnail',
                                    'width' => '80'
                                ]
                            ));
                        }
                        ?>
                    </td>
                    <td class="table-body-cell is-data">
                        <div class="item-title"><?php
                            if (method_exists($product, 'get_name')) {
                                echo $product->get_name();
                            }
                            ?></div>
                        <div class="item-data"><?php
                            echo nl2br(Cart::getProductMeta($cartItem, true));

                            // Backorder notification
                            if ($product->backorders_require_notification()
                                && $product->is_on_backorder($cartItem['quantity'])
                            ) {
                                echo '<p class="backorder_notification">'
                                    . esc_html__('Available on backorder', 'woocommerce')
                                    . '</p>';
                            }
                            ?></div>
                    </td>
                    <?php if (!$arguments['hidePrices']) { ?>
                        <td class="table-body-cell is-price" align="right" width="60">
                            <span class="item-price<?php
                                echo $price == 0 ? ' is-zero-price ' : '';
                                ?>"><?php
                                if (Settings::getStep($arguments['id'], $cartItem['step_id'], 'show_item_price')) {
                                    // apply the filter for Subscriptions support
                                    echo apply_filters('woocommerce_cart_product_price', wc_price($price), $product);
                                }
                                ?></span>
                        </td>
                    <?php } ?>
                    <td class="table-body-cell is-quantity" align="right" width="60">
                        <span class="item-quantity"><?php echo $cartItem['quantity']; ?></span>
                    </td>
                    <?php
                    if (!$arguments['hidePrices']) {
                        ?>
                        <td class="table-body-cell is-subtotal" align="right" width="60">
                            <span class="item-subtotal<?php
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
                    ?>
                    <th class="table-body-cell is-name"
                        colspan="<?php echo $arguments['hidePrices'] ? 1 : 2; ?>" align="left">
                        <span class="item-name"><?php echo wp_kses_post($cartItem['key']); ?></span>
                    </th>
                    <td class="table-body-cell is-value" colspan="<?php echo $arguments['hidePrices'] ? 2 : 3; ?>">
                        <span class="item-value"><?php
                            echo Utils::replaceImagesToBase64InHtml($cartItem['display_value']);
                            ?></span>
                    </td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }
        ?>
    </tbody>
    <?php if (!$arguments['hidePrices']) { ?>
        <tfoot class="table-footer">
            <tr class="table-footer-row is-total">
                <th class="table-footer-cell is-caption" align="left" colspan="4"><?php
                    echo wp_kses_post($arguments['totalString']);
                    ?></th>
                <td class="table-footer-cell is-value" align="right"
                    data-th="<?php echo esc_attr($arguments['totalString']); ?>"><?php
                    echo wp_kses_post($arguments['cartTotalPrice']);
                    ?></td>
            </tr>
        </tfoot>
    <?php } ?>
</table>
<div class="description is-bottom <?php echo esc_attr($arguments['pageSubClass']); ?>"><?php
    echo PDF::prepareContent($arguments['resultPDFBottomDescription'], $arguments['formData']);
    ?></div>
