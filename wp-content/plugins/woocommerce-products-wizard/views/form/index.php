<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Cart;
use WCProductsWizard\Form;
use WCProductsWizard\Product;
use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$activeStepId = Form::getActiveStepId($id);
$stepId = isset($stepId) ? $stepId : $activeStepId;
$step = Form::getStep($id, $stepId);
$arguments = Template::getHTMLArgs([
    'id' => $id,
    'formId' => null,
    'title' => isset($step['name']) ? $step['name'] : '',
    'description' => isset($step['description']) ? $step['description'] : '',
    'thumbnail' => isset($step['thumbnail']) ? $step['thumbnail'] : '',
    'bottomDescription' => isset($step['bottomDescription']) ? $step['bottomDescription'] : '',
    'notices' => WCProductsWizard\Instance()->form->getNotices($stepId),
    'page' => Form::getStepPageValue($stepId),
    'filter' => Form::getFilterValue($id, $stepId),
    'productsPerPage' => Form::getStepProductsPerPageValue($stepId),
    'orderBy' => Form::getStepOrderByValue($stepId),
    'filterPosition' => Settings::getStep($id, $stepId, 'filter_position'),
    'showStepsNames' => Settings::getPost($id, 'show_steps_names'),
    'enableStepToggleButton' => Settings::getPost($id, 'enable_step_toggle_button'),
    'stepToggleButtonClass' => Settings::getPost($id, 'step_toggle_button_class'),
    'stepsAreExpanded' => Settings::getPost($id, 'steps_are_expanded'),
    'stepFilter' => Settings::getStep($id, $stepId, 'step_filter'),
    'mode' => 'step-by-step',
    'disabled' => false
]);

$arguments['stepId'] = $stepId; // force define the current step
$isExpanded = true;
$fieldsetId = "{$arguments['id']}-{$arguments['stepId']}";
$class = ['woocommerce-products-wizard-step', 'woocommerce-products-wizard-form', 'is-step-' . $arguments['stepId']];

if (!empty(Cart::getByStepId($arguments['id'], $arguments['stepId']))) {
    $class[] = 'has-products-in-cart';
}

if ($activeStepId == $arguments['stepId']) {
    $class[] = 'is-active';
}

if (in_array($arguments['mode'], ['single-step', 'sequence', 'expanded-sequence'])
    && $arguments['enableStepToggleButton']
) {
    $isExpanded = isset($_COOKIE["#woocommerce-products-wizard-form-{$arguments['id']}-{$arguments['stepId']}-expanded"])
        ? $_COOKIE["#woocommerce-products-wizard-form-{$arguments['id']}-{$arguments['stepId']}-expanded"]
        : $arguments['stepsAreExpanded'] || !empty($arguments['filter'][$arguments['stepId']]);
    ?>
    <a href="#woocommerce-products-wizard-form-<?php echo esc_attr($fieldsetId); ?>"
        class="woocommerce-products-wizard-control is-step-toggle btn <?php
        echo esc_attr($arguments['stepToggleButtonClass']);
        ?>"
        role="button" data-component="wcpw-toggle"
        data-target="#woocommerce-products-wizard-form-<?php echo esc_attr($fieldsetId); ?>"
        aria-controls="woocommerce-products-wizard-form-<?php echo esc_attr($fieldsetId); ?>"
        aria-expanded="<?php echo var_export(filter_var($isExpanded, FILTER_VALIDATE_BOOLEAN), true); ?>">
        <?php
        echo $arguments['thumbnail']
            ? wp_get_attachment_image(
                $arguments['thumbnail'],
                'thumbnail',
                false,
                ['class' => 'woocommerce-products-wizard-nav-button-thumbnail']
            )
            . ' ' : '';
        ?><span class="woocommerce-products-wizard-form-toggle"><?php echo wp_kses_post($arguments['title']); ?></span>
    </a>
    <?php
}
?>
<fieldset class="<?php echo esc_attr(implode(' ', $class)); ?>"
    data-component="wcpw-form-step" data-id="<?php echo esc_attr($arguments['stepId']); ?>"
    id="woocommerce-products-wizard-form-<?php echo esc_attr($fieldsetId); ?>"
    aria-expanded="<?php echo var_export(filter_var($isExpanded, FILTER_VALIDATE_BOOLEAN), true); ?>"<?php
    disabled($arguments['disabled']);
    ?>>
    <?php
    if ($arguments['showStepsNames']) {
        Template::html('form/title', $arguments);
    }

    if (!empty($arguments['notices'])) {
        foreach ($arguments['notices'] as $notice) {
            Template::html("messages/{$notice['view']}", array_replace($arguments, $notice));
        }
    }

    if ($arguments['description']) {
        Template::html('form/description', $arguments);
    }

    if (is_numeric($arguments['stepId'])) {
        ?>
        <input type="hidden" form="<?php echo esc_attr($arguments['formId']); ?>"
            name="productsToAddChecked[<?php echo esc_attr($arguments['stepId']); ?>][]" value="">
        <div class="woocommerce-products-wizard-form-controls"><?php
            if ($arguments['filterPosition'] == 'before-products') {
                Template::html('form/filter/index', $arguments);
            }

            Template::html('form/order-by', $arguments);
            Template::html('form/products-per-page', $arguments);
            ?></div>
        <?php
        if ($arguments['stepFilter'] && class_exists('\WCStepFilter\Template')) {
            \WCStepFilter\Template::html(
                'index',
                [
                    'id' => $arguments['stepFilter'],
                    'wizardArguments' => [
                        'id' => $arguments['id'],
                        'stepId' => $arguments['stepId'],
                        'formId' => $arguments['formId'],
                        'filter' => $arguments['filter'],
                        'productsPerPage' => $arguments['productsPerPage'],
                        'page' => $arguments['page'],
                        'orderBy' => $arguments['orderBy']
                    ]
                ]
            );
        } else {
            Product::request($arguments);
        }
    }

    if ($arguments['bottomDescription']) {
        Template::html(
            'form/description',
            array_replace(
                $arguments,
                [
                    'descriptionSubClass' => 'is-bottom',
                    'description' => $arguments['bottomDescription']
                ]
            )
        );
    }
    ?>
</fieldset>
