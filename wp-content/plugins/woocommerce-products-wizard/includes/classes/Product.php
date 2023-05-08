<?php
namespace WCProductsWizard;

/**
 * Product Class
 *
 * @class Product
 * @version 8.11.1
 */
class Product
{
    /** Class Constructor */
    public function __construct()
    {
        // price
        add_filter('woocommerce_product_get_price', [$this, 'priceFilter'], 10, 2);
        add_filter('woocommerce_product_variation_get_price', [$this, 'priceFilter'], 10, 2);
        add_filter('woocommerce_variation_prices', [$this, 'variationPricesFilter'], 10, 2);

        // query
        add_action('pre_get_posts', [$this, 'preGerPostsFilter']);
    }

    /**
     * Product price filter
     *
     * @param float $price
     * @param \WC_Product $product
     *
     * @return float
     */
    public function priceFilter($price, $product)
    {
        if (did_action('wcpw_before_output') == did_action('wcpw_after_output') + 1
            || did_action('wcsf_before_output') == did_action('wcsf_after_output') + 1
        ) {
            $wizardId = Instance()->getCurrentId();
            $discountedPrice = self::applyPriceDiscount($price, $wizardId, $product);

            if ($discountedPrice == $price) {
                return $price;
            }

            if (Settings::getPost($wizardId, 'price_discount_type') == 'show-as-sale') {
                $product->set_sale_price($discountedPrice);
            } else {
                // hide regular price of the products with a native sale
                $product->set_regular_price($discountedPrice);
            }

            return $discountedPrice;
        }

        return $price;
    }

    /**
     * Variation prices filter
     *
     * @param array $prices
     * @param \WC_Product_Variable $product
     *
     * @return array
     */
    public function variationPricesFilter($prices, $product)
    {
        if (did_action('wcpw_before_output') == did_action('wcpw_after_output') + 1) {
            foreach ($prices['price'] as &$price) {
                $discountedPrice = self::applyPriceDiscount($price, Instance()->getCurrentId(), $product);

                if ($discountedPrice < $price) {
                    $price = $discountedPrice;
                }
            }

            if (Settings::getPost(Instance()->getCurrentId(), 'price_discount_type') == 'show-as-sale') {
                foreach ($prices['sale_price'] as &$price) {
                    $discountedPrice = self::applyPriceDiscount($price, Instance()->getCurrentId(), $product);

                    if ($discountedPrice < $price) {
                        $price = $discountedPrice;
                    }
                }
            }
        }

        return $prices;
    }

    /**
     * Get wizard's price discount rule
     *
     * @param integer $wizardId
     * @param \WC_Product $product
     *
     * @return array|null
     */
    public static function getDiscountRule($wizardId, $product)
    {
        $type = $product->get_type();
        $output = null;
        $commonRule = null;
        $discounts = [];

        // variations level
        if ($type == 'variation') {
            $discounts = (array) Settings::getProductVariation($product->get_id(), 'discount');
        } elseif ($type == 'variable' && method_exists($product, 'get_children')) {
            $variations = $product->get_children();
            $discounts = (array) Settings::getProductVariation(reset($variations), 'discount');
        }

        foreach ($discounts as $discount) {
            if (!isset($discount['id'], $discount['value']) || !$discount['value']) {
                continue;
            }

            if ($discount['id']) {
                if ($discount['id'] == $wizardId) {
                    $output = $discount;
                }
            } else {
                $commonRule = $discount;
            }
        }

        if (!is_null($output)) {
            return $output;
        } elseif (!is_null($commonRule)) {
            return $commonRule;
        }

        // product level
        $discounts = (array) Settings::getProduct(
            $type == 'variation' ? $product->get_parent_id() : $product->get_id(),
            'discount'
        );

        foreach ($discounts as $discount) {
            if (!isset($discount['id'], $discount['value']) || !$discount['value']) {
                continue;
            }

            if ($discount['id']) {
                if ($discount['id'] == $wizardId) {
                    $output = $discount;
                }
            } else {
                $commonRule = $discount;
            }
        }

        if (!is_null($output)) {
            return $output;
        } elseif (!is_null($commonRule)) {
            return $commonRule;
        }

        // category level
        foreach ($product->get_category_ids() as $categoryId) {
            $discounts = (array) Settings::getProductCategory($categoryId, 'discount');

            foreach ($discounts as $discount) {
                if (!isset($discount['id'], $discount['value']) || !$discount['value']) {
                    continue;
                }

                if ($discount['id']) {
                    if ($discount['id'] == $wizardId) {
                        $output = $discount;
                    }
                } else {
                    $commonRule = $discount;
                }
            }

            if (!is_null($output)) {
                return $output;
            } elseif (!is_null($commonRule)) {
                return $commonRule;
            }
        }

        return $output;
    }

    /**
     * Get wizard's price discount rule by product ID
     *
     * @param integer $wizardId
     * @param integer $productId
     * @param integer||null $variationId
     *
     * @return array|null
     */
    public static function getDiscountRuleById($wizardId, $productId, $variationId = null)
    {
        $commonRule = null;
        $output = null;

        // variation level
        if ($variationId) {
            $discounts = (array) Settings::getProductVariation($variationId, 'discount');

            foreach ($discounts as $discount) {
                if (!isset($discount['id'], $discount['value']) || !$discount['value']) {
                    continue;
                }

                if ($discount['id']) {
                    if ($discount['id'] == $wizardId) {
                        $output = $discount;
                    }
                } else {
                    $commonRule = $discount;
                }
            }

            if (!is_null($output)) {
                return $output;
            } elseif (!is_null($commonRule)) {
                return $commonRule;
            }
        }

        // product level
        $discounts = (array) Settings::getProduct($productId, 'discount');

        foreach ($discounts as $discount) {
            if (!isset($discount['id'], $discount['value']) || !$discount['value']) {
                continue;
            }

            if ($discount['id']) {
                if ($discount['id'] == $wizardId) {
                    $output = $discount;
                }
            } else {
                $commonRule = $discount;
            }
        }

        if (!is_null($output)) {
            return $output;
        } elseif (!is_null($commonRule)) {
            return $commonRule;
        }

        // category level
        foreach (Product::getTermsIds($productId) as $categoryId) {
            $discounts = (array) Settings::getProductCategory($categoryId, 'discount');

            foreach ($discounts as $discount) {
                if (!isset($discount['id'], $discount['value']) || !$discount['value']) {
                    continue;
                }

                if ($discount['id']) {
                    if ($discount['id'] == $wizardId) {
                        $output = $discount;
                    }
                } else {
                    $commonRule = $discount;
                }
            }

            if (!is_null($output)) {
                return $output;
            } elseif (!is_null($commonRule)) {
                return $commonRule;
            }
        }

        return $output;
    }

    /**
     * Get handled product price by discount rule
     *
     * @param float $price
     * @param array $discount
     *
     * @return float
     */
    public static function handlePriceWithDiscountRule($price, $discount)
    {
        switch ($discount['type']) {
            case 'percentage':
                return max(0, $price - ($price * (float) $discount['value'] / 100));

            case 'fixed':
                return max(0, $price - (float) $discount['value']);

            case 'precise_price':
                return max(0, (float) $discount['value']);
        }

        return $price;
    }

    /**
     * Get, filter and return available product attributes and variables
     *
     * @param array $arguments
     *
     * @return array
     */
    public static function getVariationArguments($arguments)
    {
        $defaults = [
            'id' => null,
            'stepId' => null,
            'product' => false,
            'cart' => [],
            'defaultAttributes' => []
        ];

        $arguments = array_replace($defaults, $arguments);
        $product = $arguments['product'];
        $output = [
            'variations' => [],
            'attributes' => []
        ];

        if (!($product instanceof \WC_Product_Variable || $product instanceof \WC_Product_Variation)) {
            return apply_filters('wcpw_variation_arguments', $output, $arguments);
        }

        $defaultSelectedVariations = null;
        $selectedByDefault = (array) Settings::getStep($arguments['id'], $arguments['stepId'], 'selected_items_by_default'); // phpcs:ignore
        $excludedProductsIds = (array) Settings::getStep($arguments['id'], $arguments['stepId'], 'excluded_products');
        $requestedAttributes = (array) Settings::getStep($arguments['id'], $arguments['stepId'], 'attributes');
        $productId = $product->get_id();
        $variations = method_exists($product, 'get_available_variations') ? $product->get_available_variations() : [];
        $attributes = $product->get_variation_attributes();
        $cartProduct = null;
        $attributesToRemove = [];
        $attributesToSave = [];
        $attributesOutput = [];
        $minQty = Product::getMinQuantity($arguments['id'], $arguments['stepId']);
        $maxQty = Product::getMaxQuantity($arguments['id'], $arguments['stepId'], $arguments['product']);

        foreach ($variations as $key => &$variation) {
            if (in_array($variation['variation_id'], $excludedProductsIds)
                || !Utils::getAvailabilityByRules(
                    $arguments['id'],
                    Settings::getProductVariation($variation['variation_id'], 'availability_rules'),
                    "product-variation-{$variation['variation_id']}"
                )
                || !apply_filters('wcpw_variation_available', true, $variation, $arguments)
            ) {
                // save attributes to remove
                foreach ($variation['attributes'] as $attributeItemKey => $attributeItemValue) {
                    $attributesToRemove[$attributeItemKey][] = $attributeItemValue;
                }

                // remove the unmet variation at all
                unset($variations[$key]);

                continue;
            }

            // collect attributes to save
            foreach ($variation['attributes'] as $attributeItemKey => $attributeItemValue) {
                $attributesToSave[$attributeItemKey][] = $attributeItemValue;
            }

            // change quantity values according the settings
            if (is_numeric($minQty)) {
                $variation['min_qty'] = $minQty;
            }

            if (is_numeric($maxQty)) {
                $variation['max_qty'] = $maxQty;
            }

            // define default selected variation by the setting
            if (in_array($variation['variation_id'], $selectedByDefault)) {
                $defaultSelectedVariations = $variation;
            }

            // change image size
            if (!empty($variation['image_src'])) {
                $src = wp_get_attachment_image_src(get_post_thumbnail_id($variation['variation_id']), 'shop_catalog');
                $variation['image_src'] = $src[0];
            } elseif (!empty($variation['image']['src'])) {
                $src = wp_get_attachment_image_src(get_post_thumbnail_id($variation['variation_id']), 'shop_catalog');
                $variation['image']['src'] = is_array($src) && isset($src[0]) ? $src[0] : '';
            }

            // need to show the price for all variations because of the possible discounts
            $_variation = wc_get_product($variation['variation_id']);
            $variation['price_html'] = '<span class="price">' . $_variation->get_price_html() . '</span>';
        }

        unset($variation);
        unset($_variation);

        // clean by requested attributes
        foreach ($requestedAttributes as $attribute) {
            $attributeParts = explode('#', $attribute);
            $taxonomy = reset($attributeParts);
            $id = end($attributeParts);

            if (!Utils::getAvailabilityByRules(
                $arguments['id'],
                Settings::getProductAttribute($id, 'availability_rules'),
                "product-attribute-{$id}"
            )) {
                $attributesToRemove[$taxonomy][] = get_term_field('slug', $id, $taxonomy);
            }
        }

        // clean attributes to remove from attributes to save
        foreach ($attributesToSave as $attributeKey => $attributeValue) {
            if (!isset($attributesToRemove[$attributeKey])) {
                continue;
            }

            $attributesToRemove[$attributeKey] = array_diff($attributesToRemove[$attributeKey], $attributeValue);
        }

        // find and remove unmet product attributes
        foreach ($attributesToRemove as $attributeToRemoveItemKey => $attributeToRemoveItemValue) {
            foreach ($attributes as $attributeKey => $attributeValue) {
                if (urldecode(str_replace('attribute_', '', $attributeToRemoveItemKey))
                    != mb_strtolower(str_replace(' ', '-', $attributeKey))
                ) {
                    continue;
                }

                foreach ($attributeToRemoveItemValue as $attributeToRemoveItemValueItem) {
                    foreach ($attributeValue as $attributeItemValueItemKey => $attributeItemValueItemValue) {
                        if (urldecode($attributeToRemoveItemValueItem) != urldecode($attributeItemValueItemValue)) {
                            continue;
                        }

                        // unset product attribute
                        unset($attributes[$attributeKey][$attributeItemValueItemKey]);
                    }
                }
            }
        }

        // find this product in the cart
        foreach ($arguments['cart'] as $cartItem) {
            if (isset($cartItem['product_id'], $cartItem['step_id'])
                && $productId == $cartItem['product_id'] && $cartItem['step_id'] == $arguments['stepId']
            ) {
                $cartProduct = $cartItem;

                break;
            }
        }

        // get pure attributes array
        foreach ($attributes as $attributeKey => $attributeValue) {
            $selectedAttribute = '';
            $key = strtolower(sanitize_title($attributeKey));

            // set active product if have in the cart
            if (isset($_REQUEST['attribute_' . $key])) {
                $selectedAttribute = $_REQUEST['attribute_' . $key];
            } elseif ($cartProduct && isset($cartProduct['variation']['attribute_' . $key])) {
                $selectedAttribute = $cartProduct['variation']['attribute_' . $key];
            } elseif ($defaultSelectedVariations
                && isset($defaultSelectedVariations['attributes']['attribute_' . $key])
            ) {
                $selectedAttribute = $defaultSelectedVariations['attributes']['attribute_' . $key];
            } elseif (isset($arguments['defaultAttributes'][$key])) {
                $selectedAttribute = $arguments['defaultAttributes'][$key];
            }

            // Get terms if this is a taxonomy - ordered
            if (taxonomy_exists($attributeKey)) {
                switch (wc_attribute_orderby($attributeKey)) {
                    case 'name': {
                        $args = [
                            'orderby' => 'name',
                            'hide_empty' => false,
                            'menu_order' => false
                        ];

                        break;
                    }

                    case 'id': {
                        $args = [
                            'orderby' => 'id',
                            'order' => 'ASC',
                            'menu_order' => false,
                            'hide_empty' => false
                        ];

                        break;
                    }

                    case 'menu_order':
                        $args = ['menu_order' => 'ASC', 'hide_empty' => false];
                        break;

                    default:
                        $args = [];
                }

                foreach (get_terms($attributeKey, $args) as $term) {
                    if (!in_array($term->slug, $attributeValue)) {
                        continue;
                    }

                    if (!$selectedAttribute
                        && (!isset($attributesOutput[$attributeKey]) || count($attributesOutput[$attributeKey]) == 0)
                    ) {
                        $selected = true;
                    } else {
                        $selected = strtolower(sanitize_title($selectedAttribute))
                            == strtolower(sanitize_title($term->slug));
                    }

                    $attributesOutput[$attributeKey][] = [
                        'id' => $term->term_id,
                        'name' => $term->name,
                        'value' => $term->slug,
                        'selected' => $selected,
                        'thumbnailId' => Settings::getProductAttribute($term->term_id, 'thumbnail')
                    ];
                }
            } elseif (is_array($attributeValue) && !empty($attributeValue)) {
                foreach ($attributeValue as $option) {
                    if (!$selectedAttribute
                        && (!isset($attributesOutput[$attributeKey]) || count($attributesOutput[$attributeKey]) == 0)
                    ) {
                        $selected = true;
                    } else {
                        $selected = strtolower(sanitize_title($selectedAttribute))
                            == strtolower(sanitize_title($option));
                    }

                    $attributesOutput[$attributeKey][] = [
                        'name' => $option,
                        'value' => $option,
                        'selected' => $selected
                    ];
                }
            }
        }

        $output = [
            'variations' => $variations,
            'attributes' => $attributesOutput
        ];

        return apply_filters('wcpw_variation_arguments', $output, $arguments);
    }

    /**
     * Return a list of product attributes for a product
     *
     * @param $product \WC_Product
     *
     * @return array
     */
    public static function getAttributes($product)
    {
        $output = [];
        $displayDimensions = apply_filters(
            'wc_product_enable_dimensions_display',
            $product->has_weight() || $product->has_dimensions()
        );

        if ($displayDimensions && $product->has_weight()) {
            $output['weight'] = [
                'label' => L10N::r('Weight', 'woocommerce'),
                'value' => wc_format_weight($product->get_weight())
            ];
        }

        if ($displayDimensions && $product->has_dimensions()) {
            $output['dimensions'] = [
                'label' => L10N::r('Dimensions', 'woocommerce'),
                'value' => wc_format_dimensions($product->get_dimensions(false))
            ];
        }

        // Add product attributes to list.
        $attributes = array_filter($product->get_attributes(), 'wc_attributes_array_filter_visible');

        foreach ($attributes as $attribute) {
            if (!$attribute instanceof \WC_Product_Attribute) {
                continue;
            }

            $values = [];

            if ($attribute->is_taxonomy()) {
                $attributeValues = wc_get_product_terms($product->get_id(), $attribute->get_name(), ['fields' => 'all']); // phpcs:ignore

                foreach ($attributeValues as $attributeValue) {
                    $values[] = esc_html($attributeValue->name);
                }
            } else {
                $values = $attribute->get_options();

                foreach ($values as &$value) {
                    $value = esc_html($value);
                }
            }

            $output['attribute_' . sanitize_title_with_dashes($attribute->get_name())] = [
                'label' => wc_attribute_label($attribute->get_name()),
                'value' => apply_filters(
                    'woocommerce_attribute',
                    wpautop(wptexturize(implode(', ', $values))),
                    $attribute,
                    $values
                )
            ];
        }

        return apply_filters('woocommerce_display_product_attributes', $output, $product);
    }

    /**
     * Merge arguments with products query part
     *
     * @param array $args
     * @param array $productsIds - specific products only
     *
     * @return array
     */
    public static function addRequestArgs($args, $productsIds = [])
    {
        $defaults = [
            'id' => null,
            'stepId' => null,
            'page' => 1,
            'orderBy' => null,
            'productsPerPage' => null,
            'filter' => []
        ];

        $args = array_replace($defaults, $args);
        $productsPerPage = Settings::getStep($args['id'], $args['stepId'], 'products_per_page');
        $allSelectedItemsByDefault = Settings::getStep($args['id'], $args['stepId'], 'all_selected_items_by_default');
        $noSelectedItemsByDefault = Settings::getStep($args['id'], $args['stepId'], 'no_selected_items_by_default');
        $selectedItemsByDefault = Settings::getStep($args['id'], $args['stepId'], 'selected_items_by_default');
        $orderBy = Settings::getStep($args['id'], $args['stepId'], 'order_by');
        $activeProductsIds = Cart::getProductsAndVariationsIds($args['id'], ['includeSteps' => $args['stepId']]);

        // get products by filtered ids
        if (empty($productsIds)) {
            $productsIds = self::getStepProductsIds($args['id'], $args['stepId'], $args);
        }

        $queryArgs = [
            'orderby' => $orderBy,
            'order' => Settings::getStep($args['id'], $args['stepId'], 'order'),
            'post_type' => ['product', 'product_variation'],
            'post__in' => $productsIds,
            'posts_per_page' => -1,
            'numberposts' => -1,
            'ignore_sticky_posts' => true,
            'paged' => $args['page']
        ];

        // args for price ordering
        if ($args['orderBy']) {
            $orderByValue = explode('-', $args['orderBy']);
            $orderBy = esc_attr($orderByValue[0]);
            $order = !empty($orderByValue[1]) ? $orderByValue[1] : 'ASC';
            $queryArgs = array_replace(
                $queryArgs,
                WC()->query->get_catalog_ordering_args($orderBy, $order)
            );
        }

        if ($queryArgs['orderby'] == 'price') {
            $queryArgs['orderby'] = 'meta_value_num';
            $queryArgs['meta_key'] = '_price';
        }

        // change products per page value
        if (is_numeric($args['productsPerPage']) && $args['productsPerPage']) {
            $queryArgs['posts_per_page'] = $queryArgs['numberposts'] = $args['productsPerPage'];
        } elseif ($productsPerPage != 0) {
            $queryArgs['posts_per_page'] = $queryArgs['numberposts'] = $productsPerPage;
        }

        if (empty($activeProductsIds) && !$noSelectedItemsByDefault) {
            if ($allSelectedItemsByDefault) {
                $activeProductsIds = $productsIds;
            } elseif (!empty($selectedItemsByDefault)) {
                $activeProductsIds = $selectedItemsByDefault;
            } else {
                // set the first product as active
                $productsQuery = get_posts(array_replace(
                    $queryArgs,
                    [
                        'numberposts' => 1,
                        'posts_per_page' => 1,
                        'fields' => 'ids'
                    ]
                ));

                if (isset($productsQuery[0])) {
                    $activeProductsIds[] = $productsQuery[0];
                }
            }
        }

        $output = array_replace(
            $args,
            [
                'queryArgs' => $queryArgs,
                'itemTemplate' => 'form/item/' . Template::getFormItemName($args['id'], $args['stepId']),
                'cart' => Cart::get($args['id']),
                'activeProductsIds' => $activeProductsIds,
                'hidePrices' => Settings::getPost($args['id'], 'hide_prices')
                    || !Settings::getStep($args['id'], $args['stepId'], 'show_item_price'),
                'severalProducts' => Settings::getStep($args['id'], $args['stepId'], 'several_products'),
                'hideChooseElement' => Settings::getStep($args['id'], $args['stepId'], 'hide_choose_element'),
                'soldIndividually' => Settings::getStep($args['id'], $args['stepId'], 'sold_individually'),
                'mergeThumbnailWithGallery' =>
                    Settings::getStep($args['id'], $args['stepId'], 'merge_item_thumbnail_with_gallery'),
                'enableTitleLink' => Settings::getStep($args['id'], $args['stepId'], 'enable_item_title_link'),
                'enableThumbnailLink' => Settings::getStep($args['id'], $args['stepId'], 'enable_item_thumbnail_link')
            ]
        );

        return apply_filters('wcpw_products_request_args', $output, $productsIds, $args);
    }

    /**
     * Makes the products query considering all conditions
     *
     * @param array $args
     *
     * @return string
     */
    public static function request($args)
    {
        $defaults = [
            'id' => null,
            'stepId' => null
        ];

        $args = array_replace($defaults, $args);

        if (!$args['id'] || !$args['stepId']) {
            return '';
        }

        // for 3rd party plugins compatibility
        if (!Instance()->getCurrentId()) {
            Instance()->setCurrentId($args['id']);
        }

        if (!Instance()->getCurrentStepId()) {
            Instance()->setCurrentStepId($args['stepId']);
        }

        // there are no products requested - show nothing
        if (empty(Settings::getStep($args['id'], $args['stepId'], 'categories'))
            && empty(Settings::getStep($args['id'], $args['stepId'], 'attributes'))
            && empty(array_filter((array) Settings::getStep($args['id'], $args['stepId'], 'included_products')))
        ) {
            return '';
        }

        $productsIds = self::getStepProductsIds($args['id'], $args['stepId'], $args);

        if (!empty($productsIds)) {
            $args = self::addRequestArgs($args, $productsIds);
            $template = Template::getFormName($args['id'], $args['stepId']);

            return Template::html("form/layouts/{$template}", $args);
        }

        // should be products but not found
        return Template::html('messages/nothing-found', $args);
    }

    /**
     * Add a product to the main woocommerce cart
     *
     * @param array $args
     *
     * @throws \Exception
     *
     * @return string|bool
     */
    public static function addToMainCart($args)
    {
        $defaults = [
            'product_id' => null,
            'quantity' => 1,
            'variation_id' => null,
            'variation' => [],
            'data' => [
                'wcpw_id' => null,
                'wcpw_step_id' => null
            ],
            'request' => null
        ];

        $args = array_replace_recursive($defaults, $args);
        $cartQuantity = 0;

        do_action('wcpw_before_add_to_main_cart', $args);

        // get the same product's quantity from the main cart and remove it
        if (apply_filters('wcpw_add_to_main_cart_merge_products_quantity', false, $args)) {
            $cart = WC()->cart->get_cart();

            foreach ($cart as $cartItemKey => $cartItem) {
                if ($cartItem['product_id'] != $args['product_id']
                    || $cartItem['variation_id'] != $args['variation_id']
                    || $cartItem['variation'] != $args['variation']
                    || $cartItem['wcpw_id'] != $args['data']['wcpw_id']
                    || $cartItem['wcpw_step_id'] != $args['data']['wcpw_step_id']
                ) {
                    continue;
                }

                $cartQuantity += (float) $cartItem['quantity'];

                WC()->cart->remove_cart_item($cartItemKey);
            }
        }

        if ($args['request']) {
            // emulate post data passing
            $_POST = array_merge($_POST, $args['request']);
        }

        return WC()->cart->add_to_cart(
            $args['product_id'],
            $args['quantity'] + $cartQuantity,
            $args['variation_id'],
            $args['variation'],
            $args['data']
        );
    }

    /**
     * Prepare step products request query considering all conditions
     *
     * @param integer $wizardId
     * @param integer|string $stepId
     * @param array $args
     *
     * @return array
     */
    public static function getStepProductsIds($wizardId, $stepId, $args = [])
    {
        $defaults = [
            'filter' => [],
            'queryArgs' => [
                'post_type' => 'product',
                'fields' => 'ids',
                'posts_per_page' => -1,
                'numberposts' => -1,
                'tax_query' => ['relation' => 'AND'],
                'meta_query' => ['relation' => 'AND'],
                'post__not_in' => [],
                'post__in' => []
            ]
        ];

        $args = array_replace_recursive($defaults, $args);
        $output = [];

        if (!$stepId || !is_numeric($stepId)) {
            return apply_filters('wcpw_step_products_ids', $output, $wizardId, $stepId, $args);
        }

        $categories = Settings::getStep($wizardId, $stepId, 'categories');
        $attributes = Settings::getStep($wizardId, $stepId, 'attributes');
        $includedProductsIds = array_filter((array) Settings::getStep($wizardId, $stepId, 'included_products'));

        // there are no products selected
        if (empty($categories) && empty($attributes) && empty(array_filter($includedProductsIds))) {
            return $output;
        }

        $noProductsUntilFiltering = Settings::getStep($wizardId, $stepId, 'no_products_until_filtering');
        $excludedProductsIds = Settings::getStep($wizardId, $stepId, 'excluded_products');
        $excludeAddedProductsOfSteps = Settings::getStep($wizardId, $stepId, 'exclude_added_products_of_steps');

        if ($noProductsUntilFiltering && empty($args['filter'])) {
            return apply_filters('wcpw_step_products_ids', $output, $wizardId, $stepId, $args);
        }

        // apply default filter values
        if (empty($args['filter']) && Settings::getStep($wizardId, $stepId, 'apply_default_filters')) {
            foreach (Form::getFilterFields($wizardId, $stepId) as $filterField) {
                if (empty($filterField['default'])) {
                    continue;
                }

                $args['filter'][] = [$filterField['key'] => $filterField['default']];
            }
        }

        // product request by current category
        $queryArgs = $args['queryArgs'];

        if (current_user_can('manage_woocommerce')) {
            $queryArgs['post_status'] = 'any';
        }

        // exclude other steps added products
        if (!empty($excludeAddedProductsOfSteps)) {
            $excludeAddedProducts = Cart::getProductsAndVariationsIds(
                $wizardId,
                ['includeSteps' => wp_parse_id_list($excludeAddedProductsOfSteps)]
            );

            if (!empty($excludeAddedProducts)) {
                $queryArgs['post__not_in'] += $excludeAddedProducts;
            }
        }

        // exclude products
        if (is_array($excludedProductsIds)) {
            $excludedProductsIds = array_filter($excludedProductsIds);

            if (!empty($excludedProductsIds)) {
                $queryArgs['post__not_in'] += $excludedProductsIds;
            }
        }

        // query specific products only
        if (!empty($includedProductsIds)) {
            $queryArgs['post__in'] += $includedProductsIds;
            $queryArgs['post_type'] = ['product', 'product_variation'];
        }

        // query by categories
        if (!empty($categories)) {
            $include = [];

            foreach ($categories as $categoryId) {
                if (Utils::getAvailabilityByRules(
                    $wizardId,
                    Settings::getProductCategory($categoryId, 'availability_rules'),
                    "product-category-{$categoryId}"
                )) {
                    $include[] = $categoryId;
                }
            }

            if (!empty($include)) {
                // have some categories to request
                $queryArgs['tax_query'][] = [
                    'taxonomy' => 'product_cat',
                    'field' => 'id',
                    'terms' => $include,
                    'operator' => 'IN'
                ];
            } else {
                // make a no results query
                $queryArgs['tax_query'][] = [
                    'taxonomy' => 'product_cat',
                    'field' => 'id',
                    'terms' => [0],
                    'operator' => 'IN'
                ];
            }
        }

        // query by attributes
        if (!empty($attributes)) {
            $include = [];

            foreach ($attributes as $attribute) {
                $attributeParts = explode('#', $attribute);
                $taxonomy = reset($attributeParts);
                $id = end($attributeParts);

                if (Utils::getAvailabilityByRules(
                    $wizardId,
                    Settings::getProductAttribute($id, 'availability_rules'),
                    "product-attribute-{$id}"
                )) {
                    $include[$taxonomy][] = $id;
                }
            }

            if (!empty($include)) {
                // have some attributes to request
                foreach ($include as $taxonomy => $ids) {
                    $queryArgs['tax_query'][] = [
                        'taxonomy' => $taxonomy,
                        'field' => 'id',
                        'terms' => $ids,
                        'operator' => 'IN'
                    ];
                }
            } else {
                // make a no results query
                $queryArgs['tax_query'][] = [
                    'taxonomy' => 'product_cat',
                    'field' => 'id',
                    'terms' => [0],
                    'operator' => 'IN'
                ];
            }
        }

        // blend in filter args query
        $queryArgs = array_merge_recursive($queryArgs, self::filterArgsToQuery($args['filter'], $wizardId, $stepId));

        // make a query
        $products = get_posts(apply_filters('wcpw_step_products_query_args', $queryArgs, $wizardId, $stepId, $args));

        foreach ($products as $productId) {
            // different types might be requested
            switch (get_post_type($productId)) {
                default:
                case 'product':
                    $product = new \WC_Product((int) $productId);
                    break;

                case 'product_variation':
                    $product = new \WC_Product_Variation((int) $productId);
            }

            $available = $product->is_visible() && $product->is_purchasable()
                && ($product->is_in_stock() || $product->backorders_allowed())
                && Utils::getAvailabilityByRules(
                    $wizardId,
                    Settings::getProduct((int) $productId, 'availability_rules'),
                    "product-{$productId}"
                );

            if (apply_filters('wcpw_product_availability', $available, $productId, $wizardId, $stepId, $args)) {
                $output[] = (int) $productId;
            }
        }

        return apply_filters('wcpw_step_products_ids', $output, $wizardId, $stepId, $args);
    }

    // <editor-fold desc="Filters">
    /**
     * Prepare query array from filter value
     *
     * @param array $data
     * @param integer $wizardId
     * @param integer|string $stepId
     *
     * @return array
     */
    public static function filterArgsToQuery($data, $wizardId, $stepId)
    {
        $output = [];
        $taxQuery = [];
        $metaQuery = [];

        if (!is_array($data) || empty($data)) {
            return $output;
        }

        foreach ($data as $filterKey => $values) {
            if (!is_array($values) || empty($values)) {
                continue;
            }

            foreach ($values as $key => $value) {
                if (empty($value)) {
                    continue;
                }

                switch ($key) {
                    case 'price': {
                        $metaQuery[] = [
                            'key' => '_price',
                            'value' => $value['from'],
                            'compare' => '>=',
                            'type' => 'numeric'
                        ];

                        $metaQuery[] = [
                            'key' => '_price',
                            'value' => $value['to'],
                            'compare' => '<=',
                            'type' => 'numeric'
                        ];

                        break;
                    }

                    case 'category': {
                        if (is_array($value) && !empty(array_filter($value))) {
                            $taxQuery[] = [
                                'taxonomy' => 'product_cat',
                                'field' => 'id',
                                'terms' => array_filter($value),
                                'operator' => 'IN'
                            ];
                        }

                        break;
                    }

                    case 'tag': {
                        if (is_array($value) && !empty(array_filter($value))) {
                            $taxQuery[] = [
                                'taxonomy' => 'product_tag',
                                'field' => 'id',
                                'terms' => array_filter($value),
                                'operator' => 'IN'
                            ];
                        }

                        break;
                    }

                    case 'search': {
                        $output['s'] = is_array($value) ? reset($value) : $value;
                        $output['wcpw_search'] = "$wizardId-$stepId";
                        break;
                    }

                    // attribute
                    default: {
                        if (!taxonomy_exists("pa_{$key}")) {
                            break;
                        }

                        if (!isset($value['from'])) {
                            // attribute simple
                            if (!empty(array_filter($value))) {
                                $taxQuery[] = [
                                    'taxonomy' => "pa_{$key}",
                                    'field' => 'id',
                                    'terms' => array_filter($value),
                                    'operator' => 'IN'
                                ];
                            }

                            if (filter_var(
                                get_option('woocommerce_hide_out_of_stock_items'),
                                FILTER_VALIDATE_BOOLEAN
                            )) {
                                // skip products with out-of-stock variations by requested attributes
                                global $wpdb;

                                $visibilityTerms = wc_get_product_visibility_term_ids();

                                // using of an SQL query avoids cross wp_queries problems
                                $queryString =
                                    "SELECT $wpdb->posts.post_parent, $wpdb->term_relationships.term_taxonomy_id "
                                    . "FROM $wpdb->posts "
                                    . "LEFT JOIN $wpdb->term_relationships "
                                    . "ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) "
                                    . "INNER JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) "
                                    . "WHERE $wpdb->posts.post_type = 'product_variation' "
                                    . "AND $wpdb->posts.post_status IN ('publish', 'private') "
                                    . "AND ($wpdb->postmeta.meta_key = 'attribute_pa_{$key}' "
                                    . "AND $wpdb->postmeta.meta_value IN ("
                                    . implode(',', array_map(
                                        function ($value) {
                                            return "'" . esc_sql($value) . "'";
                                        },
                                        get_terms([
                                            'taxonomy' => "pa_{$key}",
                                            'include' => $value,
                                            'fields' => 'id=>slug'
                                        ])
                                    ))
                                    . "))";

                                $results = $wpdb->get_results($queryString);
                                $productsIds = [];

                                foreach ($results as $result) {
                                    $productsIds[$result->post_parent][] = (int) $result->term_taxonomy_id;
                                }

                                foreach ($productsIds as $id => $variations) {
                                    $counts = array_count_values($variations);

                                    if (isset($counts[$visibilityTerms['outofstock']])
                                        && $counts[$visibilityTerms['outofstock']] == count($variations)
                                    ) {
                                        $output['post__not_in'][] = $id;
                                    }
                                }
                            }

                            break;
                        }

                        // attribute range
                        $filterBy = [];
                        $terms = get_terms(['taxonomy' => "pa_{$key}"]);

                        foreach ($terms as $term) {
                            $termValue = (float) $term->name;

                            $issetFirstValue = isset($value['from']) && !empty($value['from']);
                            $firstCondition = $value['from'] <= $termValue;
                            $issetSecondValue = isset($value['to']) && !empty($value['to']);
                            $secondCondition = $value['to'] >= $termValue;

                            if (($issetFirstValue && $firstCondition && $issetSecondValue && $secondCondition)
                                || (!$issetSecondValue && $issetFirstValue && $firstCondition)
                                || (!$issetFirstValue && $issetSecondValue && $secondCondition)
                            ) {
                                $filterBy[] = $term->term_id;
                            }
                        }

                        if (!empty($filterBy)) {
                            $taxQuery[] = [
                                'taxonomy' => "pa_{$key}",
                                'field' => 'id',
                                'terms' => $filterBy,
                                'operator' => 'IN'
                            ];
                        }
                    }
                }
            }
        }

        if (!empty($metaQuery)) {
            $output['meta_query'] = $metaQuery;
        }

        if (!empty($taxQuery)) {
            $output['tax_query'] = $taxQuery;
        }

        return apply_filters('wcpw_filter_args_to_query', $output, $data);
    }

    /**
     * WP_Query pre-get posts action
     *
     * @param \WP_Query $query
     */
    public function preGerPostsFilter($query)
    {
        // work only with the wizard search
        if (!isset($query->query['wcpw_search']) || empty($query->query['wcpw_search'])) {
            return;
        }

        list($wizardId, $stepId) = explode('-', $query->query['wcpw_search']);

        if (!$wizardId || !$stepId) {
            return;
        }

        $searchBy = Settings::getStep($wizardId, $stepId, 'filter_search_by');

        if (!$searchBy || empty($searchBy)) {
            return;
        }

        global $wpdb;

        $n = !empty($query->query_vars['exact']) ? '' : '%';
        $term = $n . $wpdb->esc_like($query->get('s')) . $n;
        $searchParts = [];

        // reset default query string
        $query->set('s', '');

        if (in_array('post_title', $searchBy)) {
            $searchParts[] = $wpdb->prepare("($wpdb->posts.post_title LIKE %s)", $term);
        }

        if (in_array('post_content', $searchBy)) {
            $searchParts[] = $wpdb->prepare("($wpdb->posts.post_content LIKE %s)", $term);
        }

        if (in_array('post_excerpt', $searchBy)) {
            $searchParts[] = $wpdb->prepare("($wpdb->posts.post_excerpt LIKE %s)", $term);
        }

        if (in_array('product_cat', $searchBy)) {
            $searchParts[] = $wpdb->prepare('(wcpw_tt.taxonomy = %s AND wcpw_t.name LIKE %s)', 'product_cat', $term);
        }

        if (in_array('product_tag', $searchBy)) {
            $searchParts[] = $wpdb->prepare('(wcpw_tt.taxonomy = %s AND wcpw_t.name LIKE %s)', 'product_tag', $term);
        }

        $search = '(' . implode('OR', $searchParts) . ')';

        if (!empty($search)) {
            $search = " AND ({$search}) ";
        }

        add_filter('posts_join_request', [$this, 'postsQueryJointSearchFilter']);
        add_filter('posts_distinct_request', [$this, 'postsQueryDistinctSearchFilter']);
        add_filter('posts_search', function () use ($search) {
            return $search;
        });
    }

    /**
     * WP_Query request "distinct" part filter
     *
     * @return string
     */
    public function postsQueryDistinctSearchFilter()
    {
        return 'DISTINCT';
    }

    /**
     * WP_Query request "joint" part filter
     *
     * @param string $join
     *
     * @return string
     */
    public function postsQueryJointSearchFilter($join)
    {
        global $wpdb;

        $join .= " LEFT JOIN $wpdb->term_relationships wcpw_tr ON ($wpdb->posts.ID = wcpw_tr.object_id) ";
        $join .= " LEFT JOIN $wpdb->term_taxonomy wcpw_tt ON (wcpw_tr.term_taxonomy_id = wcpw_tt.term_taxonomy_id) ";
        $join .= " LEFT JOIN $wpdb->terms wcpw_t ON (wcpw_tt.term_id = wcpw_t.term_id) ";

        return $join;
    }
    // </editor-fold>

    /**
     * Get minimum quantity value
     *
     * @param integer $wizardId
     * @param integer|string $stepId
     *
     * @return integer
     */
    public static function getMinQuantity($wizardId, $stepId)
    {
        return self::getQuantityValue($wizardId, $stepId);
    }

    /**
     * Get max quantity value
     *
     * @param integer $wizardId
     * @param integer|string $stepId
     * @param \WC_Product $product
     *
     * @return integer
     */
    public static function getMaxQuantity($wizardId, $stepId, $product = null)
    {
        $output = self::getQuantityValue($wizardId, $stepId, 'max');

        // check product stock quantity
        if ($product) {
            $manageStock = false;

            if (method_exists($product, 'get_manage_stock')) {
                $manageStock = $product->get_manage_stock();
            } elseif (isset($product->manage_stock)) {
                $manageStock = $product->manage_stock;
            }

            if (filter_var($manageStock, FILTER_VALIDATE_BOOLEAN)) {
                $stock = (int) $product->get_stock_quantity();

                if ($output) {
                    return min($output, $stock);
                } else {
                    return $stock;
                }
            }
        }

        return $output;
    }

    /**
     * Get min or max quantity value
     *
     * @param integer $wizardId
     * @param integer|string $stepId
     * @param string $type
     *
     * @return integer
     */
    public static function getQuantityValue($wizardId, $stepId, $type = 'min')
    {
        $rule = Settings::getStep($wizardId, $stepId, "{$type}_product_quantity");

        if (!$rule) {
            return 0;
        }

        if (!is_array($rule)) {
            // @since 3.19.0 - older versions support
            $rule = [
                'type' => 'count',
                'value' => $rule
            ];
        }

        return Form::checkStepQuantitiesRule($wizardId, $rule);
    }

    /**
     * Get product's terms ids array
     *
     * @param integer $productId
     * @param array $args
     *
     * @return array
     */
    public static function getTermsIds($productId, $args = [])
    {
        $defaults = [
            'taxonomy' => 'product_cat',
            'all' => false // get also parent terms even aren't attached
        ];

        $args = array_replace($defaults, $args);
        $taxonomy = $args['taxonomy'];

        static $termsIds = [];

        if (isset($termsIds[$taxonomy][$productId])) {
            return $termsIds[$taxonomy][$productId];
        }

        $output = wp_get_post_terms($productId, $taxonomy, ['fields' => 'ids']);

        if ($args['all']) {
            foreach ($output as $termId) {
                $output = array_merge($output, get_ancestors($termId, $taxonomy, 'taxonomy'));
            }
        }

        $termsIds[$taxonomy][$productId] = $output;

        return $output;
    }

    /**
     * Apply wizard's price discount
     *
     * @param float $price
     * @param integer $wizardId
     * @param \WC_Product $product
     *
     * @return float
     */
    public static function applyPriceDiscount($price, $wizardId, $product)
    {
        static $noRulesProducts = [];

        // will be no rules
        if (isset($noRulesProducts[$wizardId], $noRulesProducts[$wizardId][$product->get_id()])) {
            return $price;
        }

        $commonDiscount = (float) Settings::getPost($wizardId, 'price_discount');
        $productDiscount = self::getDiscountRule($wizardId, $product);

        // have no discount at all
        if (empty($productDiscount) && !$commonDiscount) {
            if (!isset($noRulesProducts[$wizardId])) {
                $noRulesProducts[$wizardId] = [];
            }

            $noRulesProducts[$wizardId][$product->get_id()] = $product->get_id();

            return $price;
        }

        // have some discount rule
        if (!empty($productDiscount)) {
            $price = self::handlePriceWithDiscountRule($price, $productDiscount);
        } elseif ($commonDiscount) {
            $price = max(0, (float) $price - ((float) $price * $commonDiscount / 100));
        }

        return $price;
    }

    // <editor-fold desc="Deprecated">
    /**
     * Get variation view type
     *
     * @param integer $wizardId
     *
     * @return string
     *
     * @deprecated 4.0.0 Use Settings::getProduct($wizardId, 'item_variations_template')
     */
    public static function getVariationType($wizardId)
    {
        return Settings::getProduct($wizardId, 'item_variations_template');
    }
    // </editor-fold>
}
