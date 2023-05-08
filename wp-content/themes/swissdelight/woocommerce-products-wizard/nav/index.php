<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$arguments = Template::getHTMLArgs([
    'navTemplate' => Settings::getPost($id, 'nav_template'),
    'toggleMobileNavOn' => Settings::getPost($id, 'toggle_mobile_nav_on', 'post', 'sm')
]);

if ($arguments['toggleMobileNavOn'] == 'always') {
    return;
}

Template::html('nav/list/' . $arguments['navTemplate'], $arguments);
