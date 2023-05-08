<?php
defined('ABSPATH') || exit;

use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'label' => esc_html__('Category', 'woocommerce-products-wizard'),
    'filterKey' => null,
    'stepId' => null,
    'key' => 'category',
    'filterThumbnailSize' => 'thumbnail',
    'values' => []
]);
?>
<fieldset class="woocommerce-products-wizard-form-filter-field form-group is-image-radio is-<?php
    echo esc_attr($arguments['key']);
    ?>">
    <legend class="woocommerce-products-wizard-form-filter-field-title"><?php
        echo wp_kses_post($arguments['label']);
        ?></legend>
    <?php foreach ($arguments['values'] as $value) { ?>
        <span class="woocommerce-products-wizard-form-filter-field-value is-value-<?php
            echo esc_attr($value['id']);
            ?>">
            <label class="woocommerce-products-wizard-form-filter-field-value-label">
                <input type="radio"
                    name="<?php
                    echo esc_attr(
                        "wcpwFilter[{$arguments['stepId']}][{$arguments['filterKey']}][{$arguments['key']}][]"
                    );
                    ?>"
                    value="<?php echo esc_attr($value['id']); ?>"
                    class="woocommerce-products-wizard-form-filter-field-value-input sr-only visually-hidden"<?php
                    checked($value['isActive']);
                    ?>>
                <?php
                echo $value['thumbnailId']
                    ? wp_get_attachment_image(
                        $value['thumbnailId'],
                        $arguments['filterThumbnailSize'],
                        false,
                        ['class' => 'woocommerce-products-wizard-form-filter-field-value-thumbnail']
                    )
                    : '';
                ?>
                <span class="woocommerce-products-wizard-form-filter-field-value-name"><?php
                    echo wp_kses_post($value['name']);
                    ?></span>
            </label>
        </span>
    <?php } ?>
</fieldset>
