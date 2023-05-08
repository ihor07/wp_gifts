<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$stepId = isset($stepId) ? $stepId : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$arguments = Template::getHTMLArgs([
    'product' => null,
    'class' => 'woocommerce-products-wizard-form-item',
    'showTags' => Settings::getStep($id, $stepId, 'show_item_tags')
]);

$product = $arguments['product'];

if (!$product instanceof WC_Product || !$arguments['showTags']) {
    return;
}

$tags = get_the_terms($product->get_id(), 'product_tag');

if (empty($tags) || is_wp_error($tags)) {
    return;
}
?>
<dl class="<?php echo esc_attr($arguments['class']); ?>-tags">
    <dt class="<?php echo esc_attr($arguments['class']); ?>-tags-name sr-only visually-hidden"><?php
        esc_html_e('Tags', 'woocommerce');
        ?></dt>
    <?php foreach ($tags as $tag) { ?>
        <dd class="<?php echo esc_attr("is-id-{$tag->term_id} " . $arguments['class']); ?>-tags-value badge"><?php
            echo wp_kses_post($tag->name);
            ?></dd>
    <?php } ?>
</dl>
