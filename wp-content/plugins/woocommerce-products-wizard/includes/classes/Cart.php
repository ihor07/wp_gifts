<?php
namespace WCProductsWizard;

/**
 * Cart Class
 *
 * @class Cart
 * @version 8.3.1
 */
class Cart
{
    // <editor-fold desc="Properties">
    /**
     * Cart content cache
     * @var array
     */
    protected static $cache = [];

    /**
     * Session key variable
     * @var string
     */
    public static $sessionKey = 'woocommerce-products-wizard-cart';

    /**
     * Array of items for a further work
     * @var array
     */
    public static $itemsBuffer = [];

    /**
     * Step ID used as out of any wizard steps
     * @var integer
     */
    public static $outStepId = 1000;

    /**
     * Properties of a compressed cart item
     * @var array
     */
    public static $compressedItemKeys = [
        'key' => 'k',
        'value' => 'va',
        'step_id' => 's',
        'product_id' => 'p',
        'variation_id' => 'v',
        'variation' => 'vn',
        'quantity' => 'q'
    ];
    // </editor-fold>

    // <editor-fold desc="Core">
    /** Class Constructor */
    public function __construct()
    {
        // actions
        add_action('wp', [$this, 'requests']);

        // cart output filters
        add_filter('woocommerce_cart_item_remove_link', [$this, 'itemRemoveLinkFilter'], 10, 2);
        add_filter('woocommerce_cart_item_quantity', [$this, 'itemQuantityFilter'], 10, 3);
        add_filter('woocommerce_cart_item_class', [$this, 'itemClass'], 10, 2);
        add_filter('woocommerce_cart_item_price', [$this, 'itemPriceFilter'], 30, 2);
        add_filter('woocommerce_cart_item_subtotal', [$this, 'itemSubTotalFilter'], 30, 2);
        add_filter('woocommerce_cart_item_thumbnail', [$this, 'itemThumbnailFilter'], 10, 3);
        add_filter('woocommerce_cart_item_name', [$this, 'itemNameFilter'], 10, 3);
        add_filter('woocommerce_cart_item_visible', [$this, 'itemVisibilityFilter'], 20, 2);
        add_filter('woocommerce_widget_cart_item_visible', [$this, 'itemVisibilityFilter'], 20, 2);
        add_filter('woocommerce_checkout_cart_item_visible', [$this, 'itemVisibilityFilter'], 20, 2);
        add_filter('woocommerce_get_item_data', [$this, 'itemDataFilter'], 10, 2);
        add_filter('woocommerce_cart_contents_count', [$this, 'contentCountFilter']);
        add_filter('woocommerce_get_cart_contents', [$this, 'contentFilter']);
        add_action('woocommerce_before_calculate_totals', [$this, 'beforeCalculateAction'], 20);

        // item quantity update
        add_action('woocommerce_after_cart_item_quantity_update', [$this, 'quantityUpdateAction'], 10, 4);

        // items remove filters
        add_action('woocommerce_remove_cart_item', [$this, 'itemRemoveAction']);
        add_action('woocommerce_cart_item_removed', [$this, 'itemAfterRemoveAction']);
        add_action('woocommerce_before_cart_item_quantity_zero', [$this, 'itemRemoveAction'], 10);
        add_action('woocommerce_before_cart_item_quantity_zero', [$this, 'itemAfterRemoveAction'], 11);

        // items restore filters
        add_action('woocommerce_restore_cart_item', [$this, 'itemRestoreAction']);
        add_action('woocommerce_cart_item_restored', [$this, 'itemAfterRestoreAction']);
    }

    /** Add request actions */
    public function requests()
    {
        // edit cart item using the wizard
        if (isset($_POST['wcpwEditCartItem']) && $_POST['wcpwEditCartItem']) {
            $cartItemKey = wc_clean($_POST['wcpwEditCartItem']);
            $cartItem = WC()->cart->get_cart_item($cartItemKey);

            if ($cartItem) {
                try {
                    // set the cart content
                    self::setContentByItem($cartItem);

                    // set the last active step
                    $stepsIds = Form::getStepsIds($cartItem['wcpw_id']);

                    Form::setActiveStep($cartItem['wcpw_id'], end($stepsIds));

                    // redirect to the wizard
                    $url = parse_url($cartItem['wcpw_edit_url']);

                    parse_str($url['query'], $query);

                    $query['wcpwEditCartItem'] = $cartItemKey;
                    $url['query'] = http_build_query($query);
                    $link = Utils::buildUrl($url);

                    wp_redirect($link);

                    // js version of redirect
                    exit("<script>document.location = '$link';</script>");
                } catch (\Exception $exception) {
                    // nothing
                }
            }
        }
    }

    /**
     * Clear cart cache
     *
     * @param integer $wizardId
     */
    public static function clearCache($wizardId = null)
    {
        if ($wizardId) {
            self::$cache[$wizardId] = [];
        } else {
            self::$cache = [];
        }
    }
    // </editor-fold>

    // <editor-fold desc="Get content">
    /**
     * Get cart from the session
     *
     * @param integer $wizardId
     * @param array $args
     *
     * @return array
     */
    public static function get($wizardId, $args = [])
    {
        do_action('wcpw_get_cart', $wizardId, $args);

        $defaults = [
            'checkDefaultContent' => true,
            'includeSteps' => [],
            'excludeSteps' => []
        ];

        $args = array_replace($defaults, $args);
        $argsHash = md5(serialize($args));

        if (isset(self::$cache[$wizardId][$argsHash])) {
            return apply_filters('wcpw_cart', self::$cache[$wizardId][$argsHash], $wizardId, $args);
        }

        if (!empty($args['includeSteps']) && !is_array($args['includeSteps'])) {
            $args['includeSteps'] = [(int) $args['includeSteps']];
        }

        if (!empty($args['excludeSteps']) && !is_array($args['excludeSteps'])) {
            $args['excludeSteps'] = [(int) $args['excludeSteps']];
        }

        $output = [];
        $stepsIds = Settings::getStepsIds($wizardId);
        $storage = (array) Storage::get(self::$sessionKey, $wizardId);

        // set session from the query arguments
        if (empty(array_filter($storage)) && isset($_REQUEST['wcpwCart']) && !empty($_REQUEST['wcpwCart'])) {
            $storage = self::parseFromString($wizardId, $_REQUEST['wcpwCart']);
            self::set($wizardId, $storage);
        }

        // set session from the default cart
        if (empty(array_filter($storage)) && $args['checkDefaultContent']) {
            $storage = self::getDefaultContent($wizardId, $args);
            self::set($wizardId, $storage);
        }

        if (!empty(array_filter($storage))) {
            foreach ($storage as $key => $item) {
                $item = is_array($item) ? $item : (array) unserialize($item);

                if (empty(array_filter($item))) {
                    continue;
                }

                // handle product
                if (isset($item['product_id'], $item['step_id']) && $item['product_id']) {
                    if ((!empty($args['includeSteps']) && !in_array((int) $item['step_id'], $args['includeSteps']))
                        || (!empty($args['excludeSteps']) && in_array((int) $item['step_id'], $args['excludeSteps']))
                    ) {
                        continue;
                    }

                    $productId = isset($item['variation_id']) && $item['variation_id']
                        ? $item['variation_id']
                        : $item['product_id'];

                    $item = apply_filters(
                        'woocommerce_get_cart_item_from_session',
                        array_merge($item, ['data' => wc_get_product($productId)]),
                        $item,
                        $key
                    );
                }

                // handle step data
                if (isset($item['key'], $item['value'])) {
                    if ((!empty($args['includeSteps']) && !in_array((int) $item['step_id'], $args['includeSteps']))
                        || (!empty($args['excludeSteps']) && in_array((int) $item['step_id'], $args['excludeSteps']))
                    ) {
                        continue;
                    }

                    $item = apply_filters('wcpw_cart_step_data', $item, $wizardId, $key);
                }

                if (!empty($item)) {
                    $output[$key] = $item;
                }
            }
        }

        // clear cart from any empty values
        $output = array_filter($output);

        // place steps data upper the products
        uasort($output, function (array $a) use ($wizardId, $args, $output) {
            return (int) (isset($a['product_id'])
                + apply_filters('wcpw_move_cart_step_data_to_end', false, $wizardId, $args, $output));
        });

        // sort cart items by steps
        $cartCopy = $output;
        $output = [];

        foreach ($stepsIds as $stepId) {
            foreach ($cartCopy as $cartItemKey => $cartItem) {
                if (!isset($cartItem['step_id']) || $cartItem['step_id'] != $stepId) {
                    continue;
                }

                $output[$cartItemKey] = $cartItem;

                unset($cartCopy[$cartItemKey]);
            }
        }

        // add items out the steps (added through a redirect)
        if (empty($args['includeSteps'])) {
            $output = array_diff_key($cartCopy, $output) + $output;
        }

        // save cache
        self::$cache[$wizardId][$argsHash] = $output;

        return apply_filters('wcpw_cart', $output, $wizardId, $args);
    }

    /**
     * Get compressed cart array
     *
     * @param integer $wizardId
     * @param array $args
     *
     * @return array
     */
    public static function getCompressed($wizardId, $args = [])
    {
        $output = [];

        foreach (self::get($wizardId, $args) as $cartItemKey => $cartItem) {
            // remove file and empty inputs
            if ((isset($cartItem['type']) && $cartItem['type'] == 'file')
                || (isset($cartItem['value']) && empty($cartItem['value']))
            ) {
                continue;
            }

            foreach ($cartItem as $key => $value) {
                // drop all other keys
                if (!isset(self::$compressedItemKeys[$key])) {
                    unset($cartItem[$key]);

                    continue;
                }

                // replace keys by compressed
                if (!empty($cartItem[$key])) {
                    $cartItem[self::$compressedItemKeys[$key]] = $cartItem[$key];
                }

                unset($cartItem[$key]);
            }

            // remove a single quantity
            if (isset($cartItem['q']) && $cartItem['q'] == 1) {
                unset($cartItem['q']);
            }

            // remove products keys
            if (isset($cartItem['k']) && $cartItem['k'] == $cartItemKey) {
                unset($cartItem['k']);
            }

            if ($cartItem && !empty(array_filter($cartItem))) {
                $output[] = array_filter($cartItem);
            }
        }

        return $output;
    }

    /**
     * Get cart steps IDs from the session
     *
     * @param integer $wizardId
     * @param array $args
     *
     * @return array
     */
    public static function getStepsIds($wizardId, $args = [])
    {
        $output = [];

        foreach (self::get($wizardId, $args) as $cartItem) {
            if (!isset($cartItem['step_id'])) {
                continue;
            }

            $output[$cartItem['step_id']] = $cartItem['step_id'];
        }

        return apply_filters('wcpw_cart_steps_ids', $output, $wizardId, $args);
    }

    /**
     * Get cart products and variations IDs
     *
     * @param integer $wizardId
     * @param array $args
     *
     * @return array
     */
    public static function getProductsAndVariationsIds($wizardId, $args = [])
    {
        $output = [];

        foreach (self::get($wizardId, $args) as $cartItem) {
            if (!isset($cartItem['product_id'])) {
                continue;
            }

            $output[] = $cartItem['product_id'];

            if (isset($cartItem['variation_id']) && !empty($cartItem['variation_id'])) {
                $output[] = $cartItem['variation_id'];
            }
        }

        return apply_filters('wcpw_cart_products_and_variations_ids', $output, $wizardId, $args);
    }

    /**
     * Get cart categories IDs
     *
     * @param integer $wizardId
     * @param array $args
     *
     * @return array
     */
    public static function getCategoriesIds($wizardId, $args = [])
    {
        $output = [];

        foreach (self::get($wizardId, $args) as $cartItem) {
            if (!isset($cartItem['product_id'])) {
                continue;
            }

            $output = array_merge($output, Product::getTermsIds($cartItem['product_id']));
        }

        $output = array_unique($output);

        return apply_filters('wcpw_cart_categories_ids', $output, $wizardId, $args);
    }

    /**
     * Get cart products attribute values
     *
     * @param integer $wizardId
     * @param string $attribute
     * @param array $args
     *
     * @return array
     */
    public static function getAttributeValues($wizardId, $attribute, $args = [])
    {
        $output = [];
        $attribute = ltrim($attribute, 'pa_'); // unified workflow

        foreach (self::get($wizardId, $args) as $cartItem) {
            if (!isset($cartItem['product_id'])) {
                continue;
            }

            if (isset($cartItem['variation_id']) && $cartItem['variation_id']
                && ($variation = wc_get_product($cartItem['variation_id'])) && $variation
                && ($variationAttribute = $variation->get_attribute($attribute)) && $variationAttribute
                && ($variationTerm = get_term_by('name', $variationAttribute, "pa_{$attribute}")) && $variationTerm
            ) {
                // try to define specific variation attribute value
                $output[] = $variationTerm->term_id;
            } else {
                // just get all product attribute values
                $output = array_merge(
                    $output,
                    Product::getTermsIds($cartItem['product_id'], ['taxonomy' => "pa_{$attribute}"])
                );
            }
        }

        $output = array_unique($output);

        return apply_filters('wcpw_cart_attribute_values', $output, $wizardId, $attribute, $args);
    }

    /**
     * Get cart by step ID
     *
     * @param integer $wizardId
     * @param integer|string $stepId
     *
     * @return array
     */
    public static function getByStepId($wizardId, $stepId)
    {
        $output = self::get($wizardId, ['includeSteps' => [$stepId]]);

        return apply_filters('wcpw_cart_by_step_id', $output, $wizardId, $stepId);
    }

    /**
     * Get default cart from setting
     *
     * @param integer $wizardId
     * @param array $args
     *
     * @return array
     */
    public static function getDefaultContent($wizardId, $args = [])
    {
        $output = (array) get_post_meta($wizardId, '_default_cart_content', 1);

        return (array) apply_filters('wcpw_default_cart_content', $output, $wizardId, $args);
    }
    // </editor-fold>

    // <editor-fold desc="Get items">
    /**
     * Get cart item by key
     *
     * @param integer $wizardId
     * @param string $key
     *
     * @return array
     */
    public static function getItemByKey($wizardId, $key)
    {
        $cart = self::get($wizardId);
        $output = isset($cart[$key]) ? $cart[$key] : null;

        return apply_filters('wcpw_cart_item_by_key', $output, $wizardId, $key);
    }

    /**
     * Get cart product data by ID
     *
     * @param integer $wizardId
     * @param integer $productId
     * @param null|integer|string $stepId - for specific step only
     *
     * @return bool|null
     */
    public static function getProductById($wizardId, $productId, $stepId = null)
    {
        $output = null;

        foreach (self::get($wizardId) as $cartItem) {
            if (isset($cartItem['product_id'], $cartItem['step_id'])
                && $cartItem['product_id'] == $productId && (!$stepId || $cartItem['step_id'] == $stepId)
            ) {
                $output = $cartItem;

                break;
            }
        }

        return apply_filters('wcpw_cart_product_by_id', $output, $wizardId, $productId, $stepId);
    }

    /**
     * Get cart product data by variation data
     *
     * @param integer $wizardId
     * @param integer $variationId
     * @param array $variation
     * @param null|integer|string $stepId - for specific step only
     *
     * @return array|null
     */
    public static function getProductByVariationData($wizardId, $variationId, $variation, $stepId = null)
    {
        $output = null;

        foreach (self::get($wizardId) as $cartItemKey => $cartItem) {
            if (isset($cartItem['variation_id']) && $cartItem['variation_id'] == $variationId
                && $variation == $cartItem['variation'] && (!$stepId || $cartItem['step_id'] == $stepId)
            ) {
                $output = $cartItem;

                break;
            }
        }

        return apply_filters('wcpw_cart_product_by_variation_data', $output, $wizardId, $variationId, $variation, $stepId); // phpcs:ignore
    }

    /**
     * Get cart step data by key
     *
     * @param integer $wizardId
     * @param string $key
     * @param null|integer|string $stepId - for specific step only
     *
     * @return bool|null
     */
    public static function getStepDataByKey($wizardId, $key, $stepId = null)
    {
        $output = null;

        foreach (self::get($wizardId) as $cartItemKey => $cartItem) {
            if (isset($cartItem['key']) && $cartItem['key'] == $key && (!$stepId || $cartItem['step_id'] == $stepId)) {
                $output = $cartItem;

                break;
            }
        }

        return apply_filters('wcpw_cart_step_data_by_key', $output, $wizardId, $key, $stepId);
    }
    // </editor-fold>

    // <editor-fold desc="Get keys">
    /**
     * Get cart array key by product ID
     *
     * @param integer $wizardId
     * @param integer $productId
     * @param null|integer|string $stepId - for specific step only
     *
     * @return bool|null
     */
    public static function getKeyByProductId($wizardId, $productId, $stepId = null)
    {
        $output = null;

        foreach (self::get($wizardId) as $cartItemKey => $cartItem) {
            if (isset($cartItem['product_id'], $cartItem['step_id'])
                && $cartItem['product_id'] == $productId && (!$stepId || $cartItem['step_id'] == $stepId)
            ) {
                $output = $cartItemKey;

                break;
            }
        }

        return apply_filters('wcpw_cart_key_by_product_id', $output, $wizardId, $productId, $stepId);
    }

    /**
     * Get cart array key by product variation data
     *
     * @param integer $wizardId
     * @param integer $variationId
     * @param array $variation
     * @param null|integer|string $stepId - for specific step only
     *
     * @return bool|null
     */
    public static function getKeyByVariationData($wizardId, $variationId, $variation, $stepId = null)
    {
        $output = null;

        foreach (self::get($wizardId) as $cartItemKey => $cartItem) {
            if (isset($cartItem['variation_id']) && $cartItem['variation_id'] == $variationId
                && $variation == $cartItem['variation'] && (!$stepId || $cartItem['step_id'] == $stepId)
            ) {
                $output = $cartItemKey;

                break;
            }
        }

        return apply_filters('wcpw_cart_key_by_product_variation_data', $output, $wizardId, $variationId, $variation, $stepId); // phpcs:ignore
    }

    /**
     * Get cart array key by step data key
     *
     * @param integer $wizardId
     * @param string $key
     * @param null|integer|string $stepId - for specific step only
     *
     * @return bool|null
     */
    public static function getKeyByStepDataKey($wizardId, $key, $stepId = null)
    {
        $output = null;

        foreach (self::get($wizardId) as $cartItemKey => $cartItem) {
            if (isset($cartItem['key']) && $cartItem['key'] == $key && (!$stepId || $cartItem['step_id'] == $stepId)) {
                $output = $cartItemKey;

                break;
            }
        }

        return apply_filters('wcpw_cart_key_by_step_data_key', $output, $wizardId, $key, $stepId);
    }
    // </editor-fold>

    // <editor-fold desc="Get items data">
    /**
     * Get product meta data string
     *
     * @param array $cartItem
     * @param bool $flat
     *
     * @return string
     */
    public static function getProductMeta($cartItem, $flat = false)
    {
        if (isset($cartItem['variation']) && is_array($cartItem['variation'])) {
            foreach ($cartItem['variation'] as &$variationsItem) {
                $variationsItem = urldecode($variationsItem);
            }

            unset($variationsItem);
        }

        if (function_exists('wc_get_formatted_cart_item_data')) {
            $output = wc_get_formatted_cart_item_data($cartItem, $flat);
        } else {
            $output = WC()->cart->get_item_data($cartItem, $flat);
        }

        return apply_filters('wcpw_cart_product_meta', $output, $cartItem);
    }

    /**
     * Get kit child data array
     *
     * @param array $cartItem
     * @param int $wizardId
     * @param array $args
     *
     * @return array
     */
    public static function getKitChildData($cartItem, $wizardId, $args = [])
    {
        $defaults = [
            'uploadsSourceType' => 'basename',
            'uploadsImagesAsTag' => true
        ];

        $args = array_replace($defaults, $args);
        $key = null;
        $value = null;
        $display = null;

        if (isset($cartItem['data']) && $cartItem['data'] instanceof \WC_Product) {
            $hidePrices = Settings::getPost($wizardId, 'hide_prices')
                || !Settings::getStep($wizardId, $cartItem['step_id'], 'show_item_price');

            $data = wc_get_formatted_cart_item_data($cartItem, true);
            $price = wc_price(self::getItemDiscountedPrice($cartItem));
            $key = $cartItem['data']->get_name();
            $valueParts = [trim(preg_replace("/\r|\n/", ', ', $data)), $price, '&times;', $cartItem['quantity']];
            $value = implode(' ', $valueParts);
            $display = "<span class=\"wcpw-kit-child is-id-{$cartItem['data']->get_id()}\">"
                . "<span class=\"wcpw-kit-child-meta\">{$data}</span> "
                . ($hidePrices ? '' : "<span class=\"wcpw-kit-child-price\">{$price}</span> ")
                . '<bdi class="wcpw-kit-child-times">&times;</bdi> '
                . "<span class=\"wcpw-kit-child-quantity\">{$cartItem['quantity']}</span></span>";
        } elseif (isset($cartItem['value']) && $cartItem['value']) {
            $key = $cartItem['key'];
            $value = $cartItem['value'];
            $display = $cartItem['display_value'];

            if ($args['uploadsImagesAsTag'] && isset($cartItem['is_image']) && $cartItem['is_image']) {
                $display = $cartItem['display_value'];
            } elseif ($args['uploadsSourceType'] == 'link' && isset($cartItem['url'], $cartItem['name'])) {
                $display = '<a href="' . esc_attr($cartItem['url']) . '" rel="nofollow">' . basename($cartItem['name'])
                    . '</a>';
            } elseif ($args['uploadsSourceType'] == 'url' && isset($cartItem['url'])) {
                $display = $cartItem['url'];
            } elseif ($args['uploadsSourceType'] == 'path' && isset($cartItem['path'])) {
                $display = $cartItem['path'];
            } elseif ($args['uploadsSourceType'] == 'name' && isset($cartItem['name'])) {
                $display = $cartItem['name'];
            } elseif ($args['uploadsSourceType'] == 'basename' && isset($cartItem['name'])) {
                $display = basename($cartItem['name']);
            }
        }

        $output = [
            'key' => $key,
            'value' => apply_filters('wcpw_cart_kit_child_value_parts', $value, $cartItem, $wizardId, $args),
            'display' => apply_filters('wcpw_cart_kit_child_display', $display, $cartItem, $wizardId, $args),
            'hidden' => false
        ];

        return apply_filters('wcpw_kit_child_data', $output);
    }
    // </editor-fold>

    // <editor-fold desc="Items isset">
    /**
     * Check product is in the cart
     *
     * @param integer $wizardId
     * @param integer $productId
     * @param null|integer|string $stepId - for specific step only
     *
     * @return bool
     */
    public static function productIsset($wizardId, $productId, $stepId = null)
    {
        return (bool) self::getProductById($wizardId, $productId, $stepId);
    }

    /**
     * Check product variation is in the cart
     *
     * @param integer $wizardId
     * @param integer $variationId
     * @param array $variation
     * @param null|integer|string $stepId - for specific step only
     *
     * @return bool
     */
    public static function variationIsset($wizardId, $variationId, $variation, $stepId = null)
    {
        return (bool) self::getKeyByVariationData($wizardId, $variationId, $variation, $stepId);
    }

    /**
     * Check product category is in the cart
     *
     * @param integer $wizardId
     * @param integer $categoryId
     *
     * @return bool
     */
    public static function categoryIsset($wizardId, $categoryId)
    {
        return in_array($categoryId, self::getCategoriesIds($wizardId));
    }
    // </editor-fold>

    // <editor-fold desc="Add content">
    /**
     * Add a product to the cart
     *
     * @param integer $wizardId
     * @param array $itemData - product data
     *
     * @return string - cart item key
     *
     * @throws \Exception
     */
    public static function addProduct($wizardId, $itemData)
    {
        $defaults = [
            'step_id' => null,
            'product_id' => null,
            'quantity' => null,
            'variation_id' => '',
            'variation' => [],
            'data' => null,
            'request' => []
        ];

        $itemData = $cartItem = array_replace($defaults, $itemData);

        // ensure we don't add a variation to the cart directly by variation ID
        $id = (isset($cartItem['variation_id']) && !empty($cartItem['variation_id']))
            ? (int) $cartItem['variation_id']
            : (int) $cartItem['product_id'];

        $variation = isset($cartItem['variation']) ? $cartItem['variation'] : [];
        $variationId = (isset($cartItem['variation_id']) && !empty($cartItem['variation_id']))
            ? $cartItem['variation_id']
            : 0;

        $cartItem['data'] = !empty($cartItem['data']) ? $cartItem['data'] : [];
        $cartItem['data']['step_id'] = $cartItem['step_id'];

        // emulate post data passing
        if (!empty($cartItem['request'])) {
            $_POST = array_merge($_POST, $cartItem['request']);
        }

        // load cart item data - might be added by other plugins
        $cartItemData = (array) apply_filters(
            'woocommerce_add_cart_item_data',
            $cartItem['data'],
            $cartItem['product_id'],
            $variationId,
            $cartItem['quantity']
        );

        // sanitize variations
        if (isset($variation) && is_array($variation)) {
            foreach ($variation as &$variationItem) {
                $variationItem = sanitize_text_field($variationItem);
            }
        }

        // generate key
        $cartItemKey = WC()->cart->generate_cart_id(
            $cartItem['product_id'],
            $variationId,
            $variation,
            $cartItemData
        );

        $cartItem = array_merge(
            $cartItemData,
            [
                'key' => $cartItemKey,
                'product_id' => $cartItem['product_id'],
                'variation_id' => isset($cartItem['variation_id']) ? $cartItem['variation_id'] : '',
                'variation' => isset($cartItem['variation']) ? $cartItem['variation'] : [],
                'step_id' => $cartItem['step_id'],
                'wizard_id' => $wizardId,
                'quantity' => (float) $cartItem['quantity'],
                'request' => $cartItem['request'],
                'sold_individually' => isset($cartItem['sold_individually'])
                    ? $cartItem['sold_individually']
                    : Settings::getStep($wizardId, $cartItem['step_id'], 'sold_individually'),
                'data' => wc_get_product($id)
            ]
        );

        if (isset($itemData['has_attached_wizard']) && $itemData['has_attached_wizard']) {
            $cartItem['has_attached_wizard'] = $itemData['has_attached_wizard'];
        }

        $cartItem = apply_filters('woocommerce_add_cart_item', $cartItem, $cartItemKey);
        $cartItem = apply_filters('wcpw_add_to_cart_item', $cartItem, $wizardId, self::$sessionKey);
        $skipProducts = (array) Settings::getStep($wizardId, $cartItem['step_id'], 'dont_add_to_cart_products');

        do_action('wcpw_before_add_to_cart', $wizardId, $cartItemKey, $cartItem);

        // add to the session variable
        self::set($wizardId, $cartItem, $cartItemKey);

        if (Settings::getPost($wizardId, 'reflect_in_main_cart')
            && !Settings::getPost($wizardId, 'group_products_into_kits')
            && !Settings::getStep($wizardId, $cartItem['step_id'], 'dont_add_to_cart')
            && !in_array($cartItem['product_id'], $skipProducts)
            && (!isset($cartItem['variation_id']) || !$cartItem['variation_id']
                || !in_array($cartItem['variation_id'], $skipProducts))
        ) {
            // reflect to the main cart
            $stepTitle = Settings::getStep($id, $cartItem['step_id'], 'title');
            $itemData['data']['wcpw_id'] = $wizardId;
            $itemData['data']['wcpw_is_cart_bond'] = true;
            $itemData['data']['wcpw_step_id'] = $cartItem['step_id'];
            $itemData['data']['wcpw_step_name'] = $stepTitle ? $stepTitle : $cartItem['step_id'];

            if (Settings::getPost($wizardId, 'enable_checkout_step')) {
                $itemData['data']['wcpw_inner_checkout'] = $wizardId;
            }

            Product::addToMainCart($itemData);
        }

        // clear caches
        self::clearCache($wizardId);
        Utils::clearAvailabilityRulesCache($wizardId);

        do_action('wcpw_after_add_to_cart', $wizardId, $cartItemKey, $cartItem);

        return $cartItemKey;
    }

    /**
     * Add a step data line to the cart
     *
     * @param integer $wizardId
     * @param array $data - step data
     *
     * @return string
     */
    public static function addStepData($wizardId, $data)
    {
        $defaults = [
            'key' => null,
            'step_id' => null,
            'wizard_id' => $wizardId,
            'value' => null,
            'display_value' => null,
            'is_image' => false,
            'type' => 'string'
        ];

        $cartItem = array_replace($defaults, $data);
        $stepInputsPrice = (array) Settings::getPost($wizardId, 'step_inputs_price');
        $commonPrice = null;

        // find out the input price
        foreach ($stepInputsPrice as $stepInputPrice) {
            if (!isset($stepInputPrice['key'], $stepInputPrice['price'])
                || empty($stepInputPrice['key']) || empty($stepInputPrice['price'])
                || $stepInputPrice['key'] != $cartItem['key']
            ) {
                continue;
            }

            if (empty($stepInputPrice['value'])) {
                $commonPrice = (float) $stepInputPrice['price'];
            } elseif ((is_string($cartItem['value']) && $cartItem['value'] == $stepInputPrice['value'])
                || (is_array($cartItem['value']) && in_array($stepInputPrice['value'], $cartItem['value']))
            ) {
                $cartItem['price'] = (float) $stepInputPrice['price'];
            }
        }

        // has price
        if (!isset($cartItem['price']) && !is_null($commonPrice)) {
            $cartItem['price'] = $commonPrice;
        }

        // is file
        if ($cartItem['type'] == 'file' && is_string($cartItem['value'])) {
            $cartItem['path'] = $cartItem['value'];
            $cartItem['url'] = WC_PRODUCTS_WIZARD_UPLOADS_URL
                . str_replace(WC_PRODUCTS_WIZARD_UPLOADS_PATH, '', $cartItem['value']);

            if ($cartItem['is_image']) {
                // is an image
                $size = getimagesize((string) $cartItem['path']);
                $attributes = [
                    'alt' => basename((string) $cartItem['value']),
                    'src' => $cartItem['url'],
                    'width' => $size[0],
                    'height' => $size[1]
                ];

                $attributes = apply_filters('wcpw_cart_step_data_image_attributes', $attributes, $wizardId, $cartItem);
                $cartItem['display_value'] = '<img ' . Utils::attributesArrayToString($attributes) . '>';
            } elseif (isset($cartItem['name'])) {
                // any other file
                $cartItem['display_value'] = basename($cartItem['name']);
            }
        }

        // different value types to display
        if (is_null($cartItem['display_value'])) {
            if (is_array($cartItem['value'])) {
                $cartItem['display_value'] = wp_unslash(implode(', ', $cartItem['value']));
            } else {
                $cartItem['display_value'] = wp_unslash($cartItem['value']);
            }
        }

        $cartItemKey = $cartItem['step_id'] . '-' . $cartItem['key'];
        $cartItem = apply_filters('wcpw_add_to_cart_item', $cartItem, $wizardId, self::$sessionKey);

        do_action('wcpw_before_add_to_cart', $wizardId, $cartItemKey, $cartItem);

        self::set($wizardId, $cartItem, $cartItemKey);

        // clear caches
        self::clearCache($wizardId);
        Utils::clearAvailabilityRulesCache($wizardId);

        do_action('wcpw_after_add_to_cart', $wizardId, $cartItemKey, $cartItem);

        return $cartItemKey;
    }
    // </editor-fold>

    // <editor-fold desc="Remove content">
    /**
     * Remove cart item by the cart array item key
     *
     * @param integer $wizardId
     * @param integer|string $key
     *
     * @return bool
     */
    public static function removeByCartKey($wizardId, $key)
    {
        do_action('wcpw_before_remove_by_cart_key', $wizardId, $key);

        if (Settings::getPost($wizardId, 'reflect_in_main_cart')
            && apply_filters('wcpw_remove_main_cart_reflected_products', true)
        ) {
            // reflect to the main cart
            $product = Storage::get(self::$sessionKey, $wizardId, $key);

            if (isset($product['product_id']) && $product['product_id']) {
                foreach (WC()->cart->get_cart() as $cartItemKey => $cartItem) {
                    if (isset($cartItem['wcpw_id'], $cartItem['wcpw_is_cart_bond'], $cartItem['product_id'])
                        && $cartItem['product_id'] == $product['product_id']
                        && $cartItem['wcpw_id'] == $wizardId
                        && $cartItem['wcpw_step_id'] == $product['step_id']
                        && $cartItem['wcpw_is_cart_bond']
                    ) {
                        WC()->cart->remove_cart_item($cartItemKey);
                    }
                }
            }
        }

        self::$itemsBuffer[] = Storage::get(self::$sessionKey, $wizardId, $key);
        Storage::remove(self::$sessionKey, $wizardId, $key);

        // clear caches
        self::clearCache($wizardId);
        Utils::clearAvailabilityRulesCache($wizardId);

        do_action('wcpw_after_remove_by_cart_key', $wizardId, $key);

        return true;
    }

    /**
     * Remove product from the cart by the product id
     *
     * @param integer $wizardId
     * @param integer|string $productId
     * @param null|integer|string $stepId - for specific step only
     *
     * @return bool
     */
    public static function removeByProductId($wizardId, $productId, $stepId = null)
    {
        do_action('wcpw_before_remove_by_product_id', $wizardId, $productId, $stepId);

        $keyToRemove = self::getKeyByProductId($wizardId, $productId, $stepId);

        if (!$keyToRemove) {
            return false;
        }

        self::removeByCartKey($wizardId, $keyToRemove);

        // clear caches
        self::clearCache($wizardId);
        Utils::clearAvailabilityRulesCache($wizardId);

        do_action('wcpw_after_remove_by_product_id', $wizardId, $productId, $stepId);

        return true;
    }

    /**
     * Remove product from the cart by variation data
     *
     * @param integer $wizardId
     * @param integer|string $variationId
     * @param array $variation
     * @param null|integer|string $stepId - for specific step only
     *
     * @return bool
     */
    public static function removeByVariation($wizardId, $variationId, $variation, $stepId = null)
    {
        do_action('wcpw_before_remove_by_variation', $wizardId, $variationId, $variation, $stepId);

        $keyToRemove = self::getKeyByVariationData($wizardId, $variationId, $variation, $stepId);

        if (!$keyToRemove) {
            return false;
        }

        self::removeByCartKey($wizardId, $keyToRemove);

        // clear caches
        self::clearCache($wizardId);
        Utils::clearAvailabilityRulesCache($wizardId);

        do_action('wcpw_after_remove_by_variation', $wizardId, $variationId, $variation, $stepId);

        return true;
    }

    /**
     * Remove step data from the cart by key
     *
     * @param integer $wizardId
     * @param string $key
     * @param null|integer|string $stepId - for specific step only
     *
     * @return bool
     */
    public static function removeByStepDataKey($wizardId, $key, $stepId = null)
    {
        do_action('wcpw_before_remove_by_step_data_key', $wizardId, $key, $stepId);

        $keyToRemove = self::getKeyByStepDataKey($wizardId, $key, $stepId);

        if (!$keyToRemove) {
            return false;
        }

        self::removeByCartKey($wizardId, $keyToRemove);

        // clear caches
        self::clearCache($wizardId);
        Utils::clearAvailabilityRulesCache($wizardId);

        do_action('wcpw_after_remove_by_step_data_key', $wizardId, $key, $stepId);

        return true;
    }

    /**
     * Remove items from the cart by step Id
     *
     * @param integer $wizardId
     * @param integer|string $stepId
     * @param array $args
     */
    public static function removeByStepId($wizardId, $stepId, $args = [])
    {
        $defaults = [
            'removeProducts' => true,
            'removeStepData' => true
        ];

        $args = array_replace($defaults, $args);

        do_action('wcpw_before_remove_by_step_id', $wizardId, $stepId, $args);

        foreach (self::get($wizardId) as $cartItemKey => $cartItem) {
            if (($cartItem['step_id'] == $stepId)
                && (($args['removeProducts'] && isset($cartItem['product_id']) && $cartItem['product_id'])
                || ($args['removeStepData'] && isset($cartItem['key'], $cartItem['value']) && $cartItem['value']))
            ) {
                self::removeByCartKey($wizardId, $cartItemKey);
            }
        }

        // clear caches
        self::clearCache($wizardId);
        Utils::clearAvailabilityRulesCache($wizardId);

        do_action('wcpw_after_remove_by_step_id', $wizardId, $stepId, $args);
    }

    /**
     * Truncate the cart
     *
     * @param integer $wizardId
     */
    public static function truncate($wizardId)
    {
        do_action('wcpw_before_truncate', $wizardId);

        if (Settings::getPost($wizardId, 'reflect_in_main_cart')
            && apply_filters('wcpw_remove_main_cart_reflected_products', true)
        ) {
            // reflect to the main cart
            $wcCart = WC()->cart->get_cart();

            foreach ($wcCart as $cartItemKey => $cartItem) {
                if (isset($cartItem['wcpw_id'], $cartItem['wcpw_is_cart_bond'])
                    && $cartItem['wcpw_id'] == $wizardId && $cartItem['wcpw_is_cart_bond']
                ) {
                    WC()->cart->remove_cart_item($cartItemKey);
                }
            }
        }

        Storage::remove(self::$sessionKey, $wizardId);

        // clear caches
        self::clearCache($wizardId);
        Utils::clearAvailabilityRulesCache($wizardId);

        do_action('wcpw_after_truncate', $wizardId);
    }
    // </editor-fold>

    // <editor-fold desc="Remove content actions">
    /**
     * Woocommerce cart item removing action
     *
     * @param string $itemKey
     */
    public function itemRemoveAction($itemKey)
    {
        // avoid for recursion of actions calls
        add_filter('wcpw_remove_main_cart_reflected_products', '__return_false');

        $cart = WC()->cart->get_cart();
        $itemData = WC()->cart->get_cart_item($itemKey);

        // remove this product from the wizard with the reflecting cart option
        if (isset($itemData['wcpw_id'], $itemData['wcpw_is_cart_bond']) && $itemData['wcpw_is_cart_bond']) {
            self::removeByProductId($itemData['wcpw_id'], $itemData['product_id']);
        }

        // remove products from the same kit
        if (isset($itemData['wcpw_kit_id']) && $itemData['wcpw_kit_id'] && $itemData['wcpw_is_kit_root']) {
            foreach ($cart as $cartItemKey => $cartItem) {
                if (!isset($cartItem['wcpw_kit_id'])
                    || $cartItem['wcpw_is_kit_root']
                    || $cartItem['wcpw_kit_id'] != $itemData['wcpw_kit_id']
                    || $itemKey == $cartItemKey
                ) {
                    continue;
                }

                self::$itemsBuffer[] = $cartItemKey;
            }
        }
    }

    /** Woocommerce cart item after removing action */
    public function itemAfterRemoveAction()
    {
        // remove all items in the buffer
        if (!empty(self::$itemsBuffer)) {
            foreach (self::$itemsBuffer as $cartItemKey) {
                WC()->cart->remove_cart_item($cartItemKey);
            }

            // clear buffer
            self::$itemsBuffer = [];
        }
    }

    /**
     * Woocommerce cart item restoring action
     *
     * @param string $itemKey
     */
    public function itemRestoreAction($itemKey)
    {
        $removed = WC()->cart->get_removed_cart_contents();
        $itemData = $removed[$itemKey];

        // restore products from the same kit
        if (isset($itemData['wcpw_kit_id']) && $itemData['wcpw_kit_id'] && $itemData['wcpw_is_kit_root']) {
            foreach ($removed as $cartItemKey => $cartItem) {
                if (!isset($cartItem['wcpw_kit_id'])
                    || $cartItem['wcpw_is_kit_root']
                    || $cartItem['wcpw_kit_id'] != $itemData['wcpw_kit_id']
                    || $itemKey == $cartItemKey
                ) {
                    continue;
                }

                self::$itemsBuffer[] = $cartItemKey;
            }
        }
    }

    /** Woocommerce cart item after restoring action */
    public function itemAfterRestoreAction()
    {
        // restore all items in the buffer
        if (!empty(self::$itemsBuffer)) {
            foreach (self::$itemsBuffer as $cartItemKey) {
                WC()->cart->restore_cart_item($cartItemKey);
            }

            // clear buffer
            self::$itemsBuffer = [];
        }
    }
    // </editor-fold>

    // <editor-fold desc="Get price">
    /**
     * Get the total value of the cart
     *
     * @param integer $wizardId
     * @param array $args
     *
     * @return float
     */
    public static function getTotal($wizardId, $args = [])
    {
        $defaults = [
            'cart' => null,
            'reCalculateDiscount' => false
        ];
        
        $args = array_replace($defaults, $args);

        $cart = is_null($args['cart']) ? self::get($wizardId) : $args['cart'];
        $groupProductsIntoKits = Settings::getPost($wizardId, 'group_products_into_kits');
        $kitsType = Settings::getPost($wizardId, 'kits_type');
        $kitBasePrice = Settings::getPost($wizardId, 'kit_base_price');
        $output = $groupProductsIntoKits && $kitsType == 'combined' && $kitBasePrice
            ? (float) $kitBasePrice
            : 0;

        foreach ($cart as $cartItem) {
            // should be step input or existent product with qty
            if (!((isset($cartItem['key'], $cartItem['value'], $cartItem['price']) && $cartItem['price'])
                || (isset($cartItem['data'], $cartItem['quantity']) && $cartItem['data'] && $cartItem['quantity'] > 0
                    && ($product = $cartItem['data']) && $product instanceof \WC_Product && $product->exists()))
            ) {
                continue;
            }

            if ($args['reCalculateDiscount']) {
                $price = self::getItemDiscountedPrice($cartItem);
            } else {
                $price = self::getItemPrice($cartItem);
            }

            $output += (float) ($price * (isset($cartItem['quantity']) ? $cartItem['quantity'] : 1));
        }

        return apply_filters('wcpw_cart_total', $output, $wizardId);
    }

    /**
     * Get the total price string of the cart
     *
     * @param integer $wizardId
     * @param array $args
     *
     * @return string
     */
    public static function getTotalPrice($wizardId, $args = [])
    {
        $output = wc_price(self::getTotal($wizardId, $args));

        if (self::displayPricesIncludesTax()) {
            if (!self::pricesIncludeTax() && WC()->cart->get_subtotal_tax() > 0) {
                $output .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat()
                    . '</small>';
            }
        } else {
            if (self::pricesIncludeTax() && WC()->cart->get_subtotal_tax() > 0) {
                $output .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat()
                    . '</small>';
            }
        }

        return apply_filters('wcpw_cart_total_price', $output, $wizardId);
    }

    /**
     * Get root item kit base price
     *
     * @param array $cartItem
     * @param array $args
     *
     * @return float
     */
    public static function getItemKitBasePrice($cartItem, $args = [])
    {
        if (!isset($cartItem['wcpw_kit_base_price']) || !$cartItem['wcpw_kit_base_price']) {
            return 0;
        }

        $defaults = [
            'checkTax' => self::displayPricesIncludesTax() || self::pricesIncludeTax(),
            'displayIncludeTax' => self::displayPricesIncludesTax(),
            'pricesIncludeTax' => self::pricesIncludeTax()
        ];

        $args = array_replace($defaults, $args);

        return self::modifyPriceAccordingItemTaxes($cartItem['wcpw_kit_base_price'], $cartItem, $args);
    }

    /**
     * Get root item step input children price
     *
     * @param array $cartItem
     *
     * @return float
     */
    public static function getItemStepInputChildrenPrice($cartItem)
    {
        $output = 0;

        if (isset($cartItem['wcpw_kit_children']) && is_array($cartItem['wcpw_kit_children'])
            && !empty($cartItem['wcpw_kit_children'])
        ) {
            foreach ($cartItem['wcpw_kit_children'] as $child) {
                if (!isset($child['key'], $child['value'])) {
                    continue;
                }

                $output += self::getItemPrice($child, ['parentItem' => $cartItem]);
            }
        }

        return $output;
    }

    /**
     * Get pure item price
     *
     * @param array $cartItem
     * @param array $args
     *
     * @return float
     */
    public static function getItemPrice($cartItem, $args = [])
    {
        $defaults = [
            'parentItem' => null,
            'checkTax' => self::displayPricesIncludesTax() || self::pricesIncludeTax(),
            'displayIncludeTax' => self::displayPricesIncludesTax(),
            'pricesIncludeTax' => self::pricesIncludeTax(),
            'context' => 'view'
        ];

        $args = array_replace($defaults, $args);
        $output = 0;

        // is step input
        if (isset($cartItem['key'], $cartItem['value'], $cartItem['price']) && $cartItem['price']) {
            $output = $cartItem['price'];

            if ($args['parentItem']) {
                $output = self::modifyPriceAccordingItemTaxes($output, $args['parentItem'], $args);
            }

            return apply_filters('wcpw_cart_item_price', $output, $cartItem, $args);
        }

        if (!isset($cartItem['data']) || !$cartItem['data']) {
            return apply_filters('wcpw_cart_item_price', $output, $cartItem, $args);
        }

        // is product
        $product = $cartItem['data'];

        if (!$product instanceof \WC_Product) {
            return apply_filters('wcpw_cart_item_price', $output, $cartItem, $args);
        }

        if (!$args['checkTax']) {
            $output = (float) $product->get_price($args['context']);

            return apply_filters('wcpw_cart_item_price', $output, $cartItem, $args);
        }

        if (in_array($product->get_type(), ['subscription', 'subscription_variation'])
            && function_exists('wcs_get_price_including_tax')
            && function_exists('wcs_get_price_excluding_tax')
        ) {
            $output = $args['displayIncludeTax'] || !$args['pricesIncludeTax']
                ? wcs_get_price_including_tax($product)
                : wcs_get_price_excluding_tax($product);
        } else {
            $output = $args['displayIncludeTax'] || !$args['pricesIncludeTax']
                ? wc_get_price_including_tax($product)
                : wc_get_price_excluding_tax($product);
        }

        return apply_filters('wcpw_cart_item_price', $output, $cartItem, $args);
    }

    /**
     * Get discounted item price according the rules
     *
     * @param array $cartItem
     * @param array $args
     *
     * @return float
     */
    public static function getItemDiscountedPrice($cartItem, $args = [])
    {
        $output = self::getItemPrice($cartItem, $args);
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

        return apply_filters('wcpw_cart_item_discounted_price', $output, $cartItem, $args);
    }

    /**
     * Get final item price with discounts, children, and base price
     *
     * @param array $cartItem
     * @param array $args
     *
     * @return float
     */
    public static function getItemFinalPrice($cartItem, $args = [])
    {
        $output = self::getItemDiscountedPrice($cartItem, $args);
        $output += self::getItemKitBasePrice($cartItem, ['checkTax' => false]);

        // calculate total children price
        if (isset($cartItem['wcpw_kit_children']) && is_array($cartItem['wcpw_kit_children'])
            && !empty($cartItem['wcpw_kit_children'])
        ) {
            $args['parentItem'] = $cartItem;

            foreach ($cartItem['wcpw_kit_children'] as $child) {
                $output += self::getItemDiscountedPrice($child, $args)
                    * (isset($child['quantity']) ? $child['quantity'] : 1);
            }
        }

        return apply_filters('wcpw_cart_item_final_price', $output, $cartItem);
    }
    // </editor-fold>

    // <editor-fold desc="Setters">
    /**
     * Set cart content
     *
     * @param integer $wizardId
     * @param array $value
     * @param string $key
     */
    public static function set($wizardId, $value, $key = null)
    {
        // merge product data with previously removed to keep EPO uploaded files
        if ($key && is_array($value) && isset($value['step_id'], $value['product_id'], $value['variation_id'])
            && apply_filters('wcpw_cart_merge_removed_product_data', false)
        ) {
            foreach (self::$itemsBuffer as $cartItemKey => $cartItem) {
                if ($cartItem['step_id'] != $value['step_id']
                    || $cartItem['product_id'] != $value['product_id']
                    || $cartItem['variation_id'] != $value['variation_id']
                ) {
                    continue;
                }

                $value = array_replace_recursive($cartItem, $value);

                break;
            }
        }

        Storage::set(self::$sessionKey, $wizardId, $value, $key);
    }

    /**
     * Set item price
     *
     * @param array $cartItem
     * @param float $value
     */
    public static function setItemPrice($cartItem, $value)
    {
        if (method_exists($cartItem['data'], 'set_price')) {
            $cartItem['data']->set_price($value);
        } else {
            $cartItem['data']->price = $value;
        }

        // WooCommerce Dynamic Pricing & Discounts with EPO price plugin fix
        if (property_exists($cartItem['data'], 'rightpress_in_cart')
            && property_exists($cartItem['data'], 'tm_epo_product_original_price')
        ) {
            $cartItem['data']->rightpress_in_cart = false;
        }
    }

    /**
     * Handles on cart item quantity update
     *
     * @param string $itemKey
     * @param integer $newQuantity
     * @param integer $oldQuantity
     * @param \WC_Cart $cart
     */
    public function quantityUpdateAction($itemKey, $newQuantity, $oldQuantity, $cart)
    {
        // change kit products quantity accordingly the root product
        if (isset($cart->cart_contents[$itemKey]['wcpw_is_kit_root'])
            && $cart->cart_contents[$itemKey]['wcpw_is_kit_root']
            && !$cart->cart_contents[$itemKey]['wcpw_is_kit_quantity_fixed']
        ) {
            foreach ($cart->cart_contents as $cartItemKey => $cartItem) {
                if (!isset($cartItem['wcpw_kit_id'])
                    || $itemKey == $cartItemKey
                    || $cart->cart_contents[$itemKey]['wcpw_kit_id'] != $cartItem['wcpw_kit_id']
                ) {
                    continue;
                }

                if ($oldQuantity == $cartItem['quantity']) {
                    $newChildQuantity = $newQuantity;
                } else {
                    $newChildQuantity = $newQuantity >= $oldQuantity
                        ? $cartItem['quantity'] * $newQuantity
                        : round($cartItem['quantity'] / $oldQuantity);
                }

                $cart->cart_contents[$cartItemKey]['quantity'] = $newChildQuantity;
            }
        }
    }

    /**
     * Set cart content by WC cart item
     *
     * @param array $cartItem
     *
     * @return array - added items keys
     *
     * @throws \Exception
     */
    public static function setContentByItem($cartItem)
    {
        $output = [];
        $wizardId = $cartItem['wcpw_id'];

        // firstly clear
        self::truncate($wizardId);

        if ((!isset($cartItem['wcpw_is_base_kit_product']) || !$cartItem['wcpw_is_base_kit_product'])
            && (!isset($cartItem['wcpw_has_attached_wizard']) || !$cartItem['wcpw_has_attached_wizard'])
        ) {
            // add the product itself but not the base or one with attached wizard
            $productData = [
                'product_id' => $cartItem['product_id'],
                'variation_id' => $cartItem['variation_id'],
                'variation' => $cartItem['variation'],
                'quantity' => $cartItem['quantity'],
                'step_id' => $cartItem['wcpw_step_id'],
                'data' => [],
                'request' => $cartItem['wcpw_request']
            ];

            $output[] = self::addProduct($wizardId, $productData);
        }

        // add children
        if (isset($cartItem['wcpw_kit_children']) && !empty($cartItem['wcpw_kit_children'])) {
            foreach ($cartItem['wcpw_kit_children'] as $child) {
                if (isset($child['key'], $child['value'])) {
                    $stepData = [
                        'key' => $child['key'],
                        'value' => $child['value'],
                        'step_id' => $child['step_id'],
                        'display_value' => $child['display_value']
                    ];

                    $output[] = self::addStepData($wizardId, $stepData);
                } elseif (isset($child['product_id'])) {
                    $productData = [
                        'product_id' => $child['product_id'],
                        'variation_id' => $child['variation_id'],
                        'variation' => $child['variation'],
                        'quantity' => $child['quantity'],
                        'step_id' => $child['step_id'],
                        'data' => [],
                        'request' => $child['request']
                    ];

                    $output[] = self::addProduct($wizardId, $productData);
                }
            }
        }

        return $output;
    }
    // </editor-fold>

    // <editor-fold desc="Utils">
    /**
     * Modify the price value accordingly the cart item taxes to keep it the same
     *
     * @param float $price
     * @param array $cartItem
     * @param array $args
     *
     * @return float
     */
    public static function modifyPriceAccordingItemTaxes($price, $cartItem, $args = [])
    {
        $defaults = [
            'checkTax' => self::displayPricesIncludesTax() || self::pricesIncludeTax(),
            'displayIncludeTax' => self::displayPricesIncludesTax(),
            'pricesIncludeTax' => self::pricesIncludeTax()
        ];

        $args = array_replace($defaults, $args);

        if (!isset($cartItem['data']) || !$cartItem['data']) {
            return 0;
        }

        $product = $cartItem['data'];

        if (!$product instanceof \WC_Product) {
            return 0;
        }

        // change price by the product's tax to keep it the same
        if ($args['checkTax'] && $product->is_taxable()) {
            $taxRates = \WC_Tax::get_rates($product->get_tax_class());
            $baseTaxRates = \WC_Tax::get_base_tax_rates($product->get_tax_class('unfiltered'));
            $removeTax = apply_filters('woocommerce_adjust_non_base_location_prices', true)
                ? \WC_Tax::calc_tax($price, $baseTaxRates, true)
                : \WC_Tax::calc_tax($price, $taxRates, true);

            if ($args['displayIncludeTax'] && !$args['pricesIncludeTax']) {
                $price -= array_sum($removeTax);
            } elseif (!$args['displayIncludeTax'] && $args['pricesIncludeTax']) {
                $price += array_sum($removeTax);
            }
        }

        return $price;
    }

    /**
     * Are taxes showed included in prices
     *
     * @return bool
     */
    public static function displayPricesIncludesTax()
    {
        // some problems are possible while PDF sending via CF7
        if (function_exists('WC') && property_exists(\WC(), 'cart')
            && WC()->cart && method_exists(WC()->cart, 'display_prices_including_tax')
        ) {
            return WC()->cart->display_prices_including_tax();
        }

        return get_option('woocommerce_tax_display_cart') == 'incl';
    }

    /**
     * Are taxes included in prices
     *
     * @return bool
     */
    public static function pricesIncludeTax()
    {
        if (function_exists('wc_prices_include_tax()')) {
            return wc_prices_include_tax();
        }

        return get_option('woocommerce_prices_include_tax') === 'yes';
    }

    /**
     * Generate cart item ID
     *
     * @param array $cartItem
     *
     * @return string|null
     */
    public static function generateProductId($cartItem)
    {
        if (!isset($cartItem['product_id'], $cartItem['step_id'])) {
            return null;
        }

        return WC()->cart->generate_cart_id(
            $cartItem['product_id'],
            isset($cartItem['variation_id']) ? $cartItem['variation_id'] : '',
            isset($cartItem['variation']) ? $cartItem['variation'] : [],
            ['step_id' => $cartItem['step_id']]
        );
    }

    /**
     * Parse cart string into an array
     *
     * @param integer $wizardId
     * @param string $data
     *
     * @return array
     */
    public static function parseFromString($wizardId, $data)
    {
        $output = json_decode(wp_unslash($data), true);

        if (!empty($output) && is_array($output) && !empty(array_filter($output))) {
            $output = array_filter($output);

            foreach ($output as &$cartItem) {
                foreach (self::$compressedItemKeys as $new => $old) {
                    if (!isset($cartItem[$old])) {
                        continue;
                    }

                    if (empty($cartItem[$old])) {
                        unset($cartItem[$old]);

                        continue;
                    }

                    $cartItem[$new] = $cartItem[$old];

                    unset($cartItem[$old]);
                }

                if (isset($cartItem['product_id'])) {
                    if (!isset($cartItem['quantity'])) {
                        $cartItem['quantity'] = 1;
                    }

                    if (!isset($cartItem['request'])) {
                        $cartItem['request'] = [];
                    }

                    if (!isset($cartItem['data'])) {
                        $cartItem['data'] = [];
                    }

                    if (!isset($cartItem['wizard_id'])) {
                        $cartItem['wizard_id'] = $wizardId;
                    }

                    if (!isset($cartItem['key'])) {
                        $cartItem['key'] = WC()->cart->generate_cart_id(
                            $cartItem['product_id'],
                            isset($cartItem['variation_id']) ? $cartItem['variation_id'] : 0,
                            isset($cartItem['variation']) ? $cartItem['variation'] : '',
                            (array) apply_filters(
                                'woocommerce_add_cart_item_data',
                                $cartItem['data'],
                                $cartItem['product_id'],
                                isset($cartItem['variation_id']) ? $cartItem['variation_id'] : 0,
                                $cartItem['quantity']
                            )
                        );
                    }
                }
            }

            unset($cartItem);
        }

        return $output;
    }
    // </editor-fold>

    // <editor-fold desc="Output">
    /**
     * WC cart item remove button filter
     *
     * @param string $html
     * @param string $cartItemKey
     *
     * @return string
     */
    public function itemRemoveLinkFilter($html, $cartItemKey)
    {
        $cartItem = WC()->cart->get_cart_item($cartItemKey);

        // if isn't from a kit or kit root item
        if (!isset($cartItem['wcpw_kit_id']) || $cartItem['wcpw_is_kit_root']) {
            return $html;
        }

        return '';
    }

    /**
     * WC cart item quantity input filter
     *
     * @param string $html
     * @param string $cartItemKey
     * @param array $cartItem
     *
     * @return string
     */
    public function itemQuantityFilter($html, $cartItemKey, $cartItem)
    {
        if ($cartItemKey && isset($cartItem['wcpw_is_kit_root'])
            && $cartItem['wcpw_is_kit_root'] && !$cartItem['wcpw_is_kit_quantity_fixed']
        ) {
            return $html;
        }

        if (!isset($cartItem['wcpw_kit_id'])) {
            return $html;
        }

        return $cartItem['quantity'];
    }

    /**
     * WC table row class filter
     *
     * @param string $class
     * @param string $cartItem
     *
     * @return string
     */
    public function itemClass($class, $cartItem)
    {
        if (is_array($cartItem) && isset($cartItem['wcpw_kit_id'], $cartItem['wcpw_is_kit_root'])) {
            $class .= ' wcpw-kit-root';
        }

        return $class;
    }

    /**
     * WC cart item data filter
     *
     * @param array $itemData
     * @param array $cartItem
     *
     * @return array
     */
    public function itemDataFilter($itemData, $cartItem)
    {
        // add children to a combined kit product
        if (isset($cartItem['wcpw_kit_children'], $cartItem['wcpw_kit_type'])
            && $cartItem['wcpw_kit_type'] == 'combined'
        ) {
            $kitBasePrice = self::getItemKitBasePrice($cartItem, ['checkTax' => false]);

            if ($kitBasePrice) {
                $itemData[] = [
                    'key' => isset($cartItem['wcpw_kit_base_price_string'])
                        ? esc_attr($cartItem['wcpw_kit_base_price_string'])
                        : '',
                    'value' => $kitBasePrice,
                    'display' => wc_price($kitBasePrice),
                    'hide' => false
                ];
            }

            if (!Settings::getPost($cartItem['wcpw_id'], 'hide_prices')
                && isset($cartItem['wcpw_default_price']) && $cartItem['wcpw_default_price']
            ) {
                $price = wc_price($cartItem['wcpw_default_price']);
                $display = "<span class=\"wcpw-kit-child\"><span class=\"wcpw-kit-child-price\">{$price}</span> "
                    . '<bdi class="wcpw-kit-child-times">&times;</bdi> '
                    . "<span class=\"wcpw-kit-child-quantity\">{$cartItem['quantity']}</span></span>";

                $itemData[] = [
                    'key' => L10N::r('Price', 'woocommerce'),
                    'value' => implode(' ', [$price, '&times;', $cartItem['quantity']]),
                    'display' => $display,
                    'hide' => false
                ];
            }

            foreach ($cartItem['wcpw_kit_children'] as $child) {
                $itemData[] = self::getKitChildData($child, $cartItem['wcpw_id']);
            }

            return $itemData;
        }

        // add kit id to an order's lines
        if (isset($cartItem['wcpw_kit_title'], $cartItem['wcpw_kit_id'])
            && !empty($cartItem['wcpw_kit_title']) && !empty($cartItem['wcpw_kit_id'])
        ) {
            $itemData[] = [
                'key' => $cartItem['wcpw_kit_title'],
                'value' => $cartItem['wcpw_kit_id'],
                'display' => '',
                'hidden' => true
            ];
        }

        return $itemData;
    }

    /**
     * WC cart product visibility filter
     *
     * @param bool $visible
     * @param array $cartItem
     *
     * @return bool
     */
    public function itemVisibilityFilter($visible, $cartItem)
    {
        if (isset($cartItem['wcpw_is_hidden_product']) && $cartItem['wcpw_is_hidden_product']) {
            $visible = false;
        }

        return $visible;
    }

    /**
     * WC cart item price filter
     *
     * @param string $price
     * @param array $cartItem
     *
     * @return string
     */
    public function itemPriceFilter($price, $cartItem)
    {
        if (!isset($cartItem['wcpw_kit_price']) || !$cartItem['wcpw_kit_price']) {
            return $price;
        }

        $product = $cartItem['data'];
        $price = wc_price($cartItem['wcpw_kit_price']);

        if (!$product->is_taxable()) {
            return $price;
        }

        if (self::displayPricesIncludesTax()) {
            if (!self::pricesIncludeTax() && WC()->cart->get_subtotal_tax() > 0) {
                $price .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat()
                    . '</small>';
            }
        } elseif (self::pricesIncludeTax() && WC()->cart->get_subtotal_tax() > 0) {
            $price .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat()
                . '</small>';
        }

        return $price;
    }

    /**
     * WC cart item sub total price filter
     *
     * @param string $price
     * @param array $cartItem
     *
     * @return string
     */
    public function itemSubTotalFilter($price, $cartItem)
    {
        if (!isset($cartItem['wcpw_kit_price']) || !$cartItem['wcpw_kit_price']) {
            return $price;
        }

        $product = $cartItem['data'];
        $price = wc_price($cartItem['wcpw_kit_price'] * $cartItem['quantity']);

        if (!$product->is_taxable()) {
            return $price;
        }

        if (self::displayPricesIncludesTax()) {
            if (!self::pricesIncludeTax() && WC()->cart->get_subtotal_tax() > 0) {
                $price .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat()
                    . '</small>';
            }
        } elseif (self::pricesIncludeTax() && WC()->cart->get_subtotal_tax() > 0) {
            $price .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat()
                . '</small>';
        }

        return $price;
    }

    /**
     * WC cart item image filter
     *
     * @param string $image
     * @param array $cartItem
     * @param string $cartItemKey
     *
     * @return string
     */
    public function itemThumbnailFilter($image, $cartItem, $cartItemKey)
    {
        if (isset($cartItem['wcpw_kit_thumbnail_url'])) {
            $attributes = [
                'class' => 'wcpw-cart-item-generated-thumbnail',
                'src' => $cartItem['wcpw_kit_thumbnail_url'],
                'alt' => get_the_title($cartItem['product_id'])
            ];

            $attributes
                = apply_filters('wcpw_cart_item_generated_thumbnail_attributes', $attributes, $cartItem, $cartItemKey);
            $output = '<img ' . Utils::attributesArrayToString($attributes) . '>';

            return apply_filters('wcpw_cart_item_generated_thumbnail', $output, $cartItem, $cartItemKey);
        }

        return $image;
    }

    /**
     * WC cart item name filter
     *
     * @param string $name
     * @param array $cartItem
     * @param string $cartItemKey
     *
     * @return string
     */
    public function itemNameFilter($name, $cartItem, $cartItemKey)
    {
        if (isset($cartItem['wcpw_id'], $cartItem['wcpw_edit_url'])
            && Settings::getPost($cartItem['wcpw_id'], 'enable_kit_edit_button')
            && (!isset($cartItem['wcpw_inner_checkout']) || !$cartItem['wcpw_inner_checkout'])
            && is_cart()
        ) {
            $attributes = [
                'type' => 'submit',
                'class' => 'wcpw-cart-item-edit-button button button-small btn btn-default btn-secondary btn-sm',
                'name' => 'wcpwEditCartItem',
                'value' => $cartItemKey
            ];

            $attributes = apply_filters('wcpw_cart_item_edit_button_attributes', $attributes, $cartItem, $cartItemKey);
            $name .= ' <button ' . Utils::attributesArrayToString($attributes)
                . '>' . esc_html__('Edit') . '</button>';
        }

        return $name;
    }

    /**
     * WC cart calculation action
     *
     * @param \WC_Cart $cart
     */
    public function beforeCalculateAction($cart)
    {
        if ((is_admin() && !wp_doing_ajax()) || did_action('wcpw_before_calculate_totals')) {
            return;
        }

        do_action('wcpw_before_calculate_totals', $cart);

        $cartContent = $cart->get_cart();

        foreach ($cartContent as $cartItemKey => &$cartItem) {
            // only for WCPW products
            if (!isset($cartItem['wcpw_id']) || !$cartItem['wcpw_id']) {
                continue;
            }

            // is a kit child product but have no parent
            if (array_key_exists('wcpw_kit_parent_key', $cartItem)
                && !isset($cartContent[$cartItem['wcpw_kit_parent_key']])
            ) {
                $cart->remove_cart_item($cartItemKey);

                continue;
            }

            $product = $cartItem['data'];

            if (!$product instanceof \WC_Product) {
                continue;
            }

            // is a kit root product
            if (isset($cartItem['wcpw_kit_children'], $cartItem['wcpw_kit_type'])
                && !empty($cartItem['wcpw_kit_children']) && is_array($cartItem['wcpw_kit_children'])
            ) {
                // change image
                if (isset($cartItem['wcpw_kit_thumbnail_id']) && $cartItem['wcpw_kit_thumbnail_id']) {
                    if (method_exists($product, 'set_image_id')) {
                        $product->set_image_id($cartItem['wcpw_kit_thumbnail_id']);
                    } else {
                        $cartItem['data']->image_id = $cartItem['wcpw_kit_thumbnail_id'];
                    }
                }

                // change visibility
                if (method_exists($product, 'set_catalog_visibility')) {
                    try {
                        $product->set_catalog_visibility('hidden');
                    } catch (\Exception $exception) {
                        continue;
                    }

                    // variable products fix
                    if (method_exists($product, 'set_parent_data') && method_exists($product, 'get_parent_data')) {
                        $parentData = $product->get_parent_data();
                        $parentData['catalog_visibility'] = 'hidden';
                        $product->set_parent_data($parentData);
                    }
                } else {
                    $cartItem['data']->catalog_visibility = 'hidden';
                }

                // set price
                if ($cartItem['wcpw_kit_type'] == 'combined'
                    || (isset($cartItem['wcpw_is_base_kit_product']) && $cartItem['wcpw_is_base_kit_product'])
                ) {
                    // use the fixed price
                    if (isset($cartItem['wcpw_kit_fixed_price']) && $cartItem['wcpw_kit_fixed_price']) {
                        self::setItemPrice($cartItem, $cartItem['wcpw_kit_fixed_price']);

                        continue;
                    }

                    // replace the real price and show the final price instead
                    if (!(isset($cartItem['wcpw_is_base_kit_product']) && $cartItem['wcpw_is_base_kit_product'])) {
                        WC()->cart->cart_contents[$cartItemKey]['wcpw_default_price']
                            = $cartItem['wcpw_default_price']
                            = self::getItemDiscountedPrice($cartItem);
                    }

                    WC()->cart->cart_contents[$cartItemKey]['wcpw_kit_price']
                        = $cartItem['wcpw_kit_price']
                        = self::getItemFinalPrice($cartItem);

                    self::setItemPrice(
                        $cartItem,
                        self::getItemKitBasePrice($cartItem)
                        + self::getItemStepInputChildrenPrice($cartItem)
                        + (isset($cartItem['wcpw_is_base_kit_product']) && $cartItem['wcpw_is_base_kit_product']
                            ? 0
                            : self::getItemDiscountedPrice($cartItem, ['checkTax' => false, 'context' => 'wcpw_cart'])
                        )
                    );
                } else {
                    // set product price with discounts
                    self::setItemPrice(
                        $cartItem,
                        self::getItemDiscountedPrice($cartItem, ['checkTax' => false, 'context' => 'wcpw_cart'])
                    );
                }

                continue;
            }

            // null the child price if the parent have a fixed price
            if (isset($cartItem['wcpw_kit_parent_key']) && $cartItem['wcpw_kit_parent_key']
                && isset($cartContent[$cartItem['wcpw_kit_parent_key']]['wcpw_kit_fixed_price'])
                && $cartContent[$cartItem['wcpw_kit_parent_key']]['wcpw_kit_fixed_price']
            ) {
                self::setItemPrice($cartItem, 0);

                continue;
            }

            // set product price with discounts
            self::setItemPrice(
                $cartItem,
                self::getItemDiscountedPrice($cartItem, ['checkTax' => false, 'context' => 'wcpw_cart'])
            );
        }

        unset($cartItem);
    }

    /**
     * WC cart content filter
     *
     * @param array $cart
     *
     * @return array
     */
    public function contentFilter($cart)
    {
        if (did_action('wcpw_before_output') == did_action('wcpw_after_output') + 1
            && Settings::getPost(Instance()->getCurrentId(), 'enable_checkout_step')
            && apply_filters('wcpw_checkout_show_only_own_products', true, $cart)
        ) {
            foreach ($cart as $cartItemKey => $cartItem) {
                if (!isset($cartItem['wcpw_inner_checkout'])
                    || $cartItem['wcpw_inner_checkout'] != Instance()->getCurrentId()
                ) {
                    if (isset(
                        $cartItem['associated_parent'],
                        $cart[$cartItem['associated_parent']],
                        $cart[$cartItem['associated_parent']]['wcpw_inner_checkout']
                    ) && $cart[$cartItem['associated_parent']]['wcpw_inner_checkout'] == Instance()->getCurrentId()
                    ) {
                        // keep EPO attached products
                        continue;
                    }

                    // remove not-wizard checkout products
                    unset($cart[$cartItemKey]);
                }
            }
        }

        return $cart;
    }

    /**
     * WC cart content count filter
     *
     * @param integer $quantity
     *
     * @return integer
     */
    public function contentCountFilter($quantity)
    {
        $hidden = 0;

        foreach (WC()->cart->get_cart() as $cartItem) {
            if (isset($cartItem['wcpw_id'], $cartItem['wcpw_kit_parent_key']) && $cartItem['wcpw_kit_parent_key']
                && Settings::getPost($cartItem['wcpw_id'], 'skip_child_products_count')
            ) {
                $hidden += $cartItem['quantity'];
            }
        }

        $quantity -= $hidden;

        return $quantity;
    }
    // </editor-fold>

    // <editor-fold desc="Deprecated">
    /**
     * Get products from the cart by the term ID
     *
     * @param integer $wizardId
     * @param integer $termId
     *
     * @return array
     *
     * @deprecated since 6.0.0 use getProductsByStepId
     */
    public static function getProductsByTermId($wizardId, $termId)
    {
        return self::getByStepId($wizardId, $termId);
    }
    // </editor-fold>
}
