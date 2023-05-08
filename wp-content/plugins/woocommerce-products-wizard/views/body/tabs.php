<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Form;
use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$arguments = Template::getHTMLArgs([
    'stepId' => null,
    'mode' => 'tabs',
    'navItems' => Form::getNavItems($id),
    'navTemplate' => Settings::getPost($id, 'nav_template'),
    'showSidebar' => Settings::isSidebarShowed($id),
    'sidebarPosition' => Settings::getPost($id, 'sidebar_position'),
    'toggleMobileNavOn' => Settings::getPost($id, 'toggle_mobile_nav_on', 'post', 'sm'),
    'mainFullWidthClass' => Settings::getPost($id, 'main_full_width_class', 'post', 'col-xs-12 col-12 col'),
    'mainWithSidebarClass' => Settings::getPost($id, 'main_with_sidebar_class', 'post', 'col-lg-9 col-md-8 col-xs-12 col-12 col')
]);

if (!$arguments['showSidebar'] || $arguments['sidebarPosition'] == 'top') {
    $mainClass = $arguments['mainFullWidthClass'];
} else {
    $mainClass = $arguments['mainWithSidebarClass'];
}
?>
<div class="woocommerce-products-wizard-body row woocommerce-products-wizard-main-row is-tabs <?php
    echo esc_attr("is-{$arguments['mode']}-mode is-step-{$arguments['stepId']}");
    ?>"
    role="tablist" data-component="wcpw-main-row">
    <?php
    if ($arguments['showSidebar']) {
        Template::html('sidebar', $arguments);
    }
    ?>
    <div class="woocommerce-products-wizard-main <?php echo esc_attr($mainClass); ?>"><?php
        foreach ($arguments['navItems'] as $navItem) {
            if ($arguments['navTemplate'] != 'none') {
                Template::html('nav/button', array_replace($arguments, $navItem));
            }

            if (isset($navItem['state']) && $navItem['state'] == 'active') {
                if ($arguments['stepId'] == 'result') {
                    Template::html('result', $arguments);
                } elseif ($arguments['stepId'] == 'checkout') {
                    Template::html('checkout', $arguments);
                } else {
                    Template::html('form/index', $arguments);

                    if (isset($navItem['merged_steps']) && !empty($navItem['merged_steps'])) {
                        foreach ($navItem['merged_steps'] as $mergedStepId) {
                            $arguments['stepId'] = $mergedStepId;

                            Template::html('form/index', $arguments);
                        }
                    }
                }
            }
        }
        ?></div>
</div>
