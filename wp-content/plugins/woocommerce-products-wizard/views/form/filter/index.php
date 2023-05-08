<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$stepId = isset($stepId) ? $stepId : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Form;
use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$arguments = Template::getHTMLArgs([
    'id' => $id,
    'stepId' => $stepId,
    'filter' => Form::getFilterValue($id, $stepId),
    'filterLabel' => Settings::getPost($id, 'filter_label'),
    'filterResetButtonText' => Settings::getPost($id, 'filter_reset_button_text'),
    'filterResetButtonClass' => Settings::getPost($id, 'filter_reset_button_class'),
    'filterSubmitButtonText' => Settings::getPost($id, 'filter_submit_button_text'),
    'filterSubmitButtonClass' => Settings::getPost($id, 'filter_submit_button_class'),
    'filterThumbnailSize' => Settings::getStep($id, $stepId, 'filter_thumbnail_size'),
    'filterIsExpanded' => Settings::getStep($id, $stepId, 'filter_is_expanded'),
    'filterAutoSubmit' => Settings::getStep($id, $stepId, 'filter_auto_submit', false)
]);

$filters = Form::getFilterFields($arguments['id'], $arguments['stepId'], $arguments['filter']);

if (empty($filters)) {
    return;
}

if (is_string($arguments['filterThumbnailSize']) && strpos($arguments['filterThumbnailSize'], ',') !== false) {
    $arguments['filterThumbnailSize'] = explode(',', $arguments['filterThumbnailSize']);
}

$filterId = "{$arguments['id']}-{$arguments['stepId']}";
$isExpanded = isset($_COOKIE["#woocommerce-products-wizard-form-filter-content-{$arguments['id']}-{$arguments['stepId']}-expanded"])
    ? $_COOKIE["#woocommerce-products-wizard-form-filter-content-{$arguments['id']}-{$arguments['stepId']}-expanded"]
    : ($arguments['filterIsExpanded'] || !empty($arguments['filter'][$arguments['stepId']]));
?>
<form class="woocommerce-products-wizard-form-filter panel panel-default card" action="#" method="get"
    data-component="wcpw-filter<?php echo $arguments['filterAutoSubmit'] ? ' wcpw-submit-on-change' : ''; ?>"
    data-step-id="<?php echo esc_attr($arguments['stepId']); ?>">
    <div class="woocommerce-products-wizard-form-filter-header panel-heading">
        <h3 class="woocommerce-products-wizard-form-filter-title panel-title card-header">
            <a href="#woocommerce-products-wizard-form-filter-content-<?php echo esc_attr($filterId); ?>"
                class="woocommerce-products-wizard-form-filter-toggle" role="button" data-component="wcpw-toggle"
                data-target="#woocommerce-products-wizard-form-filter-content-<?php echo esc_attr($filterId); ?>"
                aria-controls="woocommerce-products-wizard-form-filter-content-<?php echo esc_attr($filterId); ?>"
                aria-expanded="<?php echo var_export(filter_var($isExpanded, FILTER_VALIDATE_BOOLEAN), true); ?>">
                <!--spacer-->
                <?php echo wp_kses_post($arguments['filterLabel']); ?>
                <!--spacer-->
            </a>
        </h3>
    </div>
    <div class="woocommerce-products-wizard-form-filter-content"
        id="woocommerce-products-wizard-form-filter-content-<?php echo esc_attr($filterId); ?>"
        data-component="woocommerce-products-wizard-form-filter-content" role="group"
        aria-expanded="<?php echo var_export(filter_var($isExpanded, FILTER_VALIDATE_BOOLEAN), true); ?>">
        <div class="woocommerce-products-wizard-form-filter-body panel-body card-body">
            <?php
            foreach ($filters as $filterKey => $filter) {
                Template::html(
                    "form/filter/fields/{$filter['view']}",
                    array_replace($arguments, $filter)
                );
            }
            ?>
        </div>
        <div class="woocommerce-products-wizard-form-filter-footer panel-footer card-footer">
            <button class="woocommerce-products-wizard-form-filter-reset woocommerce-products-wizard-control is-filter-reset btn <?php
                echo esc_attr($arguments['filterResetButtonClass']);
                ?>"
                type="reset"
                data-component="wcpw-filter-reset"
                data-step-id="<?php echo esc_attr($arguments['stepId']); ?>">
                <span class="woocommerce-products-wizard-control-inner">
                    <!--spacer-->
                    <?php echo wp_kses_post($arguments['filterResetButtonText']); ?>
                    <!--spacer-->
                </span>
            </button>
            <button class="woocommerce-products-wizard-form-filter-submit woocommerce-products-wizard-control is-filter-submit btn <?php
                echo esc_attr($arguments['filterSubmitButtonClass']);
                ?>"
                type="submit"
                data-component="wcpw-filter-submit">
                <span class="woocommerce-products-wizard-control-inner">
                    <!--spacer-->
                    <?php echo wp_kses_post($arguments['filterSubmitButtonText']); ?>
                    <!--spacer-->
                </span>
            </button>
        </div>
    </div>
    <?php
    // no-js version forms values binding
    foreach (['wcpwOrderBy', 'wcpwProductsPerPage'] as $key) {
        if (isset($_GET[$key]) && !empty($_GET[$key])) {
            echo '<input type="hidden" name="wcpwFilter" value="'
                . esc_attr(http_build_query((array) $_GET[$key]))  . '">';
        }
    }
    ?>
</form>
