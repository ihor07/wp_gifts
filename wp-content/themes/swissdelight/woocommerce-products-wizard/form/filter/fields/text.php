<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$stepId = isset($stepId) ? $stepId : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Settings;
use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'label' => esc_html__('Title', 'woocommerce-products-wizard'),
    'filterSearchResultsDropdown' => Settings::getStep($id, $stepId, 'filter_search_results_dropdown'),
    'filterKey' => null,
    'stepId' => $stepId,
    'key' => 'title',
    'value' => []
]);
?>
<fieldset class="woocommerce-products-wizard-form-filter-field form-group is-text is-<?php
    echo esc_attr($arguments['key']);
    ?>">
    <legend class="woocommerce-products-wizard-form-filter-field-title"><?php
        echo wp_kses_post($arguments['label']);
        ?></legend>
    <div class="woocommerce-products-wizard-search-form">
        <label class="woocommerce-products-wizard-form-filter-field-label sr-only visually-hidden"
            for="woocommerce-products-wizard-form-filter-<?php echo esc_attr("{$arguments['stepId']}-{$arguments['filterKey']}"); ?>"><?php
            echo wp_kses_post($arguments['label']);
            ?></label>
        <input type="text"
            id="woocommerce-products-wizard-form-filter-<?php echo esc_attr("{$arguments['stepId']}-{$arguments['filterKey']}"); ?>"
            <?php if ($arguments['filterSearchResultsDropdown']) { ?>
                data-component="wcpw-search-form-input"
                data-step-id="<?php echo esc_attr($arguments['stepId']); ?>"
                data-target="#woocommerce-products-wizard-form-filter-<?php echo esc_attr("{$arguments['stepId']}-{$arguments['filterKey']}"); ?>-list"
            <?php } ?>
            class="form-control woocommerce-products-wizard-form-filter-field-value-input woocommerce-products-wizard-search-form-input"
            name="<?php echo esc_attr("wcpwFilter[{$arguments['stepId']}][{$arguments['filterKey']}][{$arguments['key']}]"); ?>"
            value="<?php
            echo esc_attr(is_array($arguments['value']) ? reset($arguments['value']) : $arguments['value']);
            ?>">
        <?php if ($arguments['filterSearchResultsDropdown']) { ?>
            <ul class="woocommerce-products-wizard-form-filter-field-value-datalist woocommerce-products-wizard-search-form-results"
                id="woocommerce-products-wizard-form-filter-<?php echo esc_attr("{$arguments['stepId']}-{$arguments['filterKey']}"); ?>-list"
                data-component="wcpw-search-form-results"
                data-item-template="<li><a href='#' data-value='${name}' role='button'>${type}: ${name}</a></li>"
                data-target="#woocommerce-products-wizard-form-filter-<?php echo esc_attr("{$arguments['stepId']}-{$arguments['filterKey']}"); ?>"></ul>
        <?php } ?>
    </div>
</fieldset>
