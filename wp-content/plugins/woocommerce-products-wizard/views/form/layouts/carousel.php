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
    'id' => $id,
    'stepId' => $stepId,
    'itemTemplate' => 'form/item/type-1',
    'queryArgs' => [],
    'grid' => Settings::getStep($id, $stepId, 'grid_column'),
    'gridWithSidebar' => Settings::getStep($id, $stepId, 'grid_with_sidebar_column'),
    'showSidebar' => Settings::isSidebarShowed($id)
]);

$colClasses = ['col'];
$gridColumn = $arguments['grid'];
$productsQuery = new WP_Query($arguments['queryArgs']);

if (!$productsQuery->have_posts()) {
    Template::html('messages/nothing-found', $arguments);
}

if ($arguments['showSidebar']) {
    $gridColumn = $arguments['gridWithSidebar'];
}

if (!isset($gridColumn['xxs'])) {
    $gridColumn['xxs'] = 12;
}

$colClasses[] = "col-{$gridColumn['xxs']}";

unset($gridColumn['xxs']);

foreach ($gridColumn as $col => $value) {
    $colClasses[] = "col-{$col}-{$value}";
}

$colClass = implode(' ', $colClasses);

echo '<div class="woocommerce-products-wizard-form-layout is-carousel row products">';

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

    echo '<div class="' . $colClass . '">';

    Template::html($arguments['itemTemplate'], $arguments);

    echo '</div>';
}

echo '</div>';

Template::html('form/pagination', array_merge(['productsQuery' => $productsQuery], $arguments));

wp_reset_query(); // better than $productsQuery->reset_postdata();
