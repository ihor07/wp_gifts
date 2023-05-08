<?php
defined('ABSPATH') || exit;

use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'label' => esc_html__('Category', 'woocommerce-products-wizard'),
    'filterKey' => null,
    'stepId' => null,
    'key' => 'category',
    'values' => []
]);
?>
<fieldset class="woocommerce-products-wizard-form-filter-field form-group is-select is-<?php
    echo esc_attr($arguments['key']);
    ?>">
    <legend class="woocommerce-products-wizard-form-filter-field-title"><?php
        echo wp_kses_post($arguments['label']);
        ?></legend>
    <label class="woocommerce-products-wizard-form-filter-field-label sr-only visually-hidden"
        for="woocommerce-products-wizard-form-filter-<?php
        echo esc_attr("{$arguments['stepId']}-{$arguments['filterKey']}");
        ?>"><?php
        echo wp_kses_post($arguments['label']);
        ?></label>
    <select class="form-control woocommerce-products-wizard-form-filter-field-value-input form-select"
        name="<?php
        echo esc_attr("wcpwFilter[{$arguments['stepId']}][{$arguments['filterKey']}][{$arguments['key']}][]");
        ?>"
        id="woocommerce-products-wizard-form-filter-<?php
        echo esc_attr("{$arguments['stepId']}-{$arguments['filterKey']}");
        ?>">
        <?php foreach ($arguments['values'] as $value) { ?>
            <option value="<?php echo esc_attr($value['id']); ?>"<?php selected($value['isActive']); ?>><?php
                echo wp_kses_post($value['name']);
                ?></option>
        <?php } ?>
    </select>
</fieldset>
