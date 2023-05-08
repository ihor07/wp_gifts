<?php
namespace WCProductsWizard;

/**
 * API Class
 *
 * @class API
 * @version 1.0.0
 */
class API
{
    /** Class Constructor */
    public function __construct()
    {
        add_action('init', [$this, 'initAction']);
        add_action('wcpw_cron', [$this, 'sync']);
        add_action(
            'in_plugin_update_message-' . WC_PRODUCTS_WIZARD_PLUGIN_BASENAME,
            [$this, 'pluginUpdateMessageAction'],
            10,
            2
        );
    }

    /** Fires on the plugin init action */
    public function initAction()
    {
        if (!wp_next_scheduled('wcpw_cron')) {
            wp_schedule_event(time(), 'weekly', 'wcpw_cron');
        }
    }

    /**
     * Get license activation token
     *
     * @return string
     */
    public static function getPurchaseToken()
    {
        return get_option('woocommerce_products_wizard_purchase_token', '');
    }

    /**
     * Get a random installation token string
     *
     * @return string
     */
    public static function getInstallationToken()
    {
        $installationToken = get_option('woocommerce_products_wizard_installation_token');

        if (!$installationToken) {
            $installationToken = substr(
                md5(get_bloginfo('siteurl') . get_bloginfo('name') . get_bloginfo('description') . rand()),
                0,
                32
            );

            update_option('woocommerce_products_wizard_installation_token', $installationToken);
        }

        return $installationToken;
    }

    /**
     * Make API request
     *
     * @param array $args
     * @param string $cacheKey
     *
     * @return \WP_Error|object
     */
    public static function request($args, $cacheKey = null)
    {
        global $wp_version;

        if ($cacheKey && ($output = get_transient('wcpw_request_' . $cacheKey)) && $output) {
            return $output;
        }

        $defaults = [
            'headers' => ['Accept' => 'application/json'],
            'body' => [
                'id' => 'WCPW',
                'version' => defined('WC_PRODUCTS_WIZARD_VERSION') ? WC_PRODUCTS_WIZARD_VERSION : '11.0.2',
                'action' => null,
                'domain' => str_replace(['http://', 'https://'], '', get_site_url()),
                'purchase_code' => get_option('woocommerce_products_wizard_purchase_code', ''),
                'purchase_token' => self::getPurchaseToken(),
                'installation_token' => self::getInstallationToken()
            ],
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url('/'),
            'timeout' => 10
        ];

        $defaults['body'] = array_replace($defaults['body'], $args);
        $output = wp_remote_post('https://api.troll-winner.ru/plugin', $defaults);

        if (is_wp_error($output) || wp_remote_retrieve_response_code($output) != 200
            || empty(wp_remote_retrieve_body($output))
        ) {
            return $output;
        }

        $output = json_decode(wp_remote_retrieve_body($output));

        if ($cacheKey) {
            set_transient('wcpw_request_' . $cacheKey, $output, DAY_IN_SECONDS);
        }

        return $output;
    }

    /** Sync plugin data action */
    public static function sync()
    {
        $response = self::request(['action' => 'sync']);

        if (property_exists($response, 'settings_models') && !empty($response->settings_models)) {
            update_option('woocommerce_products_wizard_settings_models', $response->settings_models);
        }

        if (property_exists($response, 'error_code') && $response->error_code == 'license_error') {
            delete_option('woocommerce_products_wizard_purchase_code');
            delete_option('woocommerce_products_wizard_purchase_token');
            delete_option('woocommerce_products_wizard_settings_models');
        }
    }

    /**
     * WP plugin API action
     *
     * @param object $output
     * @param string $action
     * @param array $args
     *
     * @return \WP_Error|object
     */
    public static function pluginApiAction($output, $action, $args)
    {
        if ('plugin_information' != $action || WC_PRODUCTS_WIZARD_PLUGIN_DIR_NAME != $args->slug) {
            return $output;
        }

        $response = self::request(['action' => 'get_info'], 'info');

        if (is_wp_error($response) || !is_object($response) || !property_exists($response, 'version')) {
            return $output;
        }

        // convert sub-objects to arrays
        if (!empty($response->sections)) {
            $response->sections = (array) $response->sections;
        }

        if (!empty($response->banners)) {
            $response->banners = (array) $response->banners;
        }

        return $response;
    }

    /**
     * WP plugins update action
     *
     * @param object $transient
     *
     * @return object
     */
    public static function updatePluginAction($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $response = self::request(['action' => 'get_info'], 'info');

        if (is_wp_error($response) || !is_object($response) || !property_exists($response, 'version')) {
            return $transient;
        }

        if (version_compare(WC_PRODUCTS_WIZARD_VERSION, $response->version, '<')
            && version_compare($response->requires, get_bloginfo('version'), '<')
            && version_compare($response->requires_php, PHP_VERSION, '<')
        ) {
            $output = new \stdClass();
            $output->slug = WC_PRODUCTS_WIZARD_PLUGIN_DIR_NAME;
            $output->plugin = WC_PRODUCTS_WIZARD_PLUGIN_BASENAME;
            $output->new_version = $response->version;
            $output->tested = $response->tested;
            $output->package = $response->package;

            $transient->response[$output->plugin] = $output;
        }

        return $transient;
    }

    /**
     * After plugins update action
     *
     * @param object $instance
     * @param array $options
     */
    public static function pluginUpdatedAction($instance, $options)
    {
        if ($options['action'] == 'update' && $options['type'] == 'plugin') {
            delete_transient('wcpw_request_info');
            self::sync();
        }
    }

    /**
     * Plugin update error output action
     *
     * @param array $plugin
     */
    public static function pluginUpdateMessageAction($plugin)
    {
        if (empty($plugin['package'])) {
            $message = self::getPurchaseToken()
                ? '<a href="https://products-wizard.troll-winner.ru/plugin-shop-page/">'
                . L10N::r('Plugin support is expired. Renew it to unlock extra possibilities and enable auto-updates.')
                . '</a>'
                : '<a href="' . esc_url(admin_url('admin.php?page=wc-settings&tab=products_wizard&section=license'))
                . '">' . L10N::r('Verify your license key to unlock extra possibilities and enable auto-updates')
                . '</a>';

            echo '<br>' . $message;
        }
    }
}
