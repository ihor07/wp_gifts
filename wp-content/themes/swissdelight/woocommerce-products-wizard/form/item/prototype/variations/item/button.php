<?php
defined('ABSPATH') || exit;

use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'class' => 'woocommerce-products-wizard-form-item',
    'product' => null,
    'formId' => null,
    'stepId' => null,
    'attributeKey' => null,
    'attributeValues' => []
]);

$product = $arguments['product'];

if (!$product instanceof WC_Product) {
    return;
}

$fieldName = "productsToAdd[{$arguments['stepId']}-{$product->get_id()}][variation][attribute_"
    . sanitize_title($arguments['attributeKey']) . ']';
?>
<div class="<?php echo esc_attr($arguments['class']); ?>-variations-item form-group is-button">
    <dt class="<?php echo esc_attr($arguments['class']); ?>-variations-item-name-wrapper">
        <label class="<?php echo esc_attr($arguments['class']); ?>-variations-item-name form-label"
            for="<?php echo esc_attr($fieldName); ?>"><?php
            echo wc_attribute_label($arguments['attributeKey']);
            ?></label>
    </dt>
    <dd class="<?php echo esc_attr($arguments['class']); ?>-variations-item-value-wrapper">
        <fieldset class="<?php echo esc_attr($arguments['class']); ?>-variations-item-value is-button"
            data-component="wcpw-product-variations-item"
            data-name="attribute_<?php echo sanitize_title($arguments['attributeKey']); ?>">
            <?php foreach ($arguments['attributeValues'] as $attributeValue) { ?>
                <label class="<?php echo esc_attr($arguments['class']); ?>-variations-item-value-label">
                    <input type="radio"
                        class="<?php
                        echo esc_attr($arguments['class']);
                        ?>-variations-item-value-input sr-only visually-hidden is-hidden"
                        data-component="wcpw-product-variations-item-value wcpw-product-variations-item-input"
                        data-name="attribute_<?php echo sanitize_title($arguments['attributeKey']); ?>"
                        form="<?php echo esc_attr($arguments['formId']); ?>"
                        name="<?php echo esc_attr($fieldName); ?>"
                        value="<?php echo esc_attr($attributeValue['value']); ?>"<?php
                        checked($attributeValue['selected']);
                        ?>>
                    <span class="<?php
                        echo esc_attr($arguments['class']);
                        ?>-variations-item-value-caption btn btn-outline-secondary is-button"><?php
                        echo wp_kses_post($attributeValue['name']);
                        ?></span>
                </label>
            <?php } ?>
        </fieldset>
    </dd>
</div>
