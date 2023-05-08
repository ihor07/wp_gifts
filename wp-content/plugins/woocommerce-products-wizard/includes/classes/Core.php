<?php
namespace WCProductsWizard;

/**
 * Core Class
 *
 * @class Core
 * @version 12.0.0
 */
class Core
{
    // <editor-fold desc="Properties">
    /**
     * Self instance variable
     * @var Core The single instance of the class
     */
    protected static $instance = null;

    /**
     * Is WooCommerce active
     * @var bool
     */
    public static $wcIsActive = false;

    /**
     * Wizard post type name
     * @var string
     */
    public static $postTypeName = 'wc_product_wizard';

    /**
     * Current working wizard ID
     * @var int
     */
    public $currentId = 0;

    /**
     * Current working wizard step ID
     * @var string
     */
    private $currentStepId = '';

    /**
     * API class instance variable
     * @var API
     */
    public $api = null;

    /**
     * Storage instance variable
     * @var Storage
     */
    public $storage = null;

    /**
     * Cart instance variable
     * @var Cart
     */
    public $cart = null;

    /**
     * Admin part instance variable
     * @var Admin
     */
    protected $admin = null;

    /**
     * Template class instance variable
     * @var Template
     */
    public $template = null;

    /**
     * Product class instance variable
     * @var Product
     */
    public $product = null;

    /**
     * Order class instance variable
     * @var Order
     */
    public $order = null;

    /**
     * Settings class instance variable
     * @var Settings
     */
    public $settings = null;

    /**
     * Form class instance variable
     * @var Form
     */
    public $form = null;

    /**
     * Integration class instance variable
     * @var Integration
     */
    public $integration = null;

    /**
     * PDF class instance variable
     * @var PDF
     */
    public $pdf = null;
    // </editor-fold>

    /** Class Constructor */
    public function __construct()
    {
        self::$instance = $this;

        $this->loadClasses();

        add_action('wcpw_before', [$this, 'enqueueAssets']);
        add_action('plugins_loaded', [$this, 'loadTextDomain']);
        add_action('plugins_loaded', [$this, 'pluginsLoadedAction']);
        add_action('woocommerce_init', [$this, 'wcInitAction'], 1);
        add_action('wcpw_before_output', [$this, 'beforeOutputAction']);
        add_action('wcpw_after_output', [$this, 'afterOutputAction']);
        add_action('wcpw_shortcode', [$this, 'shortCodeAction']);

        do_action('wcpw_init', $this);
    }

    /** Load main classes */
    public function loadClasses()
    {
        // include base slave classes
        $requiredClasses = [
            'L10N',
            'API',
            'Utils',
            'Thumbnail',
            'Styles',
            'Settings',
            'PDF',
            'Storage',
            'Cart',
            'Template',
            'Form',
            'Product',
            'Order',
            'Integration'
        ];

        foreach ($requiredClasses as $requiredClass) {
            if (!class_exists('\\WCProductsWizard\\' . $requiredClass)
                && file_exists(__DIR__ . DIRECTORY_SEPARATOR . $requiredClass . '.php')
            ) {
                require_once(__DIR__ . DIRECTORY_SEPARATOR . $requiredClass . '.php');
            }
        }

        $this->api = new API();
        $this->settings = new Settings();
        $this->storage = new Storage();
        $this->cart = new Cart();
        $this->template = new Template();
        $this->integration = new Integration();
        $this->product = new Product();
        $this->order = new Order();
        $this->form = new Form();
        $this->pdf = new PDF();
    }

    /**
     * Get single class instance
     *
     * @static
     * @return Core
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get current working wizard ID
     *
     * @return int
     */
    public function getCurrentId()
    {
        return $this->currentId;
    }

    /**
     * Set current working wizard ID
     *
     * @param int $currentId
     */
    public function setCurrentId($currentId)
    {
        $this->currentId = $currentId;
    }

    /**
     * Get current working wizard step ID
     *
     * @return string
     */
    public function getCurrentStepId()
    {
        return $this->currentStepId;
    }

    /**
     * Set current working wizard step ID
     *
     * @param string $currentStepId
     */
    public function setCurrentStepId($currentStepId)
    {
        $this->currentStepId = $currentStepId;
    }

    /** Fires on plugins are loaded */
    public function pluginsLoadedAction()
    {
        self::$wcIsActive = class_exists('\WooCommerce');

        if (!self::$wcIsActive) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-warning is-dismissible"><p>'
                    . L10N::r('WooCommerce is required for WC Products Wizard')
                    . '</p></div>';
            });
        }
    }

    /** Fires on woocommerce plugin is loaded */
    public function wcInitAction()
    {
        // start WC session variable if needed
        if ($this->settings->getGlobal('store_session_in_db') && function_exists('WC')) {
            if (method_exists(WC(), 'initialize_session')) {
                WC()->initialize_session();
            }

            if (method_exists(WC()->session, 'set_customer_session_cookie')) {
                WC()->session->set_customer_session_cookie(true);
            }
        }

        // if is admin page
        if (is_admin()) {
            if (!class_exists('\\WCProductsWizard\\Admin')) {
                require_once(__DIR__ . DIRECTORY_SEPARATOR . 'Admin.php');
            }

            $this->admin = new Admin();
        }
    }

    /**
     * Short-code call action
     *
     * @param array $attributes
     */
    public function shortCodeAction($attributes)
    {
        if (!isset($attributes['id']) || empty($attributes['id'])) {
            return;
        }

        if ($this->settings::getPost($attributes['id'], 'reset_on_showing', 'post', false)) {
            $this->form::reset($attributes);
        }
    }

    /**
     * Fires on output call
     *
     * @param array $args
     */
    public function beforeOutputAction($args)
    {
        if (isset($args['id']) && $args['id']) {
            $this->setCurrentStepId((int) $args['id']);
        }
    }

    /** Fires after output call */
    public function afterOutputAction()
    {
        $this->setCurrentId(0);
        $this->setCurrentStepId('');
    }

    /**
     * Styles and scripts enqueue
     *
     * @param array $arguments
     */
    public function enqueueAssets($arguments = [])
    {
        $defaults = ['id' => null];
        $arguments = array_replace($defaults, $arguments);
        $path = WC_PRODUCTS_WIZARD_DEBUG ? 'src' : 'assets';
        $suffix = WC_PRODUCTS_WIZARD_DEBUG ? '' : '.min';
        $stylesFolder = WC_PRODUCTS_WIZARD_DEBUG ? 'scss' : 'css';
        $scriptsIncludingType = $this->settings->getGlobal('scripts_including_type');
        $includedScripts = $this->settings->getGlobal('included_scripts');
        $stylesIncludingType = $this->settings->getGlobal('styles_including_type');

        wp_enqueue_script('jquery');

        if ($scriptsIncludingType == 'single' && !WC_PRODUCTS_WIZARD_DEBUG) {
            wp_enqueue_script(
                'woocommerce-products-wizard-scripts',
                WC_PRODUCTS_WIZARD_PLUGIN_URL . 'assets/front/js/scripts.min.js',
                ['jquery'],
                WC_PRODUCTS_WIZARD_VERSION,
                true
            );
        } elseif ($scriptsIncludingType == 'multiple' || WC_PRODUCTS_WIZARD_DEBUG) {
            if (in_array('formdata-polyfill', $includedScripts)) {
                wp_enqueue_script(
                    'woocommerce-products-wizard-formdata-polyfilll',
                    WC_PRODUCTS_WIZARD_PLUGIN_URL . "$path/front/js/formdata-polyfill$suffix.js",
                    [],
                    '3.0.20',
                    true
                );
            }

            if (in_array('bootstrap-util', $includedScripts)) {
                wp_enqueue_script(
                    'woocommerce-products-wizard-bootstrap-util',
                    WC_PRODUCTS_WIZARD_PLUGIN_URL . "$path/front/js/util$suffix.js",
                    ['jquery'],
                    '4.6.1',
                    true
                );
            }

            if (in_array('bootstrap-modal', $includedScripts)) {
                $dependency = ['jquery'];

                if (in_array('bootstrap-util', $includedScripts)) {
                    $dependency[] = 'woocommerce-products-wizard-bootstrap-util';
                }

                wp_enqueue_script(
                    'woocommerce-products-wizard-bootstrap-modal',
                    WC_PRODUCTS_WIZARD_PLUGIN_URL . "$path/front/js/modal$suffix.js",
                    $dependency,
                    '4.6.1',
                    true
                );
            }

            if (in_array('sticky-kit', $includedScripts)) {
                wp_enqueue_script(
                    'woocommerce-products-wizard-sticky-kit',
                    WC_PRODUCTS_WIZARD_PLUGIN_URL . "$path/front/js/sticky-kit$suffix.js",
                    ['jquery'],
                    WC_PRODUCTS_WIZARD_VERSION,
                    true
                );
            }

            if (in_array('wNumb', $includedScripts)) {
                wp_enqueue_script(
                    'woocommerce-products-wizard-wNumb',
                    WC_PRODUCTS_WIZARD_PLUGIN_URL . "$path/front/js/wNumb$suffix.js",
                    [],
                    '1.2.0',
                    true
                );
            }

            if (in_array('nouislider', $includedScripts)) {
                wp_enqueue_script(
                    'woocommerce-products-wizard-nouislider',
                    WC_PRODUCTS_WIZARD_PLUGIN_URL . "$path/front/js/nouislider$suffix.js",
                    [],
                    '14.6.3',
                    true
                );
            }

            if (in_array('nouislider-launcher', $includedScripts)) {
                $dependency = [];

                if (in_array('wNumb', $includedScripts)) {
                    $dependency[] = 'woocommerce-products-wizard-wNumb';
                }

                if (in_array('nouislider', $includedScripts)) {
                    $dependency[] = 'woocommerce-products-wizard-nouislider';
                }

                wp_enqueue_script(
                    'woocommerce-products-wizard-nouislider-launcher',
                    WC_PRODUCTS_WIZARD_PLUGIN_URL . "$path/front/js/nouislider-launcher$suffix.js",
                    $dependency,
                    WC_PRODUCTS_WIZARD_VERSION,
                    true
                );
            }

            if (in_array('masonry', $includedScripts)) {
                wp_enqueue_script(
                    'woocommerce-products-wizard-masonry',
                    WC_PRODUCTS_WIZARD_PLUGIN_URL . "$path/front/js/masonry.pkgd$suffix.js",
                    [],
                    '4.2.2',
                    true
                );
            }

            if (in_array('app', $includedScripts)) {
                wp_enqueue_script(
                    'woocommerce-products-wizard-app',
                    WC_PRODUCTS_WIZARD_PLUGIN_URL . "$path/front/js/wcpw.jquery$suffix.js",
                    ['jquery'],
                    WC_PRODUCTS_WIZARD_VERSION,
                    true
                );
            }

            if (in_array('variation-form', $includedScripts)) {
                wp_enqueue_script(
                    'woocommerce-products-wizard-variation-form',
                    WC_PRODUCTS_WIZARD_PLUGIN_URL . "$path/front/js/wcpw-variation-form.jquery$suffix.js",
                    ['jquery'],
                    WC_PRODUCTS_WIZARD_VERSION,
                    true
                );
            }

            if (in_array('hooks', $includedScripts)) {
                $dependency = [];

                if (in_array('app', $includedScripts)) {
                    $dependency[] = 'woocommerce-products-wizard-app';
                }

                if (in_array('variation-form', $includedScripts)) {
                    $dependency[] = 'woocommerce-products-wizard-variation-form';
                }

                wp_enqueue_script(
                    'woocommerce-products-wizard-hooks',
                    WC_PRODUCTS_WIZARD_PLUGIN_URL . "$path/front/js/hooks$suffix.js",
                    $dependency,
                    WC_PRODUCTS_WIZARD_VERSION,
                    true
                );
            }
        }

        switch ($stylesIncludingType) {
            case 'custom': {
                wp_enqueue_style(
                    'woocommerce-products-wizard-full-custom',
                    WC_PRODUCTS_WIZARD_UPLOADS_URL . 'app-full-custom.css',
                    [],
                    get_option('woocommerce_products_wizard_styles_compiled_time', '')
                );

                break;
            }

            case 'full': {
                wp_enqueue_style(
                    'woocommerce-products-wizard-full',
                    WC_PRODUCTS_WIZARD_PLUGIN_URL . "$path/front/$stylesFolder/app-full$suffix.css",
                    [],
                    WC_PRODUCTS_WIZARD_VERSION
                );

                break;
            }

            case 'basic': {
                wp_enqueue_style(
                    'woocommerce-products-wizard',
                    WC_PRODUCTS_WIZARD_PLUGIN_URL . "$path/front/$stylesFolder/app$suffix.css",
                    [],
                    WC_PRODUCTS_WIZARD_VERSION
                );
            }
        }

        // WooCommerce assets versions before 3.0.0
        if (function_exists('WC') && get_option('woocommerce_enable_lightbox') === 'yes') {
            $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
            $assetsPath = str_replace(['http:', 'https:'], '', WC()->plugin_url()) . '/assets';

            wp_enqueue_script(
                'prettyPhoto',
                "$assetsPath/js/prettyPhoto/jquery.prettyPhoto$suffix.js",
                ['jquery'],
                '3.1.6',
                true
            );

            wp_enqueue_script(
                'prettyPhoto-init',
                "$assetsPath/js/prettyPhoto/jquery.prettyPhoto.init$suffix.js",
                ['prettyPhoto'],
                '3.1.6',
                true
            );

            wp_enqueue_style('woocommerce_prettyPhoto', "$assetsPath/css/prettyPhoto.css");
        }

        // init step filter assets if necessary
        foreach ($this->form::getStepsIds($arguments['id']) as $stepId) {
            if ($this->settings::getStep($arguments['id'], $stepId, 'step_filter')) {
                do_action('wcsf_before');

                break;
            }
        }
    }

    /** Load text domain */
    public function loadTextDomain()
    {
        load_plugin_textdomain(
            'woocommerce-products-wizard',
            false,
            basename(WC_PRODUCTS_WIZARD_PLUGIN_PATH) . '/languages/'
        );
    }

    // <editor-fold desc="Deprecated">
    /**
     * Get product thumbnail image or placeholder path
     *
     * @param integer $attachmentId
     * @param string $size
     *
     * @return string
     *
     * @deprecated 8.2.0
     */
    public static function getThumbnailPath($attachmentId = null, $size = 'thumbnail')
    {
        return Utils::getThumbnailPath($attachmentId, $size);
    }

    /**
     * Find and replace image src URLs by base64 version in HTML
     *
     * @param string $string
     *
     * @return string
     *
     * @deprecated 8.2.0
     */
    public static function replaceImagesToBase64InHtml($string)
    {
        return Utils::replaceImagesToBase64InHtml($string);
    }

    /**
     * Make string of attributes from array
     *
     * @param array $attributes
     *
     * @return string
     *
     * @deprecated 8.2.0
     */
    public static function attributesArrayToString($attributes)
    {
        return Utils::attributesArrayToString($attributes);
    }

    /**
     * Generate thumbnail file and save it in uploads
     *
     * @param integer $wizardId
     * @param array $cart
     *
     * @return array
     *
     * @deprecated 10.1.0
     */
    public static function generateThumbnail($wizardId, $cart = [])
    {
        return Thumbnail::generate($wizardId, $cart);
    }
    // </editor-fold>
}
