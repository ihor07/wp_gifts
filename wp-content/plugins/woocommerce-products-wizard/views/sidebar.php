<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;
$stepId = isset($stepId) ? $stepId : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$arguments = Template::getHTMLArgs([
    'mode' => 'step-by-step',
    'filterPosition' => Settings::getStep($id, $stepId, 'filter_position'),
    'sidebarPosition' => Settings::getPost($id, 'sidebar_position'),
    'sidebarLeftClass' => Settings::getPost($id, 'sidebar_left_class', 'post', 'col-lg-3 col-md-4 col-xs-12 col-12 col'),
    'sidebarRightClass' => Settings::getPost($id, 'sidebar_right_class', 'post', 'col-lg-3 col-md-4 col-xs-12 col-12 col order-md-last'),
    'sidebarTopClass' => Settings::getPost($id, 'sidebar_top_class', 'post', 'col-xs-12 col-12 col')
]);

switch ($arguments['sidebarPosition']) {
    case 'top':
        $sidebarClass = $arguments['sidebarTopClass'];
        break;

    case 'left':
        $sidebarClass = $arguments['sidebarLeftClass'];
        break;

    default:
    case 'right':
        $sidebarClass = $arguments['sidebarRightClass'];
}
?>
<aside class="woocommerce-products-wizard-sidebar <?php echo esc_attr($sidebarClass); ?>"><?php
    if ($arguments['filterPosition'] == 'before-widget'
        && !in_array($arguments['mode'], ['single-step', 'sequence', 'expanded-sequence'])
    ) {
        Template::html('form/filter/index', $arguments);
    }

    Template::html('widget/index', $arguments);
    ?></aside>
