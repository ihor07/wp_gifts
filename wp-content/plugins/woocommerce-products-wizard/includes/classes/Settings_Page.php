<?php
namespace WCProductsWizard;

/**
 * Settings_Page Class
 *
 * @class Settings_Page
 * @version 1.4.1
 */
// phpcs:disable
class Settings_Page extends \WC_Settings_Page
{
    public $settings = [];

    /**
     * Constructor.
     *
     * @param array $settings
     */
    public function __construct($settings)
    {
        $this->settings = $settings;
        $this->id = 'products_wizard';
        $this->label = L10N::r('Products Wizard');

        parent::__construct();

        add_action('woocommerce_sections_' . $this->id, [$this, 'actions']);
    }

    /**
     * Get sections.
     *
     * @return array
     */
    public function get_sections()
    {
        $sections = [
            '' => L10N::r('General', 'woocommerce'),
            'custom_styles' => L10N::r('Custom styles'),
            'templates' => L10N::r('Templates'),
            'license' => L10N::r('License')
        ];

        return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
    }

    /**
     * Get settings array.
     *
     * @param string $current_section Current section.
     *
     * @return array
     */
    public function get_settings($current_section = '')
    {
        $settings = [];

        foreach ($this->settings as $key => $setting) {
            if (isset($setting['section']) && $setting['section'] == $current_section) {
                $settings[$key] = $setting;
            }
        }

        return apply_filters('woocommerce_get_settings_' . $this->id, $settings);
    }

    /** Output the settings */
    public function output()
    {
        global $current_section;

        $sections = $this->get_sections();
        $settings = $this->get_settings($current_section);
        $description = '';

        if ($current_section == 'custom_styles') {
            $description = L10N::r('Use with the "Styles including type" setting equal to "Custom full styles file"');
        }

        woocommerce_admin_fields([
            'section_title' => [
                'name' => isset($sections[$current_section]) ? $sections[$current_section] : 'No title',
                'type' => 'title',
                'desc' => $description,
                'id' => 'wcpw_settings_section_title'
            ]
        ]);

        woocommerce_admin_fields($settings);

        if ($current_section == 'custom_styles') {
            $themes = [];
            $sources = WC_PRODUCTS_WIZARD_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'front'
                . DIRECTORY_SEPARATOR . 'scss' . DIRECTORY_SEPARATOR . 'bootswatch';

            foreach (scandir($sources) as $file) {
                if (is_dir($sources . DIRECTORY_SEPARATOR . $file) && !in_array($file, ['.', '..'])) {
                    $themes[$file] = $file;
                }
            }
            ?>
            <tr>
                <th scope="row">
                    <label for="wcpw-settings-bootswatch-theme"><?php L10N::e('Apply Bootswatch theme'); ?></label>
                </th>
                <td>
                    <select name="wcpw-settings-bootswatch-theme" id="wcpw-settings-bootswatch-theme"
                        data-mode="advanced"><?php
                        foreach ($themes as $theme) {
                            echo '<option value="' . esc_attr($theme) . '" '
                                . selected(
                                    isset($_REQUEST['wcpw-settings-bootswatch-theme'])
                                    && $_REQUEST['wcpw-settings-bootswatch-theme'] == $theme,
                                    true,
                                    false
                                )
                                . '>' . esc_html($theme) . '</option>';
                        }
                        ?></select>
                    <button class="button button-primary" id="wcpw-settings-bootswatch-theme-submit"
                        name="wcpw-settings-bootswatch-theme-submit" value="1"><?php
                        L10N::e('Apply');
                        ?></button>
                    <p class="description">
                        <strong><?php L10N::e('Warning:'); ?></strong>
                        <?php L10N::e('Overrides the "Custom SCSS" setting'); ?><br>
                        <a href="https://bootswatch.com/default/" target="_blank"><?php
                            L10N::e('See all themes on Bootswatch.com');
                            ?></a>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="wcpw-settings-reset"><?php L10N::e('Reset to defaults'); ?></label>
                </th>
                <td>
                    <button class="button button-secondary" id="wcpw-settings-reset" name="wcpw-settings-reset"
                        data-component="wcpw-settings-reset"
                        data-confirm-message="<?php echo esc_attr(L10N::r('Reset settings?')); ?>"><?php
                        L10N::e('Reset');
                        ?></button>
                </td>
            </tr>
            <?php
        } elseif ($current_section == 'templates') {
            $GLOBALS['hide_save_button'] = true;
            ?>
            <tr>
                <th scope="row">
                    <label for="wcpw-templates-action"><?php esc_attr_e('Action'); ?></label>
                </th>
                <td class="forminp">
                    <select name="wcpw-templates-action" id="wcpw-templates-action">
                        <option value="copy"><?php L10N::e('Copy to the active theme'); ?></option>
                        <option value="delete"><?php L10N::e('Delete from the active theme'); ?></option>
                    </select>
                    <button class="button button-primary" name="wcpw-templates-submit-action"><?php
                        esc_attr_e('Apply');
                        ?></button>
                </td>
            </tr>
            <tr><td colspan="2" class="td-full"><?php
                $this->outputTemplatesList(Template::getList());
                ?></td></tr>
            <?php
        } elseif ($current_section == 'license') {
            $GLOBALS['hide_save_button'] = true;
            $isActivated = API::getPurchaseToken();
            ?>
            <tr>
                <th scope="row"><?php L10N::e('Status'); ?></th>
                <td><span class="wcpw-license-status <?php
                    echo $isActivated ? 'is-activated' : 'not-activated';
                    ?>"><?php echo $isActivated ? L10N::r('Activated') : L10N::r('Not activated'); ?></span></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit" name="wcpw-license-action"
                        data-component="wcpw-license-action"
                        class="<?php echo $isActivated ? 'button-secondary' : 'button-primary'; ?>"
                        value="<?php echo $isActivated ? 'deactivate' : 'activate'; ?>"><?php
                        echo $isActivated ? L10N::r('Deactivate') : L10N::r('Activate');
                        ?></button>
                </td>
            </tr>
            <?php
        }

        woocommerce_admin_fields([
            'section_end' => [
                'type' => 'sectionend',
                'id' => 'wcpw_settings_section_end'
            ]
        ]);
    }

    /** Action handlers */
    public function actions()
    {
        $nonce = wc_get_var($_REQUEST['woocommerce-settings'], wc_get_var($_REQUEST['_wpnonce'], ''));

        if (!wp_verify_nonce($nonce, 'woocommerce-settings')) {
            return;
        }

        // settings reset action
        if (isset($_REQUEST['wcpw-settings-reset'])) {
            global $current_section;

            foreach ($this->get_settings($current_section) as $setting) {
                delete_option($setting['key']);
            }
        }

        // apply bootswatch theme
        if (isset($_REQUEST['wcpw-settings-bootswatch-theme-submit'], $_REQUEST['wcpw-settings-bootswatch-theme'])
            && $_REQUEST['wcpw-settings-bootswatch-theme-submit'] && $_REQUEST['wcpw-settings-bootswatch-theme']
        ) {
            try {
                // blend in bootswatch theme into the pure variables file
                $bootswatchPath = WC_PRODUCTS_WIZARD_PLUGIN_PATH . implode(
                    DIRECTORY_SEPARATOR,
                    [
                        'src',
                        'front',
                        'scss',
                        'bootswatch',
                        (string) $_REQUEST['wcpw-settings-bootswatch-theme']
                    ]
                );

                $model = Settings::getModel(['source' => 'global']);
                $variables = Utils::fileGetContents($bootswatchPath . DIRECTORY_SEPARATOR . '_variables.scss');
                $bootswatch = Utils::fileGetContents($bootswatchPath . DIRECTORY_SEPARATOR . '_bootswatch.scss');
                $replaces = [
                    '/* Bootswatch variables place */' =>
                        $variables
                        . '@import "bootstrap/variables";'
                        . PHP_EOL . 'section.woocommerce-products-wizard {'
                        . PHP_EOL . '$font-family-base: $font-family-sans-serif;'
                        . PHP_EOL . '$font-family-code: $font-family-monospace;'
                        . PHP_EOL . 'font-family: $font-family-base; '
                        . PHP_EOL . $bootswatch . '}',
                    ' !default' => ''
                ];

                $variablesScss = str_replace(array_keys($replaces), array_values($replaces), Styles::getSCSSVariablesString());

                update_option($model['custom_styles_mode']['key'], 'advanced');
                update_option($model['custom_scss']['key'], $variablesScss);
                Styles::compileFile();
            } catch (\Exception $exception) {
                exit(L10N::r('SCSS compiling error') . '<br>' . $exception->getMessage());
            } catch (\ScssPhp\ScssPhp\Exception\SassException $exception) {
                exit(L10N::r('SCSS compiling error') . '<br>' . $exception->getMessage());
            }
        }

        // templates actions
        if (isset($_REQUEST['wcpw-templates-action'], $_REQUEST['wcpw-templates-list-item'])) {
            $themePath = get_stylesheet_directory();

            switch ($_REQUEST['wcpw-templates-action']) {
                case 'copy': {
                    foreach ($_REQUEST['wcpw-templates-list-item'] as $item) {
                        $dir = dirname($themePath . DIRECTORY_SEPARATOR . WC_PRODUCTS_WIZARD_THEME_TEMPLATES_DIR . DIRECTORY_SEPARATOR . $item);

                        if (!file_exists($dir)) {
                            mkdir($dir, 0755, true);
                        }

                        copy(
                            WC_PRODUCTS_WIZARD_PLUGIN_PATH . 'views' . DIRECTORY_SEPARATOR . $item,
                            $themePath . DIRECTORY_SEPARATOR . WC_PRODUCTS_WIZARD_THEME_TEMPLATES_DIR . DIRECTORY_SEPARATOR . $item
                        );
                    }

                    break;
                }

                case 'delete': {
                    foreach ($_REQUEST['wcpw-templates-list-item'] as $item) {
                        if (!file_exists($themePath . DIRECTORY_SEPARATOR . WC_PRODUCTS_WIZARD_THEME_TEMPLATES_DIR . DIRECTORY_SEPARATOR . $item)) {
                            continue;
                        }

                        if (!is_dir($themePath . DIRECTORY_SEPARATOR . WC_PRODUCTS_WIZARD_THEME_TEMPLATES_DIR . DIRECTORY_SEPARATOR . $item)) {
                            unlink($themePath . DIRECTORY_SEPARATOR . WC_PRODUCTS_WIZARD_THEME_TEMPLATES_DIR . DIRECTORY_SEPARATOR . $item);
                        }
                    }
                }
            }
        }

        // license actions
        if (isset($_POST['wcpw-license-action']) && !empty(trim(wp_unslash($_POST['wcpw-license-action'])))) {
            $action = trim(wp_unslash($_POST['wcpw-license-action']));

            if ($action == 'flush') {
                global $current_section;

                foreach ($this->get_settings($current_section) as $setting) {
                    delete_option($setting['key']);
                }

                delete_option('woocommerce_products_wizard_purchase_token');
                delete_option('woocommerce_products_wizard_settings_models');
                delete_transient('wcpw_request_info');

                return;
            }

            $response = API::request([
                'action' => $action . '_license',
                'purchase_code' => isset($_POST['woocommerce_products_wizard_purchase_code'])
                    ? $_POST['woocommerce_products_wizard_purchase_code']
                    : null
            ]);

            if (is_wp_error($response) || property_exists($response, 'error')) {
                $error = is_wp_error($response) ? $response->get_error_message() : $response->error;

                echo '<div id="message" class="error is-dismissible"><p>' . esc_html($error) . '</p></div>';
            } else {
                if (property_exists($response, 'message') && $response->message) {
                    echo '<div id="message" class="updated notice is-dismissible"><p>'
                        . esc_html($response->message) . '</p></div>';
                }

                switch ($action) {
                    case 'activate': {
                        if (property_exists($response, 'purchase_code') && !empty($response->purchase_code)) {
                            update_option('woocommerce_products_wizard_purchase_code', $response->purchase_code);
                        }

                        if (property_exists($response, 'purchase_token') && !empty($response->purchase_token)) {
                            update_option('woocommerce_products_wizard_purchase_token', $response->purchase_token);
                        }

                        if (property_exists($response, 'settings_models') && !empty($response->settings_models)) {
                            update_option(
                                'woocommerce_products_wizard_settings_models',
                                json_decode(json_encode($response->settings_models), true)
                            );
                        }

                        break;
                    }

                    case 'deactivate': {
                        global $current_section;

                        foreach ($this->get_settings($current_section) as $setting) {
                            delete_option($setting['key']);
                        }

                        delete_option('woocommerce_products_wizard_purchase_token');
                        delete_option('woocommerce_products_wizard_settings_models');
                    }
                }

                delete_transient('wcpw_request_info');
            }
        }
    }

    /** Save settings */
    public function save()
    {
        global $current_section;

        woocommerce_update_options($this->get_settings($current_section));

        if ($current_section == 'custom_styles') {
            try {
                Styles::compileFile();
                update_option('woocommerce_products_wizard_styles_compiled_time', time());
            } catch (\Exception $exception) {
                exit(L10N::r('SCSS compiling error') . '<br>' . $exception->getMessage());
            } catch (\ScssPhp\ScssPhp\Exception\SassException $exception) {
                exit(L10N::r('SCSS compiling error') . '<br>' . $exception->getMessage());
            }
        }
    }

    /**
     * Output plugin templates list HTML
     *
     * @param array $list
     */
    public function outputTemplatesList($list)
    {
        echo '<ul class="wcpw-templates-list" data-component="wcpw-templates-list">';

        foreach ($list as $item) {
            if (isset($item['children'])) {
                echo '<li class="wcpw-templates-list-item is-folder'
                    . ($item['is_in_theme'] ? ' is-in-theme' : '') . '" data-component="wcpw-templates-list-item">'
                    . '<label class="wcpw-templates-list-item-label" title="' . esc_attr($item['relative_path']) . '">'
                    . ' <input type="checkbox" data-component="wcpw-templates-list-item-input" data-type="folder">'
                    . ' <code class="wcpw-templates-list-item-name"> ' . $item['name'] . '</code> </label>';

                $this->outputTemplatesList($item['children']);

                echo '</li>';
            } else {
                echo '<li class="wcpw-templates-list-item is-file'
                    . ($item['is_in_theme'] ? ' is-in-theme' : '') . '" data-component="wcpw-templates-list-item">'
                    . '<label class="wcpw-templates-list-item-label" title="' . esc_attr($item['relative_path']) . '">'
                    . ' <input type="checkbox" name="wcpw-templates-list-item[]"'
                    . ' value="' . esc_attr($item['relative_path']) . '"'
                    . ' data-component="wcpw-templates-list-item-input" data-type="file">'
                    . ' <code class="wcpw-templates-list-item-name"> ' . $item['name'] . '</code> </label></li>';
            }
        }

        echo '</ul>';
    }
}
// phpcs:enable
