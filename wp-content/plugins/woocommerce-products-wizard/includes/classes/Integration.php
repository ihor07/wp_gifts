<?php
namespace WCProductsWizard;

/**
 * Integration Class
 *
 * @class Integration
 * @version 4.8.1
 */
class Integration
{
    // <editor-fold desc="Core">
    /**
     * All significant AJAX actions
     * @var array
     */
    public static $ajaxActions = [
        'wcpwSubmit',
        'wcpwGetStep',
        'wcpwSkipStep',
        'wcpwSkipAll',
        'wcpwReset',
        'wcpwAddCartProduct',
        'wcpwRemoveCartProduct',
        'wcpwUpdateCartProduct'
    ];

    /**
     * All significant cart item keys
     * @var array
     */
    public static $cartItemKeys = [
        'wcpw_id',
        'wcpw_edit_url',
        'wcpw_product_discount',
        'wcpw_discount',
        'wcpw_kit_type',
        'wcpw_is_base_kit_product',
        'wcpw_default_price',
        'wcpw_edit_url',
        'wcpw_kit_price',
        'wcpw_kit_base_price',
        'wcpw_kit_base_price_string',
        'wcpw_kit_thumbnail_url',
        'wcpw_kit_thumbnail_path',
        'wcpw_kit_thumbnail_id',
        'wcpw_kit_parent_key',
        'wcpw_is_hidden_product',
        'wcpw_kit_id',
        'wcpw_kit_title',
        'wcpw_is_kit_root',
        'wcpw_is_kit_quantity_fixed',
        'wcpw_kit_pdf'
    ];

    /**
     * Single product add to cart form ID
     * @var string
     */
    public static $productSingleAddToCartFormId = 'woocommerce-single-product-add-to-cart-form';

    /**
     * WC template parts to handle
     * @var array
     */
    public static $templatePartsToFilter = [
        'single-product/add-to-cart/simple.php',
        'single-product/add-to-cart/variable.php'
    ];

    /** Class Constructor */
    public function __construct()
    {
        // Extra Product Options
        add_filter('woocommerce_tm_quick_view', [$this, 'epoQuickViewFilter']);
        add_action('woocommerce_init', [$this, 'reInitEPO']);
        add_filter('wcpw_add_all_to_main_cart_items', [$this, 'epoAddAllToMainCart']);
        add_filter('wcpw_cart_item_price', [$this, 'epoCartItemPriceFilter'], 10, 3);
        add_filter('wcpw_cart_item_discounted_price', [$this, 'epoCartItemDiscountedPriceFilter'], 10, 2);
        add_filter('woocommerce_get_item_data', [$this, 'epoItemDataFilter'], 10, 2);
        add_action('woocommerce_before_add_to_cart_button', [$this, 'epoBeforeAddToCartButton']);
        add_filter('woocommerce_add_cart_item_data', [$this, 'epoAddCartItemDataFilter']);
        add_filter( 'woocommerce_add_cart_item_data', 'add_custom_item_data', 10, 3 );
        add_action('tm_epo_register_addons_scripts', [$this, 'epoRegisterAddonsScripts']);

        // CF7
        add_action('wpcf7_before_send_mail', [$this, 'attachFilesOnCF7SendMail']);
        add_filter('wpcf7_form_response_output', [$this, 'CF7FormResponseOutputFilter'], 10, 4);

        // Dynamic Pricing & Discounts
        add_filter('rp_wcdpd_request_is_product_feed', [$this, 'rpRequestIsProductFeedFilter']);

        // outer woocommerce products
        add_action('woocommerce_add_to_cart', [$this, 'redirectOnProductAdd'], 100, 6);
        add_action('woocommerce_add_to_cart', [$this, 'attachToCartProduct'], 20, 6);
        add_action('woocommerce_add_cart_item_data', [$this, 'removeEditableProduct']);
        add_filter('woocommerce_add_to_cart_validation', [$this, 'addToCartValidation']);
        add_action('woocommerce_before_template_part', [$this, 'beforeTemplatePartAction']);
        add_action('woocommerce_after_template_part', [$this, 'afterTemplatePartAction']);
        add_action('wp_head', [$this, 'attachToPage']);
    }
    // </editor-fold>

    // <editor-fold desc="Outer products">
    /**
     * Before WC template output action
     *
     * @param string $name
     */
    public function beforeTemplatePartAction($name)
    {
        global $product;

        // attach wizard products to the product added to cart
        if (!$product || !$product instanceof \WC_Product) {
            return;
        }

        $productId = $product->get_id();
        $wizardId = Settings::getProduct($productId, 'attach_wizard');

        if (!$wizardId) {
            foreach (Product::getTermsIds($productId, ['all' => true]) as $categoryId) {
                $wizardId = Settings::getProductCategory($categoryId, 'attach_wizard');

                if ($wizardId) {
                    break;
                }
            }
        }

        if (!$wizardId) {
            return;
        }

        if (in_array($name, self::$templatePartsToFilter)) {
            ob_start();
        }
    }

    /**
     * After WC template output action
     *
     * @param string $name
     */
    public function afterTemplatePartAction($name)
    {
        global $product;

        // attach wizard products to the product added to cart
        if (!$product || !$product instanceof \WC_Product) {
            return;
        }

        $productId = $product->get_id();
        $wizardId = Settings::getProduct($productId, 'attach_wizard');

        if (!$wizardId) {
            foreach (Product::getTermsIds($productId, ['all' => true]) as $categoryId) {
                $wizardId = Settings::getProductCategory($categoryId, 'attach_wizard');

                if ($wizardId) {
                    break;
                }
            }
        }

        if (!$wizardId) {
            return;
        }

        if (in_array($name, self::$templatePartsToFilter)) {
            $html = ob_get_clean();
            $html = preg_replace(
                '/\<form class=/',
                '<form data-component="wcpw-form" id="' . esc_attr(self::$productSingleAddToCartFormId) . '" class=',
                $html,
                1
            );

            echo $html;
        }
    }

    /** Output wizard on a single product page */
    public function attachToPage()
    {
        $productId = get_the_ID();

        if (!$productId || get_post_type($productId) != 'product') {
            return;
        }

        // find wizards attached to products
        $wizardId = Settings::getProduct($productId, 'attach_wizard');
        $place = Settings::getProduct($productId, 'attached_wizard_place');
        $tabTitle = Settings::getProduct($productId, 'tab_title');

        if (!$wizardId) {
            foreach (Product::getTermsIds($productId, ['all' => true]) as $categoryId) {
                $wizardId = Settings::getProductCategory($categoryId, 'attach_wizard');

                if ($wizardId) {
                    $place = Settings::getProductCategory($categoryId, 'attached_wizard_place');
                    $tabTitle = Settings::getProductCategory($categoryId, 'tab_title');

                    break;
                }
            }
        }

        if (!$wizardId) {
            return;
        }

        $html = Template::html(
            'app',
            [
                'id' => $wizardId,
                'formId' => self::$productSingleAddToCartFormId,
                'attachedProduct' => $productId
            ],
            ['echo' => false]
        );

        // reset the default product query
        wp_reset_query();

        switch ($place) {
            case 'before_form': {
                add_action('woocommerce_before_add_to_cart_form', function () use ($html) {
                    echo $html;
                });

                break;
            }

            case 'after_form': {
                add_action('woocommerce_after_add_to_cart_form', function () use ($html) {
                    echo $html;
                });

                break;
            }

            case 'tab': {
                add_filter('woocommerce_product_tabs', function ($tabs) use ($html, $tabTitle) {
                    $tabs['woocommerce_products_wizard'] = [
                        'title' => $tabTitle,
                        'priority' => 5,
                        'callback' => function () use ($html) {
                            echo $html;
                        }
                    ];

                    return $tabs;
                });
            }
        }

        remove_action('wp_head', [$this, 'attachToPage']);
    }

    /**
     * Attach wizard products to the product added to cart
     *
     * @param string $cartItemKey
     * @param int $productId
     * @param int $quantity
     * @param int $variationId
     * @param array $variation
     * @param array $cartItemData
     *
     * @throws \Exception
     */
    public function attachToCartProduct($cartItemKey, $productId, $quantity, $variationId, $variation, $cartItemData)
    {
        if (did_action('wcpw_before_add_to_main_cart')
            || !isset($_REQUEST['attach-to-product'], $_REQUEST['woocommerce-products-wizard'], $_REQUEST['id'])
        ) {
            return;
        }

        $key = null;

        if (isset(WC()->cart->cart_contents[$cartItemKey])) {
            unset(WC()->cart->cart_contents[$cartItemKey]);
        }

        try {
            $productData = [
                'product_id' => $productId,
                'variation_id' => $variationId,
                'variation' => $variation,
                'quantity' => 1,
                'step_id' => Cart::$outStepId,
                'data' => $cartItemData,
                'request' => $_REQUEST,
                'sold_individually' => true,
                'has_attached_wizard' => true
            ];

            $id = (int) $_REQUEST['id'];
            $key = Cart::addProduct($id, $productData);
            $productsAdded = Instance()->form->addToMainCart($_REQUEST);

            if ($quantity > 1) {
                // multiple the products quantity
                foreach ($productsAdded as $key => $product) {
                    WC()->cart->set_quantity($key, $quantity * $product['quantity']);
                }
            }

            if (trim(Settings::getPost($id, 'final_redirect_url'))) {
                wp_safe_redirect(Settings::getFinalRedirectUrl($id));
                exit;
            }
        } catch (\Exception $exception) {
            if ($key) {
                Cart::removeByCartKey($_REQUEST['id'], $key);
            }

            Instance()->form->addNotice(
                $exception->getCode() ? $exception->getCode() : Form::getActiveStepId($_REQUEST['id']),
                [
                    'view' => 'custom',
                    'message' => $exception->getMessage()
                ]
            );

            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * Remove the product from the cart before adding it as edited
     * Solves 1 stock quantity products issue
     *
     * @param array $cartItem
     *
     * @return array
     */
    public function removeEditableProduct($cartItem)
    {
        if (did_action('wcpw_before_add_to_main_cart')
            || !isset(
                $_REQUEST['attach-to-product'],
                $_REQUEST['woocommerce-products-wizard'],
                $_REQUEST['editCartItem']
            )
        ) {
            return $cartItem;
        }

        WC()->cart->remove_cart_item($_REQUEST['editCartItem']);

        return $cartItem;
    }

    /**
     * Validate product before add to cart
     *
     * @param bool $value
     *
     * @return bool
     */
    public function addToCartValidation($value)
    {
        // don't add product to cart while it's a simple wizard action
        if (did_action('wcpw_before_add_to_main_cart')
            || !isset($_REQUEST['attach-to-product'], $_REQUEST['woocommerce-products-wizard'], $_REQUEST['id'])
        ) {
            return $value;
        }

        $actionsToSkip = [
            'add-cart-product',
            'remove-cart-product',
            'update-cart-product',
            'submit',
            'reset',
            'skip-step',
            'skip-all',
            'submit-and-skip-all',
            'get-step'
        ];

        foreach ($actionsToSkip as $action) {
            if (isset($_REQUEST[$action])) {
                return false;
            }
        }

        return $value;
    }

    /**
     * Redirect to a wizard on product add to WC cart
     *
     * @param string $cartItemKey
     * @param int $productId
     * @param int $quantity
     * @param int $variationId
     * @param array $variation
     * @param array $cartItemData
     */
    public function redirectOnProductAdd($cartItemKey, $productId, $quantity, $variationId, $variation, $cartItemData)
    {
        if (did_action('wcpw_before_add_to_main_cart')) {
            return;
        }

        $wizardId = Settings::getProduct($productId, 'redirect_on_add_to_cart');
        $stepId = Settings::getProduct($productId, 'redirect_step_id');
        $activeStepId = Settings::getProduct($productId, 'redirect_active_step_id');
        $link = Settings::getProduct($productId, 'redirect_link');

        if (!$wizardId || !$link) {
            foreach (Product::getTermsIds($productId, ['all' => true]) as $categoryId) {
                $wizardId = Settings::getProductCategory($categoryId, 'redirect_on_add_to_cart');

                if ($wizardId) {
                    $stepId = Settings::getProductCategory($categoryId, 'redirect_step_id');
                    $stepId = $stepId ? $stepId : $categoryId;
                    $activeStepId = Settings::getProductCategory($categoryId, 'redirect_active_step_id');
                    $link = Settings::getProductCategory($categoryId, 'redirect_link');

                    break;
                }
            }
        }

        if ($wizardId && $link) {
            // if url is absolute
            if (strpos($link, home_url()) === false) {
                $link = home_url() . '/' . $link;
            }

            // product data
            $productData = [
                'product_id' => $productId,
                'variation_id' => $variationId,
                'variation' => $variation,
                'quantity' => $quantity,
                'step_id' => is_numeric($stepId) ? $stepId : Cart::$outStepId,
                'data' => $cartItemData,
                'request' => $_REQUEST
            ];

            $productData = apply_filters('wcpw_redirect_to_wizard_product_data', $productData, $wizardId, $cartItemKey);

            try {
                // remove WC cart item
                WC()->cart->remove_cart_item($cartItemKey);

                // clear the wizard's cart and state, then add the new item
                Form::reset(['id' => $wizardId]);
                Cart::truncate($wizardId);

                if ($activeStepId) {
                    Form::setActiveStep($wizardId, $activeStepId);
                }

                Cart::addProduct($wizardId, $productData);

                $link = apply_filters('wcpw_redirect_to_wizard_link', $link, $wizardId, $cartItemKey, $productData);

                if (wp_doing_ajax()) {
                    // use ajax-response trick with the link
                    wp_send_json([
                        'error' => true,
                        'product_url' => $link
                    ]);
                }

                wp_redirect($link);

                // js version of redirect
                exit("<script>document.location = '$link';</script>");
            } catch (\Exception $exception) {
                exit($exception->getMessage());
            }
        }
    }
    // </editor-fold>

    // <editor-fold desc="Extra Product Options">
    /**
     * Filter EPO function value if is ajax action
     *
     * @param bool $qv
     *
     * @return bool
     */
    public function epoQuickViewFilter($qv)
    {
        if (isset($_POST['action']) && in_array($_POST['action'], self::$ajaxActions)) {
            // output EPO inline styles
            add_action('wcpw_after_output', 'wp_print_footer_scripts');

            return true;
        }

        return $qv;
    }

    /** ReInit EPO plugin functions */
    public function reInitEPO()
    {
        if (isset($_POST['action']) && in_array($_POST['action'], self::$ajaxActions)
            && (function_exists('\\TM_EPO') || function_exists('\\THEMECOMPLETE_EPO'))
        ) {
            global $wp_query;

            // required in further code of EPO
            $wp_query->is_page = true;

            if (function_exists('\\TM_EPO') && method_exists(TM_EPO(), 'init_vars')) {
                TM_EPO()->init_vars();
            } elseif (function_exists('\\THEMECOMPLETE_EPO') && method_exists(THEMECOMPLETE_EPO(), 'init_vars')) {
                THEMECOMPLETE_EPO()->init_vars();
            }

            if (function_exists('\\TM_EPO') && method_exists(TM_EPO(), 'tm_epo_fields')) {
                TM_EPO()->tm_epo_fields(get_the_ID(), get_the_ID());
            } elseif (function_exists('\\THEMECOMPLETE_EPO') && method_exists(THEMECOMPLETE_EPO(), 'tm_epo_fields')) {
                THEMECOMPLETE_EPO()->tm_epo_fields(get_the_ID(), get_the_ID());
            }

            if (function_exists('\\TM_EPO') && method_exists(TM_EPO(), 'tm_add_inline_style')) {
                TM_EPO()->tm_add_inline_style();
            } elseif (function_exists('\\THEMECOMPLETE_EPO')
                && method_exists(THEMECOMPLETE_EPO(), 'tm_add_inline_style')
            ) {
                THEMECOMPLETE_EPO()->tm_add_inline_style();
            }

            if (function_exists('\\TM_EPO') && method_exists(TM_EPO(), 'tm_epo_totals')) {
                TM_EPO()->tm_epo_totals(get_the_ID(), get_the_ID());
            } elseif (function_exists('\\THEMECOMPLETE_EPO') && method_exists(THEMECOMPLETE_EPO(), 'tm_epo_totals')) {
                THEMECOMPLETE_EPO()->tm_epo_totals(get_the_ID(), get_the_ID());
            }

            // for different versions
            if (function_exists('TM_EPO_DISPLAY')) {
                if (method_exists(TM_EPO_DISPLAY(), 'tm_epo_fields')) {
                    TM_EPO_DISPLAY()->tm_epo_fields(get_the_ID(), get_the_ID());
                }

                if (method_exists(TM_EPO_DISPLAY(), 'tm_epo_totals')) {
                    TM_EPO_DISPLAY()->tm_epo_totals(get_the_ID(), get_the_ID());
                }
            } elseif (function_exists('THEMECOMPLETE_EPO_DISPLAY')) {
                if (method_exists(THEMECOMPLETE_EPO_DISPLAY(), 'tm_epo_fields')) {
                    THEMECOMPLETE_EPO_DISPLAY()->tm_epo_fields(get_the_ID(), get_the_ID());
                }

                if (method_exists(THEMECOMPLETE_EPO_DISPLAY(), 'tm_epo_totals')) {
                    THEMECOMPLETE_EPO_DISPLAY()->tm_epo_totals(get_the_ID(), get_the_ID());
                }
            }
        }
    }

    /** Enqueue EPO scripts action */
    public function epoRegisterAddonsScripts()
    {
        $productId = get_the_ID();

        if (!$productId || get_post_type($productId) != 'product') {
            return;
        }

        // find wizards attached to products
        $wizardId = Settings::getProduct($productId, 'attach_wizard');

        if (!$wizardId) {
            foreach (Product::getTermsIds($productId, ['all' => true]) as $categoryId) {
                $wizardId = Settings::getProductCategory($categoryId, 'attach_wizard');

                if ($wizardId) {
                    break;
                }
            }
        }

        if ($wizardId && function_exists('THEMECOMPLETE_EPO')) {
            THEMECOMPLETE_EPO()->current_option_features[] = 'product';
        }
    }

    /**
     * Add to main cart filter
     *
     * @param array $items
     *
     * @return array
     */
    public function epoAddAllToMainCart($items)
    {
        foreach ($items as &$item) {
            if (!isset($item['tmcartepo']) || empty($item['tmcartepo'])) {
                continue;
            }

            foreach ($item['tmdata']['tmcartepo_data'] as $index => $value) {
                if (!empty($item['request'][$value['attribute']]) || !isset($item['tmcartepo'][$index]['value'])) {
                    continue;
                }

                $item['request'][$value['attribute']] = $item['tmcartepo'][$index]['value'];
            }
        }

        return $items;
    }

    /** Before "add to cart" button output on a product's page */
    public function epoBeforeAddToCartButton()
    {
        if (!(function_exists('\\TM_EPO') || function_exists('\\THEMECOMPLETE_EPO'))) {
            return;
        }

        if ((function_exists('\\TM_EPO') && !TM_EPO()->is_edit_mode())
            || (function_exists('\\THEMECOMPLETE_EPO') && !THEMECOMPLETE_EPO()->is_edit_mode())
        ) {
            return;
        }

        $cart = WC()->cart->get_cart();
        $cartItemKey = function_exists('\\TM_EPO')
            ? TM_EPO()->cart_edit_key
            : THEMECOMPLETE_EPO()->cart_edit_key;

        foreach (self::$cartItemKeys as $key) {
            if (isset($cart[$cartItemKey], $cart[$cartItemKey][$key])) {
                echo '<input type="hidden" name="'
                    . esc_attr($key) . '" value="'
                    . esc_attr($cart[$cartItemKey][$key]) . '" />';
            }
        }
    }

    /**
     * Add to cart product data filter
     *
     * @param array $data
     *
     * @return array
     */
    public function epoAddCartItemDataFilter($data)
    {
        foreach (self::$cartItemKeys as $key) {
            if (isset($_REQUEST[$key])) {
                $data[$key] = esc_sql($_REQUEST[$key]);
            }
        }

        return $data;
    }

    function add_custom_item_data( $cart_item_data, $product_id, $variation_id ) {
        $cart_item_data['custom_data'] = 'Custom Data Value';
        return $cart_item_data;
    }
    /**
     * Get cart item discounted price filter
     *
     * @param float $output
     * @param array $cartItem
     *
     * @return float
     */
    public function epoCartItemDiscountedPriceFilter($output, $cartItem)
    {
        if (!isset($cartItem['tm_epo_options_prices'], $cartItem['tm_epo_product_original_price'])
            || (float) !$cartItem['tm_epo_options_prices']
        ) {
            return $output;
        }

        $output = (float) $cartItem['tm_epo_product_original_price'];
        $commonDiscount = isset($cartItem['wcpw_discount']) && $cartItem['wcpw_discount']
            ? (float) $cartItem['wcpw_discount']
            : 0;

        $productDiscount = isset($cartItem['wcpw_product_discount']) && $cartItem['wcpw_product_discount']
            ? (array) $cartItem['wcpw_product_discount']
            : null;

        if (isset($cartItem['wcpw_is_base_kit_product']) && $cartItem['wcpw_is_base_kit_product']) {
            // null the base kit product price
            $output = 0;
        }

        // have some discount rule
        if (!empty($productDiscount)) {
            $output = Product::handlePriceWithDiscountRule($output, $productDiscount);
        } elseif ($commonDiscount) {
            $output = max(0, (float) $output - ((float) $output * $commonDiscount / 100));
        }

        // add options price without wizard discount
        $output += (float) $cartItem['tm_epo_options_prices'];

        return $output;
    }

    /**
     * Get cart item price filter
     *
     * @param float $output
     * @param array $cartItem
     * @param array $args
     *
     * @return float
     */
    public function epoCartItemPriceFilter($output, $cartItem, $args)
    {
        if (!isset($cartItem['tm_epo_options_prices']) || (float) !$cartItem['tm_epo_options_prices']) {
            return $output;
        }

        $product = isset($cartItem['data']) ? $cartItem['data'] : null;

        if (!$product instanceof \WC_Product) {
            return $output;
        }

        $defaults = [
            'checkTax' => Cart::displayPricesIncludesTax() || Cart::pricesIncludeTax(),
            'displayIncludeTax' => Cart::displayPricesIncludesTax(),
            'pricesIncludeTax' => Cart::pricesIncludeTax()
        ];

        $args = array_replace($defaults, $args);
        $output = (float) $cartItem['tm_epo_product_original_price'];

        // apply discount as the stored price is pure
        $output = Product::applyPriceDiscount($output, Instance()->getCurrentId(), $product);

        // add options price without wizard discount
        $output += (float) $cartItem['tm_epo_options_prices'];

        if ((did_action('wcpw_before_output') == did_action('wcpw_after_output') + 1
            || did_action('wcsf_before_output') == did_action('wcsf_after_output') + 1)
            && isset($cartItem['tmproducts']) && !empty($cartItem['tmproducts'])
        ) {
            // add attached products price
            foreach ($cartItem['tmproducts'] as $productData) {
                $child = wc_get_product($productData['product_id']);

                if (isset($productData['priced_individually']) && $productData['priced_individually']) {
                    $output += Cart::getItemPrice(['data' => $child]) * $productData['quantity'];
                }
            }
        }

        if (!$args['checkTax']) {
            return $output;
        }

        if (in_array($product->get_type(), ['subscription', 'subscription_variation'])
            && function_exists('wcs_get_price_including_tax')
            && function_exists('wcs_get_price_excluding_tax')
        ) {
            $output = $args['displayIncludeTax'] || !$args['pricesIncludeTax']
                ? wcs_get_price_including_tax($product, ['price' => $output])
                : wcs_get_price_excluding_tax($product, ['price' => $output]);
        } else {
            $output = $args['displayIncludeTax'] || !$args['pricesIncludeTax']
                ? wc_get_price_including_tax($product, ['price' => $output])
                : wc_get_price_excluding_tax($product, ['price' => $output]);
        }

        return $output;
    }

    /**
     * Item data filter
     *
     * @param array $itemData
     * @param array $cartItem
     *
     * @return array
     */
    public function epoItemDataFilter($itemData, $cartItem)
    {
        if (!Instance()->getCurrentId() || !isset($cartItem['tmproducts']) || empty($cartItem['tmproducts'])) {
            return $itemData;
        }

        // output attached products as meta
        foreach ($cartItem['tmproducts'] as $productData) {
            $product = wc_get_product($productData['product_id']);
            $pricedIndividually = isset($productData['priced_individually']) && $productData['priced_individually'];
            $price = $product->get_price('wcpw-epo-attached');
            $value = ['&times;', $productData['quantity']];
            $display = '<span class="wcpw-epo-child">'
                . ($pricedIndividually ? '<span class="wcpw-epo-child-price">'
                    . wc_price($price) . '</span> ' : '')
                . '<bdi class="wcpw-epo-child-times">&times;</bdi> '
                . "<span class=\"wcpw-epo-child-quantity\">{$cartItem['quantity']}</span></span>";

            if ($pricedIndividually) {
                array_unshift($value, $price);
            }

            $itemData[] = [
                'key' => $product->get_title(),
                'value' => implode(' ', $value),
                'display' => $display,
                'hide' => false
            ];
        }
        
        return $itemData;
    }
    // </editor-fold>

    // <editor-fold desc="CF7">
    /**
     * CF7 response HTML filter
     *
     * @param string $output
     * @param string $class
     * @param string $content
     * @param \WPCF7_ContactForm $cf7
     *
     * @return string
     */
    public function CF7FormResponseOutputFilter($output, $class, $content, $cf7)
    {
        $htmlName = $cf7->shortcode_attr('html_name');

        if (strpos($htmlName, 'wcpw-result-') !== false) {
            $id = str_replace('wcpw-result-', '', $htmlName);
            $output .= '<input type="hidden" name="wcpw-result" value="' . $id .'">';
        }

        return $output;
    }

    /**
     * Handle CF7 form before send and attach PDFs
     *
     * @param \WPCF7_ContactForm $cf7
     */
    public function attachFilesOnCF7SendMail($cf7)
    {
        if (!class_exists('\WPCF7_Submission')) {
            return;
        }

        $submission = \WPCF7_Submission::get_instance();

        if (!$submission) {
            return;
        }

        $id = (int) $submission->get_posted_data('wcpw-result');

        if (!$id) {
            return;
        }

        $formData = [];

        foreach ($submission->get_posted_data() as $key => $value) {
            $formData["[$key]"] = $value;
        }

        $pdf = Instance()->pdf->saveCart([
            'id' => $id,
            'name' => Settings::getPost($id, 'pdf_file_name', 'post', get_bloginfo('name')),
            'formData' => $formData,
            'pageSubClass' => 'is-contact-form-7'
        ]);

        if (is_callable($submission, 'add_uploaded_file')) {
            $submission->add_uploaded_file('wcpw-result-pdf', $pdf['path']);
        } elseif (method_exists($cf7, 'get_properties')) {
            $properties = $cf7->get_properties();
            $properties['mail']['attachments'] .= PHP_EOL . $pdf['path'];
            $properties['mail_2']['attachments'] .= PHP_EOL . $pdf['path'];
            $cf7->set_properties($properties);
        }
    }
    // </editor-fold>

    // <editor-fold desc="Dynamic Pricing & Discounts">
    /**
     * Apply discounts for product prices
     *
     * @param bool $value
     *
     * @return bool
     */
    public function rpRequestIsProductFeedFilter($value)
    {
        if (did_action('wcpw_before_output') == did_action('wcpw_after_output') + 1) {
            return true;
        }

        return $value;
    }
    // </editor-fold>
}
