<?php
namespace WCProductsWizard;

/**
 * Styles Class
 *
 * @class Styles
 * @version 1.0.2
 */
class Styles
{
    /**
     * Return the string of the _variables.scss file of the front part
     *
     * @return string CSS
     */
    public static function getSCSSVariablesString()
    {
        $output = Utils::fileGetContents(WC_PRODUCTS_WIZARD_PLUGIN_PATH . 'src/front/scss/_variables.scss');

        return apply_filters('wcpw_scss_variables_string', $output);
    }

    /**
     * Compile custom styles string from settings
     *
     * @return string CSS
     *
     * @throws \ScssPhp\ScssPhp\Exception\SassException
     */
    public static function compile()
    {
        require_once(__DIR__ . '/../vendor/scssphp/scss.inc.php');

        $variablesScss = '';
        $mode = Settings::getGlobal('custom_styles_mode');
        $compiler = new \ScssPhp\ScssPhp\Compiler();
        $compiler->setImportPaths([WC_PRODUCTS_WIZARD_PLUGIN_PATH . 'src/front/scss']);

        if (Settings::getGlobal('custom_styles_minification')) {
            $compiler->setOutputStyle(\ScssPhp\ScssPhp\OutputStyle::COMPRESSED);
        }

        if ($mode == 'simple') {
            $customVariables = [
                'font-size-base' => Settings::getGlobal('style_font_size'),
                'form-item-title-font-size' => Settings::getGlobal('style_form_item_title_font_size'),
                'form-item-price-font-size' => Settings::getGlobal('style_form_item_price_font_size'),
                'primary' => Settings::getGlobal('style_color_primary'),
                'secondary' => Settings::getGlobal('style_color_secondary'),
                'success' => Settings::getGlobal('style_color_success'),
                'info' => Settings::getGlobal('style_color_info'),
                'warning' => Settings::getGlobal('style_color_warning'),
                'danger' => Settings::getGlobal('style_color_danger'),
                'light' => Settings::getGlobal('style_color_light'),
                'dark' => Settings::getGlobal('style_color_dark')
            ];

            $customVariables = apply_filters('wcpw_custom_styles_variables', $customVariables);
            $variablesScss = self::getSCSSVariablesString();

            // use this instead of addVariables() method to overwrite the variables
            foreach ($customVariables as $variable => $value) {
                $variablesScss = preg_replace(
                    '/\$' . $variable . ': (.*);/',
                    '$' . "{$variable}: {$value};",
                    $variablesScss
                );
            }
        } elseif ($mode == 'advanced') {
            $variablesScss = Settings::getGlobal('custom_scss');
        }

        $variablesScss = str_replace('&amp;', '&', $variablesScss);
        $scssString = implode(
            ';',
            [
                '@import "bootstrap/functions"',
                '@import "bootstrap/mixins"',
                $variablesScss,
                '@import "bootstrap/variables"',
                '@import "app-full-custom.scss"'
            ]
        );

        $scssString = apply_filters('wcpw_custom_styles_scss_string', $scssString);

        return $compiler->compileString($scssString)->getCss();
    }

    /**
     * Compile custom styles file from settings
     *
     * @throws \ScssPhp\ScssPhp\Exception\SassException
     * @throws \Exception
     */
    public static function compileFile()
    {
        // create uploads folder if not exists
        if (!file_exists(WC_PRODUCTS_WIZARD_UPLOADS_PATH)) {
            mkdir(WC_PRODUCTS_WIZARD_UPLOADS_PATH, 0777, true);
        }

        $css = self::compile();
        $path = WC_PRODUCTS_WIZARD_UPLOADS_PATH . 'app-full-custom.css';

        // find and replace SVGs and fonts with base64
        preg_match_all("/url\(\"..\/images\/(.*?).svg\"\)/", $css, $SVGs);
        preg_match_all("/url\(\"..\/fonts\/(.*?)\"\)/", $css, $fonts);

        if (isset($SVGs[1]) && !empty($SVGs[1])) {
            foreach ($SVGs[1] as $SVG) {
                $filePath = WC_PRODUCTS_WIZARD_PLUGIN_URL . "src/front/images/{$SVG}.svg";
                $contents = Utils::fileGetContents($filePath);
                $css = str_replace(
                    "url(\"../images/{$SVG}.svg\")",
                    'url("data:image/svg+xml,' . Utils::encodeURIComponent($contents) . '")',
                    $css
                );
            }
        }

        if (isset($fonts[1]) && !empty($fonts[1])) {
            foreach ($fonts[1] as $font) {
                $filePath = WC_PRODUCTS_WIZARD_PLUGIN_URL . "src/front/fonts/{$font}";
                $type = pathinfo($filePath, PATHINFO_EXTENSION);
                $contents = Utils::fileGetContents($filePath);
                $css = str_replace(
                    "url(\"../fonts/{$font}\")",
                    'url("data:font/' . $type . ';base64,' . base64_encode($contents) . '")',
                    $css
                );
            }
        }

        if (!file_put_contents($path, $css)) {
            throw new \Exception('File saving error');
        }
    }
}
