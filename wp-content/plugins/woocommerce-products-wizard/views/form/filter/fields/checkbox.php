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
<fieldset class="woocommerce-products-wizard-form-filter-field form-group is-checkbox is-<?php
    echo esc_attr($arguments['key']);
    ?>">
    <legend class="woocommerce-products-wizard-form-filter-field-title"><?php
        echo wp_kses_post($arguments['label']);
        ?></legend>
    <input type="hidden" value=""
        name="<?php echo esc_attr("wcpwFilter[{$arguments['stepId']}][{$arguments['filterKey']}][{$arguments['key']}][]"); ?>">
    <?php foreach ($arguments['values'] as $value) { ?>
        <label class="woocommerce-products-wizard-form-filter-field-value-label form-check custom-control custom-checkbox is-value-<?php
            echo esc_attr($value['id']);
            ?>">
            <input type="checkbox"
                name="<?php
                echo esc_attr("wcpwFilter[{$arguments['stepId']}][{$arguments['filterKey']}][{$arguments['key']}][]");
                ?>"
                value="<?php echo esc_attr($value['id']); ?>"
                class="woocommerce-products-wizard-form-filter-field-value-input form-check-input custom-control-input"<?php
                checked($value['isActive']);
                ?>>
            <span class="woocommerce-products-wizard-form-filter-field-value-name form-check-label custom-control-label"><?php
                echo wp_kses_post($value['name']);
                ?></span>
        </label>
    <?php } ?>
</fieldset>
