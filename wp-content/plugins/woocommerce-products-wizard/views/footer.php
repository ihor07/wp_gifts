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
    'controls' => Settings::getPost($id, 'footer_controls'),
    'stickyFooter' => Settings::getPost($id, 'sticky_footer')
]);

$isSticky = !in_array($arguments['stickyFooter'], ['0', 'never']);
$class = ['is-step-' . $arguments['stepId']];

if ($isSticky) {
    $class[] = 'is-sticky' . (!in_array($arguments['stickyFooter'], ['1', 'always']) ? "-{$arguments['stickyFooter']}" : '');
}
?>
<footer class="woocommerce-products-wizard-footer <?php echo esc_attr(implode(' ', $class)); ?>"
    data-component="wcpw-footer<?php echo $isSticky ? ' wcpw-sticky-observer' : ''; ?>"><?php
    Template::html('controls/index', $arguments);
    ?></footer>
