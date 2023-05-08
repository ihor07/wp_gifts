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
    'formId' => "wcpw-form-{$id}",
    'showHeader' => Settings::getPost($id, 'show_header'),
    'showFooter' => Settings::getPost($id, 'show_footer'),
    'mode' => Settings::getPost($id, 'mode')
]);

do_action('wcpw_before_output', $arguments);

$arguments['stepId'] = Form::getActiveStepId($id); // force define the active step
$bodyTemplate = in_array($arguments['mode'], ['single-step', 'sequence', 'expanded-sequence']) ? 'single' : 'tabs';

Template::html('form', $arguments);

if ($bodyTemplate == 'tabs') {
    Template::html('progress', $arguments);
    Template::html('nav/index', $arguments);
}

if ($arguments['showHeader']) {
    Template::html('header', $arguments);
}

Template::html("body/{$bodyTemplate}", $arguments);

if ($arguments['showFooter']) {
    Template::html('footer', $arguments);
}

do_action('wcpw_after_output', $arguments);
