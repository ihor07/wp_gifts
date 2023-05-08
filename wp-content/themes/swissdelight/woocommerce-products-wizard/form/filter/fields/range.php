<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Settings;
use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'label' => esc_html__('Price', 'woocommerce-products-wizard'),
    'filterFromString' => Settings::getPost($id, 'filter_from_string'),
    'filterToString' => Settings::getPost($id, 'filter_to_string'),
    'filterKey' => null,
    'stepId' => null,
    'key' => 'price',
    'values' => [
        'min' => 0,
        'max' => 100,
        'from' => 0,
        'to' => 100
    ],
    'sliderAdditionClass' => 'horizontal',
    'sliderOptions' => [
        'orientation' => 'horizontal',
        'start' => [0, 100],
        'range' => [
            'min' => 0,
            'max' => 100
        ],
        'step' => 1,
        'format' => ['decimals' => 0],
        'connect' => true,
        'binding' => []
    ]
]);

$decimalsMin = strlen(substr(strrchr($arguments['values']['min'], '.'), 1));
$decimalsMax = strlen(substr(strrchr($arguments['values']['max'], '.'), 1));
$decimals = max($decimalsMin, $decimalsMax);
$step = 1 / pow(10, $decimals);

// set required args
$arguments['sliderOptions'] = array_replace(
    $arguments['sliderOptions'],
    [
        'start' => [
            $arguments['values']['from'],
            $arguments['values']['to']
        ],
        'range' => [
            'min' => $arguments['values']['min'],
            'max' => $arguments['values']['max']
        ],
        'step' => $step,
        'format' => ['decimals' => $decimals],
        'binding' => [
            "#woocommerce-products-wizard-form-filter-{$arguments['stepId']}-{$arguments['filterKey']}-from",
            "#woocommerce-products-wizard-form-filter-{$arguments['stepId']}-{$arguments['filterKey']}-to"
        ]
    ]
);
?>
<fieldset class="woocommerce-products-wizard-form-filter-field form-group is-range is-<?php
    echo esc_attr($arguments['key']);
    ?>">
    <legend class="woocommerce-products-wizard-form-filter-field-title"><?php
        echo wp_kses_post($arguments['label']);
        ?></legend>
    <div class="woocommerce-products-wizard-form-filter-field-slider <?php
        echo esc_attr($arguments['sliderAdditionClass']);
        ?>"
        data-component="wcpw-filter-<?php echo esc_attr($arguments['key']); ?> wcpw-no-ui-slider"
        data-options="<?php echo esc_attr(wp_json_encode($arguments['sliderOptions'])); ?>"></div>
    <div class="row">
        <div class="col-xs-6 col-6">
            <label class="woocommerce-products-wizard-form-filter-field-label"
                for="woocommerce-products-wizard-form-filter-<?php
                echo esc_attr("{$arguments['stepId']}-{$arguments['filterKey']}");
                ?>-from"><?php
                echo wp_kses_post($arguments['filterFromString']);
                ?></label>
            <input type="number"
                id="woocommerce-products-wizard-form-filter-<?php
                echo esc_attr("{$arguments['stepId']}-{$arguments['filterKey']}");
                ?>-from"
                class="woocommerce-products-wizard-form-filter-field-value-input form-control"
                name="<?php
                echo esc_attr("wcpwFilter[{$arguments['stepId']}][{$arguments['filterKey']}][{$arguments['key']}][from]");
                ?>"
                step="<?php echo esc_attr($step); ?>"
                value="<?php echo esc_attr($arguments['values']['min']); ?>">
        </div>
        <div class="col-xs-6 col-6">
            <label class="woocommerce-products-wizard-form-filter-field-label"
                for="woocommerce-products-wizard-form-filter-<?php
                echo esc_attr("{$arguments['stepId']}-{$arguments['filterKey']}");
                ?>-to"><?php echo wp_kses_post($arguments['filterToString']); ?></label>
            <input type="number"
                id="woocommerce-products-wizard-form-filter-<?php
                echo esc_attr("{$arguments['stepId']}-{$arguments['filterKey']}");
                ?>-to"
                class="woocommerce-products-wizard-form-filter-field-value-input form-control"
                name="<?php
                echo esc_attr("wcpwFilter[{$arguments['stepId']}][{$arguments['filterKey']}][{$arguments['key']}][to]");
                ?>"
                step="<?php echo esc_attr($step); ?>"
                value="<?php echo esc_attr($arguments['values']['max']); ?>">
        </div>
    </div>
</fieldset>
