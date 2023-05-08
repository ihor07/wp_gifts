<?php
namespace WCProductsWizard;

defined('WP_UNINSTALL_PLUGIN') || exit;

wp_clear_scheduled_hook('wcpw_cron');

include_once(__DIR__ . '/includes/classes/API.php');

API::request(['action' => 'uninstall_plugin']);
