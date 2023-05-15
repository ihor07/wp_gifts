<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Form;
use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$activeStepId = Form::getActiveStepId($id);
$arguments = Template::getHTMLArgs([
    'id' => $id,
    'stepId' => null,
    'mode' => 'single-step',
    'steps' => Form::getSteps($id),
    'showSidebar' => Settings::isSidebarShowed($id),
    'sidebarPosition' => Settings::getPost($id, 'sidebar_position'),
    'mainFullWidthClass' => Settings::getPost($id, 'main_full_width_class', 'post', 'col-xs-12 col-12 col'),
    'mainWithSidebarClass' => Settings::getPost($id, 'main_with_sidebar_class', 'post', 'col-lg-9 col-md-8 col-xs-12 col-12 col')
]);

if (!$arguments['showSidebar'] || $arguments['sidebarPosition'] == 'top') {
    $mainClass = $arguments['mainFullWidthClass'];
} else {
    $mainClass = $arguments['mainWithSidebarClass'];
}
?>
<div class="woocommerce-products-wizard-body row woocommerce-products-wizard-main-row is-single <?php
    echo esc_attr("is-{$arguments['mode']}-mode");
    ?>"
    data-component="wcpw-main-row">
    <?php
    if ($arguments['showSidebar']) {
        Template::html('sidebar', $arguments);
    }
    ?>
    <div class="woocommerce-products-wizard-main <?php echo esc_attr($mainClass); ?>"><?php
        foreach ($arguments['steps'] as $step) {
            $arguments['stepId'] = $step['id'];

            if ($step['id'] == 'result') {
                Template::html('result', $arguments);
            } elseif ($step['id'] == 'checkout') {
                if ($arguments['mode'] == 'sequence'
                    || ($arguments['mode'] == 'expanded-sequence' && $activeStepId == 'checkout')
                ) {
                    Template::html('checkout', $arguments);
                }
            } else {
                Template::html('form/index', $arguments);
            }

            if (in_array($arguments['mode'], ['expanded-sequence', 'sequence'])) {
                if (isset($step['merged_steps']) && !empty($step['merged_steps'])) {
                    foreach ($step['merged_steps'] as $mergedStepId) {
                        $arguments['stepId'] = $mergedStepId;

                        Template::html('form/index', $arguments);
                    }
                }

                if ($activeStepId == $step['id']) {
                    // disable further steps
                    $arguments['disabled'] = true;

                    // break after the step
                    if ($arguments['mode'] == 'sequence') {
                        break;
                    }
                }
            }
        }
        ?></div>
</div>
