<?php
/**
 * Plugin Name: WooCommerce Products Wizard
 * Description: This plugin helps you sell your products by the step-by-step wizard
 * Version: 11.0.2
 * Author: mail@troll-winner.com
 * Author URI: https://troll-winner.com/
 * Text Domain: woocommerce-products-wizard
 * Domain Path: /languages/
 * Requires at least: 4.5
 * Requires PHP: 5.5
 * WC requires at least: 2.4
 * WC tested up to: 7.5.1
 */

namespace {

    defined('ABSPATH') || exit;

    $uploadDir = wp_upload_dir();

    if (!defined('WC_PRODUCTS_WIZARD_VERSION')) {
        define('WC_PRODUCTS_WIZARD_VERSION', '11.0.2');
    }

    if (!defined('WC_PRODUCTS_WIZARD_DEBUG')) {
        if (defined('SCRIPT_DEBUG')) {
            define('WC_PRODUCTS_WIZARD_DEBUG', SCRIPT_DEBUG);
        } else {
            define('WC_PRODUCTS_WIZARD_DEBUG', false);
        }
    }

    if (!defined('WC_PRODUCTS_WIZARD_THEME_TEMPLATES_DIR')) {
        define('WC_PRODUCTS_WIZARD_THEME_TEMPLATES_DIR', 'woocommerce-products-wizard');
    }

    if (!defined('WC_PRODUCTS_WIZARD_PLUGIN_PATH')) {
        define('WC_PRODUCTS_WIZARD_PLUGIN_PATH', plugin_dir_path(__FILE__));
    }

    if (!defined('WC_PRODUCTS_WIZARD_PLUGIN_URL')) {
        define('WC_PRODUCTS_WIZARD_PLUGIN_URL', plugin_dir_url(__FILE__));
    }

    if (!defined('WC_PRODUCTS_WIZARD_PLUGIN_DIR_NAME')) {
        define('WC_PRODUCTS_WIZARD_PLUGIN_DIR_NAME', plugin_basename(__DIR__));
    }

    if (!defined('WC_PRODUCTS_WIZARD_PLUGIN_BASENAME')) {
        define('WC_PRODUCTS_WIZARD_PLUGIN_BASENAME', plugin_basename(__FILE__));
    }

    if (!defined('WC_PRODUCTS_WIZARD_UPLOADS_PATH')) {
        define(
            'WC_PRODUCTS_WIZARD_UPLOADS_PATH',
            $uploadDir['basedir'] . DIRECTORY_SEPARATOR . 'woocommerce-products-wizard' . DIRECTORY_SEPARATOR
        );
    }

    if (!defined('WC_PRODUCTS_WIZARD_UPLOADS_URL')) {
        define(
            'WC_PRODUCTS_WIZARD_UPLOADS_URL',
            $uploadDir['baseurl'] . DIRECTORY_SEPARATOR . 'woocommerce-products-wizard' . DIRECTORY_SEPARATOR
        );
    }

    if (!class_exists('\WCProductsWizard\Core')) {
        require_once(__DIR__ . '/includes/classes/Core.php');
    }

    require_once(__DIR__ . '/includes/global/legacy.php');
    require_once(__DIR__ . '/includes/global/shortcodes.php');
}

namespace WCProductsWizard {

    if (!function_exists(__NAMESPACE__  . '\Instance')) {
        function Instance()
        {
            return Core::instance();
        }
    } else {
        add_filter('admin_notices', function () {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p><strong><?php L10N::e('WooCommerce Products Wizard is enabled a few times'); ?></strong></p>
                <p><?php
                    // phpcs:disable
                    L10N::e('A few WooCommerce Products Wizard plugins are enabled at once. Keep enabled only one of them.');
                    // phpcs:enable
                    ?></p>
            </div>
            <?php
        });
    }

    $GLOBALS['WCProductsWizard'] = Instance();

    // requests
    if (class_exists(__NAMESPACE__ . '\API')) {
        add_filter('plugins_api', [__NAMESPACE__ . '\API', 'pluginApiAction'], 20, 3);
        add_filter('site_transient_update_plugins', [__NAMESPACE__ . '\API', 'updatePluginAction']);
        add_action('upgrader_process_complete', [__NAMESPACE__ . '\API', 'pluginUpdatedAction'], 10, 2);
        register_activation_hook(__FILE__, function () {
            API::request(['action' => 'activate_plugin']);
        });

        register_deactivation_hook(__FILE__, function () {
            API::request(['action' => 'deactivate_plugin']);

            wp_clear_scheduled_hook('wcpw_cron');
        });
    }
}
