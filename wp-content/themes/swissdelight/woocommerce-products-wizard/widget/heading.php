<?php
defined('ABSPATH') || exit;

use WCProductsWizard\Template;

$arguments = Template::getHTMLArgs([
    'stepId' => null,
    'cartItem' => null,
    'navItem' => [],
    'navigateUsingWidgetSteps' => false,
    'mode' => 'step-by-step'
]);

$hasNav = $arguments['navigateUsingWidgetSteps'] && in_array($arguments['mode'], ['step-by-step', 'free-walk']);
?>
<li class="woocommerce-products-wizard-widget-body-item is-heading <?php
    echo esc_attr("is-step-{$arguments['navItem']['id']}");
    echo $arguments['stepId'] == $arguments['navItem']['id'] ? ' is-current-step' : '';
    echo $hasNav ? ' has-nav' : '';
    ?>">
    <?php
    if ($hasNav) {
        echo '<button type="submit" role="tab" form="' . esc_attr($arguments['formId']) . '" '
            . 'name="' . esc_attr($arguments['navItem']['action'])
            . '" value="' . esc_attr($arguments['navItem']['value']) . '" '
            . 'class="btn btn-light woocommerce-products-wizard-widget-step-nav '
            . esc_attr($arguments['navItem']['class']) . '" '
            . 'data-component="wcpw-nav-item" data-nav-action="' . esc_attr($arguments['navItem']['action']) . '" '
            . 'data-nav-id="' . esc_attr($arguments['navItem']['value']) . '"'
            . disabled($arguments['navItem']['state'] == 'disabled', true, false)
            . '>';
    }

    if ($arguments['navItem']['thumbnail']) {
        echo wp_get_attachment_image(
            $arguments['navItem']['thumbnail'],
            'thumbnail',
            false,
            ['class' => 'woocommerce-products-wizard-widget-step-thumbnail']
        );
    }
    ?>
    <span class="woocommerce-products-wizard-widget-step-name"><?php
        echo wp_kses_post($arguments['navItem']['name']);
        ?></span>
    <?php
    if ($hasNav) {
        echo '</button>';
    }
    ?>
</li>
