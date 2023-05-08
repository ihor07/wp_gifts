<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$stepId = isset($stepId) ? $stepId : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Cart;
use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$arguments = Template::getHTMLArgs([
    'stepId' => null,
    'queryArgs' => [],
    'thumbnailString' => Settings::getPost($id, 'table_layout_thumbnail_string'),
    'titleString' => Settings::getPost($id, 'table_layout_title_string'),
    'priceString' => Settings::getPost($id, 'table_layout_price_string'),
    'toCartString' => Settings::getPost($id, 'table_layout_to_cart_string'),
    'showThumbnailColumn' => Settings::getStep($id, $stepId, 'show_table_thumbnail_column', true),
    'showTitleColumn' => Settings::getStep($id, $stepId, 'show_table_title_column', true),
    'showPriceColumn' => Settings::getStep($id, $stepId, 'show_table_price_column', true),
    'showCartColumn' => Settings::getStep($id, $stepId, 'show_table_cart_column', true),
    'hidePrices' => Settings::getPost($id, 'hide_prices'),
    'hideChooseElement' => false,
    'enableTitleLink' => false,
    'severalProducts' => false,
    'showFooterPrice' => false,
    'showFooterChoose' => false
]);

$productsQuery = new WP_Query($arguments['queryArgs']);
$inputType = $arguments['severalProducts'] ? 'checkbox' : 'radio';

if (!$productsQuery->have_posts()) {
    Template::html('messages/nothing-found', $arguments);
}
?>
<table class="woocommerce-products-wizard-form-layout is-table products woocommerce-products-wizard-form-table table
    table-bordered table-hover wcpw-table-responsive">
    <thead class="woocommerce-products-wizard-form-table-header">
        <tr>
            <?php
            if ($arguments['showThumbnailColumn']) {
                ?>
                <th class="woocommerce-products-wizard-form-table-header-thumbnail"><?php
                    echo wp_kses_post($arguments['thumbnailString']);
                    ?></th>
                <?php
            }

            if ($arguments['showTitleColumn']) {
                ?>
                <th class="woocommerce-products-wizard-form-table-header-title"><?php
                    echo wp_kses_post($arguments['titleString']);
                    ?></th>
                <?php
            }

            if ($arguments['showPriceColumn'] && !$arguments['hidePrices']) {
                ?>
                <th class="woocommerce-products-wizard-form-table-header-price"><?php
                    echo wp_kses_post($arguments['priceString']);
                    ?></th>
                <?php
            }

            if ($arguments['showCartColumn']) {
                ?>
                <th class="woocommerce-products-wizard-form-table-header-cart"><?php
                    echo wp_kses_post($arguments['toCartString']);
                    ?></th>
                <?php
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($productsQuery->have_posts()) {
            $productsQuery->the_post();

            global $product;

            if (!$product instanceof WC_Product) {
                continue;
            }

            $arguments['product'] = $product;
            $arguments['cartItem'] = Cart::getProductById($arguments['id'], $product->get_id(), $arguments['stepId']);

            // EPO product default data pass
            $_POST = !empty($arguments['cartItem']) ? $arguments['cartItem']['request'] : [];
            ?>
            <tr class="woocommerce-products-wizard-form-table-item<?php
                echo $arguments['cartItem'] ? ' is-in-cart' : '';
                ?>"
                data-component="wcpw-product"
                data-type="<?php echo esc_attr($product->get_type()); ?>"
                data-id="<?php echo esc_attr($product->get_id()); ?>"
                data-step-id="<?php echo esc_attr($arguments['stepId']); ?>"<?php
                echo $arguments['cartItem']
                    ? (' data-cart-key="' . esc_attr($arguments['cartItem']['key']) . '"')
                    : '';
                ?>>
                <?php
                if ($arguments['showThumbnailColumn']) {
                    ?>
                    <td class="woocommerce-products-wizard-form-table-item-thumbnail-wrapper"
                        data-th="<?php echo esc_attr($arguments['thumbnailString']); ?>"><?php
                        Template::html('form/item/prototype/thumbnail', $arguments);
                        ?></td>
                    <?php
                }

                if ($arguments['showTitleColumn']) {
                    ?>
                    <td class="woocommerce-products-wizard-form-table-item-title-wrapper"
                        data-th="<?php echo esc_attr($arguments['titleString']); ?>">
                        <div class="woocommerce-products-wizard-form-table-item-title-container">
                            <div class="woocommerce-products-wizard-form-table-item-check<?php
                            echo !$arguments['hideChooseElement']
                                ? esc_attr(' form-check custom-control custom-' . $inputType)
                                : '';
                            ?>">
                                <?php Template::html('form/item/prototype/choose', $arguments); ?>
                                <label class="woocommerce-products-wizard-form-table-item-title"
                                    for="woocommerce-products-wizard-form-item-choose-<?php
                                    echo esc_attr($arguments['stepId'] . '-' . $product->get_id());
                                    ?>"><?php
                                    if ($arguments['enableTitleLink']) {
                                        echo '<a href="' . $product->get_permalink() . '" target="_blank" '
                                            . 'class="woocommerce-products-wizard-outer-link '
                                            . 'woocommerce-products-wizard-form-table-item-title-link"> ';
                                    }

                                    echo $product->get_title();

                                    if ($arguments['enableTitleLink']) {
                                        echo ' </a>';
                                    }
                                    ?></label>
                            </div>
                            <?php
                            Template::html('form/item/prototype/sku', $arguments);
                            Template::html('form/item/prototype/description', $arguments);
                            ?>
                        </div>
                    </td>
                    <?php
                }

                if ($arguments['showPriceColumn'] && !$arguments['hidePrices']
                    && Settings::getStep($arguments['id'], $arguments['stepId'], 'show_item_price')
                ) {
                    ?>
                    <td class="woocommerce-products-wizard-form-table-item-price-wrapper"
                        data-th="<?php echo esc_attr($arguments['priceString']); ?>"><?php
                        Template::html('form/item/prototype/price', $arguments);
                        ?></td>
                    <?php
                }

                if ($arguments['showCartColumn']) {
                    ?>
                    <td class="woocommerce-products-wizard-form-table-item-cart-wrapper"
                        data-th="<?php echo esc_attr($arguments['toCartString']); ?>"><?php
                        Template::html('form/item/prototype/footer', $arguments);
                        ?></td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<?php
Template::html('form/pagination', array_merge(['productsQuery' => $productsQuery], $arguments));

wp_reset_query(); // better than $productsQuery->reset_postdata();
