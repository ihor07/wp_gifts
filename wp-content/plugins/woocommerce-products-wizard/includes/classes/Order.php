<?php
namespace WCProductsWizard;

/**
 * Order Class
 *
 * @class Order
 * @version 2.7.0
 */
class Order
{
    /** Class Constructor */
    public function __construct()
    {
        // admin
        add_filter('woocommerce_hidden_order_itemmeta', [$this, 'hiddenMetaFilter']);
        add_filter('woocommerce_admin_order_item_thumbnail', [$this, 'adminOrderItemThumbnailFilter'], 10, 3);

        // create
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'createOrderLineAction'], 10, 3);
        add_action('woocommerce_checkout_update_order_meta', [$this, 'updateOrderMetaAction']);

        // order item
        add_filter('woocommerce_order_item_visible', [$this, 'itemVisibilityFilter'], 20, 2);
        add_filter('woocommerce_order_item_thumbnail', [$this, 'orderItemThumbnailFilter'], 10, 2);
        add_filter('woocommerce_order_formatted_line_subtotal', [$this, 'orderFormattedLineSubtotalFilter'], 10, 3);
        add_filter('woocommerce_order_item_get_formatted_meta_data', [$this, 'itemFormattedMetaData']);

        // output
        add_action('woocommerce_after_order_notes', [$this, 'checkoutFormAction']);

        // core
        add_filter('woocommerce_is_checkout', [$this, 'isCheckoutFilter']);
    }

    /**
     * Is the page checkout filter
     *
     * @param bool $value
     *
     * @return bool
     */
    public function isCheckoutFilter($value)
    {
        if (wc_post_content_has_shortcode('woocommerce-products-wizard')) {
            global $post;

            if (preg_match_all('/woocommerce-products-wizard id="(.*?)"/s', $post->post_content, $matches)) {
                if (isset($matches[1]) && !empty($matches[1])) {
                    $id = reset($matches[1]);

                    if ($id && Settings::getPost($id, 'enable_checkout_step')
                        && Settings::getPost($id, 'mode') != 'single-step'
                    ) {
                        return true;
                    }
                }
            }
        }

        return $value;
    }

    /** Output checkout form action */
    public function checkoutFormAction()
    {
        if (did_action('wcpw_before_output') == did_action('wcpw_after_output') + 1) {
            // output wizard id value to handle it when checkout is complete
            echo '<input type="hidden" class="input-hidden" name="wcpw" id="wcpw" value="'
                . Instance()->getCurrentId() . '">';
        }
    }

    /** Update order meta action */
    public function updateOrderMetaAction()
    {
        if (!empty($_POST['wcpw'])) {
            // reset wizard state when inner checkout is complete
            $id = (int) $_POST['wcpw'];
            $stepsIds = Form::getStepsIds($id);

            // truncate the cart
            Cart::truncate($id);

            // reset active step to the first
            Form::setActiveStep($id, reset($stepsIds));
            Form::resetPreviousStepId($id);
        }
    }

    /**
     * Order product hidden meta filter
     *
     * @param array $items
     *
     * @return array
     */
    public function hiddenMetaFilter($items)
    {
        if (!is_admin()) {
            $items[] = 'wizard_id';
            $items[] = 'wizard_step';
            $items[] = 'wizard_kit';
            $items[] = 'wizard_pdf';
        }

        $items[] = '_wcpw_thumbnail';
        $items[] = '_wcpw_is_hidden_product';
        $items[] = '_wcpw_kit_price';

        return $items;
    }

    /**
     * Order line created action
     *
     * @param \WC_Order_Item_Product $item
     * @param string $cartItemKey
     * @param array $values
     */
    public function createOrderLineAction($item, $cartItemKey, $values)
    {
        // wizard and step IDs
        if (isset($values['wcpw_id'], $values['wcpw_step_name'])) {
            $item->add_meta_data('wizard_id', $values['wcpw_id']);
            $item->add_meta_data('wizard_step', $values['wcpw_step_name']);
        }

        // thumbnail URL
        if (isset($values['wcpw_kit_thumbnail_url'])) {
            $item->add_meta_data('_wcpw_thumbnail', $values['wcpw_kit_thumbnail_url']);
        }

        // kit price
        if (isset($values['wcpw_kit_price'])) {
            $item->add_meta_data('_wcpw_kit_price', $values['wcpw_kit_price']);
        }

        // kit base price
        if (isset($values['wcpw_kit_base_price'], $values['wcpw_kit_base_price_string'])) {
            $item->add_meta_data($values['wcpw_kit_base_price_string'], wc_price($values['wcpw_kit_base_price']));
        }

        // invisibility product
        if (isset($values['wcpw_is_hidden_product'])) {
            $item->add_meta_data('_wcpw_is_hidden_product', $values['wcpw_is_hidden_product']);
        }

        // kit pdf
        if (isset($values['wcpw_kit_pdf'])) {
            $item->add_meta_data(
                'wizard_pdf',
                '<a href="' . esc_attr($values['wcpw_kit_pdf']) . '">' . basename($values['wcpw_kit_pdf']) . '</a>'
            );
        }

        // children to a grouped kit product
        if (isset($values['wcpw_kit_children'], $values['wcpw_kit_type'])
            && !empty($values['wcpw_kit_children']) && $values['wcpw_kit_type'] == 'combined'
        ) {
            foreach ($values['wcpw_kit_children'] as $child) {
                $data = Cart::getKitChildData($child, $values['wcpw_id'], ['uploadsSourceType' => 'link']);
                $item->add_meta_data($data['key'], $data['display']);
            }

            return;
        }

        // kit id to an order's lines
        if (!empty($values['wcpw_kit_id'])) {
            static $kitNumber = 0;
            static $kitId = null;

            if (is_null($kitId)) {
                $kitId = $values['wcpw_kit_id'];
            }

            if ($kitId != $values['wcpw_kit_id']) {
                if ($kitNumber == 0) {
                    $kitNumber = 1;
                }

                $kitNumber++;
                $kitId = $values['wcpw_kit_id'];
            }

            $item->add_meta_data(
                'wizard_kit',
                $values['wcpw_kit_title'] . ($kitNumber ? " ($kitNumber)" : '')
            );
        }
    }

    /**
     * Order product visibility filter
     *
     * @param bool $visible
     * @param \WC_Order_Item_Product $item
     *
     * @return bool
     */
    public function itemVisibilityFilter($visible, $item)
    {
        $meta = $item->get_meta_data();

        foreach ($meta as $metaItem) {
            if (!$metaItem instanceof \WC_Meta_Data) {
                continue;
            }

            $itemData = $metaItem->get_data();

            if ($itemData['key'] == '_wcpw_is_hidden_product') {
                return !$itemData['value'];
            }
        }

        return $visible;
    }

    /**
     * Order product thumbnail filter
     *
     * @param string $image
     * @param \WC_Order_Item $item
     *
     * @return string
     */
    public function orderItemThumbnailFilter($image, $item)
    {
        $meta = $item->get_meta_data();

        foreach ($meta as $metaItem) {
            if (!$metaItem instanceof \WC_Meta_Data) {
                continue;
            }

            $itemData = $metaItem->get_data();

            if ($itemData['key'] == '_wcpw_thumbnail') {
                return $itemData['value'];
            }
        }

        return $image;
    }

    /**
     * Order product thumbnail filter in the admin part
     *
     * @param string $image
     * @param integer $id
     * @param \WC_Order_Item $item
     *
     * @return string
     */
    public function adminOrderItemThumbnailFilter($image, $id, $item)
    {
        if (!method_exists($item, 'get_meta_data')) {
            return $image;
        }

        $meta = $item->get_meta_data();

        foreach ($meta as $metaItem) {
            if (!$metaItem instanceof \WC_Meta_Data) {
                continue;
            }

            $itemData = $metaItem->get_data();

            if ($itemData['key'] != '_wcpw_thumbnail') {
                continue;
            }

            $attributes = [
                'src' => $itemData['value'],
                'alt' => get_the_title($item->get_id())
            ];

            $attributes = apply_filters('wcpw_order_item_generated_thumbnail_attributes', $attributes, $itemData);
            $output = '<img ' . Utils::attributesArrayToString($attributes) . '>';

            return apply_filters('wcpw_order_item_generated_thumbnail', $output, $itemData);
        }

        return $image;
    }

    /**
     * Order product subtotal filter
     *
     * @param string $subtotal
     * @param \WC_Order_Item $item
     * @param \WC_Order $order
     *
     * @return string
     */
    public function orderFormattedLineSubtotalFilter($subtotal, $item, $order)
    {
        if (is_admin()) {
            return $subtotal;
        }

        $meta = $item->get_meta_data();

        foreach ($meta as $metaItem) {
            if (!$metaItem instanceof \WC_Meta_Data) {
                continue;
            }

            $itemData = $metaItem->get_data();

            if ($itemData['key'] != '_wcpw_kit_price') {
                continue;
            }

            if ('excl' === get_option('woocommerce_tax_display_cart')) {
                $subtotal = wc_price(
                    $itemData['value'],
                    [
                        'ex_tax_label' => $order->get_prices_include_tax() ? 1 : 0,
                        'currency' => $order->get_currency()
                    ]
                );
            } else {
                $subtotal = wc_price($itemData['value'], ['currency' => $order->get_currency()]);
            }
        }

        return $subtotal;
    }

    /**
     * Order item formatted meta
     *
     * @param array $meta
     *
     * @return array
     */
    public function itemFormattedMetaData($meta)
    {
        if (is_admin() && did_action('woocommerce_admin_order_item_headers')) {
            // show wizard's meta only in the admin part
            return $meta;
        }

        // remove inner admin keys from checkout page and emails
        $keysToRemove = [
            'wizard_id',
            'wizard_step',
            'wizard_kit',
            'wizard_pdf'
        ];

        foreach ($meta as $key => $item) {
            if (in_array($item->key, $keysToRemove)) {
                unset($meta[$key]);
            }
        }

        return $meta;
    }
}
