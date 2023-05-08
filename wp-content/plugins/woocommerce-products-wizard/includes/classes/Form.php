<?php
namespace WCProductsWizard;

/**
 * Form Class
 *
 * @class Form
 * @version 7.13.0
 */
class Form
{
    //<editor-fold desc="Properties">
    /**
     * Active steps session keys variable
     * @var string
     */
    public static $activeStepsSessionKey = 'woocommerce-products-wizard-active-step';

    /**
     * Previous steps session keys variable
     * @var string
     */
    public static $previousStepsSessionKey = 'woocommerce-products-wizard-previous-step';

    /**
     * Ajax actions variable
     * @var array
     */
    public $ajaxActions = [];

    /**
     * Notices array
     * @var array
     */
    public $notices = [];
    //</editor-fold>

    // <editor-fold desc="Core">
    /** Class Constructor */
    public function __construct()
    {
        $this->ajaxActions = apply_filters(
            'wcpw_form_ajax_actions',
            [
                'wcpwSubmit' => 'submitAjax',
                'wcpwAddToMainCart' => 'addToMainCartAjax',
                'wcpwGetStep' => 'getAjax',
                'wcpwSkipStep' => 'skipAjax',
                'wcpwSubmitAndSkipAll' => 'submitAndSkipAllAjax',
                'wcpwSkipAll' => 'skipAllAjax',
                'wcpwReset' => 'resetAjax',
                'wcpwAddCartProduct' => 'addCartProductAjax',
                'wcpwRemoveCartProduct' => 'removeCartProductAjax',
                'wcpwUpdateCartProduct' => 'updateCartProductAjax',
                'wcpwAddCartStepData' => 'addCartStepDataAjax',
                'wcpwRemoveCartStepData' => 'removeCartStepDataAjax',
                'wcpwSearch' => 'searchAjax'
            ]
        );

        add_action('wp_loaded', [$this, 'requests']);

        foreach ($this->ajaxActions as $ajaxActionKey => $ajaxActionFunction) {
            add_action("wp_ajax_nopriv_{$ajaxActionKey}", [$this, $ajaxActionFunction]);
            add_action("wp_ajax_{$ajaxActionKey}", [$this, $ajaxActionFunction]);
        }
    }

    /** Add request actions */
    public function requests()
    {
        if (is_admin()) {
            return;
        }

        $request = $_REQUEST;

        // handle no-js forms actions
        if (isset($request['woocommerce-products-wizard'], $request['id'])) {
            // adding a product to the cart handler
            if (isset($request['add-cart-product'])) {
                try {
                    $request['productToAddKey'] = $request['add-cart-product'];
                    $this->addCartProduct($request);
                } catch (\Exception $exception) {
                    $this->addNotice(
                        $exception->getCode() ? $exception->getCode() : self::getActiveStepId($request['id']),
                        [
                            'view' => 'custom',
                            'message' => $exception->getMessage()
                        ]
                    );

                    return;
                }
            }

            // updating a product in the cart handler
            if (isset($request['update-cart-product'])) {
                try {
                    $request['productCartKey'] = $request['update-cart-product'];
                    $this->updateCartProduct($request);
                } catch (\Exception $exception) {
                    $this->addNotice(
                        $exception->getCode() ? $exception->getCode() : self::getActiveStepId($request['id']),
                        [
                            'view' => 'custom',
                            'message' => $exception->getMessage()
                        ]
                    );

                    return;
                }
            }

            // removing a product from the cart handler
            if (isset($request['remove-cart-product'])) {
                try {
                    $request['productCartKey'] = $request['remove-cart-product'];
                    $this->removeCartProduct($request);
                } catch (\Exception $exception) {
                    $this->addNotice(
                        $exception->getCode() ? $exception->getCode() : self::getActiveStepId($request['id']),
                        [
                            'view' => 'custom',
                            'message' => $exception->getMessage()
                        ]
                    );

                    return;
                }
            }

            // adding step date to the cart handler
            if (isset($request['add-cart-step-data'])) {
                try {
                    $request['stepDataToAddKey'] = $request['add-cart-step-data'];
                    $this->addCartStepData($request);
                } catch (\Exception $exception) {
                    $this->addNotice(
                        $exception->getCode() ? $exception->getCode() : self::getActiveStepId($request['id']),
                        [
                            'view' => 'custom',
                            'message' => $exception->getMessage()
                        ]
                    );

                    return;
                }
            }

            // removing step data from the cart handler
            if (isset($request['remove-cart-step-data'])) {
                try {
                    $request['stepDataKey'] = $request['remove-cart-step-data'];
                    $this->removeCartStepData($request);
                } catch (\Exception $exception) {
                    $this->addNotice(
                        $exception->getCode() ? $exception->getCode() : self::getActiveStepId($request['id']),
                        [
                            'view' => 'custom',
                            'message' => $exception->getMessage()
                        ]
                    );

                    return;
                }
            }

            // add all to main cart but not for attached to a product wizard
            if (isset($request['add-to-main-cart']) && !isset($request['attach-to-product'])) {
                try {
                    $this->addToMainCart($request);
                } catch (\Exception $exception) {
                    $this->addNotice(
                        $exception->getCode() ? $exception->getCode() : self::getActiveStepId($request['id']),
                        [
                            'view' => 'custom',
                            'message' => $exception->getMessage()
                        ]
                    );
                }
            }

            // submit form handler
            if (isset($request['submit'])) {
                try {
                    if (is_numeric($request['submit'])) {
                        // get specific step
                        $request['stepId'] = $request['submit'];
                    }

                    $this->submit($request);
                } catch (\Exception $exception) {
                    $this->addNotice(
                        $exception->getCode() ? $exception->getCode() : self::getActiveStepId($request['id']),
                        [
                            'view' => 'custom',
                            'message' => $exception->getMessage()
                        ]
                    );

                    return;
                }
            }

            // submit and skip all form handler
            if (isset($request['submit-and-skip-all'])) {
                try {
                    $this->submitAndSkipAll($request);
                } catch (\Exception $exception) {
                    $this->addNotice(
                        $exception->getCode() ? $exception->getCode() : self::getActiveStepId($request['id']),
                        [
                            'view' => 'custom',
                            'message' => $exception->getMessage()
                        ]
                    );

                    return;
                }
            }

            // simple actions
            if (isset($request['reset-all']) || isset($request['reset'])) {
                // "reset-all" is needed cause of the native "reset" action
                self::reset($request);
            }

            if (isset($request['skip-step'])) {
                self::skip($request);
            }

            if (isset($request['skip-all'])) {
                self::skipAll($request);
            }

            if (isset($request['get-step'])) {
                $request['stepId'] = $request['get-step'];

                self::get($request);
            }
        } elseif (isset($request['wcpwId'], $request['wcpwShare'])) {
            // force set cart from URL and do redirect
            Cart::set($request['wcpwId'], Cart::parseFromString($request['wcpwId'], $_REQUEST['wcpwCart']));

            $url = 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '')
                . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

            wp_redirect(str_replace('wcpwShare', '', $url));
            exit;
        } elseif (isset($request['wcpwId'], $request['wcpwStep']) && $request['wcpwId'] && $request['wcpwStep']) {
            // set active step from URL
            self::setActiveStep($request['wcpwId'], $request['wcpwStep']);
        }
    }

    /**
     * Return an AJAX reply and exit
     *
     * @param array $data
     * @param array $postData
     */
    public static function ajaxReply($data, $postData = [])
    {
        if (!empty($postData) && isset($postData['id']) && $postData['id']) {
            if (Settings::getGlobal('send_state_hash_ajax')) {
                $data['stateHash'] = md5(serialize([
                    'cart' => Cart::get($postData['id']),
                    'stepId' => self::getActiveStepId($postData['id'])
                ]));
            }

            $saveStateToURL = Settings::getPost($postData['id'], 'save_state_to_url');
            $data['saveStateToURL'] = $saveStateToURL;

            if ($saveStateToURL
                || in_array('share', Settings::getPost($postData['id'], 'header_controls'))
                || in_array('share', Settings::getPost($postData['id'], 'footer_controls'))
            ) {
                $data['id'] = (int) $postData['id'];
                $data['stepId'] = self::getActiveStepId($postData['id']);
                $data['cart'] = Cart::getCompressed($postData['id']);
            }
        }
        
        Utils::sendJSON($data);
    }
    // </editor-fold>

    // <editor-fold desc="Notices">
    /**
     * Add notice by type into a queue
     *
     * @param string $stepId
     * @param array $massageData
     */
    public function addNotice($stepId, $massageData)
    {
        $this->notices[$stepId][] = $massageData;
    }

    /**
     * Return the queue of notices
     *
     * @param string $stepId - try to get messages from one step by id or output all of messages
     *
     * @return array
     */
    public function getNotices($stepId = null)
    {
        if ($stepId) {
            // return step's messages array or nothing
            return isset($this->notices[$stepId]) ? $this->notices[$stepId] : [];
        } else {
            // return all steps messages
            return array_reduce($this->notices, 'array_merge', []);
        }
    }
    // </editor-fold>

    // <editor-fold desc="Check rules">
    /**
     * Check products min/max quantities step rule
     *
     * @param integer $wizardId
     * @param array $rule
     *
     * @return integer
     */
    public static function checkStepQuantitiesRule($wizardId, $rule)
    {
        $output = 0;

        switch ($rule['type']) {
            case 'selected-from-step': {
                foreach (Cart::get($wizardId, ['includeSteps' => wp_parse_id_list($rule['value'])]) as $cartItem) {
                    if (isset($cartItem['product_id'])) {
                        $output++;
                    }
                }

                break;
            }

            case 'least-from-step': {
                $min = null;

                foreach (Cart::get($wizardId, ['includeSteps' => wp_parse_id_list($rule['value'])]) as $cartItem) {
                    if (!isset($cartItem['quantity']) || !$cartItem['quantity']) {
                        continue;
                    }

                    if (is_null($min)) {
                        $min = $cartItem['quantity'];
                    }

                    $min = min($min, $cartItem['quantity']);
                }

                $output = (int) $min;
                break;
            }

            case 'greatest-from-step': {
                foreach (Cart::get($wizardId, ['includeSteps' => wp_parse_id_list($rule['value'])]) as $cartItem) {
                    if (!isset($cartItem['quantity']) || !$cartItem['quantity']) {
                        continue;
                    }

                    $output = max($output, $cartItem['quantity']);
                }

                break;
            }

            case 'sum-from-step': {
                foreach (Cart::get($wizardId, ['includeSteps' => wp_parse_id_list($rule['value'])]) as $cartItem) {
                    if (!isset($cartItem['quantity']) || !$cartItem['quantity']) {
                        continue;
                    }

                    $output += $cartItem['quantity'];
                }

                break;
            }

            case 'step-input-value': {
                $cartItem = Cart::getStepDataByKey($wizardId, $rule['value']);
                $output = $cartItem && isset($cartItem['value']) ? (float) $cartItem['value'] : 0;

                break;
            }

            default:
            case 'count':
                $output = (float) $rule['value'];
        }

        return apply_filters('wcpw_step_quantities_rule', (float) $output, $wizardId, $rule);
    }

    /**
     * Check step quantities and other rules
     *
     * @param array $args
     * @param string $stepId
     *
     * @throws \Exception
     */
    public static function checkStepRules($args, $stepId)
    {
        $defaults = [
            'id' => null,
            'checkMinProductsSelected' => true,
            'checkMaxProductsSelected' => true,
            'checkMinTotalProductsQuantity' => true,
            'checkMaxTotalProductsQuantity' => true,
            'productsToAdd' => [],
            'productsToAddChecked' => []
        ];

        $args = array_merge($defaults, $args);
        $totalQuantity = 0;
        $selectedCount = !isset($args['productsToAddChecked'][$stepId])
            || !is_array($args['productsToAddChecked'][$stepId])
            ? 0
            : count(array_filter($args['productsToAddChecked'][$stepId]));

        foreach ((array) $args['productsToAdd'] as $product) {
            if (!isset($product['step_id']) || $product['step_id'] != $stepId // wrong step ID
                || !isset($args['productsToAddChecked'][$stepId])
                || !is_array($args['productsToAddChecked'][$stepId]) // step have no selected products
                || (isset($product['product_id'])
                    && !in_array($product['product_id'], $args['productsToAddChecked'][$stepId]))
                    // step have no this product as selected
            ) {
                continue;
            }

            $totalQuantity += isset($product['quantity']) ? $product['quantity'] : 1;
        }

        // min products selected check
        if ($args['checkMinProductsSelected']) {
            $setting = Settings::getStep($args['id'], $stepId, 'min_products_selected');

            if ($setting) {
                if (!is_array($setting)) {
                    // @since 3.18.0 - older versions support
                    $setting = [
                        'type' => 'count',
                        'value' => $setting
                    ];
                }

                if (isset($setting['value']) && $setting['value']) {
                    $value = self::checkStepQuantitiesRule($args['id'], $setting);

                    if ($value && $selectedCount < $value) {
                        throw new \Exception(
                            Settings::getMinimumProductsSelectedMessage($args['id'], $value, $selectedCount),
                            $stepId
                        );
                    }
                }
            }
        }

        // max products selected check
        if ($args['checkMaxProductsSelected']) {
            $setting = Settings::getStep($args['id'], $stepId, 'max_products_selected');

            if ($setting) {
                if (!is_array($setting)) {
                    // @since 3.18.0 - older versions support
                    $setting = [
                        'type' => 'count',
                        'value' => $setting
                    ];
                }

                if (isset($setting['value']) && $setting['value']) {
                    $value = self::checkStepQuantitiesRule($args['id'], $setting);

                    if ($value && $selectedCount > $value) {
                        throw new \Exception(
                            Settings::getMaximumProductsSelectedMessage($args['id'], $value, $selectedCount),
                            $stepId
                        );
                    }
                }
            }
        }

        // min total products selected check
        if ($args['checkMinTotalProductsQuantity']) {
            $setting = Settings::getStep($args['id'], $stepId, 'min_total_products_quantity');

            if ($setting && isset($setting['value']) && $setting['value']) {
                $value = self::checkStepQuantitiesRule($args['id'], $setting);

                if ($value && $totalQuantity < $value) {
                    throw new \Exception(
                        Settings::getMinimumProductsSelectedMessage($args['id'], $value, $totalQuantity),
                        $stepId
                    );
                }
            }
        }

        // max total products quantity check
        if ($args['checkMaxTotalProductsQuantity']) {
            $setting = Settings::getStep($args['id'], $stepId, 'max_total_products_quantity');

            if ($setting && isset($setting['value']) && $setting['value']) {
                $value = self::checkStepQuantitiesRule($args['id'], $setting);

                if ($value && $totalQuantity > $value) {
                    throw new \Exception(
                        Settings::getMaximumProductsSelectedMessage($args['id'], $value, $totalQuantity),
                        $stepId
                    );
                }
            }
        }
    }

    /**
     * Check common quantities and other rules
     *
     * @param integer $wizardId
     * @param array $cart
     *
     * @throws \Exception
     */
    public static function checkCommonRules($wizardId, $cart)
    {
        $cartTotal = Cart::getTotal($wizardId, ['reCalculateDiscount' => true]);
        $totalProductsQuantity = 0;
        $productsSelectedCount = 0;

        foreach ((array) $cart as $cartItem) {
            if (!isset($cartItem['quantity'])) {
                continue;
            }

            $productsSelectedCount++;
            $totalProductsQuantity += $cartItem['quantity'];
        }

        $minProductsSelected = Settings::getPost($wizardId, 'min_products_selected');
        $maxProductsSelected = Settings::getPost($wizardId, 'max_products_selected');
        $minTotalQuantity = Settings::getPost($wizardId, 'min_total_products_quantity');
        $maxTotalQuantity = Settings::getPost($wizardId, 'max_total_products_quantity');
        $minProductsPrice = Settings::getPost($wizardId, 'min_products_price');
        $maxProductsPrice = Settings::getPost($wizardId, 'max_products_price');

        // min products selected check
        if ($minProductsSelected && $productsSelectedCount < $minProductsSelected) {
            throw new \Exception(
                Settings::getMinimumProductsSelectedMessage($wizardId, $minProductsSelected, $productsSelectedCount)
            );
        }

        // max products selected check
        if ($maxProductsSelected && $productsSelectedCount > $maxProductsSelected) {
            throw new \Exception(
                Settings::getMaximumProductsSelectedMessage($wizardId, $maxProductsSelected, $productsSelectedCount)
            );
        }

        // min total products quantity check
        if ($minTotalQuantity && $totalProductsQuantity < $minTotalQuantity) {
            throw new \Exception(
                Settings::getMinimumProductsSelectedMessage($wizardId, $minTotalQuantity, $totalProductsQuantity)
            );
        }

        // max total products quantity check
        if ($maxTotalQuantity && $totalProductsQuantity > $maxTotalQuantity) {
            throw new \Exception(
                Settings::getMaximumProductsSelectedMessage($wizardId, $maxTotalQuantity, $totalProductsQuantity)
            );
        }

        // min products price check
        if ($minProductsPrice && $cartTotal < $minProductsPrice) {
            throw new \Exception(Settings::getMinimumProductsPriceMessage($wizardId, $minProductsPrice, $cartTotal));
        }

        // max products price check
        if ($maxProductsPrice && $cartTotal > $maxProductsPrice) {
            throw new \Exception(Settings::getMaximumProductsPriceMessage($wizardId, $maxProductsPrice, $cartTotal));
        }
    }

    /**
     * Blend in cart quantities to check the rules
     *
     * @param integer $wizardId
     * @param array $qtyCheckArgs
     *
     * @return array
     */
    public static function getCartQuantitiesToCheck($wizardId, $qtyCheckArgs)
    {
        $cart = Cart::get($wizardId);
        $productsToAdd = (array) $qtyCheckArgs['productsToAdd'];
        $qtyCheckArgs['productsToAdd'] = [];

        // convert form input keys to hash keys
        foreach ($productsToAdd as $data) {
            $cartKey = Cart::generateProductId($data);
            $qtyCheckArgs['productsToAdd'][$cartKey] = $data;
        }

        foreach ($cart as $cartItem) {
            if (!isset($cartItem['product_id'])) {
                continue;
            }

            $cartKey = Cart::generateProductId($cartItem);

            // have no this product passed - take it from the cart
            if (!isset($qtyCheckArgs['productsToAdd'][$cartKey])) {
                $qtyCheckArgs['productsToAdd'][$cartKey] = $cartItem;
                $qtyCheckArgs['productsToAddChecked'][$cartItem['step_id']][] = $cartItem['product_id'];
            }
        }

        return $qtyCheckArgs;
    }
    // </editor-fold>

    // <editor-fold desc="Main actions">
    /**
     * Handles form submit
     *
     * @param array $args
     *
     * @return boolean
     *
     * @throws \Exception
     */
    public function submit($args)
    {
        $defaults = [
            'id' => null, // wizard ID
            'stepId' => null,
            'incrementActiveStep' => true,
            'dropNotCheckedProducts' => true,
            'passProducts' => true,
            'productsToAdd' => [],
            'productsToAddChecked' => [],
            'passStepData' => true,
            'stepsData' => []
        ];

        $args = array_merge($defaults, $args);
        $notCheckedProductsIds = [];

        do_action('wcpw_before_submit_form', $args);

        // make it an array for sure
        $args['productsToAddChecked'] = $args['productsToAddChecked'] == '[]'
            ? []
            : (array) $args['productsToAddChecked'];

        $stepsIds = array_unique(array_filter(array_keys($args['productsToAddChecked'])));
        $allStepsIds = self::getStepsIds($args['id'], ['checkAvailabilityRules' => false, 'idsOnly' => true]);
        $qtyCheckArgs = self::getCartQuantitiesToCheck($args['id'], $args);

        if (is_array($args['productsToAdd']) && !empty($args['productsToAdd'])) {
            foreach ($args['productsToAdd'] as $key => $data) {
                // emulate product selection for positive quantities according to the setting
                if (Settings::getStep($args['id'], $data['step_id'], 'add_to_cart_by_quantity')
                    && isset($data['quantity']) && (float) $data['quantity'] > 0
                ) {
                    $args['productsToAddChecked'][$data['step_id']][] = $data['product_id'];
                    $qtyCheckArgs['productsToAddChecked'][$data['step_id']][] = $data['product_id'];
                }

                if (isset($args['productsToAddChecked'][$data['step_id']])
                    && is_array($args['productsToAddChecked'][$data['step_id']])
                    && in_array($data['product_id'], $args['productsToAddChecked'][$data['step_id']])
                ) {
                    continue;
                }

                // collect product as not-checked and remove it from all args
                $notCheckedProductsIds[$data['step_id']][] = $data['product_id'];

                unset($args['productsToAdd'][$key]);

                $cartKey = Cart::generateProductId($data);

                if (isset($qtyCheckArgs['productsToAdd'][$cartKey])) {
                    unset($qtyCheckArgs['productsToAdd'][$cartKey]);
                }
            }
        }

        // need to check step rules before any actions
        foreach ($stepsIds as $stepId) {
            self::checkStepRules($qtyCheckArgs, $stepId);
        }

        foreach ($stepsIds as $stepId) {
            if (!Settings::getStep($args['id'], $stepId, 'several_products')) {
                Cart::removeByStepId($args['id'], $stepId, ['removeStepData' => false]);
            }

            if ($args['dropNotCheckedProducts'] && !empty($notCheckedProductsIds)) {
                foreach ($notCheckedProductsIds as $stepId => $productsIds) {
                    foreach ($productsIds as $productId) {
                        Cart::removeByProductId($args['id'], $productId, $stepId);
                    }
                }
            }

            if (Settings::getPost($args['id'], 'strict_cart_workflow')) {
                // remove products from the next steps
                $skip = true;

                foreach ($allStepsIds as $allStepId) {
                    if (!$skip) {
                        Cart::removeByStepId($args['id'], $allStepId);
                    }

                    if ((string) $allStepId == (string) $stepId) {
                        $skip = false;
                    }
                }
            }
        }

        if ($args['passStepData'] && is_array($args['stepsData']) && !empty($args['stepsData'])) {
            foreach ($args['stepsData'] as $stepId => $stepData) {
                if (!is_array($stepData)) {
                    continue;
                }

                foreach ($stepData as $key => $value) {
                    $data = [
                        'key' => $key,
                        'step_id' => $stepId,
                        'value' => $value
                    ];

                    Cart::addStepData($args['id'], apply_filters('wcpw_submit_form_item_data', $data, $args));
                }
            }
        }

        // files after args data
        if ($args['passStepData'] && isset($_FILES['stepsData']) && !empty($_FILES['stepsData'])) {
            // create uploads folder if not exists
            if (!file_exists(WC_PRODUCTS_WIZARD_UPLOADS_PATH)) {
                mkdir(WC_PRODUCTS_WIZARD_UPLOADS_PATH, 0777, true);
            }

            if (!file_exists(WC_PRODUCTS_WIZARD_UPLOADS_PATH . 'uploads')) {
                mkdir(WC_PRODUCTS_WIZARD_UPLOADS_PATH . 'uploads', 0777, true);
            }

            foreach ($_FILES['stepsData']['error'] as $stepId => $inputNames) {
                foreach ($inputNames as $input => $error) {
                    if (is_array($error)) {
                        // support <input multiple>
                        foreach ($error as $index => $errorItem) {
                            if ($errorItem != UPLOAD_ERR_OK
                                || $_FILES['stepsData']['size'][$stepId][$input][$index] > wp_max_upload_size()
                            ) {
                                throw new \Exception(
                                    Settings::getPost($args['id'], 'file_upload_max_size_error'),
                                    $stepId
                                );
                            }

                            $temp = $_FILES['stepsData']['tmp_name'][$stepId][$input][$index];
                            $name = basename($_FILES['stepsData']['name'][$stepId][$input][$index]);
                            $ext = pathinfo($name, PATHINFO_EXTENSION);
                            $validate = wp_check_filetype_and_ext($temp, $name);

                            if ($validate['proper_filename'] !== false) {
                                throw new \Exception(
                                    Settings::getPost($args['id'], 'file_upload_extension_error'),
                                    $stepId
                                );
                            }

                            $fileName = rtrim($name, ".$ext") . '-' . hash_file('md5', $temp) . "-$errorItem.$ext";
                            $path = WC_PRODUCTS_WIZARD_UPLOADS_PATH . "uploads/$fileName";
                            $path =
                                apply_filters('wcpw_submit_form_step_data_file_path', $path, $stepId, $input, $args);

                            if (!move_uploaded_file($temp, $path)) {
                                throw new \Exception(
                                    Settings::getPost($args['id'], 'file_upload_error'),
                                    $stepId
                                );
                            }

                            $data = [
                                'key' => "$input (" . ($index + 1) . ')',
                                'step_id' => $stepId,
                                'value' => $path,
                                'name' => $name,
                                'type' => 'file',
                                'is_image' => @is_array(getimagesize($path))
                            ];

                            Cart::addStepData($args['id'], apply_filters('wcpw_submit_form_item_data', $data, $args));
                        }
                    } else {
                        if ($error != UPLOAD_ERR_OK
                            || $_FILES['stepsData']['size'][$stepId][$input] > wp_max_upload_size()
                        ) {
                            throw new \Exception(
                                Settings::getPost($args['id'], 'file_upload_max_size_error'),
                                $stepId
                            );
                        }

                        $temp = $_FILES['stepsData']['tmp_name'][$stepId][$input];
                        $name = basename($_FILES['stepsData']['name'][$stepId][$input]);
                        $ext = pathinfo($name, PATHINFO_EXTENSION);
                        $validate = wp_check_filetype_and_ext($temp, $name);

                        if ($validate['proper_filename'] !== false) {
                            throw new \Exception(
                                Settings::getPost($args['id'], 'file_upload_extension_error'),
                                $stepId
                            );
                        }

                        $fileName = rtrim($name, ".$ext") . '-' . hash_file('md5', $temp) . ".$ext";
                        $path = WC_PRODUCTS_WIZARD_UPLOADS_PATH . "uploads/$fileName";
                        $path = apply_filters('wcpw_submit_form_step_data_file_path', $path, $stepId, $input, $args);

                        if (!move_uploaded_file($temp, $path)) {
                            throw new \Exception(
                                Settings::getPost($args['id'], 'file_upload_error'),
                                $stepId
                            );
                        }

                        $data = [
                            'key' => $input,
                            'step_id' => $stepId,
                            'value' => $path,
                            'name' => $name,
                            'type' => 'file',
                            'is_image' => @is_array(getimagesize($path))
                        ];

                        Cart::addStepData($args['id'], apply_filters('wcpw_submit_form_item_data', $data, $args));
                    }
                }
            }
        }

        if ($args['passProducts'] && is_array($args['productsToAdd']) && !empty($args['productsToAdd'])) {
            foreach ($args['productsToAdd'] as $data) {
                $defaultData = [
                    'product_id' => null,
                    'variation_id' => null,
                    'variation' => [],
                    'quantity' => 1,
                    'step_id' => null,
                    'data' => [],
                    'request' => []
                ];

                $data = array_replace($defaultData, $data);

                // if product isn't selected
                if (!($data['product_id'] && $data['step_id'] && $data['quantity'])
                    || !isset($args['productsToAddChecked'][$data['step_id']])
                    || !in_array($data['product_id'], $args['productsToAddChecked'][$data['step_id']])
                ) {
                    continue;
                }

                // find variation ID if necessary
                if (!empty($data['variation']) && !$data['variation_id']) {
                    $product = wc_get_product($data['product_id']);

                    if (!$product instanceof \WC_Product_Variable) {
                        continue;
                    }

                    $variations = $product->get_available_variations();
                    $excludedProductsIds = Settings::getStep($args['id'], $data['step_id'], 'excluded_products');

                    foreach ($variations as $variationKey => $variation) {
                        $rules = Settings::getProductVariation($variation['variation_id'], 'availability_rules');

                        if (in_array($variation['variation_id'], (array) $excludedProductsIds)
                            || !Utils::getAvailabilityByRules(
                                $args['id'],
                                $rules,
                                "product-variation-{$variation['variation_id']}"
                            )
                        ) {
                            unset($variations[$variationKey]);

                            continue;
                        }

                        $attributesMet = 0;

                        foreach ($variation['attributes'] as $attribute => $value) {
                            if (isset($data['variation'][$attribute])
                                && ($data['variation'][$attribute] == $value || $value == '')
                            ) {
                                $attributesMet++;
                            }
                        }

                        if (count($data['variation']) == $attributesMet) {
                            $data['variation_id'] = $variation['variation_id'];
                        }
                    }
                }

                try {
                    if (Settings::getStep($args['id'], $data['step_id'], 'several_variations_per_product')
                        && $data['variation_id'] && !empty($data['variation'])
                    ) {
                        $key = Cart::getKeyByVariationData($args['id'], $data['variation_id'], $data['variation'], $data['step_id']); // phpcs:ignore
                    } else {
                        $key = Cart::getKeyByProductId($args['id'], $data['product_id'], $data['step_id']);
                    }

                    if ($key) {
                        Cart::removeByCartKey($args['id'], $key);
                    }

                    Cart::addProduct($args['id'], apply_filters('wcpw_submit_form_item_data', $data, $args));
                } catch (\Exception $exception) {
                    throw new \Exception($exception->getMessage(), $data['step_id']);
                }
            }
        }

        // change active step
        if ($args['stepId']) {
            $this->setActiveStep($args['id'], $args['stepId']);
        } elseif (filter_var($args['incrementActiveStep'], FILTER_VALIDATE_BOOLEAN)) {
            $this->setActiveStep($args['id'], $this->getNextStepId($args['id']));
        }

        // clear cart cache
        Cart::clearCache($args['id']);
        Utils::clearAvailabilityRulesCache($args['id']);

        do_action('wcpw_after_submit_form', $args);

        return true;
    }

    /** Handles form submit via ajax */
    public function submitAjax()
    {
        $postData = Utils::parseArrayOfJSONs($_POST);

        try {
            $this->submit($postData);
        } catch (\Exception $exception) {
            $this->addNotice(
                $exception->getCode() ? $exception->getCode() : self::getActiveStepId($postData['id']),
                [
                    'view' => 'custom',
                    'message' => $exception->getMessage()
                ]
            );

            self::ajaxReply(
                [
                    'hasError' => true,
                    'message' => $exception->getMessage(),
                    'content' => Template::html('router', $postData, ['echo' => false])
                ],
                $postData
            );
        }

        self::ajaxReply(['content' => Template::html('router', $postData, ['echo' => false])], $postData);
    }

    /**
     * Handles adding products to the cart
     *
     * @param array $args
     *
     * @return array - products added with keys
     *
     * @throws \Exception
     */
    public function addToMainCart($args)
    {
        $defaults = [
            'id' => null,
            'stepId' => null,
            'incrementActiveStep' => false,
            'innerCheckout' => null,
            'editCartItem' => null,
            'currentPageURL' => null
        ];

        $args = array_merge($defaults, $args);
        $id = $args['id'];
        $mode = Settings::getPost($id, 'mode');
        $enableCheckoutStep = $mode != 'single-step' && Settings::getPost($id, 'enable_checkout_step');
        $stepId = $args['stepId'];
        $output = [];

        // don't pass step id to the submit method
        unset($args['stepId']);

        if (is_null($args['innerCheckout'])) {
            $args['innerCheckout'] = $enableCheckoutStep;
        }

        // submit step once again
        $this->submit($args);
        $stepsIds = self::getStepsIds($id);
        $cart = Cart::get($id);
        $cart = apply_filters('wcpw_add_all_to_main_cart_items', $cart, $id);

        do_action('wcpw_before_add_all_to_main_cart', $id, $cart);

        // <editor-fold desc="Steps rules check">
        $qtyCheckArgs = [
            'productsToAddChecked' => [],
            'productsToAdd' => []
        ];

        $qtyCheckArgs = self::getCartQuantitiesToCheck($id, $qtyCheckArgs);

        // check each step once again for all pages
        foreach ($stepsIds as $_stepId) {
            self::checkStepRules($qtyCheckArgs, $_stepId);
        }
        // </editor-fold>

        // common price and quantity rules
        self::checkCommonRules($id, $cart);

        // products already should be in the cart
        if (Settings::getPost($id, 'reflect_in_main_cart')) {
            if ($args['innerCheckout']) {
                // change active step
                if ($stepId) {
                    $this->setActiveStep($args['id'], $stepId);
                } elseif ($enableCheckoutStep) {
                    $this->setActiveStep($args['id'], $this->getNextStepId($args['id']));
                }
            }

            return $output;
        }

        if (Settings::getPost($id, 'clear_main_cart_on_confirm')) {
            // clear main cart
            WC()->cart->empty_cart();
        } elseif ($args['innerCheckout']) {
            // remove previously added main cart items from the same wizard
            foreach (WC()->cart->get_cart() as $key => $item) {
                if (isset($item['wcpw_inner_checkout']) && $item['wcpw_inner_checkout'] == $id) {
                    WC()->cart->remove_cart_item($key);
                }
            }
        }

        // remove cart item while editing
        if ($args['editCartItem']) {
            WC()->cart->remove_cart_item($args['editCartItem']);
        }

        // main work lower
        if (!empty($cart)) {
            $groupProductsIntoKits = Settings::getPost($id, 'group_products_into_kits');
            $kitsType = Settings::getPost($id, 'kits_type');
            $kitBaseProduct = Settings::getPost($id, 'kit_base_product');
            $kitId = null;
            $kitTitle = null;
            $isKitQuantityFixed = false;
            $rootKitItemCartKey = null;
            $rootKitItemKey = null;
            $rootKitItem = null;
            $commonDiscount = Settings::getPost($id, 'price_discount');

            // define the root of the kit
            if ($groupProductsIntoKits) {
                $kitId = apply_filters('wcpw_kit_id', date('d-m-Y H:i:s'), $id, $cart);

                // add pre-defined base product to the cart
                if ($kitBaseProduct) {
                    $_stepId = reset($stepsIds);
                    $productId = get_post_type($kitBaseProduct) != 'product'
                        ? wp_get_post_parent_id($kitBaseProduct)
                        : $kitBaseProduct;

                    $variationId = $productId != $kitBaseProduct ? $kitBaseProduct : '';
                    $variation = '';
                    $cartItemKey = WC()->cart->generate_cart_id($productId, $variationId, $variation, []);
                    $productData = [
                        'key' => $cartItemKey,
                        'step_id' => $_stepId,
                        'product_id' => $productId,
                        'variation_id' => $variationId,
                        'variation' => $variation,
                        'quantity' => 1,
                        'sold_individually' => 0,
                        'data' => wc_get_product($kitBaseProduct)
                    ];

                    $productData = apply_filters('wcpw_kit_base_product_data', $productData, $id, $cart);
                    $cart = [$cartItemKey => $productData] + $cart;
                }
            }

            foreach ($cart as $key => &$cartItem) {
                $skipItems = (array) Settings::getStep($id, $cartItem['step_id'], 'dont_add_to_cart_products');

                // should have a step ID and be not an excluded product/variation/step
                if (!isset($cartItem['step_id'], $cartItem['product_id'])
                    || (isset($cartItem['key'], $cartItem['value']) && empty($cartItem['value']))
                    || Settings::getStep($id, $cartItem['step_id'], 'dont_add_to_cart')
                    || in_array($cartItem['product_id'], $skipItems)
                    || (isset($cartItem['variation_id']) && $cartItem['variation_id']
                        && in_array($cartItem['variation_id'], $skipItems))
                ) {
                    continue;
                }

                $productData = [
                    'product_id' => $cartItem['product_id'],
                    'quantity' => $cartItem['quantity'],
                    'variation_id' => isset($cartItem['variation_id']) ? $cartItem['variation_id'] : null,
                    'variation' => isset($cartItem['variation']) ? $cartItem['variation'] : [],
                    'data' => isset($cartItem['data']) && is_array($cartItem['data']) ? $cartItem['data'] : [],
                    'request' => isset($cartItem['request']) && is_array($cartItem['request'])
                        ? $cartItem['request']
                        : null
                ];

                $stepTitle = Settings::getStep($id, $cartItem['step_id'], 'title');
                $productData['data']['wcpw_id'] = $id;
                $productData['data']['wcpw_step_id'] = $cartItem['step_id'];
                $productData['data']['wcpw_request'] = $cartItem['request'];
                $productData['data']['wcpw_step_name'] = $stepTitle ? $stepTitle : $cartItem['step_id'];

                if (isset($cartItem['has_attached_wizard']) && $cartItem['has_attached_wizard']) {
                    $productData['data']['wcpw_has_attached_wizard'] = $cartItem['has_attached_wizard'];
                }

                if ($args['innerCheckout']) {
                    $productData['data']['wcpw_inner_checkout'] = $id;
                }

                // product discount
                if (isset($productData['product_id']) && $productData['product_id']) {
                    $discount = Product::getDiscountRuleById(
                        $args['id'],
                        $productData['product_id'],
                        $productData['variation_id']
                    );

                    if (!empty($discount)) {
                        $productData['data']['wcpw_product_discount'] = $discount;
                    }
                }

                // common discount
                if ($commonDiscount) {
                    $productData['data']['wcpw_discount'] = $commonDiscount;
                }

                // add kit data to the product
                if ($groupProductsIntoKits) {
                    $productData['data']['wcpw_kit_type'] = $kitsType;

                    // is a root product
                    if (!$rootKitItem) {
                        // if the root item isn't defined yet make it from the first product
                        $productData['data']['wcpw_edit_url'] = $args['currentPageURL'];
                        $rootKitItem = $cartItem;
                        $rootKitItemKey = $key;
                        $kitTitle =
                            apply_filters('wcpw_kit_title', get_the_title($rootKitItem['product_id']), $id, $cart);

                        $isKitQuantityFixed = isset($cartItem['sold_individually'])
                            ? !$cartItem['sold_individually']
                            : !Settings::getStep($id, $rootKitItem['step_id'], 'sold_individually');

                        if ($kitBaseProduct) {
                            // save info about pre-defined base product
                            $productData['data']['wcpw_is_base_kit_product'] = true;
                        }

                        // check prices
                        if ($kitsType == 'combined') {
                            $kitPrice = Settings::getPost($id, 'kit_price');
                            $kitBasePrice = Settings::getPost($id, 'kit_base_price');

                            if ($kitPrice) {
                                // add fixed price data
                                $productData['data']['wcpw_kit_fixed_price'] = $kitPrice;
                            } elseif ($kitBasePrice) {
                                // add base price data
                                $productData['data']['wcpw_kit_base_price'] = $kitBasePrice;
                                $productData['data']['wcpw_kit_base_price_string'] =
                                    Settings::getPost($id, 'kit_base_price_string');
                            }
                        }

                        if ($groupProductsIntoKits || $kitBaseProduct) {
                            $thumbnailId = get_post_thumbnail_id($id);
                            $generatedThumbnail = null;

                            if (Settings::getPost($id, 'generate_thumbnail')) {
                                $generatedThumbnail = Thumbnail::generate($id, $cart);
                            }

                            $isKitQuantityFixed = false;
                            $productData['quantity'] = 1;
                            $productData['data']['wcpw_kit_children'] = [];

                            if (!empty($generatedThumbnail)) {
                                $productData['data']['wcpw_kit_thumbnail_url'] = $generatedThumbnail['url'];
                                $productData['data']['wcpw_kit_thumbnail_path'] = $generatedThumbnail['path'];
                            } elseif ($thumbnailId) {
                                $productData['data']['wcpw_kit_thumbnail_id'] = $thumbnailId;
                            }

                            // collect children
                            foreach ($cart as $childKey => $child) {
                                $skipItems = (array) Settings::getStep(
                                    $id,
                                    $cartItem['step_id'],
                                    'dont_add_to_cart_products'
                                );

                                // should have a step ID, be not an excluded product/variation/step or input field
                                if (!isset($child['step_id'])
                                    || (isset($child['key'], $child['value']) && empty($child['value']))
                                    || Settings::getStep($id, $child['step_id'], 'dont_add_to_cart')
                                    || (isset($child['product_id']) && in_array($child['product_id'], $skipItems))
                                    || (isset($child['variation_id']) && $child['variation_id']
                                        && in_array($child['variation_id'], $skipItems))
                                ) {
                                    continue;
                                }

                                // don't add itself
                                if ($rootKitItem && $childKey == $key) {
                                    continue;
                                }

                                // product discount
                                if (isset($child['product_id']) && $child['product_id']) {
                                    $discount = Product::getDiscountRuleById(
                                        $args['id'],
                                        $child['product_id'],
                                        $child['variation_id']
                                    );

                                    if (!empty($discount)) {
                                        $child['wcpw_product_discount'] = $discount;
                                    }
                                }

                                // common discount (only for products)
                                if ($commonDiscount && isset($child['product_id']) && $child['product_id']) {
                                    $child['wcpw_discount'] = $commonDiscount;
                                }

                                $productData['data']['wcpw_kit_children'][] = $child;
                            }

                            // attach PDF
                            if (Settings::getPost($id, 'attach_pdf_to_root_product')) {
                                $pdf = Instance()->pdf->saveCart([
                                    'id' => $id,
                                    'name' => Settings::getPost(
                                        $id,
                                        'pdf_file_name',
                                        'post',
                                        str_replace(' ', '-', get_bloginfo('name'))
                                    ) . '-' . md5(serialize($cart))
                                ]);

                                if ($pdf && isset($pdf['url'])) {
                                    $productData['data']['wcpw_kit_pdf'] = $pdf['url'];
                                }
                            }
                        }
                    }

                    // is a child product
                    if ($rootKitItemKey != $key) {
                        $productData['data']['wcpw_kit_parent_key'] = $rootKitItemCartKey;

                        if ($kitsType == 'combined') {
                            $productData['data']['wcpw_is_hidden_product'] = true;
                        }
                    }

                    $productData['data']['wcpw_kit_id'] = $kitId;
                    $productData['data']['wcpw_kit_title'] = $kitTitle;
                    $productData['data']['wcpw_is_kit_root'] = (int) ($key == $rootKitItemKey);
                    $productData['data']['wcpw_is_kit_quantity_fixed'] = (int) $isKitQuantityFixed;
                }

                $productData = apply_filters('wcpw_main_cart_product_data', $productData, $id, $cartItem);

                try {
                    $cartItemKey = Product::addToMainCart($productData);

                    // save kit root product key
                    if ($groupProductsIntoKits && $cartItemKey && !$rootKitItemCartKey) {
                        $rootKitItemCartKey = $cartItemKey;
                    }

                    // save product data to output
                    if ($cartItemKey) {
                        $output[$cartItemKey] = $productData;
                    } else {
                        // drop all added products in case of exception
                        foreach (array_keys($output) as $outputKey) {
                            WC()->cart->remove_cart_item($outputKey);
                        }

                        $notices = wc_get_notices('error');

                        foreach ($notices as $notice) {
                            throw new \Exception(get_the_title($productData['product_id']) . ': ' . $notice['notice']);
                        }
                    }
                } catch (\Exception $exception) {
                    // drop all added products in case of exception
                    foreach (array_keys($output) as $outputKey) {
                        WC()->cart->remove_cart_item($outputKey);
                    }

                    throw new \Exception($exception->getMessage());
                }
            }
        }

        if ($args['innerCheckout']) {
            // change active step
            if ($stepId) {
                $this->setActiveStep($args['id'], $stepId);
            } elseif ($enableCheckoutStep) {
                $this->setActiveStep($args['id'], $this->getNextStepId($args['id']));
            }
        } else {
            // truncate the cart
            Cart::truncate($id);

            // reset active step to the first
            self::setActiveStep($id, reset($stepsIds));
            self::resetPreviousStepId($id);
        }

        do_action('wcpw_after_add_all_to_main_cart', $id, $cart, $output);

        return $output;
    }

    /** Handles adding products to the cart via ajax */
    public function addToMainCartAjax()
    {
        $postData = Utils::parseArrayOfJSONs($_POST);

        try {
            $this->addToMainCart($postData);
        } catch (\Exception $exception) {
            $this->addNotice(
                $exception->getCode() ? $exception->getCode() : self::getActiveStepId($postData['id']),
                [
                    'view' => 'custom',
                    'message' => $exception->getMessage()
                ]
            );

            self::ajaxReply(
                [
                    'hasError' => true,
                    'message' => $exception->getMessage(),
                    'content' => Template::html('router', $postData, ['echo' => false])
                ],
                $postData
            );
        }

        $enableCheckoutStep = Settings::getPost($postData['id'], 'enable_checkout_step');

        self::ajaxReply(
            [
                'finalRedirectUrl' => Settings::getFinalRedirectUrl($postData['id']),
                'preventRedirect' => apply_filters(
                    'wcpw_prevent_final_redirect',
                    $enableCheckoutStep,
                    $postData
                ),
                'content' => $enableCheckoutStep || (isset($postData['getContent']) && $postData['getContent'])
                    ? Template::html('router', $postData, ['echo' => false])
                    : ''
            ],
            $postData
        );
    }

    /**
     * Get the form template
     *
     * @param array $args
     */
    public static function get($args)
    {
        $defaults = [
            'id' => null,
            'stepId' => null,
            'page' => 1
        ];

        $args = array_merge($defaults, $args);

        do_action('wcpw_before_get_form', $args);

        if (Settings::getPost($args['id'], 'strict_cart_workflow')) {
            // remove products from the next steps
            $skip = true;

            foreach (self::getStepsIds($args['id']) as $stepId) {
                if (!$skip) {
                    Cart::removeByStepId($args['id'], $stepId);
                }

                if ($stepId == $args['stepId']) {
                    $skip = false;
                }
            }
        }

        self::setActiveStep($args['id'], $args['stepId']);
        self::resetPreviousStepId($args['id']);

        do_action('wcpw_after_get_form', $args);
    }

    /** Get the form template via ajax */
    public function getAjax()
    {
        $postData = Utils::parseArrayOfJSONs($_POST);

        self::get($postData);
        self::ajaxReply(['content' => Template::html('router', $postData, ['echo' => false])], $postData);
    }

    /**
     * Handles form skipping
     *
     * @param array $args
     */
    public static function skip($args)
    {
        $defaults = ['id' => null];
        $args = array_merge($defaults, $args);

        do_action('wcpw_before_skip_form', $args);

        $activeStep = self::getActiveStepId($args['id']);

        Cart::removeByStepId($args['id'], $activeStep);
        self::setActiveStep($args['id'], self::getNextStepId($args['id']));

        do_action('wcpw_after_skip_form', $args);
    }

    /** Handles form skipping via ajax */
    public function skipAjax()
    {
        $postData = Utils::parseArrayOfJSONs($_POST);

        self::skip($postData);
        self::ajaxReply(['content' => Template::html('router', $postData, ['echo' => false])], $postData);
    }

    /**
     * Handles submit and steps skipping
     *
     * @param array $args
     *
     * @throws \Exception
     */
    public function submitAndSkipAll($args)
    {
        $defaults = ['id' => null];
        $args = array_merge($defaults, $args);

        do_action('wcpw_before_submit_and_skip_all', $args);

        $activeStepId = self::getActiveStepId($args['id']);
        $this->submit($args);
        $stepsIds = self::getStepsIds($args['id']);

        if (in_array('checkout', $stepsIds)) {
            // unset checkout step
            $stepsIds = array_diff($stepsIds, ['checkout']);
        }

        self::setPreviousStepId($args['id'], $activeStepId);
        self::setActiveStep($args['id'], end($stepsIds));

        do_action('wcpw_after_submit_and_skip_all', $args);
    }

    /** Handles submit and steps skipping via ajax */
    public function submitAndSkipAllAjax()
    {
        $postData = Utils::parseArrayOfJSONs($_POST);

        try {
            $this->submitAndSkipAll($postData);
        } catch (\Exception $exception) {
            $this->addNotice(
                $exception->getCode() ? $exception->getCode() : self::getActiveStepId($postData['id']),
                [
                    'view' => 'custom',
                    'message' => $exception->getMessage()
                ]
            );

            self::ajaxReply(
                [
                    'hasError' => true,
                    'message' => $exception->getMessage(),
                    'content' => Template::html('router', $postData, ['echo' => false])
                ],
                $postData
            );
        }

        self::ajaxReply(['content' => Template::html('router', $postData, ['echo' => false])], $postData);
    }

    /**
     * Handles all steps skipping
     *
     * @param array $args
     */
    public static function skipAll($args)
    {
        $defaults = ['id' => null];
        $args = array_merge($defaults, $args);

        do_action('wcpw_before_skip_all', $args);

        $stepsIds = self::getStepsIds($args['id']);

        if (in_array('checkout', $stepsIds)) {
            // unset checkout step
            $stepsIds = array_diff($stepsIds, ['checkout']);
        }

        self::setPreviousStepId($args['id'], self::getActiveStepId($args['id']));
        self::setActiveStep($args['id'], end($stepsIds));

        do_action('wcpw_after_skip_all', $args);
    }

    /** Handles all steps skipping via ajax */
    public function skipAllAjax()
    {
        $postData = Utils::parseArrayOfJSONs($_POST);

        self::skipAll($postData);
        self::ajaxReply(['content' => Template::html('router', $postData, ['echo' => false])], $postData);
    }

    /**
     * Reset cart and set the form to the first step
     *
     * @param array $args
     */
    public static function reset($args)
    {
        $defaults = ['id' => null];
        $args = array_merge($defaults, $args);

        do_action('wcpw_before_reset_form', $args);

        $stepsIds = self::getStepsIds($args['id']);

        if (!Settings::getPost($args['id'], 'reflect_in_main_cart')) {
            $cart = Cart::get($args['id']);

            foreach ($cart as $key => $cartItem) {
                // keep the out of the steps products (redirect to a wizard feature)
                if (!in_array($cartItem['step_id'], $stepsIds)) {
                    continue;
                }

                Cart::removeByCartKey($args['id'], $key);
            }
        } else {
            Cart::truncate($args['id']);
        }

        Storage::remove(self::$previousStepsSessionKey, $args['id']);
        self::setActiveStep($args['id'], reset($stepsIds));
        self::getNavItems($args['id'], false); // invalidate nav cache - issues occurs sometimes

        do_action('wcpw_after_reset_form', $args);
    }

    /** Reset cart and set the form to the first step via ajax */
    public function resetAjax()
    {
        $postData = Utils::parseArrayOfJSONs($_POST);

        // unset for sure because this leads to a wrong router view
        unset($postData['stepId']);

        self::reset($postData);
        self::ajaxReply(['content' => Template::html('router', $postData, ['echo' => false])], $postData);
    }
    // </editor-fold>

    // <editor-fold desc="Product actions">
    /**
     * Add product to the cart
     *
     * @param array $args
     *
     * @return bool|array
     *
     * @throws \Exception
     */
    public function addCartProduct($args)
    {
        $defaults = [
            'id' => null,
            'productToAddKey' => null,
            'productsToAdd' => [],
            'incrementActiveStep' => false,
            'dropNotCheckedProducts' => false,
            'checkMinProductsSelected' => false,
            'checkMinTotalProductsQuantity' => false,
            'passStepData' => false
        ];

        $args = array_merge($defaults, $args);
        $args['productsToAdd'] = (array) $args['productsToAdd'];
        $productToAdd = reset($args['productsToAdd']);
        $stepId = isset($productToAdd['step_id']) && $productToAdd['step_id'] ? $productToAdd['step_id'] : null;
        $behavior = Settings::getStep($args['id'], $stepId, 'add_to_cart_behavior');

        if ($args['productToAddKey'] && isset($args['productsToAdd'][$args['productToAddKey']])) {
            $productData = $args['productsToAdd'][$args['productToAddKey']];
            $args['productsToAddChecked'] = [$productData['step_id'] => [$productData['product_id']]];
        }

        if ($behavior == 'submit') {
            // set active step once again to go to the real next step
            $this->setActiveStep($args['id'], $stepId);
            $args['incrementActiveStep'] = true;
        } elseif ($behavior == 'add-to-main-cart') {
            do_action('wcpw_before_add_cart_product', $args);

            return $this->addToMainCart($args);
        }

        do_action('wcpw_before_add_cart_product', $args);

        return $this->submit($args);
    }

    /**
     * Add product to the cart via ajax
     *
     * @throws \Exception
     */
    public function addCartProductAjax()
    {
        $postData = Utils::parseArrayOfJSONs($_POST);

        try {
            $this->addCartProduct($postData);
        } catch (\Exception $exception) {
            $this->addNotice(
                $exception->getCode() ? $exception->getCode() : self::getActiveStepId($postData['id']),
                [
                    'view' => 'custom',
                    'message' => $exception->getMessage()
                ]
            );

            self::ajaxReply(
                [
                    'hasError' => true,
                    'message' => $exception->getMessage(),
                    'content' => Template::html('router', $postData, ['echo' => false])
                ],
                $postData
            );
        }

        self::ajaxReply(['content' => Template::html('router', $postData, ['echo' => false])], $postData);
    }

    /**
     * Remove product from the cart
     *
     * @param array $args
     *
     * @throws \Exception
     */
    public function removeCartProduct($args)
    {
        $defaults = [
            'id' => null,
            'productCartKey' => null
        ];

        $args = array_merge($defaults, $args);
        $cart = Cart::get($args['id']);
        $product = isset($cart[$args['productCartKey']]) ? $cart[$args['productCartKey']] : null;
        $activeStepId = self::getActiveStepId($args['id']);

        if ($product && $product['step_id'] != $activeStepId) {
            // collect all other cart products from the same step to check minimum quantities rules
            $cart = Cart::getByStepId($args['id'], $product['step_id']);
            $qtyCheckArgs = [
                'id' => $args['id'],
                'productsToAdd' => [],
                'productsToAddChecked' => []
            ];

            foreach ($cart as $cartItem) {
                if (!isset($cartItem['product_id']) || $cartItem['product_id'] == $product['product_id']) {
                    continue;
                }

                $qtyCheckArgs['productsToAddChecked'][$cartItem['step_id']][] = $cartItem['product_id'];
                $cartKey = Cart::generateProductId($cartItem);
                $qtyCheckArgs['productsToAdd'][$cartKey] = [
                    'product_id' => $cartItem['product_id'],
                    'step_id' => $cartItem['step_id'],
                    'quantity' => $cartItem['quantity']
                ];
            }

            self::checkStepRules($qtyCheckArgs, $product['step_id']);
        }

        do_action('wcpw_before_remove_cart_product', $args);

        Cart::removeByCartKey($args['id'], $args['productCartKey']);
    }

    /** Remove product from the cart via ajax */
    public function removeCartProductAjax()
    {
        $postData = Utils::parseArrayOfJSONs($_POST);
        $defaults = ['id' => null];
        $args = array_merge($defaults, $postData);

        try {
            $this->removeCartProduct($args);
        } catch (\Exception $exception) {
            $this->addNotice(
                self::getActiveStepId($args['id']),
                [
                    'view' => 'custom',
                    'message' => $exception->getMessage()
                ]
            );

            self::ajaxReply(
                [
                    'hasError' => true,
                    'message' => $exception->getMessage(),
                    'content' => Template::html('router', $postData, ['echo' => false])
                ],
                $postData
            );
        }

        self::ajaxReply(['content' => Template::html('router', $args, ['echo' => false])], $postData);
    }

    /**
     * Update product in the cart
     *
     * @param array $args
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function updateCartProduct($args)
    {
        $defaults = [
            'id' => null,
            'productCartKey' => null
        ];

        $args = array_merge($defaults, $args);
        $product = Cart::getItemByKey($args['id'], $args['productCartKey']);
        $args['productToAddKey'] = $product ? $product['product_id'] : null;

        do_action('wcpw_before_update_cart_product', $args);

        Cart::removeByCartKey($args['id'], $args['productCartKey']);

        return $this->addCartProduct($args);
    }

    /** Update product in the cart via ajax */
    public function updateCartProductAjax()
    {
        $postData = Utils::parseArrayOfJSONs($_POST);
        $defaults = ['id' => null];
        $args = array_merge($defaults, $postData);

        try {
            $this->updateCartProduct($args);
        } catch (\Exception $exception) {
            $this->addNotice(
                $exception->getCode() ? $exception->getCode() : self::getActiveStepId($args['id']),
                [
                    'view' => 'custom',
                    'message' => $exception->getMessage()
                ]
            );

            self::ajaxReply(
                [
                    'hasError' => true,
                    'message' => $exception->getMessage(),
                    'content' => Template::html('router', $postData, ['echo' => false])
                ],
                $postData
            );
        }

        self::ajaxReply(['content' => Template::html('router', $postData, ['echo' => false])], $postData);
    }
    // </editor-fold>

    // <editor-fold desc="Step data">
    /**
     * Add cart step data
     *
     * @param array $args
     *
     * @return bool|array
     *
     * @throws \Exception
     */
    public function addCartStepData($args)
    {
        $defaults = [
            'id' => null,
            'stepId' => null,
            'stepDataToAddKey' => null,
            'incrementActiveStep' => false,
            'dropNotCheckedProducts' => false,
            'checkMinProductsSelected' => false,
            'checkMinTotalProductsQuantity' => false,
            'passProducts' => false
        ];

        $args = array_merge($defaults, $args);
        $behavior = Settings::getStep($args['id'], $args['stepId'], 'add_to_cart_behavior');

        if ($args['stepDataToAddKey']) {
            // pass only one specific step data
            if (isset($args['stepsData']) && is_array($args['stepsData'])) {
                parse_str($args['stepDataToAddKey'], $stepDataToAddKey);

                if (isset($stepDataToAddKey['stepsData']) && is_array($stepDataToAddKey['stepsData'])) {
                    $keys = array_keys($stepDataToAddKey['stepsData']);
                    $stepId = reset($keys);

                    if (is_array($stepDataToAddKey['stepsData'][$stepId])) {
                        $keys = array_keys($stepDataToAddKey['stepsData'][$stepId]);
                        $key = reset($keys);
                        $args['stepsData'] = [
                            $stepId => [wp_unslash($key) => $args['stepsData'][$stepId][wp_unslash($key)]]
                        ];
                    }
                }
            }

            foreach ($args as $key => $_) {
                if (strpos($key, 'stepsData[') === false) {
                    continue;
                }

                if ($key != $args['stepDataToAddKey']) {
                    unset($args[$key]);
                }
            }
        }

        if ($behavior == 'submit') {
            $args['incrementActiveStep'] = true;
        } elseif ($behavior == 'add-to-main-cart') {
            do_action('wcpw_before_add_cart_step_data', $args);

            return $this->addToMainCart($args);
        }

        do_action('wcpw_before_add_cart_step_data', $args);

        return $this->submit($args);
    }

    /**
     * Add cart step data via ajax
     *
     * @throws \Exception
     */
    public function addCartStepDataAjax()
    {
        $postData = Utils::parseArrayOfJSONs($_POST);

        try {
            $this->addCartStepData($postData);
        } catch (\Exception $exception) {
            $this->addNotice(
                $exception->getCode() ? $exception->getCode() : self::getActiveStepId($postData['id']),
                [
                    'view' => 'custom',
                    'message' => $exception->getMessage()
                ]
            );

            self::ajaxReply(
                [
                    'hasError' => true,
                    'message' => $exception->getMessage(),
                    'content' => Template::html('router', $postData, ['echo' => false])
                ],
                $postData
            );
        }

        self::ajaxReply(['content' => Template::html('router', $postData, ['echo' => false])], $postData);
    }

    /**
     * Remove cart step data
     *
     * @param array $args
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function removeCartStepData($args)
    {
        $defaults = [
            'id' => null,
            'stepDataKey' => null,
            'stepId' => null
        ];

        $args = array_merge($defaults, $args);

        do_action('wcpw_before_remove_cart_step_data', $args);

        return Cart::removeByStepDataKey($args['id'], $args['stepDataKey'], $args['stepId']);
    }

    /** Remove cart step data via ajax */
    public function removeCartStepDataAjax()
    {
        $postData = Utils::parseArrayOfJSONs($_POST);
        $defaults = ['id' => null];
        $args = array_merge($defaults, $postData);

        try {
            $this->removeCartStepData($args);
        } catch (\Exception $exception) {
            $this->addNotice(
                self::getActiveStepId($args['id']),
                [
                    'view' => 'custom',
                    'message' => $exception->getMessage()
                ]
            );

            self::ajaxReply(
                [
                    'hasError' => true,
                    'message' => $exception->getMessage(),
                    'content' => Template::html('router', $postData, ['echo' => false])
                ],
                $postData
            );
        }

        self::ajaxReply(['content' => Template::html('router', $args, ['echo' => false])], $postData);
    }
    // </editor-fold>

    // <editor-fold desc="Steps">
    /**
     * Get an array of the steps ids which are used in the wizard
     *
     * @param integer $wizardId
     * @param array $args
     *
     * @return array
     */
    public static function getStepsIds($wizardId, $args = ['idsOnly' => true])
    {
        // have no cache because of the dynamic workflow
        $output = self::getSteps($wizardId, $args);

        return apply_filters('wcpw_steps_ids', $output, $wizardId);
    }

    /**
     * Get an array of the steps which used in the wizard
     *
     * @param integer $wizardId
     * @param array $args
     *
     * @return array
     */
    public static function getSteps($wizardId, $args = [])
    {
        $defaults = [
            'idsOnly' => false,
            'checkAvailabilityRules' => true
        ];

        $args = array_merge($defaults, $args);
        $output = [];
        $stepsIds = Settings::getStepsIds($wizardId);
        $lastVisibleStepId = null;

        foreach ($stepsIds as $stepId) {
            if ($args['checkAvailabilityRules']) {
                $availabilityRules = Settings::getStep($wizardId, $stepId, 'availability_rules');

                if (!Utils::getAvailabilityByRules($wizardId, $availabilityRules, "step-${stepId}")) {
                    continue;
                }
            }

            if ($args['idsOnly']) {
                $output[] = $stepId;

                continue;
            }

            $output[$stepId] = self::getStep($wizardId, $stepId);

            if (Settings::getStep($wizardId, $stepId, 'merge_with_previous') && !is_null($lastVisibleStepId)) {
                $output[$stepId]['merged_with_step'] = $lastVisibleStepId;
                $output[$lastVisibleStepId]['merged_steps'][] = $stepId;
            } else {
                $lastVisibleStepId = $stepId;
            }
        }

        if (Settings::getPost($wizardId, 'enable_description_step')) {
            // add description tab
            if ($args['idsOnly']) {
                array_unshift($output, 'start');
            } else {
                $output = ['start' => self::getStep($wizardId, 'start')] + $output;
            }
        }

        if (Settings::getPost($wizardId, 'enable_results_step')) {
            // add results tab
            if ($args['idsOnly']) {
                $output[] = 'result';
            } else {
                $output['result'] = self::getStep($wizardId, 'result');
            }
        }

        if (Settings::getPost($wizardId, 'enable_checkout_step')
            && Settings::getPost($wizardId, 'mode') != 'single-step'
        ) {
            // add checkout tab
            if ($args['idsOnly']) {
                $output[] = 'checkout';
            } else {
                $output['checkout'] = self::getStep($wizardId, 'checkout');
            }
        }

        return apply_filters('wcpw_steps', $output, $wizardId);
    }

    /**
     * Get step data
     *
     * @param integer $wizardId
     * @param integer|string $stepId
     * @param boolean $useCache
     *
     * @return array
     */
    public static function getStep($wizardId, $stepId, $useCache = true)
    {
        static $cache = [];

        // set global variables
        Instance()->setCurrentId($wizardId);
        Instance()->setCurrentStepId($stepId);

        if ($useCache && isset($cache[$wizardId], $cache[$wizardId][$stepId])) {
            return apply_filters('wcpw_step', $cache[$wizardId][$stepId], $wizardId, $stepId);
        }

        $output = [];

        if (is_numeric($stepId)) {
            $description = Settings::getStep($wizardId, $stepId, 'description');
            $bottomDescription = Settings::getStep($wizardId, $stepId, 'bottom_description');
            $descriptionAutoTags = Settings::getStep($wizardId, $stepId, 'description_auto_tags');
            $title = Settings::getStep($wizardId, $stepId, 'title');
            $navTitle = Settings::getStep($wizardId, $stepId, 'nav_title');
            $output = [
                'id' => $stepId,
                'name' => $title ? $title : $stepId,
                'navName' => $navTitle ? $navTitle : ($title ? $title : $stepId),
                'thumbnail' => Settings::getStep($wizardId, $stepId, 'thumbnail'),
                'categories' => Settings::getStep($wizardId, $stepId, 'categories'),
                'description' => do_shortcode($descriptionAutoTags ? wpautop($description) : $description),
                'bottomDescription' => do_shortcode(
                    $descriptionAutoTags ? wpautop($bottomDescription) : $bottomDescription
                )
            ];
        } elseif ($stepId == 'start') {
            $output = [
                'id' => 'start',
                'name' => Settings::getPost($wizardId, 'description_step_title'),
                'navName' => Settings::getPost($wizardId, 'description_step_title'),
                'thumbnail' => Settings::getPost($wizardId, 'description_step_thumbnail'),
                'description' => do_shortcode(wpautop(get_post_field('post_content', $wizardId)))
            ];
        } elseif ($stepId == 'result') {
            $output = [
                'id' => 'result',
                'name' => Settings::getPost($wizardId, 'results_step_title'),
                'navName' => Settings::getPost($wizardId, 'results_step_title'),
                'thumbnail' => Settings::getPost($wizardId, 'results_step_thumbnail'),
                'description' => do_shortcode(wpautop(Settings::getPost($wizardId, 'results_step_description')))
            ];
        } elseif ($stepId == 'checkout') {
            $output = [
                'id' => 'checkout',
                'name' => Settings::getPost($wizardId, 'checkout_step_title'),
                'navName' => Settings::getPost($wizardId, 'checkout_step_title'),
                'thumbnail' => Settings::getPost($wizardId, 'checkout_step_thumbnail'),
                'description' => do_shortcode(wpautop(Settings::getPost($wizardId, 'checkout_step_description')))
            ];
        }

        $cache[$wizardId][$stepId] = $output;

        return apply_filters('wcpw_step', $output, $wizardId, $stepId);
    }

    /**
     * Get active wizard step id from the session variable
     *
     * @param integer $wizardId
     *
     * @return string
     */
    public static function getActiveStepId($wizardId)
    {
        $stepsIds = self::getStepsIds($wizardId);
        $output = Storage::get(self::$activeStepsSessionKey, $wizardId);

        if (!$output || !in_array($output, $stepsIds)) {
            $defaultActive = Settings::getPost($wizardId, 'default_active_step');

            if ($defaultActive && in_array($defaultActive, $stepsIds)) {
                $output = $defaultActive;
            } else {
                $output = reset($stepsIds);
            }
        }

        return apply_filters('wcpw_active_step_id', $output, $wizardId);
    }

    /**
     * Get active wizard step from the session variable
     *
     * @param integer $wizardId
     *
     * @return array
     */
    public static function getActiveStep($wizardId)
    {
        $output = self::getStep($wizardId, self::getActiveStepId($wizardId));

        return apply_filters('wcpw_active_step', $output, $wizardId);
    }

    /**
     * Set active wizard step to the session variable
     *
     * @param integer $wizardId
     * @param integer|string $stepId
     */
    public static function setActiveStep($wizardId, $stepId)
    {
        Storage::set(self::$activeStepsSessionKey, $wizardId, $stepId);
    }

    /**
     * Get the next active wizard step from the session variable
     *
     * @param integer $wizardId
     *
     * @return string|null
     */
    public static function getNextStepId($wizardId)
    {
        $stepsIds = self::getStepsIds($wizardId);
        $activeStep = self::getActiveStepId($wizardId);
        $fitSteps = [];
        $activeIsFound = false;

        foreach ($stepsIds as $stepId) {
            if ($activeIsFound) {
                $fitSteps[] = $stepId;
            }

            if ($stepId == $activeStep) {
                $activeIsFound = true;
            }
        }

        foreach ($fitSteps as $stepId) {
            if (Settings::getStep($wizardId, $stepId, 'merge_with_previous')) {
                continue;
            }

            return $stepId;
        }

        return null;
    }

    /**
     * Set the previous active wizard step id
     *
     * @param integer $wizardId
     * @param integer $value
     */
    public static function setPreviousStepId($wizardId, $value)
    {
        Storage::set(self::$previousStepsSessionKey, $wizardId, $value);
    }

    /**
     * Reset the previous active wizard step id
     *
     * @param integer $wizardId
     */
    public static function resetPreviousStepId($wizardId)
    {
        Storage::remove(self::$previousStepsSessionKey, $wizardId);
    }

    /**
     * Get the previous active wizard step id
     *
     * @param integer $wizardId
     *
     * @return string|null
     */
    public static function getPreviousStepId($wizardId)
    {
        $value = Storage::get(self::$previousStepsSessionKey, $wizardId);

        if ($value) {
            return $value;
        }

        $stepsIds = self::getStepsIds($wizardId);
        $activeStep = self::getActiveStepId($wizardId);
        $fitSteps = [];

        foreach ($stepsIds as $stepId) {
            if ($stepId == $activeStep) {
                break;
            }

            $fitSteps[] = $stepId;
        }

        $fitSteps = array_reverse($fitSteps);

        foreach ($fitSteps as $stepId) {
            if (Settings::getStep($wizardId, $stepId, 'merge_with_previous')) {
                continue;
            }

            return $stepId;
        }

        return null;
    }

    /**
     * Get current progress level from zero to the last step
     *
     * @param integer $wizardId
     *
     * @return string|null
     */
    public static function getStepsProgress($wizardId)
    {
        $activeStepId = self::getActiveStepId($wizardId);
        $output = 0;

        foreach (self::getStepsIds($wizardId) as $stepId) {
            if ($stepId == $activeStepId) {
                return $output;
            }

            $output += 1;
        }

        return $output;
    }
    // </editor-fold>

    // <editor-fold desc="Navigation">
    /**
     * Check previous step existence
     *
     * @param integer $wizardId
     *
     * @return boolean
     */
    public static function canGoBack($wizardId)
    {
        $stepsIds = self::getStepsIds($wizardId);

        return reset($stepsIds) != self::getActiveStepId($wizardId);
    }

    /**
     * Check next step existence
     *
     * @param integer $wizardId
     *
     * @return boolean
     */
    public static function canGoForward($wizardId)
    {
        $stepsIds = self::getStepsIds($wizardId);

        return end($stepsIds) != self::getActiveStepId($wizardId);
    }

    /**
     * Get pagination items array
     *
     * @param array $args
     *
     * @return array
     */
    public static function getPaginationItems($args)
    {
        $output = [];
        $defaults = [
            'stepId' => null,
            'page' => 1,
            'productsQuery' => []
        ];

        $args = array_merge($defaults, $args);

        if (!$args['productsQuery'] || empty($args['productsQuery'])) {
            return [];
        }

        $paginationArgs = [
            'format' => '?wcpwPage[' . $args['stepId'] . ']=%#%',
            'base' => '%_%',
            'total' => $args['productsQuery']->max_num_pages,
            'current' => self::getStepPageValue($args['stepId'], $args['page']),
            'show_all' => false,
            'end_size' => 1,
            'mid_size' => 2,
            'prev_next' => true,
            'prev_text' => L10N::r('« Previous'),
            'next_text' => L10N::r('Next »'),
            'type' => 'array'
        ];

        $paginationArgs = apply_filters('wcpw_pagination_args', $paginationArgs, $args);

        $links = paginate_links($paginationArgs);

        foreach ((array) $links as $link) {
            // add custom classes
            $link = str_replace('page-numbers', 'page-numbers page-link', $link);

            // replace empty href
            $link = str_replace('href=""', 'href="?paged=1"', $link);
            $link = str_replace("href=''", 'href="?paged=1"', $link);

            preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $link, $result);

            if (!empty($result) && !empty($result['href'][0])) {
                $href = $result['href'][0];
                $linkParts = parse_url($href);

                parse_str($linkParts['query'], $linkPartsQuery);

                // add custom attributes
                $link = str_replace(
                    ' href=',
                    ' data-component="wcpw-form-pagination-link" data-step-id="' . $args['stepId']
                    . '" data-page="'
                    . (isset($linkPartsQuery['wcpwPage'], $linkPartsQuery['wcpwPage'][$args['stepId']])
                        ? $linkPartsQuery['wcpwPage'][$args['stepId']] : 1)
                    . '" href=',
                    $link
                );
            }

            $output[] = [
                'class' => strpos($link, 'current') !== false ? 'active' : '',
                'innerHtml' => $link
            ];
        }

        return apply_filters('wcpw_pagination_items', $output, $args);
    }

    /**
     * Return nav tabs items array
     *
     * @param integer $wizardId
     * @param boolean $useCache
     *
     * @return array
     */
    public static function getNavItems($wizardId, $useCache = true)
    {
        static $cache = [];

        if ($useCache && isset($cache[$wizardId])) {
            return apply_filters('wcpw_nav_items', $cache[$wizardId], $wizardId);
        }

        $cartSteps = Cart::getStepsIds($wizardId);
        $output = self::getSteps($wizardId);
        $activeStepId = self::getActiveStepId($wizardId);
        $nextStepId = self::getNextStepId($wizardId);
        $previousStepId = self::getPreviousStepId($wizardId);
        $isPreviousStepIdDefined = Storage::exists(self::$previousStepsSessionKey, $wizardId);
        $mode = Settings::getPost($wizardId, 'mode');
        $navAction = Settings::getPost($wizardId, 'nav_action');
        $isStrictWalk = $mode == 'step-by-step';
        $activeNavItem = null;
        $previousNavItem = null;

        foreach ($output as &$step) {
            if ($activeStepId == $step['id']) {
                // active step
                $activeNavItem = $step['id'];
                $step['action'] = 'none';
                $step['state'] = 'active';
                $step['class'] = 'active';
                $step['value'] = $step['id'];
                $step['selected'] = isset($cartSteps[$step['id']]);
            } elseif ($step['id'] == $nextStepId) {
                // next active step
                $step['action'] = $navAction == 'auto' ? 'submit' : $navAction;
                $step['state'] = 'next-active';
                $step['class'] = 'next-active';
                $step['value'] = $navAction == 'get-step' ? $step['id'] : ''; // empty is needed for step dependencies
                $step['selected'] = isset($cartSteps[$step['id']]);
            } else {
                if ($activeNavItem && $isStrictWalk) {
                    $action = 'none';
                } elseif ($navAction == 'auto') {
                    $action = $activeNavItem ? 'submit' : 'get-step';
                } else {
                    $action = $navAction;
                }

                // other items
                $step['action'] = $action;
                $step['state'] = $activeNavItem && $isStrictWalk ? 'disabled' : 'default';
                $step['class'] = $activeNavItem && $isStrictWalk ? 'disabled' : ($activeNavItem ? 'default' : 'past');
                $step['value'] = $step['id'];
                $step['selected'] = isset($cartSteps[$step['id']]);
            }

            if ($step['id'] == 'checkout') {
                // is checkout step
                $step['action'] = 'add-to-main-cart';
                $step['value'] = '';
                $step['value'] = $step['id'];
            }

            // if was "skip all" action
            if ($isStrictWalk && $isPreviousStepIdDefined) {
                if ($activeStepId == $step['id']) {
                    // active step
                    $step['action'] = 'none';
                    $step['state'] = 'active';
                    $step['class'] = 'active';
                } elseif ($previousStepId == $step['id']) {
                    // previous active step
                    $previousNavItem = $step['id'];
                    $step['action'] = 'get-step';
                    $step['state'] = 'default';
                    $step['class'] = 'last-active' . ($activeNavItem ? '' : ' past');
                } elseif (!$previousNavItem) {
                    // previous steps
                    $step['action'] = 'get-step';
                    $step['state'] = 'default';
                    $step['class'] = 'past';
                } else {
                    // other items
                    $step['action'] = 'none';
                    $step['state'] = 'disabled';
                    $step['class'] = 'disabled';
                }
            }

            if (isset($step['merged_with_step']) && !empty($step['merged_with_step'])) {
                // disable merged step
                $step['action'] = 'none';
                $step['state'] = 'disabled';
                $step['class'] = 'disabled';
            }
        }

        $cache[$wizardId] = $output;

        return apply_filters('wcpw_nav_items', $output, $wizardId);
    }
    // </editor-fold>

    // <editor-fold desc="Filter">
    /**
     * Get step filter fields array
     *
     * @param integer $wizardId
     * @param integer|string $stepId
     * @param array $appliedFilters
     *
     * @return array
     */
    public static function getFilterFields($wizardId, $stepId, $appliedFilters = [])
    {
        $output = [];
        $useStepFilters = Settings::getStep($wizardId, $stepId, 'use_step_filters');

        if ($useStepFilters && $stepId != $useStepFilters) {
            $output = self::getFilterFields($wizardId, $useStepFilters, $appliedFilters);
        }

        $filters = Settings::getStep($wizardId, $stepId, 'filters');

        if (empty($filters)) {
            return apply_filters('wcpw_filter_fields', $output, $wizardId, $stepId, $appliedFilters);
        }

        $attributeTaxonomies = wc_get_attribute_taxonomies();

        foreach ($filters as $filter) {
            if (!isset($filter['view'], $filter['source']) || !($filter['view'] && $filter['source'])) {
                continue;
            }

            $filterKey = count($output);
            $value = null;
            $values = [];
            $key = $filter['source'];
            $label = '';
            $default = isset($filter['default']) && !empty($filter['default']) ? $filter['default'] : '';
            $order = isset($filter['order']) ? $filter['order'] : 'ASC';
            $include = isset($filter['include']) && !empty($filter['include']) ? $filter['include'] : '';
            $exclude = isset($filter['exclude']) && !empty($filter['exclude']) ? $filter['exclude'] : '';
            $orderBy = isset($filter['order_by']) && isset($filter['order_by'])
                ? $filter['order_by']
                : ($include ? 'include' : 'id'); // older versions support

            switch ($key) {
                case 'category':
                case 'tag': {
                    if ($key == 'category' && empty($include)) {
                        $products = array_filter((array) Settings::getStep($wizardId, $stepId, 'included_products'));
                        $categories = array_filter((array) Settings::getStep($wizardId, $stepId, 'categories'));

                        if (!empty($products)) {
                            foreach ($products as $productId) {
                                $include = array_merge((array) $include, Product::getTermsIds($productId));
                            }

                            $include = array_unique($include);
                        } elseif (!empty($categories)) {
                            $include = [];

                            foreach ($categories as $category) {
                                $include = array_replace(
                                    $include,
                                    [$category => get_term($category, 'product_cat')],
                                    Utils::getSubTerms($category, 'product_cat')
                                );
                            }

                            $include = array_keys($include);
                        }
                    }

                    $default = wp_parse_id_list($default);
                    $value = isset($appliedFilters[$filterKey][$key]) ? $appliedFilters[$filterKey][$key] : $default;
                    $label = isset($filter['label']) && $filter['label']
                        ? $filter['label']
                        : ($key == 'tag' ? L10N::r('Tag') : L10N::r('Category'));

                    $terms = get_terms([
                        'taxonomy' => $key == 'tag' ? 'product_tag' : 'product_cat',
                        'order' => $order,
                        'orderby' => $orderBy,
                        'include' => $include,
                        'exclude' => $exclude
                    ]);

                    foreach ($terms as $term) {
                        $isActive = false;

                        if (isset($appliedFilters[$filterKey][$key])) {
                            $isActive = in_array($term->term_id, $appliedFilters[$filterKey][$key]);
                        } elseif (!empty($default)) {
                            $isActive = in_array($term->term_id, $default);
                        }

                        $values[] = [
                            'id' => $term->term_id,
                            'name' => $term->name,
                            'thumbnailId' => get_term_meta($term->term_id, 'thumbnail_id', true),
                            'isActive' => $isActive
                        ];
                    }

                    break;
                }

                case 'price': {
                    $values = Utils::getPriceLimits($wizardId, $stepId);
                    $values['from'] = $values['min'];
                    $values['to'] = $values['max'];
                    $label = isset($filter['label']) && $filter['label'] ? $filter['label'] : L10N::r('Price');
                    $default = wp_parse_id_list($default);

                    if (isset($appliedFilters[$filterKey]['price'])) {
                        $values['from'] = $appliedFilters[$filterKey]['price']['from'];
                        $values['to'] = $appliedFilters[$filterKey]['price']['to'];
                    } elseif (!empty($default)) {
                        if (isset($default[0])) {
                            $values['from'] = $default[0];
                        }

                        if (isset($default[1])) {
                            $values['to'] = $default[1];
                        }
                    }

                    break;
                }

                case 'search': {
                    $label = isset($filter['label']) && $filter['label'] ? $filter['label'] : L10N::r('Search');
                    $value = isset($appliedFilters[$filterKey]['search']) ? $appliedFilters[$filterKey]['search'] : $default; // phpcs:ignore

                    break;
                }

                // attribute
                default: {
                    if (!taxonomy_exists("pa_{$key}")) {
                        break;
                    }

                    $default = wp_parse_id_list($default);
                    $label = isset($filter['label']) && $filter['label'] ? $filter['label'] : L10N::r('Attribute');
                    $terms = get_terms([
                        'taxonomy' => "pa_{$key}",
                        'order' => $order,
                        'orderby' => $orderBy,
                        'include' => $include,
                        'exclude' => $exclude
                    ]);

                    foreach ($attributeTaxonomies as $attributeTaxonomy) {
                        if ($attributeTaxonomy->attribute_name != $key) {
                            continue;
                        }

                        $label = isset($filter['label']) && $filter['label']
                            ? $filter['label']
                            : $attributeTaxonomy->attribute_label;
                    }

                    switch ($filter['view']) {
                        case 'range': {
                            $attributeValues = [];

                            foreach ($terms as $term) {
                                $attributeValues[] = (float) $term->name;
                            }

                            $values['min'] = min($attributeValues);
                            $values['max'] = max($attributeValues);
                            $values['from'] = $values['min'];
                            $values['to'] = $values['max'];

                            if (isset($appliedFilters[$filterKey][$key])) {
                                $values['from'] = $appliedFilters[$filterKey][$key]['from'];
                                $values['to'] = $appliedFilters[$filterKey][$key]['to'];
                            }

                            break;
                        }

                        default: {
                            foreach ($terms as $term) {
                                $isActive = false;

                                if (isset($appliedFilters[$filterKey][$key])) {
                                    $isActive = in_array($term->term_id, $appliedFilters[$filterKey][$key]);
                                } elseif (!empty($default)) {
                                    $isActive = in_array($term->term_id, $default);
                                }

                                $values[] = [
                                    'id' => $term->term_id,
                                    'name' => $term->name,
                                    'thumbnailId' => get_term_meta($term->term_id, '_wcpw_thumbnail_id', true),
                                    'isActive' => $isActive
                                ];
                            }
                        }
                    }
                }
            }

            if (in_array($filter['view'], ['radio', 'inline-radio'])) {
                // set default value for radio view
                $hasActive = false;

                foreach ($values as $valuesItem) {
                    if ($valuesItem['isActive']) {
                        $hasActive = true;
                        break;
                    }
                }

                if (!$hasActive) {
                    $values[0]['isActive'] = true;
                }
            } elseif ($filter['view'] == 'select' && isset($filter['add_empty_value']) && $filter['add_empty_value']) {
                // add an empty value
                $values = array_merge(
                    [
                        '' => [
                            'id' => '',
                            'name' => '',
                            'isActive' => ''
                        ]
                    ],
                    $values
                );
            }

            $output[] = [
                'label' => $label,
                'stepId' => $stepId,
                'filterKey' => $filterKey,
                'key' => $key,
                'default' => $default,
                'view' => $filter['view'],
                'value' => $value,
                'values' => $values
            ];
        }

        return apply_filters('wcpw_filter_fields', $output, $wizardId, $stepId, $appliedFilters);
    }

    /** AJAX search request */
    public function searchAjax()
    {
        $id = isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? esc_sql($_REQUEST['id']) : null;
        $stepId = isset($_REQUEST['stepId']) && !empty($_REQUEST['stepId']) ? esc_sql($_REQUEST['stepId']) : null;
        $query = isset($_REQUEST['query']) && !empty($_REQUEST['query']) ? esc_sql($_REQUEST['query']) : null;
        $stepProductsIds = Product::getStepProductsIds($id, $stepId, ['filter' => self::getFilterValue($id, $stepId)]);
        $output = [];

        if (!$id || !$stepId || !$query || empty($stepProductsIds)) {
            wp_send_json(['items' => apply_filters('wcpw_filter_search_results', $output, $_REQUEST)]);
        }

        $searchBy = Settings::getStep($id, $stepId, 'filter_search_by');
        $categories = Settings::getStep($id, $stepId, 'categories');

        if (!empty($categories) && !empty($searchBy) && in_array('product_cat', $searchBy)) {
            $terms = get_terms([
                'taxonomy' => ['product_cat'],
                'include' => $categories,
                'fields' => 'names',
                'search' => $query
            ]);

            foreach ($terms as $term) {
                $output[] = [
                    'type' => esc_html__('Product categories', 'woocommerce'),
                    'name' => $term
                ];
            }
        }

        if (!empty($searchBy) && in_array('product_tag', $searchBy)) {
            $terms = get_terms([
                'taxonomy' => ['product_tag'],
                'fields' => 'names',
                'search' => $query
            ]);

            foreach ($terms as $term) {
                $output[] = [
                    'type' => esc_html__('Product tags', 'woocommerce'),
                    'name' => $term
                ];
            }
        }

        $productsQuery = new \WP_Query([
            'post_type' => 'product',
            'post__in' => $stepProductsIds
        ]);

        foreach ($productsQuery->posts as $post) {
            $output[] = [
                'type' => esc_html__('Product', 'woocommerce'),
                'name' => $post->post_title
            ];
        }

        wp_send_json(['items' => apply_filters('wcpw_filter_search_results', $output)]);
    }

    /**
     * Get step filter value array from the request string
     *
     * @param integer $wizardId
     * @param integer|string $stepId
     *
     * @return array
     */
    public static function getFilterValue($wizardId, $stepId)
    {
        $output = [];
        $filters = [];
        $loop = 0;
        $otherStepFilters = $stepId;
        $checkedSteps = [];

        if (isset($_REQUEST['wcpwFilter'])) {
            if (is_string($_REQUEST['wcpwFilter'])) {
                parse_str($_REQUEST['wcpwFilter'], $filters);
            } else {
                $filters = $_REQUEST['wcpwFilter'];
            }
        }

        if (isset($filters[$stepId])) {
            $output = (array) $filters[$stepId];
        }

        // check other steps filters recursively with a loop protection
        while ($otherStepFilters && !in_array($otherStepFilters, $checkedSteps) && $loop++ < 100) {
            $checkedSteps[] = $otherStepFilters;
            $otherStepFilters = Settings::getStep($wizardId, $otherStepFilters, 'use_step_filters');

            if ($otherStepFilters && isset($filters[$otherStepFilters])) {
                $output = array_replace($output, (array) $filters[$otherStepFilters]);
            }
        }

        return $output;
    }
    // </editor-fold>

    // <editor-fold desc="Query arguments">
    /**
     * Get step order-by value from the request string
     *
     * @param integer|string $stepId
     *
     * @return string
     */
    public static function getStepOrderByValue($stepId)
    {
        $output = [];

        if (isset($_REQUEST['wcpwOrderBy'])) {
            if (is_string($_REQUEST['wcpwOrderBy'])) {
                parse_str($_REQUEST['wcpwOrderBy'], $output);
            } else {
                $output = $_REQUEST['wcpwOrderBy'];
            }
        }

        return isset($output[$stepId]) ? $output[$stepId] : null;
    }

    /**
     * Get step page value from the request string
     *
     * @param integer|string $stepId
     * @param integer $default
     *
     * @return integer
     */
    public static function getStepPageValue($stepId, $default = 1)
    {
        $output = [];

        if (isset($_REQUEST['wcpwPage'])) {
            if (is_string($_REQUEST['wcpwPage'])) {
                parse_str($_REQUEST['wcpwPage'], $output);
            } else {
                $output = $_REQUEST['wcpwPage'];
            }
        }

        return isset($output[$stepId]) ? (int) $output[$stepId] : (int) $default;
    }

    /**
     * Get step products per page value from the request string
     *
     * @param integer|string $stepId
     *
     * @return integer
     */
    public static function getStepProductsPerPageValue($stepId)
    {
        $output = [];

        if (isset($_REQUEST['wcpwProductsPerPage'])) {
            if (is_string($_REQUEST['wcpwProductsPerPage'])) {
                parse_str($_REQUEST['wcpwProductsPerPage'], $output);
            } else {
                $output = $_REQUEST['wcpwProductsPerPage'];
            }
        }

        return isset($output[$stepId]) ? (int) $output[$stepId] : null;
    }
    // </editor-fold>
}
