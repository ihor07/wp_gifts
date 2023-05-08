<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$stepId = isset($stepId) ? $stepId : null;
$editCartItem = isset($editCartItem) ? $editCartItem : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Form;
use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$availableControls = [];
$minProductsSelected = Settings::getStep($id, $stepId, 'min_products_selected');
$arguments = Template::getHTMLArgs([
    'stepId' => $stepId,
    'mode' => 'step-by-step',
    'controls' => [],
    'enableResultsStep' => Settings::getPost($id, 'enable_results_step'),
    'showSidebar' => Settings::isSidebarShowed($id),
    'nextStepId' => Form::getNextStepId($id),
    'canGoBack' => Form::canGoBack($id),
    'canGoForward' => Form::canGoForward($id)
]);
?>
<div class="woocommerce-products-wizard-controls" data-component="wcpw-controls"><?php
    if ($arguments['mode'] == 'single-step') {
        // is single-step mode
        $availableControls = [
            'spacer' => true,
            'widget-toggle' => $arguments['showSidebar'],
            'reset' => true,
            'result-pdf' => true,
            'add-to-cart' => true,
            'add-to-cart-repeat' => !$editCartItem,
            'share' => true
        ];
    } elseif (is_numeric($arguments['stepId'])
        && (!$arguments['canGoForward'] || $arguments['nextStepId'] == 'checkout')
    ) {
        // is a numeric and the last step or last before checkout
        $availableControls = [
            'spacer' => true,
            'widget-toggle' => $arguments['showSidebar'],
            'reset' => true,
            'back' => $arguments['canGoBack'],
            'add-to-cart' => true,
            'add-to-cart-repeat' => !$editCartItem,
            'share' => true
        ];
    } elseif (is_numeric($arguments['stepId']) && $arguments['canGoForward']) {
        // is a numeric step but not the last
        $availableControls = [
            'spacer' => true,
            'widget-toggle' => $arguments['showSidebar'],
            'reset' => true,
            'back' => $arguments['canGoBack'],
            'skip' => (!isset($minProductsSelected['value']) || !$minProductsSelected['value']),
            'next' => true,
            'to-results' => $arguments['enableResultsStep'],
            'share' => true
        ];
    } elseif ($arguments['stepId'] == 'start') {
        // is the start step
        $availableControls = [
            'spacer' => true,
            'widget-toggle' => $arguments['showSidebar'],
            'start' => true
        ];
    } elseif ($arguments['stepId'] == 'result') {
        // is the results step
        $availableControls = [
            'spacer' => true,
            'widget-toggle' => $arguments['showSidebar'],
            'reset' => true,
            'back' => $arguments['canGoBack'],
            'result-pdf' => true,
            'add-to-cart' => true,
            'add-to-cart-repeat' => !$editCartItem,
            'share' => true
        ];
    } elseif (!$arguments['canGoForward']) {
        // is the last step
        $availableControls = [
            'spacer' => true,
            'widget-toggle' => $arguments['showSidebar'],
            'reset' => true,
            'back' => $arguments['canGoBack']
        ];
    }

    foreach ($arguments['controls'] as $control) {
        $control = $control == 'spacer-2' ? 'spacer' : $control;

        if (!isset($availableControls[$control]) || !$availableControls[$control]) {
            continue;
        }

        Template::html('controls/' . $control, $arguments);
    }
    ?></div>
