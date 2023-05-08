<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Form;
use WCProductsWizard\Settings;
use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'showProgress' => Settings::getPost($id, 'show_progress'),
    'progressLabel' => Settings::getPost($id, 'progress_label'),
    'steps' => Form::getSteps($id),
    'stepsProgress' => Form::getStepsProgress($id)
]);

$class = ['is-progress-' . $arguments['stepsProgress']];

if (!$arguments['showProgress']) {
    return;
} elseif ($arguments['showProgress'] != 'always') {
    $class[] = "d-{$arguments['showProgress']}-none";
}

$max = count($arguments['steps']) - 1;
?>
<div class="woocommerce-products-wizard-progress <?php echo esc_attr(implode(' ', $class)); ?>">
    <div class="woocommerce-products-wizard-progress-line progress" role="progressbar"
        aria-label="<?php echo esc_attr($arguments['progressLabel']); ?>"
        aria-valuenow="<?php echo esc_attr($arguments['stepsProgress']); ?>"
        aria-valuemin="0" aria-valuemax="<?php echo esc_attr($max); ?>">
        <div class="woocommerce-products-wizard-progress-line-bar progress-bar"
            style="width:<?php echo esc_attr((int) $arguments['stepsProgress'] * 100 / ($max)); ?>%;"></div>
    </div>
    <div class="woocommerce-products-wizard-progress-label"><?php
        printf(
            $arguments['progressLabel'],
            $arguments['stepsProgress'] + 1,
            count($arguments['steps'])
        );
        ?></div>

</div>
