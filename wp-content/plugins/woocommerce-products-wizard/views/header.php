<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$arguments = Template::getHTMLArgs([
    'stepId' => null,
    'controls' => Settings::getPost($id, 'header_controls'),
    'stickyHeader' => Settings::getPost($id, 'sticky_header'),
    'stickyHeaderOffsetTop' => Settings::getPost($id, 'sticky_header_offset_top')
]);

$isSticky = !in_array($arguments['stickyHeader'], ['0', 'never']);
$class = ['is-step-' . $arguments['stepId']];

if ($isSticky) {
    $class[] = 'is-sticky' . (!in_array($arguments['stickyHeader'], ['1', 'always']) ? "-{$arguments['stickyHeader']}" : '');
}
?>
<header class="woocommerce-products-wizard-header <?php echo esc_attr(implode(' ', $class)); ?>"<?php
    if ($isSticky && $arguments['stickyHeaderOffsetTop']) {
        if (is_int($arguments['stickyHeaderOffsetTop'])) {
            $arguments['stickyHeaderOffsetTop'] .= 'px';
        }

        echo ' style="top: ' . esc_attr($arguments['stickyHeaderOffsetTop']) . '"';
    }
    ?>
    data-component="wcpw-header<?php echo $isSticky ? ' wcpw-sticky-observer' : ''; ?>"><?php
    Template::html('controls/index', $arguments);
    ?></header>
