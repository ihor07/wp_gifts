<?php
namespace WCProductsWizard;

/**
 * Settings Class
 *
 * @class Settings
 * @version 9.0.0
 */
class Settings
{
    /** Class Constructor */
    public function __construct()
    {
        add_action('wp_ajax_wcpwGetFilterViewSelectOptions', [$this, 'getFilterViewSelectOptionsAjax']);
        add_filter('wcpw_settings_models', [$this, 'modelsFilter']);
    }

    /**
     * Define settings model
     *
     * @param array $args
     *
     * @return array
     */
    public static function getModel($args = [])
    {
        $defaults = [
            'source' => null,
            'getDynamicValues' => false,
            'useCache' => true
        ];

        $args = array_replace($defaults, $args);

        static $cache;

        if ($args['useCache']) {
            if ($args['source'] && $cache && isset($cache[$args['source']])) {
                return $cache[$args['source']];
            } elseif ($cache) {
                return $cache;
            }
        }

        $wizardIds = ['' => ''];
        $orderFormValues = ['' => ''];
        $imageSizeDescription = '';
        $controlsClassDescription = L10N::r('For more info check the')
            . ' <a href="' . WC_PRODUCTS_WIZARD_PLUGIN_URL
            . 'documentation/index.html#wizard-settings-controls" target="_blank">'
            . L10N::r('documentation') . '</a>';

        if ($args['getDynamicValues']) {
            global $_wp_additional_image_sizes;

            $imageSizes = [];

            // get all registered image sizes and use
            foreach ($_wp_additional_image_sizes as $imageSizeName => $imageSize) {
                $crop = $imageSize['crop'] ? ' crop' : '';
                $imageSizes[] = $imageSizeName != "{$imageSize['width']}x{$imageSize['height']}$crop"
                    ? "$imageSizeName ({$imageSize['width']}x{$imageSize['height']}$crop)"
                    : $imageSizeName;
            }

            $imageSizeDescription = '<details><summary>' . L10N::r('More') . '</summary>'
                . implode(', ', $imageSizes) . '</details>';

            // get wizards
            $wizardPosts = get_posts([
                'post_type' => Core::$postTypeName,
                'post_status' => 'publish',
                'numberposts' => -1
            ]);

            foreach ($wizardPosts as $wizardPost) {
                $wizardIds[$wizardPost->ID] = $wizardPost->post_title;
            }

            // get contact forms
            $contactFormPosts = get_posts([
                'post_type' => 'wpcf7_contact_form',
                'numberposts' => -1
            ]);

            foreach ($contactFormPosts as $contactFormPost) {
                $orderFormValues[$contactFormPost->post_title] = $contactFormPost->post_title;
            }
        }

        $availabilityRules = [
            'label' => L10N::r('Availability rules'),
            'key' => 'availability_rules',
            'type' => 'data-table',
            'inModal' => true,
            'default' => [],
            'description' => L10N::r('Show/hide the step according the specific rules'),
            'values' => [
                'source' => [
                    'label' => L10N::r('Source'),
                    'key' => 'source',
                    'type' => 'select',
                    'default' => 'product',
                    'values' => [
                        'product' => L10n::r('Product/variation'),
                        'category' => L10n::r('Category'),
                        'attribute' => L10n::r('Attribute'),
                        'custom_field' => L10n::r('Custom field')
                    ]
                ],
                'product' => [
                    'label' => L10N::r('Products'),
                    'key' => 'product',
                    'type' => 'wc-product-search',
                    'default' => []
                ],
                'category' => [
                    'label' => L10N::r('Categories'),
                    'key' => 'category',
                    'type' => 'wc-terms-search',
                    'default' => []
                ],
                'attribute' => [
                    'label' => L10N::r('Attribute'),
                    'key' => 'attribute',
                    'type' => 'wc-attributes-search',
                    'default' => []
                ],
                'custom_field_name' => [
                    'label' => L10N::r('Custom field name'),
                    'key' => 'custom_field_name',
                    'type' => 'text',
                    'default' => ''
                ],
                'custom_field_value' => [
                    'label' => L10N::r('Custom field value'),
                    'key' => 'custom_field_value',
                    'type' => 'text',
                    'default' => ''
                ],
                'condition' => [
                    'label' => L10N::r('Condition'),
                    'key' => 'condition',
                    'type' => 'select',
                    'default' => 'in_cart',
                    'values' => [
                        'in_cart' => L10N::r('In cart'),
                        'not_in_cart' => L10N::r('Not in cart')
                    ]
                ],
                'inner_relation' => [
                    'label' => L10N::r('Relation within the items'),
                    'key' => 'inner_relation',
                    'type' => 'select',
                    'default' => 'and',
                    'values' => [
                        'or' => L10N::r('OR'),
                        'and' => L10N::r('AND')
                    ]
                ],
                'outer_relation' => [
                    'label' => L10N::r('Relation with the next rule'),
                    'key' => 'outer_relation',
                    'type' => 'select',
                    'default' => 'or',
                    'values' => [
                        'or' => L10N::r('OR'),
                        'and' => L10N::r('AND')
                    ]
                ]
            ],
            'group' => L10N::r('Query')
        ];

        $thumbnailAreasValues = array_replace(
            [
                'name' => [
                    'label' => L10N::r('Area name'),
                    'key' => 'name',
                    'type' => 'text',
                    'default' => ''
                ],
                'image' => [
                    'label' => L10N::r('Image'),
                    'key' => 'image',
                    'type' => 'thumbnail',
                    'default' => ''
                ]
            ],
            array_replace_recursive(
                $availabilityRules['values'],
                [
                    'source' => [
                        'default' => 'none',
                        'values' => [
                            'none' => L10n::r('None'),
                            'product' => L10n::r('Product/variation'),
                            'category' => L10n::r('Category'),
                            'attribute' => L10n::r('Attribute'),
                            'custom_field' => L10n::r('Custom field')
                        ]
                    ]
                ]
            )
        );

        $thumbnailAreasValues['source']['label'] = L10N::r('Availability rules source');
        $thumbnailAreasValues['product']['description']
            = $thumbnailAreasValues['category']['description']
            = $thumbnailAreasValues['custom_field_name']['description']
            = $thumbnailAreasValues['custom_field_value']['description']
            = L10N::r('Keep empty to not check this field');

        unset($thumbnailAreasValues['outer_relation']);

        $global = [
            'styles_including_type' => [
                'name' => L10N::r('Styles including type'),
                'id' => 'woocommerce_products_wizard_styles_including_type',
                'key' => 'woocommerce_products_wizard_styles_including_type',
                'type' => 'select',
                'default' => 'full',
                'options' => [
                    'full' => L10N::r('Full styles file'),
                    'basic' => L10N::r('Basic styles file'),
                    'none' => L10N::r('None'),
                    'custom' => L10N::r('Custom full styles file')
                ],
                'desc' => L10N::r('For more info check the')
                    . ' <a href="' . WC_PRODUCTS_WIZARD_PLUGIN_URL
                    . 'documentation/index.html#main-settings" target="_blank">'
                    . L10N::r('documentation') . '</a>',
                'section' => ''
            ],
            'scripts_including_type' => [
                'name' => L10N::r('Scripts including type'),
                'id' => 'woocommerce_products_wizard_scripts_including_type',
                'key' => 'woocommerce_products_wizard_scripts_including_type',
                'type' => 'select',
                'default' => 'single',
                'options' => [
                    'single' => L10N::r('Single file'),
                    'multiple' => L10N::r('Multiple files')
                ],
                'desc' => L10N::r('Include scripts separately or in a single file'),
                'section' => ''
            ],
            'included_scripts' => [
                'name' => L10N::r('Included scripts'),
                'id' => 'woocommerce_products_wizard_included_scripts',
                'key' => 'woocommerce_products_wizard_included_scripts',
                'type' => 'multiselect',
                'default' => [
                    'app',
                    'hooks',
                    'variation-form',
                    'bootstrap-util',
                    'bootstrap-modal',
                    'sticky-kit',
                    'wNumb',
                    'nouislider',
                    'nouislider-launcher',
                    'masonry'
                ],
                'options' => [
                    'app' => 'App',
                    'hooks' => 'Hooks',
                    'variation-form' => 'Variation form',
                    'bootstrap-util' => 'Bootstrap util',
                    'bootstrap-modal' => 'Bootstrap modal',
                    'sticky-kit' => 'Sticky-kit',
                    'wNumb' => 'wNumb',
                    'nouislider' => 'noUiSlider',
                    'nouislider-launcher' => 'noUiSlider-launcher',
                    'masonry' => 'Masonry',
                    'formdata-polyfill' => 'FormData polyfill'
                ],
                'desc' => L10N::r('Select which files will be included with the multiple including type'),
                'css' => 'height: 250px',
                'section' => ''
            ],
            'store_session_in_db' => [
                'name' => L10N::r('Store session in the DB'),
                'id' => 'woocommerce_products_wizard_store_session_in_db',
                'key' => 'woocommerce_products_wizard_store_session_in_db',
                'type' => 'checkbox',
                'default' => 'no',
                'desc' => L10N::r('Tick in case of problems of the wizard\'s state storing'),
                'section' => ''
            ],
            'send_state_hash_ajax' => [
                'name' => L10N::r('Send current state hash via AJAX'),
                'id' => 'woocommerce_products_wizard_send_state_hash_ajax',
                'key' => 'woocommerce_products_wizard_send_state_hash_ajax',
                'type' => 'checkbox',
                'default' => 'no',
                'desc' => L10N::r('Might help in case of freezing of the wizard\'s state with caching plugins'),
                'section' => ''
            ],
            'custom_styles_minification' => [
                'name' => L10N::r('Minify custom styles'),
                'id' => 'woocommerce_products_wizard_custom_styles_minification',
                'key' => 'woocommerce_products_wizard_custom_styles_minification',
                'type' => 'checkbox',
                'default' => 'yes',
                'section' => 'custom_styles'
            ],
            'custom_styles_mode' => [
                'name' => L10N::r('Mode'),
                'id' => 'woocommerce_products_wizard_custom_styles_mode',
                'key' => 'woocommerce_products_wizard_custom_styles_mode',
                'type' => 'select',
                'default' => 'simple',
                'options' => [
                    'simple' => L10N::r('Simple'),
                    'advanced' => L10N::r('Advanced')
                ],
                'section' => 'custom_styles'
            ],
            'style_font_size' => [
                'name' => L10N::r('Font size'),
                'id' => 'woocommerce_products_wizard_style_font_size',
                'key' => 'woocommerce_products_wizard_style_font_size',
                'type' => 'text',
                'default' => '1rem',
                'custom_attributes' => ['data-mode' => 'simple'],
                'section' => 'custom_styles'
            ],
            'style_form_item_title_font_size' => [
                'name' => L10N::r('Form item title font size'),
                'id' => 'woocommerce_products_wizard_style_form_item_title_font_size',
                'key' => 'woocommerce_products_wizard_style_form_item_title_font_size',
                'type' => 'text',
                'default' => '1.25rem',
                'custom_attributes' => ['data-mode' => 'simple'],
                'section' => 'custom_styles'
            ],
            'style_form_item_price_font_size' => [
                'name' => L10N::r('Form item price font size'),
                'id' => 'woocommerce_products_wizard_style_form_item_price_font_size',
                'key' => 'woocommerce_products_wizard_style_form_item_price_font_size',
                'type' => 'text',
                'default' => '1.4rem',
                'custom_attributes' => ['data-mode' => 'simple'],
                'section' => 'custom_styles'
            ],
            'style_color_primary' => [
                'name' => L10N::r('Primary color'),
                'id' => 'woocommerce_products_wizard_style_color_primary',
                'key' => 'woocommerce_products_wizard_style_color_primary',
                'type' => 'color',
                'default' => '#007bff',
                'custom_attributes' => ['data-mode' => 'simple'],
                'section' => 'custom_styles'
            ],
            'style_color_secondary' => [
                'name' => L10N::r('Secondary color'),
                'id' => 'woocommerce_products_wizard_style_color_secondary',
                'key' => 'woocommerce_products_wizard_style_color_secondary',
                'type' => 'color',
                'default' => '#6c757d',
                'custom_attributes' => ['data-mode' => 'simple'],
                'section' => 'custom_styles'
            ],
            'style_color_success' => [
                'name' => L10N::r('Success color'),
                'id' => 'woocommerce_products_wizard_style_color_success',
                'key' => 'woocommerce_products_wizard_style_color_success',
                'type' => 'color',
                'default' => '#28a745',
                'custom_attributes' => ['data-mode' => 'simple'],
                'section' => 'custom_styles'
            ],
            'style_color_info' => [
                'name' => L10N::r('Info color'),
                'id' => 'woocommerce_products_wizard_style_color_info',
                'key' => 'woocommerce_products_wizard_style_color_info',
                'type' => 'color',
                'default' => '#17a2b8',
                'custom_attributes' => ['data-mode' => 'simple'],
                'section' => 'custom_styles'
            ],
            'style_color_warning' => [
                'name' => L10N::r('Warning color'),
                'id' => 'woocommerce_products_wizard_style_color_warning',
                'key' => 'woocommerce_products_wizard_style_color_warning',
                'type' => 'color',
                'default' => '#ffc107',
                'custom_attributes' => ['data-mode' => 'simple'],
                'section' => 'custom_styles'
            ],
            'style_color_danger' => [
                'name' => L10N::r('Danger color'),
                'id' => 'woocommerce_products_wizard_style_color_danger',
                'key' => 'woocommerce_products_wizard_style_color_danger',
                'type' => 'color',
                'default' => '#dc3545',
                'custom_attributes' => ['data-mode' => 'simple'],
                'section' => 'custom_styles'
            ],
            'style_color_light' => [
                'name' => L10N::r('Light color'),
                'id' => 'woocommerce_products_wizard_style_color_light',
                'key' => 'woocommerce_products_wizard_style_color_light',
                'type' => 'color',
                'default' => '#f8f9fa',
                'custom_attributes' => ['data-mode' => 'simple'],
                'section' => 'custom_styles'
            ],
            'style_color_dark' => [
                'name' => L10N::r('Dark color'),
                'id' => 'woocommerce_products_wizard_style_color_dark',
                'key' => 'woocommerce_products_wizard_style_color_dark',
                'type' => 'color',
                'default' => '#343a40',
                'custom_attributes' => ['data-mode' => 'simple'],
                'section' => 'custom_styles'
            ],
            'custom_scss' => [
                'name' => L10N::r('Custom SCSS'),
                'id' => 'woocommerce_products_wizard_custom_scss',
                'key' => 'woocommerce_products_wizard_custom_scss',
                'type' => 'textarea',
                'default' => str_replace(' !default', '', Styles::getSCSSVariablesString()),
                'desc' => L10N::r('You can overwrite any Bootstrap 5 variables or add custom styles here'),
                'css' => 'height:400px;width:100%',
                'custom_attributes' => ['data-mode' => 'advanced'],
                'section' => 'custom_styles'
            ],
            'purchase_code' => [
                'name' => L10N::r('Purchase code'),
                'id' => 'woocommerce_products_wizard_purchase_code',
                'key' => 'woocommerce_products_wizard_purchase_code',
                'type' => 'text',
                'default' => '',
                'desc' => L10N::r('Verify your license key to unlock extra possibilities and enable auto-updates'),
                'custom_attributes' => ['required' => 'required'],
                'section' => 'license'
            ],
            'license_consent' => [
                'name' => L10N::r('Consent'),
                'id' => 'woocommerce_products_wizard_license_consent',
                'key' => 'woocommerce_products_wizard_license_consent',
                'type' => 'checkbox',
                'default' => false,
                'desc' => L10N::r('I agree that the license data will be transmitted to the license server'),
                'custom_attributes' => ['required' => 'required'],
                'section' => 'license'
            ]
        ];

        $post = [
            // <editor-fold desc="Behavior">
            'mode' => [
                'label' => L10N::r('Work mode'),
                'key' => '_mode',
                'type' => 'select',
                'default' => 'step-by-step',
                'values' => [
                    'step-by-step' => L10N::r('Step by step'),
                    'free-walk' => L10N::r('Free walk'),
                    'single-step' => L10N::r('Single step'),
                    'sequence' => L10N::r('Sequence'),
                    'expanded-sequence' => L10N::r('Expanded sequence')
                ],
                'group' => L10N::r('Behavior')
            ],
            'nav_action' => [
                'label' => L10N::r('Navigation action'),
                'key' => '_nav_action',
                'type' => 'select',
                'default' => 'auto',
                'values' => [
                    'auto' => L10N::r('Auto'),
                    'submit' => L10N::r('Submit'),
                    'get-step' => L10N::r('Get step'),
                    'none' => L10N::r('None')
                ],
                'group' => L10N::r('Behavior')
            ],
            'final_redirect_url' => [
                'label' => L10N::r('Final redirect URL'),
                'key' => '_final_redirect_url',
                'type' => 'text',
                'default' => get_permalink(function_exists('wc_get_page_id') ? wc_get_page_id('cart') : ''),
                // phpcs:disable
                'description' => L10N::r('Open a page after the "Add to main cart" action. Doesn\'t matter with the checkout step'),
                // phpcs:enable
                'group' => L10N::r('Behavior')
            ],
            'save_state_to_url' => [
                'label' => L10N::r('Save state to URL'),
                'key' => '_save_state_to_url',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Save current wizard state into URL query arguments for sharing'),
                'group' => L10N::r('Behavior')
            ],
            'default_active_step' => [
                'label' => L10N::r('Default active step'),
                'key' => '_default_active_step',
                'type' => 'text',
                'default' => '',
                'description' => L10N::r('Number of a step or string - "start", "result"'),
                'group' => L10N::r('Behavior')
            ],
            'check_availability_rules' => [
                'label' => L10N::r('Check availability rules'),
                'key' => '_check_availability_rules',
                'type' => 'checkbox',
                'default' => true,
                'description' => L10N::r('Check availability rules setting everywhere'),
                'separate' => true,
                'group' => L10N::r('Behavior')
            ],
            'scrolling_top_on_update' => [
                'label' => L10N::r('Scrolling top on the form update'),
                'key' => '_scrolling_top_on_update',
                'type' => 'checkbox',
                'default' => true,
                'group' => L10N::r('Behavior')
            ],
            'scrolling_up_gap' => [
                'label' => L10N::r('The gap on scrolling up'),
                'key' => '_scrolling_up_gap',
                'type' => 'number',
                'default' => 0,
                'description' => L10N::r('px'),
                'separate' => true,
                'group' => L10N::r('Behavior')
            ],
            'hide_prices' => [
                'label' => L10N::r('Hide prices'),
                'key' => '_hide_prices',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Hide product prices within the wizard'),
                'group' => L10N::r('Behavior')
            ],
            'price_discount' => [
                'label' => L10N::r('Price discount %'),
                'key' => '_price_discount',
                'type' => 'number',
                'default' => 0,
                'min' => 0,
                'max' => 100,
                'step' => 'any',
                'group' => L10N::r('Behavior')
            ],
            'price_discount_type' => [
                'label' => L10N::r('Price discount type'),
                'key' => '_price_discount_type',
                'type' => 'select',
                'default' => 'replace-prices',
                'values' => [
                    'replace-prices' => L10N::r('Replace prices'),
                    'show-as-sale' => L10N::r('Show as sale')
                ],
                'separate' => true,
                'group' => L10N::r('Behavior')
            ],
            // </editor-fold>
            // <editor-fold desc="Cart">
            'strict_cart_workflow' => [
                'label' => L10N::r('Strict cart workflow'),
                'key' => '_strict_cart_workflow',
                'type' => 'checkbox',
                'default' => true,
                'description' => L10N::r('Drop products from steps after the current'),
                'group' => L10N::r('Cart')
            ],
            'clear_main_cart_on_confirm' => [
                'label' => L10N::r('Clear main cart on the wizard confirm'),
                'key' => '_clear_main_cart_on_confirm',
                'type' => 'checkbox',
                'default' => false,
                'group' => L10N::r('Cart')
            ],
            'show_steps_in_cart' => [
                'label' => L10N::r('Show steps in the cart'),
                'key' => '_show_steps_in_cart',
                'type' => 'select',
                'default' => 'never',
                'values' => [
                    'never' => L10N::r('Never'),
                    'selected' => L10N::r('Selected'),
                    'all' => L10N::r('All')
                ],
                'description' => L10N::r('Appears in the results step and widget'),
                'group' => L10N::r('Cart')
            ],
            'navigate_using_widget_steps' => [
                'label' => L10N::r('Navigate using widget steps'),
                'key' => '_navigate_using_widget_steps',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Use steps in cart setting. Doesn\'t work for single-step layouts'),
                'group' => L10N::r('Cart')
            ],
            'reflect_in_main_cart' => [
                'label' => L10N::r('Reflect products in the main cart immediately'),
                'key' => '_reflect_in_main_cart',
                'type' => 'checkbox',
                'default' => false,
                // phpcs:disable
                'description' => L10N::r('Adds and removes products in the main cart accordingly to the wizard. Don\'t use with kits'),
                // phpcs:enable
                'separate' => true,
                'group' => L10N::r('Cart')
            ],
            'min_products_selected' => [
                'label' => L10N::r('Minimum products selected'),
                'key' => '_min_products_selected',
                'type' => 'number',
                'default' => '',
                'min' => 0,
                'description' => L10N::r('Count of selected items of wizard'),
                'group' => L10N::r('Cart')
            ],
            'max_products_selected' => [
                'label' => L10N::r('Maximum products selected'),
                'key' => '_max_products_selected',
                'type' => 'number',
                'default' => '',
                'min' => 0,
                'description' => L10N::r('Count of selected items of wizard'),
                'separate' => true,
                'group' => L10N::r('Cart')
            ],
            'min_total_products_quantity' => [
                'label' => L10N::r('Minimum total products quantity'),
                'key' => '_min_total_products_quantity',
                'type' => 'number',
                'default' => '',
                'min' => 0,
                'description' => L10N::r('Total selected products and their quantities'),
                'group' => L10N::r('Cart')
            ],
            'max_total_products_quantity' => [
                'label' => L10N::r('Maximum total products quantity'),
                'key' => '_max_total_products_quantity',
                'type' => 'number',
                'default' => '',
                'min' => 0,
                'description' => L10N::r('Total selected products and their quantities'),
                'separate' => true,
                'group' => L10N::r('Cart')
            ],
            'min_products_price' => [
                'label' => L10N::r('Minimum products price'),
                'key' => '_min_products_price',
                'type' => 'number',
                'default' => '',
                'min' => 0,
                'step' => 'any',
                'description' => L10N::r('Total price of selected items of wizard'),
                'group' => L10N::r('Cart')
            ],
            'max_products_price' => [
                'label' => L10N::r('Maximum products price'),
                'key' => '_max_products_price',
                'type' => 'number',
                'default' => '',
                'min' => 0,
                'step' => 'any',
                'description' => L10N::r('Total price of selected items of wizard'),
                'separate' => true,
                'group' => L10N::r('Cart')
            ],
            'step_inputs_price' => [
                'label' => L10N::r('Step inputs price'),
                'key' => '_step_inputs_price',
                'type' => 'data-table',
                'default' => [],
                'values' => [
                    'key' => [
                        'label' => L10N::r('Name'),
                        'key' => 'key',
                        'type' => 'text',
                        'default' => ''
                    ],
                    'value' => [
                        'label' => L10N::r('Value'),
                        'key' => 'value',
                        'type' => 'text',
                        'default' => ''
                    ],
                    'price' => [
                        'label' => L10N::r('Price'),
                        'key' => 'price',
                        'type' => 'number',
                        'default' => '',
                        'min' => 0,
                        'step' => 'any'
                    ]
                ],
                'description' => L10N::r('Define step input shortcode prices'),
                'separate' => true,
                'group' => L10N::r('Cart')
            ],
            // </editor-fold>
            // <editor-fold desc="Kits">
            'group_products_into_kits' => [
                'label' => L10N::r('Group products into kits'),
                'key' => '_group_products_into_kits',
                'type' => 'checkbox',
                'default' => false,
                // phpcs:disable
                'description' => L10N::r('Group products into kits after adding to the main cart. Doesn\'t work with reflect products in the main cart option'),
                // phpcs:enable
                'group' => L10N::r('Kits')
            ],
            'kits_type' => [
                'label' => L10N::r('Kits type'),
                'key' => '_kits_type',
                'type' => 'select',
                'default' => 'separated',
                'values' => [
                    'separated' => L10N::r('Separated products'),
                    'combined' => L10N::r('Combined product')
                ],
                'group' => L10N::r('Kits')
            ],
            'kit_base_product' => [
                'label' => L10N::r('Kit base product'),
                'key' => '_kit_base_product',
                'type' => 'wc-product-search',
                'default' => [],
                'multiple' => false,
                // phpcs:disable
                'description' => L10N::r('Define specific product to use as a base of the kit. Its price will be zeroed.'),
                // phpcs:enable
                'separate' => true,
                'group' => L10N::r('Kits')
            ],
            'kit_base_price' => [
                'label' => L10N::r('Combined kit base price'),
                'key' => '_kit_base_price',
                'type' => 'number',
                'default' => '',
                'min' => 0,
                'step' => 'any',
                'description' => L10N::r('Set the base price of combined kits. It will be included by default.'),
                'group' => L10N::r('Kits')
            ],
            'kit_base_price_string' => [
                'label' => L10N::r('Combined kit base price string'),
                'key' => '_kit_base_price_string',
                'type' => 'text',
                'default' => L10N::r('Base price'),
                'group' => L10N::r('Kits')
            ],
            'kit_price' => [
                'label' => L10N::r('Combined kit fixed price'),
                'key' => '_kit_price',
                'type' => 'number',
                'default' => '',
                'min' => 0,
                'step' => 'any',
                'description' => L10N::r('Set the fixed price of combined kits. Overwrites kit base price!'),
                'separate' => true,
                'group' => L10N::r('Kits')
            ],
            'skip_child_products_count' => [
                'label' => L10N::r('Skip child products count'),
                'key' => '_skip_child_products_count',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Child products will not be counted in the cart'),
                'group' => L10N::r('Kits')
            ],
            'enable_kit_edit_button' => [
                'label' => L10N::r('Enable kit "Edit" button in the main cart'),
                'key' => '_enable_kit_edit_button',
                'type' => 'checkbox',
                'default' => true,
                'description' => L10N::r('Allows to return to the wizard and modify the selection'),
                'group' => L10N::r('Kits')
            ],
            'attach_pdf_to_root_product' => [
                'label' => L10N::r('Attach results PDF to the root product'),
                'key' => '_attach_pdf_to_root_product',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Allows to have the results PDF in the order admin part'),
                'separate' => true,
                'group' => L10N::r('Kits')
            ],
            // </editor-fold>
            // <editor-fold desc="Thumbnail">
            'generate_thumbnail' => [
                'label' => L10N::r('Generate thumbnail'),
                'key' => '_generate_thumbnail',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Will be used for combined kits type or kit with a defined base product'),
                'group' => L10N::r('Thumbnail')
            ],
            'thumbnail_canvas_width' => [
                'label' => L10N::r('Thumbnail canvas width'),
                'key' => '_thumbnail_canvas_width',
                'type' => 'number',
                'default' => 540,
                'min' => 100,
                'group' => L10N::r('Thumbnail')
            ],
            'thumbnail_canvas_height' => [
                'label' => L10N::r('Thumbnail canvas height'),
                'key' => '_thumbnail_canvas_height',
                'type' => 'number',
                'default' => 360,
                'min' => 100,
                'group' => L10N::r('Thumbnail')
            ],
            'thumbnail_areas' => [
                'label' => L10N::r('Thumbnail areas'),
                'key' => '_thumbnail_areas',
                'type' => 'data-table',
                'default' => [],
                'values' => [
                    'name' => [
                        'label' => L10N::r('Name'),
                        'key' => 'name',
                        'type' => 'text',
                        'data-component' => 'wcpw-thumbnail-generator-area-name',
                        'default' => ''
                    ],
                    'x' => [
                        'label' => L10N::r('X placement'),
                        'key' => 'x',
                        'type' => 'number',
                        'data-component' => 'wcpw-thumbnail-generator-area-x',
                        'default' => ''
                    ],
                    'y' => [
                        'label' => L10N::r('Y placement'),
                        'key' => 'y',
                        'type' => 'number',
                        'data-component' => 'wcpw-thumbnail-generator-area-y',
                        'default' => ''
                    ],
                    'width' => [
                        'label' => L10N::r('Width'),
                        'key' => 'width',
                        'type' => 'number',
                        'data-component' => 'wcpw-thumbnail-generator-area-width',
                        'default' => ''
                    ],
                    'height' => [
                        'label' => L10N::r('Height'),
                        'key' => 'height',
                        'type' => 'number',
                        'data-component' => 'wcpw-thumbnail-generator-area-height',
                        'default' => ''
                    ],
                    'image' => [
                        'label' => L10N::r('Image'),
                        'key' => 'image',
                        'type' => 'thumbnail',
                        'data-component' => 'wcpw-thumbnail-generator-area-image wcpw-thumbnail',
                        'default' => '',
                        'description' => L10N::r('Default image of area')
                    ],
                    'availability_rules' => array_replace(
                        $availabilityRules,
                        ['description' => L10N::r('Show/hide the area according the specific rules')]
                    )
                ],
                'group' => L10N::r('Thumbnail')
            ],
            // </editor-fold>
            // <editor-fold desc="Layout">
            'nav_template' => [
                'label' => L10N::r('Navigation template'),
                'key' => '_nav_template',
                'type' => 'select',
                'default' => 'tabs',
                'values' => ($args['getDynamicValues'] ? Template::getNavList() : []),
                'description' => L10N::r('For modes with navigation'),
                'group' => L10N::r('Layout')
            ],
            'sticky_nav' => [
                'label' => L10N::r('Sticky navigation'),
                'key' => '_sticky_nav',
                'type' => 'select',
                'default' => 'always',
                'values' => [
                    'never' => L10N::r('Never'),
                    'xxs' => L10N::r('XXS'),
                    'xs' => L10N::r('XS'),
                    'sm' => L10N::r('S'),
                    'md' => L10N::r('M'),
                    'lg' => L10N::r('L'),
                    'xl' => L10N::r('XL'),
                    'xxl' => L10N::r('XXL'),
                    'always' => L10N::r('Always')
                ],
                'description' => L10N::r('Select required screen size'),
                'group' => L10N::r('Layout')
            ],
            'sticky_nav_offset_top' => [
                'label' => L10N::r('Sticky navigation offset top'),
                'key' => '_sticky_nav_offset_top',
                'type' => 'text',
                'default' => '0',
                'description' => L10N::r('CSS value'),
                'separate' => true,
                'group' => L10N::r('Layout')
            ],
            'show_sidebar' => [
                'label' => L10N::r('Show sidebar'),
                'key' => '_show_sidebar',
                'type' => 'select',
                'default' => 'not_empty_until_result_step',
                'values' => [
                    'always' => L10N::r('Always'),
                    'always_until_result_step' => L10N::r('Always until the results step'),
                    'not_empty' => L10N::r('Then isn\'t empty'),
                    'not_empty_until_result_step' => L10N::r('Then isn\'t empty until the results step'),
                    'never' => L10N::r('Never')
                ],
                'group' => L10N::r('Layout')
            ],
            'sidebar_position' => [
                'label' => L10N::r('Sidebar position'),
                'key' => '_sidebar_position',
                'type' => 'select',
                'default' => 'right',
                'values' => [
                    'right' => L10N::r('Right'),
                    'left' => L10N::r('Left'),
                    'top' => L10N::r('Top')
                ],
                'group' => L10N::r('Layout')
            ],
            'widget_is_expanded' => [
                'label' => L10N::r('Expand widget by default'),
                'key' => '_widget_is_expanded',
                'type' => 'checkbox',
                'default' => true,
                'description' => L10N::r('For mobile view mostly'),
                'group' => L10N::r('Layout')
            ],
            'sticky_widget' => [
                'label' => L10N::r('Sticky widget'),
                'key' => '_sticky_widget',
                'type' => 'checkbox',
                'default' => true,
                'group' => L10N::r('Layout')
            ],
            'sticky_widget_offset_top' => [
                'label' => L10N::r('Sticky widget offset top'),
                'key' => '_sticky_widget_offset_top',
                'type' => 'number',
                'default' => 60,
                'description' => L10N::r('px'),
                'separate' => true,
                'group' => L10N::r('Layout')
            ],
            'show_header' => [
                'label' => L10N::r('Show header'),
                'key' => '_show_header',
                'type' => 'checkbox',
                'default' => true,
                'group' => L10N::r('Layout')
            ],
            'sticky_header' => [
                'label' => L10N::r('Sticky header'),
                'key' => '_sticky_header',
                'type' => 'select',
                'default' => 'always',
                'values' => [
                    'never' => L10N::r('Never'),
                    'xxs' => L10N::r('XXS'),
                    'xs' => L10N::r('XS'),
                    'sm' => L10N::r('S'),
                    'md' => L10N::r('M'),
                    'lg' => L10N::r('L'),
                    'xl' => L10N::r('XL'),
                    'xxl' => L10N::r('XXL'),
                    'always' => L10N::r('Always')
                ],
                'description' => L10N::r('Select required screen size'),
                'group' => L10N::r('Layout')
            ],
            'sticky_header_offset_top' => [
                'label' => L10N::r('Sticky header offset top'),
                'key' => '_sticky_header_offset_top',
                'type' => 'text',
                'default' => '4rem',
                'description' => L10N::r('CSS value'),
                'separate' => true,
                'group' => L10N::r('Layout')
            ],
            'show_footer' => [
                'label' => L10N::r('Show footer'),
                'key' => '_show_footer',
                'type' => 'checkbox',
                'default' => true,
                'group' => L10N::r('Layout')
            ],
            'sticky_footer' => [
                'label' => L10N::r('Sticky footer'),
                'key' => '_sticky_footer',
                'type' => 'select',
                'default' => 'always',
                'values' => [
                    'never' => L10N::r('Never'),
                    'xxs' => L10N::r('XXS'),
                    'xs' => L10N::r('XS'),
                    'sm' => L10N::r('S'),
                    'md' => L10N::r('M'),
                    'lg' => L10N::r('L'),
                    'xl' => L10N::r('XL'),
                    'xxl' => L10N::r('XXL'),
                    'always' => L10N::r('Always')
                ],
                'description' => L10N::r('Select required screen size'),
                'separate' => true,
                'group' => L10N::r('Layout')
            ],
            'show_steps_names' => [
                'label' => L10N::r('Show step name'),
                'key' => '_show_steps_names',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Show step name above the description and products'),
                'separate' => true,
                'group' => L10N::r('Layout')
            ],
            // </editor-fold>
            // <editor-fold desc="Steps">
            'enable_description_step' => [
                'label' => L10N::r('Enable description step'),
                'key' => '_enable_description_tab',
                'type' => 'checkbox',
                'default' => true,
                'group' => L10N::r('Steps')
            ],
            'description_step_title' => [
                'label' => L10N::r('Description step title'),
                'key' => '_description_tab_title',
                'type' => 'text',
                'default' => L10N::r('Welcome'),
                'group' => L10N::r('Steps')
            ],
            'description_step_thumbnail' => [
                'label' => L10N::r('Description step thumbnail'),
                'key' => '_description_tab_thumbnail',
                'type' => 'thumbnail',
                'default' => '',
                'separate' => true,
                'group' => L10N::r('Steps')
            ],
            'enable_results_step' => [
                'label' => L10N::r('Enable results step'),
                'key' => '_enable_results_tab',
                'type' => 'checkbox',
                'default' => true,
                'group' => L10N::r('Steps')
            ],
            'results_step_title' => [
                'label' => L10N::r('Results step title'),
                'key' => '_results_tab_title',
                'type' => 'text',
                'default' => L10N::r('Total'),
                'group' => L10N::r('Steps')
            ],
            'results_step_thumbnail' => [
                'label' => L10N::r('Results step thumbnail'),
                'key' => '_results_tab_thumbnail',
                'type' => 'thumbnail',
                'default' => '',
                'group' => L10N::r('Steps')
            ],
            'results_step_description' => [
                'label' => L10N::r('Results step description'),
                'key' => '_results_tab_description',
                'type' => 'editor',
                'default' => '',
                'group' => L10N::r('Steps')
            ],
            'show_results_step_table' => [
                'label' => L10N::r('Show results step table'),
                'key' => '_show_results_tab_table',
                'type' => 'checkbox',
                'default' => true,
                'group' => L10N::r('Steps')
            ],
            'results_step_contact_form' => [
                'label' => L10N::r('Results step contact form'),
                'key' => '_results_tab_contact_form',
                'type' => 'select',
                'default' => '',
                'values' => $orderFormValues,
                'description' =>
                    L10N::r('Might have special shortcodes. For more info check the')
                    . ' <a href="' . WC_PRODUCTS_WIZARD_PLUGIN_URL
                    . 'documentation/index.html#wizard-settings-tabs" target="_blank">'
                    . L10N::r('documentation') . '</a>',
                'group' => L10N::r('Steps')
            ],
            'results_price_string' => [
                'label' => L10N::r('Results "price" string'),
                'key' => '_results_price_string',
                'type' => 'text',
                'default' => L10N::r('Price'),
                'group' => L10N::r('Steps')
            ],
            'results_thumbnail_string' => [
                'label' => L10N::r('Results "thumbnail" string'),
                'key' => '_results_thumbnail_string',
                'type' => 'text',
                'default' => L10N::r('Thumbnail'),
                'group' => L10N::r('Steps')
            ],
            'results_data_string' => [
                'label' => L10N::r('Results "data" string'),
                'key' => '_results_data_string',
                'type' => 'text',
                'default' => L10N::r('Product'),
                'group' => L10N::r('Steps')
            ],
            'results_remove_string' => [
                'label' => L10N::r('Results "remove" string'),
                'key' => '_results_remove_string',
                'type' => 'text',
                'default' => L10N::r('Remove'),
                'group' => L10N::r('Steps')
            ],
            'results_quantity_string' => [
                'label' => L10N::r('Results "quantity" string'),
                'key' => '_results_quantity_string',
                'type' => 'text',
                'default' => L10N::r('Quantity'),
                'separate' => true,
                'group' => L10N::r('Steps')
            ],
            'enable_checkout_step' => [
                'label' => L10N::r('Enable checkout step'),
                'key' => '_enable_checkout_step',
                'type' => 'checkbox',
                'default' => false,
                // phpcs:disable
                'description' => L10N::r('Doesn\'t work with the single-step, attached mode, and reflecting products in the main cart option'),
                // phpcs:enable
                'group' => L10N::r('Steps')
            ],
            'checkout_step_title' => [
                'label' => L10N::r('Checkout step title'),
                'key' => '_checkout_step_title',
                'type' => 'text',
                'default' => L10N::r('Checkout'),
                'group' => L10N::r('Steps')
            ],
            'checkout_step_thumbnail' => [
                'label' => L10N::r('Checkout step thumbnail'),
                'key' => '_checkout_step_thumbnail',
                'type' => 'thumbnail',
                'default' => '',
                'group' => L10N::r('Steps')
            ],
            'checkout_step_description' => [
                'label' => L10N::r('Checkout step description'),
                'key' => '_checkout_step_description',
                'type' => 'editor',
                'default' => '[woocommerce_checkout]',
                'description' => L10N::r('Use [woocommerce_checkout] shortcode'),
                'separate' => true,
                'group' => L10N::r('Steps')
            ],
            // </editor-fold>
            // <editor-fold desc="Strings">
            'empty_cart_message' => [
                'label' => L10N::r('Empty cart'),
                'key' => '_empty_cart_message',
                'type' => 'text',
                'default' => L10N::r('Your cart is empty'),
                'group' => L10N::r('Strings')
            ],
            'nothing_found_message' => [
                'label' => L10N::r('Nothing found'),
                'key' => '_nothing_found_message',
                'type' => 'text',
                'default' => L10N::r('No products were found matching your selection.', 'woocommerce'),
                'separate' => true,
                'group' => L10N::r('Strings')
            ],
            'minimum_products_selected_message' => [
                'label' => L10N::r('Minimum products selected'),
                'key' => '_minimum_products_selected_message',
                'type' => 'text',
                'default' => L10N::r('Minimum selected items are required: %limit%'),
                'description' => L10N::r('"%limit%" - products limit')
                    . '<br>'
                    . L10N::r('"%value%" - current products count'),
                'group' => L10N::r('Strings')
            ],
            'maximum_products_selected_message' => [
                'label' => L10N::r('Maximum products selected'),
                'key' => '_maximum_products_selected_message',
                'type' => 'text',
                'default' => L10N::r('Maximum items selected: %limit%'),
                'description' => L10N::r('"%limit%" - products limit')
                    . '<br>'
                    . L10N::r('"%value%" - current products count'),
                'group' => L10N::r('Strings')
            ],
            'minimum_products_price_message' => [
                'label' => L10N::r('Minimum products price'),
                'key' => '_minimum_products_price_message',
                'type' => 'text',
                'default' => L10N::r('Minimum products price is %limit%. Your cart is only %value%'),
                'description' => L10N::r('"%limit%" - products price limit')
                    . '<br>'
                    . L10N::r('"%value%" - current products price'),
                'group' => L10N::r('Strings')
            ],
            'maximum_products_price_message' => [
                'label' => L10N::r('Maximum products price'),
                'key' => '_maximum_products_price_message',
                'type' => 'text',
                'default' => L10N::r('Maximum products price is %limit%. Your cart is %value%'),
                'description' => L10N::r('"%limit%" - products price limit')
                    . '<br>'
                    . L10N::r('"%value%" - current products price'),
                'separate' => true,
                'group' => L10N::r('Strings')
            ],
            'subtotal_string' => [
                'label' => L10N::r('Subtotal string'),
                'key' => '_subtotal_string',
                'type' => 'text',
                'default' => L10N::r('Subtotal'),
                'group' => L10N::r('Strings')
            ],
            'discount_string' => [
                'label' => L10N::r('Discount string'),
                'key' => '_discount_string',
                'type' => 'text',
                'default' => L10N::r('Discount'),
                'group' => L10N::r('Strings')
            ],
            'total_string' => [
                'label' => L10N::r('Total string'),
                'key' => '_total_string',
                'type' => 'text',
                'default' => L10N::r('Total'),
                'separate' => true,
                'group' => L10N::r('Strings')
            ],
            'table_layout_price_string' => [
                'label' => L10N::r('Table layout price string'),
                'key' => '_table_layout_price_string',
                'type' => 'text',
                'default' => L10N::r('Price'),
                'group' => L10N::r('Strings')
            ],
            'table_layout_thumbnail_string' => [
                'label' => L10N::r('Table layout thumbnail string'),
                'key' => '_table_layout_thumbnail_string',
                'type' => 'text',
                'default' => L10N::r('Thumbnail'),
                'group' => L10N::r('Strings')
            ],
            'table_layout_title_string' => [
                'label' => L10N::r('Table layout title string'),
                'key' => '_table_layout_title_string',
                'type' => 'text',
                'default' => L10N::r('Title'),
                'group' => L10N::r('Strings')
            ],
            'table_layout_to_cart_string' => [
                'label' => L10N::r('Table layout to cart string'),
                'key' => '_table_layout_to_cart_string',
                'type' => 'text',
                'default' => L10N::r('To cart'),
                'separate' => true,
                'group' => L10N::r('Strings')
            ],
            'file_upload_max_size_error' => [
                'label' => L10N::r('File upload max size error'),
                'key' => '_file_upload_max_size_error',
                'type' => 'text',
                'default' => L10N::r('The uploaded file is too large'),
                'group' => L10N::r('Strings')
            ],
            'file_upload_extension_error' => [
                'label' => L10N::r('File upload extension error'),
                'key' => '_file_upload_extension_error',
                'type' => 'text',
                'default' => L10N::r('The uploaded file extension is forbidden'),
                'group' => L10N::r('Strings')
            ],
            'file_upload_error' => [
                'label' => L10N::r('File upload error'),
                'key' => '_file_upload_error',
                'type' => 'text',
                'default' => L10N::r('File upload error'),
                'description' => L10N::r('For other unpredicted error cases'),
                'separate' => true,
                'group' => L10N::r('Strings')
            ],
            // </editor-fold>
            // <editor-fold desc="Controls">
            'header_controls' => [
                'label' => L10N::r('Header controls'),
                'key' => '_header_controls',
                'type' => 'multi-select',
                'default' => [
                    'widget-toggle',
                    'spacer',
                    'start',
                    'reset',
                    'back',
                    'skip',
                    'next',
                    'to-results',
                    'result-pdf',
                    'add-to-cart',
                    'add-to-cart-repeat',
                    'share'
                ],
                'values' => [
                    'widget-toggle' => L10N::r('Widget toggle'),
                    'spacer' => L10N::r('Free space'),
                    'spacer-2' => L10N::r('Free space'),
                    'start' => L10N::r('Start'),
                    'reset' => L10N::r('Reset'),
                    'back' => L10N::r('Back'),
                    'skip' => L10N::r('Skip'),
                    'next' => L10N::r('Next'),
                    'to-results' => L10N::r('To results'),
                    'result-pdf' => L10N::r('Result PDF'),
                    'add-to-cart' => L10N::r('Add to cart'),
                    'add-to-cart-repeat' => L10N::r('Add to cart and repeat'),
                    'share' => L10N::r('Share')
                ],
                'group' => L10N::r('Controls')
            ],
            'footer_controls' => [
                'label' => L10N::r('Footer controls'),
                'key' => '_footer_controls',
                'type' => 'multi-select',
                'default' => [
                    'widget-toggle',
                    'spacer',
                    'start',
                    'reset',
                    'back',
                    'skip',
                    'next',
                    'to-results',
                    'result-pdf',
                    'add-to-cart',
                    'add-to-cart-repeat',
                    'share'
                ],
                'values' => [
                    'widget-toggle' => L10N::r('Widget toggle'),
                    'spacer' => L10N::r('Free space'),
                    'spacer-2' => L10N::r('Free space'),
                    'start' => L10N::r('Start'),
                    'reset' => L10N::r('Reset'),
                    'back' => L10N::r('Back'),
                    'skip' => L10N::r('Skip'),
                    'next' => L10N::r('Next'),
                    'to-results' => L10N::r('To results'),
                    'result-pdf' => L10N::r('Result PDF'),
                    'add-to-cart' => L10N::r('Add to cart'),
                    'add-to-cart-repeat' => L10N::r('Add to cart and repeat'),
                    'share' => L10N::r('Share')
                ],
                'group' => L10N::r('Controls')
            ],
            'start_button_text' => [
                'label' => L10N::r('"Start" button text'),
                'key' => '_start_button_text',
                'type' => 'text',
                'default' => L10N::r('Start'),
                'group' => L10N::r('Controls')
            ],
            'start_button_class' => [
                'label' => L10N::r('"Start" button class'),
                'key' => '_start_button_class',
                'type' => 'text',
                'default' => 'btn-primary',
                'description' => $controlsClassDescription,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'add_to_cart_button_text' => [
                'label' => L10N::r('"Add to cart" button text'),
                'key' => '_add_to_cart_button_text',
                'type' => 'text',
                'default' => L10N::r('Add to cart'),
                'description' =>
                    L10N::r('Use the [wcpw-cart-total-price] special shortcode to output the cart total price'),
                'group' => L10N::r('Controls')
            ],
            'add_to_cart_button_class' => [
                'label' => L10N::r('"Add to cart" button class'),
                'key' => '_add_to_cart_button_class',
                'type' => 'text',
                'default' => 'btn-danger',
                'description' => $controlsClassDescription,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'add_to_cart_repeat_button_text' => [
                'label' => L10N::r('"Add to cart and repeat" button text'),
                'key' => '_add_to_cart_repeat_button_text',
                'type' => 'text',
                'default' => L10N::r('Confirm and repeat'),
                'description' => L10N::r('Use the [wcpw-cart-total-price] special shortcode to output the cart total price'), // phpcs:ignore
                'group' => L10N::r('Controls')
            ],
            'add_to_cart_repeat_button_class' => [
                'label' => L10N::r('"Add to cart and repeat" button class'),
                'key' => '_add_to_cart_repeat_button_class',
                'type' => 'text',
                'default' => 'btn-success',
                'description' => $controlsClassDescription,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'result_pdf_button_text' => [
                'label' => L10N::r('"Result PDF" button text'),
                'key' => '_result_pdf_button_text',
                'type' => 'text',
                'default' => L10N::r('Get PDF'),
                'group' => L10N::r('Controls')
            ],
            'result_pdf_button_class' => [
                'label' => L10N::r('"Result PDF" button class'),
                'key' => '_result_pdf_button_class',
                'type' => 'text',
                'default' => 'btn-info',
                'description' => $controlsClassDescription,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'back_button_text' => [
                'label' => L10N::r('"Back" button text'),
                'key' => '_back_button_text',
                'type' => 'text',
                'default' => L10N::r('Back'),
                'group' => L10N::r('Controls')
            ],
            'back_button_class' => [
                'label' => L10N::r('"Back" button class'),
                'key' => '_back_button_class',
                'type' => 'text',
                'default' => 'btn-default btn-light show-icon-on-mobile icon-left hide-text-on-mobile',
                'description' => $controlsClassDescription,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'next_button_text' => [
                'label' => L10N::r('"Next" button text'),
                'key' => '_next_button_text',
                'type' => 'text',
                'default' => L10N::r('Next'),
                'group' => L10N::r('Controls')
            ],
            'next_button_class' => [
                'label' => L10N::r('"Next" button class'),
                'key' => '_next_button_class',
                'type' => 'text',
                'default' => 'btn-primary show-icon-on-mobile icon-right hide-text-on-mobile',
                'description' => $controlsClassDescription,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'reset_button_text' => [
                'label' => L10N::r('"Reset" button text'),
                'key' => '_reset_button_text',
                'type' => 'text',
                'default' => L10N::r('Reset'),
                'group' => L10N::r('Controls')
            ],
            'reset_button_class' => [
                'label' => L10N::r('"Reset" button class'),
                'key' => '_reset_button_class',
                'type' => 'text',
                'default' => 'btn-warning show-icon-on-mobile icon-right hide-text-on-mobile',
                'description' => $controlsClassDescription,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'skip_button_text' => [
                'label' => L10N::r('"Skip" button text'),
                'key' => '_skip_button_text',
                'type' => 'text',
                'default' => L10N::r('Skip'),
                'group' => L10N::r('Controls')
            ],
            'skip_button_class' => [
                'label' => L10N::r('"Skip" button class'),
                'key' => '_skip_button_class',
                'type' => 'text',
                'default' => 'btn-default btn-light show-icon-on-mobile icon-right hide-text-on-mobile',
                'description' => $controlsClassDescription,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'to_results_button_behavior' => [
                'label' => L10N::r('"To results" button behavior'),
                'key' => '_to_results_button_behavior',
                'type' => 'select',
                'default' => L10N::r('skip-all'),
                'values' => [
                    'skip-all' => L10N::r('Skip all'),
                    'submit-and-skip-all' => L10N::r('Submit step and skip all')
                ],
                'group' => L10N::r('Controls')
            ],
            'to_results_button_text' => [
                'label' => L10N::r('"To results" button text'),
                'key' => '_to_results_button_text',
                'type' => 'text',
                'default' => L10N::r('To results'),
                'group' => L10N::r('Controls')
            ],
            'to_results_button_class' => [
                'label' => L10N::r('"To results" button class'),
                'key' => '_to_results_button_class',
                'type' => 'text',
                'default' => 'btn-success show-icon-on-mobile icon-right hide-text-on-mobile',
                'description' => $controlsClassDescription,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'share_button_text' => [
                'label' => L10N::r('"Share" button text'),
                'key' => '_share_button_text',
                'type' => 'text',
                'default' => L10N::r('Get share link'),
                'group' => L10N::r('Controls')
            ],
            'share_button_success_message' => [
                'label' => L10N::r('"Share" button success text'),
                'key' => '_share_button_success_message',
                'type' => 'text',
                'default' => L10N::r('Copied!'),
                'group' => L10N::r('Controls')
            ],
            'share_button_class' => [
                'label' => L10N::r('"Share" button class'),
                'key' => '_share_button_class',
                'type' => 'text',
                'default' => 'btn-outline-info show-icon-on-mobile icon-right hide-text-on-mobile',
                'description' => $controlsClassDescription,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'widget_toggle_button_text' => [
                'label' => L10N::r('"Toggle widget" button text'),
                'key' => '_widget_toggle_button_text',
                'type' => 'text',
                'default' => L10N::r('Toggle cart'),
                'description' =>
                    L10N::r('Use the [wcpw-cart-total-price] special shortcode to output the cart total price'),
                'group' => L10N::r('Controls')
            ],
            'widget_toggle_button_class' => [
                'label' => L10N::r('"Toggle widget" button class'),
                'key' => '_widget_toggle_button_class',
                'type' => 'text',
                'default' => 'd-inline-block d-md-none btn-default btn-light show-icon icon-left hide-text',
                'description' => $controlsClassDescription,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'enable_remove_button' => [
                'label' => L10N::r('Enable "Remove" button'),
                'key' => '_enable_remove_button',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Appears in the cart widget and results table'),
                'group' => L10N::r('Controls')
            ],
            'remove_button_text' => [
                'label' => L10N::r('"Remove" button text'),
                'key' => '_remove_button_text',
                'type' => 'text',
                'default' => L10N::r('Remove'),
                'group' => L10N::r('Controls')
            ],
            'remove_button_class' => [
                'label' => L10N::r('"Remove" button class'),
                'key' => '_remove_button_class',
                'type' => 'text',
                'default' => 'btn-light btn-sm show-icon icon-left hide-text',
                'description' => $controlsClassDescription,
                'group' => L10N::r('Controls'),
                'separate' => true
            ],
            'enable_edit_button' => [
                'label' => L10N::r('Enable "Edit" button'),
                'key' => '_enable_edit_button',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Appears in the cart widget and results table'),
                'group' => L10N::r('Controls')
            ],
            'edit_button_text' => [
                'label' => L10N::r('"Edit" button text'),
                'key' => '_edit_button_text',
                'type' => 'text',
                'default' => L10N::r('Edit'),
                'group' => L10N::r('Controls')
            ],
            'edit_button_class' => [
                'label' => L10N::r('"Edit" button class'),
                'key' => '_edit_button_class',
                'type' => 'text',
                'default' => 'btn-link btn-sm show-icon icon-left hide-text',
                'description' => $controlsClassDescription,
                'group' => L10N::r('Controls'),
                'separate' => true
            ],
            'enable_step_toggle_button' => [
                'label' => L10N::r('Enable step toggle button'),
                'key' => '_enable_step_toggle_button',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Only for single step modes'),
                'group' => L10N::r('Controls')
            ],
            'step_toggle_button_class' => [
                'label' => L10N::r('Toggle step button class'),
                'key' => '_step_toggle_button_class',
                'type' => 'text',
                'default' => 'd-block btn-default btn-light',
                'description' => $controlsClassDescription,
                'group' => L10N::r('Controls')
            ],
            'steps_are_expanded' => [
                'label' => L10N::r('Expand steps by default'),
                'key' => '_steps_are_expanded',
                'type' => 'checkbox',
                'default' => true,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            // </editor-fold>
            // <editor-fold desc="Result PDF">
            'result_pdf_header_height' => [
                'label' => L10N::r('Result PDF header height'),
                'key' => '_result_pdf_header_height',
                'type' => 'number',
                'default' => 2,
                'min' => 0,
                'step' => 'any',
                'description' => L10N::r('cm'),
                'group' => L10N::r('Result PDF')
            ],
            'result_pdf_header_content' => [
                'label' => L10N::r('Result PDF header content'),
                'key' => '_result_pdf_header_content',
                'type' => 'editor',
                'default' => get_bloginfo('name'),
                'description' => L10N::r('PDF shortcodes for using:')
                    . '<br>'
                    . ' [wcpw-result-pdf-page-number], [wcpw-result-pdf-page-total], [wcpw-result-pdf-new-page]',
                'group' => L10N::r('Result PDF')
            ],
            'result_pdf_top_description' => [
                'label' => L10N::r('Result PDF top description'),
                'key' => '_result_pdf_top_description',
                'type' => 'editor',
                'default' => L10N::r('Our commercial proposal'),
                'group' => L10N::r('Result PDF')
            ],
            'result_pdf_footer_height' => [
                'label' => L10N::r('Result PDF footer height'),
                'key' => '_result_pdf_footer_height',
                'type' => 'number',
                'default' => 2,
                'min' => 0,
                'step' => 'any',
                'description' => L10N::r('cm'),
                'group' => L10N::r('Result PDF')
            ],
            'result_pdf_footer_content' => [
                'label' => L10N::r('Result PDF footer content'),
                'key' => '_result_pdf_footer_content',
                'type' => 'editor',
                'default' => '[wcpw-result-pdf-page-number] / [wcpw-result-pdf-page-total]',
                'group' => L10N::r('Result PDF')
            ],
            'result_pdf_bottom_description' => [
                'label' => L10N::r('Result PDF bottom description'),
                'key' => '_result_pdf_bottom_description',
                'type' => 'editor',
                'default' => L10N::r('Get more info:') . ' ' . get_bloginfo('url'),
                'group' => L10N::r('Result PDF')
            ],
            'result_pdf_additional_css' => [
                'label' => L10N::r('Result PDF additional CSS'),
                'key' => '_result_pdf_additional_css',
                'type' => 'textarea',
                'default' => 'body {font-size: 12pt}'
                    . PHP_EOL . '.header-inner, .footer-inner {padding: 1em; background-color: #f5f5f5;}',
                'separate' => true,
                'group' => L10N::r('Result PDF')
            ],
            // </editor-fold>
            // <editor-fold desc="Filter">
            'filter_label' => [
                'label' => L10N::r('Filter label'),
                'key' => '_filter_label',
                'type' => 'text',
                'default' => L10N::r('Filter'),
                'separate' => true,
                'group' => L10N::r('Filter')
            ],
            'filter_reset_button_text' => [
                'label' => L10N::r('"Reset" button text'),
                'key' => '_filter_reset_button_text',
                'type' => 'text',
                'default' => L10N::r('Reset'),
                'group' => L10N::r('Filter')
            ],
            'filter_reset_button_class' => [
                'label' => L10N::r('"Reset" button class'),
                'key' => '_filter_reset_button_class',
                'type' => 'text',
                'default' => 'btn-default btn-light show-icon icon-left',
                'description' => L10N::r('For more info check the')
                    . ' <a href="' . WC_PRODUCTS_WIZARD_PLUGIN_URL
                    . 'documentation/index.html#wizard-settings-controls" target="_blank">'
                    . L10N::r('documentation') . '</a>',
                'separate' => true,
                'group' => L10N::r('Filter')
            ],
            'filter_submit_button_text' => [
                'label' => L10N::r('"Submit" button text'),
                'key' => '_filter_submit_button_text',
                'type' => 'text',
                'default' => L10N::r('Filter'),
                'group' => L10N::r('Filter')
            ],
            'filter_submit_button_class' => [
                'label' => L10N::r('"Submit" button class'),
                'key' => '_filter_submit_button_class',
                'type' => 'text',
                'default' => 'btn-danger show-icon icon-right',
                'separate' => true,
                'group' => L10N::r('Filter')
            ],
            'filter_from_string' => [
                'label' => L10N::r('"From" string'),
                'key' => '_filter_from_string',
                'type' => 'text',
                'default' => L10N::r('From'),
                'group' => L10N::r('Filter')
            ],
            'filter_to_string' => [
                'label' => L10N::r('"To" string'),
                'key' => '_filter_to_string',
                'type' => 'text',
                'default' => L10N::r('To'),
                'separate' => true,
                'group' => L10N::r('Filter')
            ]
            // </editor-fold>
        ];

        $step = [
            // <editor-fold desc="Basic">
            'title' => [
                'label' => L10N::r('Title'),
                'key' => 'title',
                'type' => 'text',
                'default' => '',
                'group' => L10N::r('Basic')
            ],
            'nav_title' => [
                'label' => L10N::r('Navigation title'),
                'key' => 'nav_title',
                'type' => 'text',
                'default' => '',
                'description' => L10N::r('Navigation item title. Optional'),
                'group' => L10N::r('Basic')
            ],
            'notes' => [
                'label' => L10N::r('Notes'),
                'key' => 'notes',
                'type' => 'text',
                'default' => '',
                'description' => L10N::r('Step notes only for developers'),
                'group' => L10N::r('Basic')
            ],
            'thumbnail' => [
                'label' => L10N::r('Thumbnail'),
                'key' => 'thumbnail',
                'type' => 'thumbnail',
                'default' => '',
                'separate' => true,
                'group' => L10N::r('Basic')
            ],
            'description' => [
                'label' => L10N::r('Top description'),
                'key' => 'description',
                'type' => 'editor',
                'default' => '',
                'inModal' => true,
                'group' => L10N::r('Basic')
            ],
            'bottom_description' => [
                'label' => L10N::r('Bottom description'),
                'key' => 'bottom_description',
                'type' => 'editor',
                'default' => '',
                'inModal' => true,
                'description' => L10N::r('For more info check the')
                    . ' <a href="' . WC_PRODUCTS_WIZARD_PLUGIN_URL
                    . 'documentation/index.html#step-settings-captions" target="_blank">'
                    . L10N::r('documentation') . '</a>',
                'group' => L10N::r('Basic')
            ],
            'description_auto_tags' => [
                'label' => L10N::r('Handle description with auto tags'),
                'key' => 'description_auto_tags',
                'type' => 'checkbox',
                'default' => true,
                'group' => L10N::r('Basic')
            ],
            // </editor-fold>
            // <editor-fold desc="Query">
            'categories' => [
                'label' => L10N::r('Categories for using'),
                'key' => 'categories',
                'type' => 'wc-terms-search',
                'default' => [],
                'values' => [],
                'description' => L10N::r('Select categories to request products'),
                'group' => L10N::r('Query')
            ],
            'attributes' => [
                'label' => L10N::r('Attributes for using'),
                'key' => 'attributes',
                'type' => 'wc-attributes-search',
                'default' => [],
                'values' => [],
                'description' => L10N::r('Select attributes to request products'),
                'group' => L10N::r('Query')
            ],
            'included_products' => [
                'label' => L10N::r('Included products'),
                'key' => 'included_products',
                'type' => 'wc-product-search',
                'default' => [],
                'description' => L10N::r('Define specific products to output'),
                'group' => L10N::r('Query')
            ],
            'excluded_products' => [
                'label' => L10N::r('Excluded products'),
                'key' => 'excluded_products',
                'type' => 'wc-product-search',
                'default' => [],
                'description' => L10N::r('Exclude specific products or variations'),
                'group' => L10N::r('Query')
            ],
            'exclude_added_products_of_steps' => [
                'label' => L10N::r('Exclude already added products of steps'),
                'key' => 'exclude_added_products_of_steps',
                'type' => 'text',
                'default' => '',
                'pattern' => '([0-9]+.{0,1}[0-9]*,{0,1})*[0-9]',
                'description' =>
                    L10N::r('Hide steps products which are in the cart. Define steps IDs separated by a comma.'),
                'separate' => true,
                'group' => L10N::r('Query')
            ],
            'availability_rules' => array_replace($availabilityRules, ['separate' => true]),
            'order' => [
                'label' => L10N::r('Order'),
                'key' => 'order',
                'type' => 'select',
                'default' => 'ASC',
                'values' => [
                    'ASC' => L10N::r('ASC'),
                    'DESC' => L10N::r('DESC')
                ],
                'group' => L10N::r('Query')
            ],
            'order_by' => [
                'label' => L10N::r('Order by'),
                'key' => 'order_by',
                'type' => 'select',
                'default' => 'menu_order',
                'values' => [
                    'ID' => L10N::r('ID'),
                    'author' => L10N::r('Author'),
                    'name' => L10N::r('Name'),
                    'date' => L10N::r('Date'),
                    'modified' => L10N::r('Modified'),
                    'rand' => L10N::r('Rand'),
                    'comment_count' => L10N::r('Comment count'),
                    'menu_order' => L10N::r('Menu order'),
                    'post__in' => L10N::r('Included products'),
                    'price' => L10N::r('Price')
                ],
                'group' => L10N::r('Query')
            ],
            'enable_order_by_dropdown' => [
                'label' => L10N::r('Enable "Order by" dropdown'),
                'key' => 'enable_order_by_dropdown',
                'type' => 'checkbox',
                'default' => false,
                'group' => L10N::r('Query')
            ],
            'products_per_page' => [
                'label' => L10N::r('Products per page'),
                'key' => 'products_per_page',
                'type' => 'number',
                'default' => 0,
                'min' => 0,
                'description' => L10N::r('Zero is equal infinity'),
                'group' => L10N::r('Query')
            ],
            'products_per_page_items' => [
                'label' => L10N::r('Products per page items'),
                'key' => 'products_per_page_items',
                'type' => 'data-table',
                'default' => ['' => ''],
                'values' => [
                    'items' => [
                        'label' => L10N::r('Products per page items'),
                        'key' => 'products_per_page_items',
                        'type' => 'number',
                        'default' => '',
                        'min' => 1
                    ]
                ],
                'showHeader' => false,
                'description' => L10N::r('Define a few values to show a "Products per page" dropdown'),
                'group' => L10N::r('Query')
            ],
            // </editor-fold>
            // <editor-fold desc="Cart">
            'several_products' => [
                'label' => L10N::r('Can select several products'),
                'key' => 'several_products',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Replace radio-inputs with checkboxes to select multiple products'),
                'group' => L10N::r('Cart')
            ],
            'several_variations_per_product' => [
                'label' => L10N::r('Can select several variations of the same product'),
                'key' => 'several_variations_per_product',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Use with "Can select several products" setting'),
                'group' => L10N::r('Cart')
            ],
            'hide_choose_element' => [
                'label' => L10N::r('Hide choose element'),
                'key' => 'hide_choose_element',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Use with individual product controls or "Add to cart by quantity" setting'),
                'separate' => true,
                'group' => L10N::r('Cart')
            ],
            'dont_add_to_cart' => [
                'label' => L10N::r('Don\'t add to main cart'),
                'key' => 'dont_add_to_cart',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Don\'t add products from this step to WooCommerce cart'),
                'group' => L10N::r('Cart')
            ],
            'dont_add_to_cart_products' => [
                'label' => L10N::r('Don\'t add specific products to main cart'),
                'key' => 'dont_add_to_cart_products',
                'type' => 'wc-product-search',
                'default' => [],
                'description' => L10N::r('Don\'t add specific products from this step to WooCommerce cart'),
                'separate' => true,
                'group' => L10N::r('Cart')
            ],
            'selected_items_by_default' => [
                'label' => L10N::r('Selected items by default'),
                'key' => 'selected_items_by_default',
                'type' => 'wc-product-search',
                'default' => [],
                // phpcs:disable
                'description' => L10N::r('Products and their variations might be selected separately. Keep empty for auto-selecting.'),
                // phpcs:enable
                'group' => L10N::r('Cart')
            ],
            'all_selected_items_by_default' => [
                'label' => L10N::r('All items are selected by default'),
                'key' => 'all_selected_items_by_default',
                'type' => 'checkbox',
                'default' => false,
                'group' => L10N::r('Cart')
            ],
            'no_selected_items_by_default' => [
                'label' => L10N::r('No selected items by default'),
                'key' => 'no_selected_items_by_default',
                'type' => 'checkbox',
                'default' => false,
                'separate' => true,
                'group' => L10N::r('Cart')
            ],
            'sold_individually' => [
                'label' => L10N::r('Sold individually'),
                'key' => 'sold_individually',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Hide products quantity input'),
                'group' => L10N::r('Cart')
            ],
            'add_to_cart_by_quantity' => [
                'label' => L10N::r('Add to cart by quantity'),
                'key' => 'add_to_cart_by_quantity',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Add all positive quantity products to cart on submit'),
                'group' => L10N::r('Cart')
            ],
            'default_product_quantity' => [
                'label' => L10N::r('Default product quantity'),
                'key' => 'product_quantity_by_default',
                'type' => 'number',
                'default' => 1,
                'min' => 0,
                'description' => L10N::r('Product quantity input value by default'),
                'group' => L10N::r('Cart')
            ],
            'min_product_quantity' => [
                'label' => L10N::r('Minimum product quantity'),
                'key' => 'min_product_quantity',
                'type' => 'group',
                'default' => [],
                'showHeader' => true,
                'description' => L10N::r('Product quantity input limit'),
                'group' => L10N::r('Cart')
            ],
            'max_product_quantity' => [
                'label' => L10N::r('Maximum product quantity'),
                'key' => 'max_product_quantity',
                'type' => 'group',
                'default' => [],
                'showHeader' => true,
                'description' => L10N::r('Product quantity input limit'),
                'separate' => true,
                'group' => L10N::r('Cart')
            ],
            'min_products_selected' => [
                'label' => L10N::r('Minimum products selected'),
                'key' => 'min_products_selected',
                'type' => 'group',
                'default' => [],
                'showHeader' => true,
                'description' => L10N::r('Count of selected products NOT including their quantities'),
                'group' => L10N::r('Cart')
            ],
            'max_products_selected' => [
                'label' => L10N::r('Maximum products selected'),
                'key' => 'max_products_selected',
                'type' => 'group',
                'default' => [],
                'showHeader' => true,
                'description' => L10N::r('Count of selected products NOT including their quantities'),
                'separate' => true,
                'group' => L10N::r('Cart')
            ],
            'min_total_products_quantity' => [
                'label' => L10N::r('Minimum total products quantity'),
                'key' => 'min_total_products_quantity',
                'type' => 'group',
                'default' => [],
                'showHeader' => true,
                'description' => L10N::r('Count of selected products including their quantities'),
                'group' => L10N::r('Cart')
            ],
            'max_total_products_quantity' => [
                'label' => L10N::r('Maximum total products quantity'),
                'key' => 'max_total_products_quantity',
                'type' => 'group',
                'default' => [],
                'showHeader' => true,
                'description' => L10N::r('Count of selected products including their quantities'),
                'group' => L10N::r('Cart')
            ],
            // </editor-fold>
            // <editor-fold desc="Controls">
            'buttons_nonblocking_requests' => [
                'label' => L10N::r('Make nonblocking requests'),
                'key' => 'buttons_nonblocking_requests',
                'type' => 'checkbox',
                'default' => false,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'enable_add_to_cart_button' => [
                'label' => L10N::r('Enable "Add to cart" button'),
                'key' => 'enable_add_to_cart_button',
                'type' => 'checkbox',
                'default' => false,
                'group' => L10N::r('Controls')
            ],
            'add_to_cart_behavior' => [
                'label' => L10N::r('"Add to cart" button behavior'),
                'key' => 'add_to_cart_behavior',
                'type' => 'select',
                'default' => 'default',
                'values' => [
                    'default' => L10N::r('Stay on the same step'),
                    'submit' => L10N::r('Go next'),
                    'add-to-main-cart' => L10N::r('Add to main cart')
                ],
                'group' => L10N::r('Controls')
            ],
            'add_to_cart_button_text' => [
                'label' => L10N::r('"Add to cart" button text'),
                'key' => 'add_to_cart_button_text',
                'type' => 'text',
                'default' => L10N::r('Add to cart'),
                'group' => L10N::r('Controls')
            ],
            'add_to_cart_button_class' => [
                'label' => L10N::r('"Add to cart" button class'),
                'key' => 'add_to_cart_button_class',
                'type' => 'text',
                'default' => 'btn-primary btn-sm show-icon icon-left hide-text',
                'description' => $controlsClassDescription,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'enable_update_button' => [
                'label' => L10N::r('Enable "Update" button'),
                'key' => 'enable_update_button',
                'type' => 'checkbox',
                'default' => false,
                'group' => L10N::r('Controls')
            ],
            'update_button_text' => [
                'label' => L10N::r('"Update" button text'),
                'key' => 'update_button_text',
                'type' => 'text',
                'default' => L10N::r('Update'),
                'group' => L10N::r('Controls')
            ],
            'update_button_class' => [
                'label' => L10N::r('"Update" button class'),
                'key' => 'update_button_class',
                'type' => 'text',
                'default' => 'btn-primary btn-sm show-icon icon-left hide-text',
                'description' => $controlsClassDescription,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'enable_remove_button' => [
                'label' => L10N::r('Enable "Remove" button'),
                'key' => 'enable_remove_button',
                'type' => 'checkbox',
                'default' => false,
                'group' => L10N::r('Controls')
            ],
            'remove_button_text' => [
                'label' => L10N::r('"Remove" button text'),
                'key' => 'remove_button_text',
                'type' => 'text',
                'default' => L10N::r('Remove'),
                'group' => L10N::r('Controls')
            ],
            'remove_button_class' => [
                'label' => L10N::r('"Remove" button class'),
                'key' => 'remove_button_class',
                'type' => 'text',
                'default' => 'btn-danger btn-sm show-icon icon-left hide-text',
                'description' => $controlsClassDescription,
                'separate' => true,
                'group' => L10N::r('Controls')
            ],
            'hide_remove_button' => [
                'label' => L10N::r('Hide remove button'),
                'key' => 'hide_remove_button',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('If enabled for the wizard in the cart widget'),
                'group' => L10N::r('Controls')
            ],
            'hide_edit_button' => [
                'label' => L10N::r('Hide edit button'),
                'key' => 'hide_edit_button',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('If enabled for the wizard in the cart widget and results table'),
                'group' => L10N::r('Controls')
            ],
            // </editor-fold>
            // <editor-fold desc="View">
            'template' => [
                'label' => L10N::r('Template'),
                'key' => 'template',
                'type' => 'select',
                'default' => 'list',
                'values' => ($args['getDynamicValues'] ? Template::getFormList() : []),
                'description' => L10N::r('Use "grid column" setting for grid, masonry, and carousel templates configuring'), // phpcs:ignore
                'separate' => true,
                'group' => L10N::r('View')
            ],
            'grid_column' => [
                'label' => L10N::r('Grid column size'),
                'key' => 'grid_column',
                'type' => 'group',
                'default' => [],
                'values' => [
                    [
                        'label' => L10N::r('XXS'),
                        'key' => 'xxs',
                        'type' => 'number',
                        'default' => 12,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('XS'),
                        'key' => 'xs',
                        'type' => 'number',
                        'default' => 6,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('S'),
                        'key' => 'sm',
                        'type' => 'number',
                        'default' => 4,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('M'),
                        'key' => 'md',
                        'type' => 'number',
                        'default' => 4,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('L'),
                        'key' => 'lg',
                        'type' => 'number',
                        'default' => 4,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('XL'),
                        'key' => 'xl',
                        'type' => 'number',
                        'default' => 3,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('XXL'),
                        'key' => 'xxl',
                        'type' => 'number',
                        'default' => 3,
                        'min' => 1,
                        'max' => 12
                    ]
                ],
                'showHeader' => true,
                'description' => L10N::r('Value from 1 to 12 according to the screen size'),
                'group' => L10N::r('View')
            ],
            'grid_with_sidebar_column' => [
                'label' => L10N::r('Grid column size then the sidebar is showed'),
                'key' => 'grid_with_sidebar_column',
                'type' => 'group',
                'default' => [],
                'values' => [
                    [
                        'label' => L10N::r('XXS'),
                        'key' => 'xxs',
                        'type' => 'number',
                        'default' => 12,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('XS'),
                        'key' => 'xs',
                        'type' => 'number',
                        'default' => 6,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('S'),
                        'key' => 'sm',
                        'type' => 'number',
                        'default' => 4,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('M'),
                        'key' => 'md',
                        'type' => 'number',
                        'default' => 4,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('L'),
                        'key' => 'lg',
                        'type' => 'number',
                        'default' => 4,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('XL'),
                        'key' => 'xl',
                        'type' => 'number',
                        'default' => 3,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('XXL'),
                        'key' => 'xxl',
                        'type' => 'number',
                        'default' => 3,
                        'min' => 1,
                        'max' => 12
                    ]
                ],
                'showHeader' => true,
                'description' => L10N::r('Value from 1 to 12 according to the screen size'),
                'separate' => true,
                'group' => L10N::r('View')
            ],
            'item_template' => [
                'label' => L10N::r('Product template'),
                'key' => 'item_template',
                'type' => 'select',
                'default' => 'type-1',
                'values' => ($args['getDynamicValues'] ? Template::getFormItemList() : []),
                'description' => L10N::r('Doesn\'t matter for the "Table" template'),
                'after' => '<div class="wcpw-form-item-template-preview" '
                    . 'data-component="wcpw-form-item-template-preview" '
                    . 'data-src="' . WC_PRODUCTS_WIZARD_PLUGIN_URL . "assets/admin/images/item-template/" . '" '
                    . '></div>',
                'group' => L10N::r('View')
            ],
            'item_variations_template' => [
                'label' => L10N::r('Product variation template'),
                'key' => 'variations_type',
                'type' => 'select',
                'default' => 'select',
                'values' => ($args['getDynamicValues'] ? Template::getVariationsTypeList() : []),
                'separate' => true,
                'group' => L10N::r('View')
            ],
            'show_item_thumbnails' => [
                'label' => L10N::r('Show product thumbnails'),
                'key' => 'show_thumbnails',
                'type' => 'checkbox',
                'default' => true,
                'group' => L10N::r('View')
            ],
            'item_thumbnail_size' => [
                'label' => L10N::r('Thumbnail size'),
                'key' => 'thumbnail_size',
                'type' => 'text',
                'default' => 'shop_catalog',
                // phpcs:disable
                'description' => L10N::r('Set width and height separated by a comma or use string value. For example thumbnail, medium, large') . $imageSizeDescription,
                // phpcs:enable
                'group' => L10N::r('View')
            ],
            'enable_item_thumbnail_link' => [
                'label' => L10N::r('Enable thumbnail link'),
                'key' => 'enable_thumbnail_link',
                'type' => 'checkbox',
                'default' => true,
                'group' => L10N::r('View')
            ],
            'merge_item_thumbnail_with_gallery' => [
                'label' => L10N::r('Merge thumbnail with gallery'),
                'key' => 'merge_thumbnail_with_gallery',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Show gallery within the thumbnail element'),
                'separate' => true,
                'group' => L10N::r('View')
            ],
            'show_item_gallery' => [
                'label' => L10N::r('Show product gallery'),
                'key' => 'show_galleries',
                'type' => 'checkbox',
                'default' => true,
                'group' => L10N::r('View')
            ],
            'item_gallery_column' => [
                'label' => L10N::r('Gallery column size'),
                'key' => 'gallery_column',
                'type' => 'group',
                'default' => [],
                'values' => [
                    [
                        'label' => L10N::r('XXS'),
                        'key' => 'xxs',
                        'type' => 'number',
                        'min' => 1,
                        'max' => 12,
                        'default' => 4
                    ],
                    [
                        'label' => L10N::r('XS'),
                        'key' => 'xs',
                        'type' => 'number',
                        'min' => 1,
                        'max' => 12,
                        'default' => 4
                    ],
                    [
                        'label' => L10N::r('S'),
                        'key' => 'sm',
                        'type' => 'number',
                        'min' => 1,
                        'max' => 12,
                        'default' => 3
                    ],
                    [
                        'label' => L10N::r('M'),
                        'key' => 'md',
                        'type' => 'number',
                        'default' => 3,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('L'),
                        'key' => 'lg',
                        'type' => 'number',
                        'default' => 3,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('XL'),
                        'key' => 'xl',
                        'type' => 'number',
                        'default' => 3,
                        'min' => 1,
                        'max' => 12
                    ],
                    [
                        'label' => L10N::r('XXL'),
                        'key' => 'xxl',
                        'type' => 'number',
                        'default' => 3,
                        'min' => 1,
                        'max' => 12
                    ]
                ],
                'showHeader' => true,
                'description' => L10N::r('Value from 1 to 12 according to the screen size'),
                'separate' => true,
                'group' => L10N::r('View')
            ],
            'show_item_descriptions' => [
                'label' => L10N::r('Show product description'),
                'key' => 'show_descriptions',
                'type' => 'checkbox',
                'default' => true,
                'group' => L10N::r('View')
            ],
            'item_description_source' => [
                'label' => L10N::r('Description source'),
                'key' => 'item_description_source',
                'type' => 'select',
                'default' => 'content',
                'values' => [
                    'content' => L10N::r('Product content'),
                    'excerpt' => L10N::r('Product short description'),
                    'none' => L10N::r('None')
                ],
                'separate' => true,
                'group' => L10N::r('View')
            ],
            'enable_item_title_link' => [
                'label' => L10N::r('Enable product title link'),
                'key' => 'enable_title_link',
                'type' => 'checkbox',
                'default' => false,
                'group' => L10N::r('View')
            ],
            'show_item_attributes' => [
                'label' => L10N::r('Show product attributes'),
                'key' => 'show_attributes',
                'type' => 'checkbox',
                'default' => false,
                'group' => L10N::r('View')
            ],
            'show_item_availabilities' => [
                'label' => L10N::r('Show product availabilities'),
                'key' => 'show_availabilities',
                'type' => 'checkbox',
                'default' => true,
                'group' => L10N::r('View')
            ],
            'show_item_sku' => [
                'label' => L10N::r('Show product SKU'),
                'key' => 'show_sku',
                'type' => 'checkbox',
                'default' => false,
                'group' => L10N::r('View')
            ],
            'show_item_tags' => [
                'label' => L10N::r('Show product tags'),
                'key' => 'show_tags',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Showed above the thumbnail'),
                'group' => L10N::r('View')
            ],
            'show_item_price' => [
                'label' => L10N::r('Show product price'),
                'key' => 'show_prices',
                'type' => 'checkbox',
                'default' => true,
                'separate' => true,
                'group' => L10N::r('View')
            ],
            // </editor-fold>
            // <editor-fold desc="Filters">
            'use_step_filters' => [
                'label' => L10N::r('Use filters of a step'),
                'key' => 'use_step_filters',
                'type' => 'number',
                'default' => '',
                'description' => L10N::r('Define step ID to use its filters instead of the own ones'),
                'group' => L10N::r('Filters')
            ],
            'filters' => [
                'label' => L10N::r('Filters'),
                'key' => 'filters',
                'type' => 'data-table',
                'inModal' => true,
                'values' => [
                    'source' => [
                        'label' => L10N::r('Source'),
                        'key' => 'source',
                        'type' => 'select',
                        'default' => '',
                        'values' => ($args['getDynamicValues'] ? self::getFilterSourcesList() : [])
                    ],
                    'view' => [
                        'label' => L10N::r('View'),
                        'key' => 'view',
                        'type' => 'ajax-select',
                        'default' => '',
                        'target-parent' => '[data-component="wcpw-data-table-item"]',
                        'target-selector' => '[data-key="source"] select',
                        'action' => 'wcpwGetFilterViewSelectOptions'
                    ],
                    'label' => [
                        'label' => L10N::r('Label'),
                        'key' => 'label',
                        'type' => 'text',
                        'default' => ''
                    ],
                    'order' => [
                        'label' => L10N::r('Order'),
                        'key' => 'order',
                        'type' => 'select',
                        'default' => 'ASC',
                        'values' => [
                            'ASC' => L10N::r('ASC'),
                            'DESC' => L10N::r('DESC')
                        ]
                    ],
                    'order_by' => [
                        'label' => L10N::r('Order by'),
                        'key' => 'order_by',
                        'type' => 'select',
                        'default' => 'menu_order',
                        'values' => [
                            'ID' => L10N::r('ID'),
                            'name' => L10N::r('Name'),
                            'count' => L10N::r('Count'),
                            'slug' => L10N::r('Slug'),
                            'description' => L10N::r('Description'),
                            'term_group' => L10N::r('Term group'),
                            'parent' => L10N::r('Parent'),
                            'include' => L10N::r('Include')
                        ]
                    ],
                    'include' => [
                        'label' => L10N::r('Include terms'),
                        'key' => 'include',
                        'type' => 'text',
                        'default' => '',
                        'pattern' => '([0-9]+.{0,1}[0-9]*,{0,1})*[0-9]',
                        'description' =>
                            L10N::r('Define terms IDs separated by a comma')
                    ],
                    'exclude' => [
                        'label' => L10N::r('Exclude terms'),
                        'key' => 'exclude',
                        'type' => 'text',
                        'default' => '',
                        'pattern' => '([0-9]+.{0,1}[0-9]*,{0,1})*[0-9]',
                        'description' => L10N::r('Define terms IDs separated by a comma')
                    ],
                    'default' => [
                        'label' => L10N::r('Default values'),
                        'key' => 'default',
                        'type' => 'text',
                        'default' => '',
                        'description' =>
                            L10N::r('Set default selected values. Define values or terms IDs separated by a comma.')
                    ],
                    'add_empty_value' => [
                        'label' => L10N::r('Add empty value'),
                        'key' => 'add_empty_value',
                        'type' => 'checkbox',
                        'default' => false,
                        'description' => L10N::r('For the select view')
                    ]
                ],
                'default' => [],
                'group' => L10N::r('Filters')
            ],
            'apply_default_filters' => [
                'label' => L10N::r('Apply default filter values'),
                'key' => 'apply_default_filters',
                'type' => 'checkbox',
                'default' => false,
                'description' => L10N::r('Filter products by default values immediately'),
                'group' => L10N::r('Filters')
            ],
            'filter_position' => [
                'label' => L10N::r('Filter position'),
                'key' => 'filter_position',
                'type' => 'select',
                'default' => 'before-products',
                'values' => [
                    'before-products' => L10N::r('Before products'),
                    'before-widget' => L10N::r('Before sidebar widget')
                ],
                'description' => L10N::r('Sidebar filter not working for single-step layouts'),
                'group' => L10N::r('Filters')
            ],
            'filter_is_expanded' => [
                'label' => L10N::r('Expand filter by default'),
                'key' => 'filter_is_expanded',
                'type' => 'checkbox',
                'default' => false,
                'group' => L10N::r('Filters')
            ],
            'filter_thumbnail_size' => [
                'label' => L10N::r('Filter thumbnail size'),
                'key' => 'filter_thumbnail_size',
                'type' => 'text',
                'default' => 'thumbnail',
                // phpcs:disable
                'description' => L10N::r('Set width and height separated by a comma or use string value. For example thumbnail, medium, large') . $imageSizeDescription,
                // phpcs:enable
                'group' => L10N::r('Filters')
            ]
            // </editor-fold>
        ];

        if (class_exists('\WCStepFilter\Filter') && method_exists('\WCStepFilter\Filter', 'getPostsIds')) {
            $step['step_filter'] = [
                'label' => L10N::r('WC Step Filter for using'),
                'key' => 'step_filter',
                'type' => 'select',
                'default' => '',
                'values' => ['' => ''] + \WCStepFilter\Filter::getPostsIds(),
                'description' =>
                    L10N::r('Use WooCommerce Step Filter to output questions before products')
                    . '<br>'
                    . L10N::r('Enable the "Show results within" setting for the filter'),
                'group' => L10N::r('Filters')
            ];
        }

        // blend in min/max setting values
        foreach ([
             'min_products_selected',
             'max_products_selected',
             'min_product_quantity',
             'max_product_quantity',
             'min_total_products_quantity',
             'max_total_products_quantity'
        ] as $setting
        ) {
            $step[$setting]['description'] .= '</br>'
                . L10N::r('Define fixed value, step input name, or steps IDs separated by a comma');

            $step[$setting]['values'] = [
                'type' => [
                    'label' => L10N::r('Type'),
                    'key' => 'type',
                    'type' => 'select',
                    'default' => 'number',
                    'values' => [
                        'count' => L10N::r('Fixed value'),
                        'selected-from-step' => L10N::r('Count of selected products of steps'),
                        'least-from-step' => L10N::r('Least product quantity of steps'),
                        'greatest-from-step' => L10N::r('Greatest product quantity of steps'),
                        'sum-from-step' => L10N::r('Sum of products quantities of steps'),
                        'step-input-value' => L10N::r('Step input value')
                    ]
                ],
                'value' => [
                    'label' => L10N::r('Value'),
                    'key' => 'value',
                    'type' => 'text',
                    'default' => ''
                ]
            ];
        }

        $product = [
            'availability_rules' => array_replace_recursive(
                $availabilityRules,
                [
                    'key' => '_wcpw_availability_rules',
                    'description' => L10N::r('Show/hide the variation according the specific rules'),
                    'values' => [
                        'wizard' => [
                            'label' => L10N::r('Wizard'),
                            'key' => 'wizard',
                            'type' => 'select',
                            'default' => '',
                            'description' => L10N::r('Keep empty to influence any wizard'),
                            'values' => ['' => ''] + $wizardIds
                        ]
                    ]
                ]
            ),
            'item_variations_template' => [
                'label' => L10N::r('Variation template'),
                'key' => '_wcpw_variations_type',
                'type' => 'select',
                'default' => 'default',
                'values' => ['default' => L10N::r('Default')]
                    + ($args['getDynamicValues'] ? Template::getVariationsTypeList() : [])
            ],
            'discount' => [
                'label' => L10N::r('Discount'),
                'key' => '_wcpw_discount',
                'type' => 'data-table',
                'default' => [],
                'showHeader' => true,
                'values' => [
                    'id' => [
                        'label' => L10N::r('Wizard'),
                        'key' => 'id',
                        'type' => 'select',
                        'default' => '',
                        'values' => $wizardIds
                    ],
                    'type' => [
                        'label' => L10N::r('Discount type'),
                        'key' => 'type',
                        'type' => 'select',
                        'default' => 'percentage',
                        'values' => [
                            'percentage' => L10N::r('Percentage'),
                            'fixed' => L10N::r('Fixed'),
                            'precise_price' => L10N::r('Precise price')
                        ]
                    ],
                    'value' => [
                        'label' => L10N::r('Value'),
                        'key' => 'value',
                        'type' => 'number',
                        'default' => '',
                        'min' => 0,
                        'step' => 'any'
                    ]
                ],
                'description' => L10N::r('Reduce simple product price bought using a products wizard')
                    . '<br>' . L10N::r('Keep the first field empty to influence all wizards')
            ],
            'attach_wizard' => [
                'label' => L10N::r('Attach a products wizard to the product'),
                'key' => '_wcpw_attach_wizard',
                'type' => 'select',
                'default' => '',
                'values' => $wizardIds,
                'description' => L10N::r('Doesn\'t work with reflecting products in the main cart option')
            ],
            'attached_wizard_place' => [
                'label' => L10N::r('Attached products wizard place'),
                'key' => '_wcpw_attached_wizard_place',
                'type' => 'select',
                'default' => 'before_form',
                'values' => [
                    'before_form' => L10N::r('Before form'),
                    'after_form' => L10N::r('After form'),
                    'tab' => L10N::r('Separate tab')
                ]
            ],
            'tab_title' => [
                'label' => L10N::r('Separate tab title'),
                'key' => '_wcpw_tab_title',
                'type' => 'text',
                'default' => L10N::r('WooCommerce Products Wizard')
            ],
            'redirect_on_add_to_cart' => [
                'label' => L10N::r('Redirect to a products wizard on add to cart event'),
                'key' => '_wcpw_redirect_on_add_to_cart',
                'type' => 'select',
                'default' => '',
                'values' => $wizardIds
            ],
            'redirect_link' => [
                'label' => L10N::r('Redirect link'),
                'key' => '_wcpw_redirect_link',
                'type' => 'text',
                'default' => ''
            ],
            'redirect_step_id' => [
                'label' => L10N::r('Step ID for using after redirect'),
                'key' => '_wcpw_step_id_after_redirect',
                'type' => 'number',
                'default' => '',
                // phpcs:disable
                'description' => L10N::r('If you want the product will be out of any step then set it to any out of steps IDs value')
                // phpcs:enable
            ],
            'redirect_active_step_id' => [
                'label' => L10N::r('Active step ID after redirect'),
                'key' => '_wcpw_redirect_active_step_id',
                'type' => 'text',
                'default' => '',
                'description' => L10N::r('Define specific step ID or keep empty')
            ],
            'thumbnail_areas' => [
                'label' => L10N::r('Generated thumbnail areas data'),
                'key' => '_wcpw_thumbnail_areas',
                'type' => 'data-table',
                'default' => [],
                'values' => $thumbnailAreasValues,
                'inModal' => true,
                'description' => L10N::r('Input the name of the area you want to replace with an image')
            ]
        ];

        $productVariation = [
            'availability_rules' => array_replace_recursive(
                $availabilityRules,
                [
                    'label' => L10N::r('Availability rules in a products wizard'),
                    'key' => '_wcpw_variation_availability_rules',
                    'description' => L10N::r('Show/hide the product according the specific rules'),
                    'values' => [
                        'wizard' => [
                            'label' => L10N::r('Wizard'),
                            'key' => 'wizard',
                            'type' => 'select',
                            'default' => '',
                            'description' => L10N::r('Keep empty to influence any wizard'),
                            'values' => ['' => ''] + $wizardIds
                        ]
                    ]
                ]
            ),
            'discount' => [
                'label' => L10N::r('Products wizard discount'),
                'key' => '_wcpw_variation_discount',
                'type' => 'data-table',
                'default' => [],
                'values' => [
                    'id' => [
                        'label' => L10N::r('Wizard'),
                        'key' => 'id',
                        'type' => 'select',
                        'default' => '',
                        'values' => $wizardIds
                    ],
                    'type' => [
                        'label' => L10N::r('Discount type'),
                        'key' => 'type',
                        'type' => 'select',
                        'default' => 'percentage',
                        'values' => [
                            'percentage' => L10N::r('Percentage'),
                            'fixed' => L10N::r('Fixed'),
                            'precise_price' => L10N::r('Precise price')
                        ]
                    ],
                    'value' => [
                        'label' => L10N::r('Value'),
                        'key' => 'value',
                        'type' => 'number',
                        'default' => '',
                        'min' => 0,
                        'step' => 'any'
                    ]
                ],
                'description' => L10N::r('Reduce product variations prices bought using a products wizard')
                    . '<br>' . L10N::r('Keep the first field empty to influence all wizards')
            ],
            'thumbnail_areas' => [
                'label' => L10N::r('Products wizard generated thumbnail areas data'),
                'key' => '_wcpw_variation_thumbnail_areas',
                'type' => 'data-table',
                'default' => [],
                'values' => $thumbnailAreasValues,
                'inModal' => true,
                'description' => L10N::r('Input the name of the area you want to replace with an image')
            ]
        ];

        $productCategory = [
            'availability_rules' => array_replace_recursive(
                $availabilityRules,
                [
                    'label' => L10N::r('Availability rules in a products wizard'),
                    'key' => '_wcpw_availability_rules',
                    'description' => L10N::r('Show/hide the category according the specific rules'),
                    'values' => [
                        'wizard' => [
                            'label' => L10N::r('Wizard'),
                            'key' => 'wizard',
                            'type' => 'select',
                            'default' => '',
                            'description' => L10N::r('Keep empty to influence any wizard'),
                            'values' => ['' => ''] + $wizardIds
                        ]
                    ]
                ]
            ),
            'discount' => [
                'label' => L10N::r('Discount in a products wizard'),
                'key' => '_wcpw_discount',
                'type' => 'data-table',
                'default' => [],
                'values' => [
                    'id' => [
                        'label' => L10N::r('Wizard'),
                        'key' => 'id',
                        'type' => 'select',
                        'default' => '',
                        'values' => $wizardIds
                    ],
                    'type' => [
                        'label' => L10N::r('Discount type'),
                        'key' => 'type',
                        'type' => 'select',
                        'default' => 'percentage',
                        'values' => [
                            'percentage' => L10N::r('Percentage'),
                            'fixed' => L10N::r('Fixed'),
                            'precise_price' => L10N::r('Precise price')
                        ]
                    ],
                    'value' => [
                        'label' => L10N::r('Value'),
                        'key' => 'value',
                        'type' => 'number',
                        'default' => '',
                        'min' => 0,
                        'step' => 'any'
                    ]
                ],
                'showHeader' => true,
                'description' => L10N::r('Reduce products price bought using a products wizard')
                    . '<br>' . L10N::r('Keep the first field empty to influence all wizards'),
            ],
            'attach_wizard' => [
                'label' => L10N::r('Attach products wizard to products'),
                'key' => '_wcpw_attach_wizard',
                'type' => 'select',
                'default' => '',
                'values' => $wizardIds,
                'description' => L10N::r('Doesn\'t work with reflecting products in the main cart option')
            ],
            'attached_wizard_place' => [
                'label' => L10N::r('Attached products wizard place'),
                'key' => '_wcpw_attached_wizard_place',
                'type' => 'select',
                'default' => 'before_form',
                'values' => [
                    'before_form' => L10N::r('Before form'),
                    'after_form' => L10N::r('After form'),
                    'tab' => L10N::r('Separate tab')
                ]
            ],
            'tab_title' => [
                'label' => L10N::r('Separate products wizard tab title'),
                'key' => '_wcpw_tab_title',
                'type' => 'text',
                'default' => L10N::r('WooCommerce Products Wizard')
            ],
            'redirect_on_add_to_cart' => [
                'label' => L10N::r('Redirect to a products wizard on add to cart event'),
                'key' => '_wcpw_redirect_on_add_to_cart',
                'type' => 'select',
                'default' => '',
                'values' => $wizardIds
            ],
            'redirect_link' => [
                'label' => L10N::r('Products wizard redirect link'),
                'key' => '_wcpw_redirect_link',
                'type' => 'text',
                'default' => ''
            ],
            'redirect_step_id' => [
                'label' => L10N::r('Products wizard step ID for using after redirect'),
                'key' => '_wcpw_step_id_after_redirect',
                'type' => 'number',
                'default' => '',
                // phpcs:disable
                'description' => L10N::r('If you want the product will be out of any step then set it to any out of steps IDs value')
                // phpcs:enable
            ],
            'redirect_active_step_id' => [
                'label' => L10N::r('Active step ID after a products wizard redirect'),
                'key' => '_wcpw_redirect_active_step_id',
                'type' => 'text',
                'default' => '',
                'description' => L10N::r('Define specific step ID or keep empty')
            ],
            'thumbnail_areas' => [
                'label' => L10N::r('Products wizard generated thumbnail areas data'),
                'key' => '_wcpw_thumbnail_areas',
                'type' => 'data-table',
                'default' => [],
                'values' => $thumbnailAreasValues,
                'inModal' => true,
                'description' => L10N::r('Input the name of the area you want to replace with an image')
            ]
        ];

        $productAttribute = [
            'availability_rules' => array_replace_recursive(
                $availabilityRules,
                [
                    'label' => L10N::r('Availability rules in a products wizard'),
                    'key' => '_wcpw_availability_rules',
                    'description' => L10N::r('Show/hide the attribute value according the specific rules'),
                    'values' => [
                        'wizard' => [
                            'label' => L10N::r('Wizard'),
                            'key' => 'wizard',
                            'type' => 'select',
                            'default' => '',
                            'description' => L10N::r('Keep empty to influence any wizard'),
                            'values' => ['' => ''] + $wizardIds
                        ]
                    ]
                ]
            ),
            'thumbnail' => [
                'label' => L10N::r('Products wizard thumbnail'),
                'key' => '_wcpw_thumbnail_id',
                'type' => 'thumbnail',
                'default' => '',
                'description' => L10N::r('Used for variation attributes')
            ],
            'thumbnail_areas' => [
                'label' => L10N::r('Products wizard generated thumbnail areas data'),
                'key' => '_wcpw_thumbnail_areas',
                'type' => 'data-table',
                'default' => [],
                'values' => $thumbnailAreasValues,
                'inModal' => true,
                'description' => L10N::r('Input the name of the area you want to replace with an image')
            ]
        ];

        $models = [
            'global' => apply_filters('wcpw_global_settings_model', $global),
            'post' => apply_filters('wcpw_post_settings_model', $post),
            'step' => apply_filters('wcpw_step_settings_model', $step),
            'product' => apply_filters('wcpw_product_settings_model', $product),
            'product_variation' => apply_filters('wcpw_product_variation_settings_model', $productVariation),
            'product_category' => apply_filters('wcpw_product_category_settings_model', $productCategory),
            'product_attribute' => apply_filters('wcpw_product_attribute_settings_model', $productAttribute)
        ];

        $cache = apply_filters('wcpw_settings_models', $models);

        if ($args['source'] && isset($cache[$args['source']])) {
            return $cache[$args['source']];
        }

        return $cache;
    }

    /**
     * Settings models filter
     *
     * @param array $models
     *
     * @return array
     */
    public static function modelsFilter($models)
    {
        $option = (array) get_option('woocommerce_products_wizard_settings_models', []);

        if (empty(array_filter($option))) {
            return $models;
        }

        foreach (array_filter($option) as $source => $settings) {
            foreach ($settings as $key => $setting) {
                $setting = (array) $setting;

                if (!isset($setting['version_from']) || !$setting['version_from']) {
                    continue;
                }

                if (isset($setting['version_to']) && $setting['version_to']
                    && version_compare(WC_PRODUCTS_WIZARD_VERSION, $setting['version_to'], '>=')
                ) {
                    continue;
                }

                if (version_compare(WC_PRODUCTS_WIZARD_VERSION, $setting['version_from'], '>=')) {
                    $models[$source][$key] = $setting;
                }
            }
        }

        return $models;
    }

    /**
     * Handle the setting value according to the type
     *
     * @param mixed $value
     * @param string $type
     *
     * @return string|float|bool|array
     */
    public static function handleSettingType($value, $type = 'string')
    {
        switch ($type) {
            case 'checkbox':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);

            case 'number':
                return (float) $value;

            case 'array':
            case 'data-table':
            case 'group':
            case 'multi-select':
                return (array) $value;

            case 'string':
                return (string) $value;
        }

        return $value;
    }

    /**
     * Get global setting
     *
     * @param string $setting
     * @param mixed $default
     *
     * @return string|float|bool|array
     */
    public static function getGlobal($setting, $default = null)
    {
        static $cache = [];

        if (isset($cache[$setting])) {
            return apply_filters('wcpw_global_setting', $cache[$setting], $setting);
        }

        $model = self::getModel(['getDynamicValues' => false, 'source' => 'global']);

        if (!isset($model[$setting])) {
            $cache[$setting] = $default;

            return apply_filters('wcpw_global_setting', $default, $setting);
        }

        $value = isset($model[$setting]['key']) ? get_option($model[$setting]['key'], null) : null;

        if (is_null($value)) {
            if ($default) {
                $value = $default;
            } elseif (isset($model[$setting]['default'])) {
                $value = $model[$setting]['default'];
            }
        }

        $value = self::handleSettingType($value, $model[$setting]['type']);
        $cache[$setting] = $value;

        return apply_filters('wcpw_global_setting', $value, $setting);
    }

    /**
     * Get one of post settings
     *
     * @param integer $id
     * @param string $setting
     * @param string $modelSource
     * @param mixed $default
     *
     * @return string|float|boolean|array
     */
    public static function getPost($id, $setting, $modelSource = 'post', $default = null)
    {
        static $cache = [];

        if (isset($cache[$id], $cache[$id][$setting])) {
            return apply_filters('wcpw_post_setting', $cache[$id][$setting], $id, $setting, $modelSource);
        }

        $model = self::getModel(['getDynamicValues' => false, 'source' => $modelSource]);

        if (!isset($model[$setting])) {
            $cache[$id][$setting] = $default;

            return apply_filters('wcpw_post_setting', $default, $id, $setting, $modelSource);
        }

        $value = null;

        if (isset($model[$setting]['key'])) {
            $value = get_post_meta($id, $model[$setting]['key']);
        }

        if (empty($value)) {
            if ($default) {
                $value = $default;
            } elseif (isset($model[$setting]['default'])) {
                $value = $model[$setting]['default'];
            }
        } elseif (isset($value[0])) {
            $value = $value[0];
        }

        $value = self::handleSettingType($value, $model[$setting]['type']);

        if (!isset($cache[$id])) {
            $cache[$id] = [];
        }

        $cache[$id][$setting] = $value;

        return apply_filters('wcpw_post_setting', $value, $id, $setting, $modelSource);
    }

    /**
     * Return one post settings array
     *
     * @param integer $id
     * @param array $args
     *
     * @return array
     */
    public static function getPostArray($id, $args = [])
    {
        $defaults = ['public' => false];
        $args = array_merge($defaults, $args);
        $model = self::getModel(['getDynamicValues' => false, 'source' => 'post']);
        $output = [];

        foreach ($model as $settingModelKey => $settingModel) {
            if ($args['public'] && (!isset($settingModel['public']) || !$settingModel['public'])) {
                continue;
            }

            $output[$settingModelKey] = self::getPost($id, $settingModelKey);
        }

        return apply_filters('wcpw_post_settings', $output, $id, $args);
    }

    /**
     * Get an array of the steps ids which are used in the wizard
     *
     * @param integer $wizardId
     *
     * @return array
     */
    public static function getStepsIds($wizardId)
    {
        static $cache = [];

        if (!isset($cache[$wizardId])) {
            $cache[$wizardId] = (array) get_post_meta($wizardId, '_steps_ids', 1);
        }

        return apply_filters('wcpw_steps_ids_setting', $cache[$wizardId], $wizardId);
    }

    /**
     * Get steps settings record from DB
     *
     * @param integer $wizardId
     *
     * @return array
     */
    public static function getStepsSettings($wizardId)
    {
        static $cache = [];

        if (!isset($cache[$wizardId])) {
            $cache[$wizardId] = (array) get_post_meta($wizardId, '_steps_settings', 1);
        }

        return apply_filters('wcpw_steps_settings', $cache[$wizardId], $wizardId);
    }

    /**
     * Get one of wizard step setting
     *
     * @param integer $wizardId
     * @param integer|string $stepId
     * @param string $setting
     * @param mixed $default
     *
     * @return string|float|boolean|array
     */
    public static function getStep($wizardId, $stepId, $setting, $default = null)
    {
        static $cache = [];

        if (isset(
            $cache[$wizardId],
            $cache[$wizardId][$stepId],
            $cache[$wizardId][$stepId][$setting]
        )) {
            return apply_filters('wcpw_step_setting', $cache[$wizardId][$stepId][$setting], $wizardId, $stepId, $setting); // phpcs:ignore
        }

        $model = self::getModel(['getDynamicValues' => false, 'source' => 'step']);

        if (!isset($model[$setting])) {
            $cache[$wizardId][$stepId][$setting] = $default;

            return apply_filters('wcpw_step_setting', $default, $wizardId, $stepId, $setting);
        }

        $meta = self::getStepsSettings($wizardId);

        if ($meta && isset($meta[$stepId], $meta[$stepId][$model[$setting]['key']])) {
            $value = self::handleSettingType($meta[$stepId][$model[$setting]['key']], $model[$setting]['type']);
            $cache[$wizardId][$stepId][$setting] = $value;

            return apply_filters('wcpw_step_setting', $value, $wizardId, $stepId, $setting);
        }

        if ($default) {
            $value = $default;
        } elseif (isset($model[$setting]['default'])) {
            $value = self::handleSettingType($model[$setting]['default'], $model[$setting]['type']);
        } else {
            $value = null;
        }

        $cache[$wizardId][$stepId][$setting] = $value;

        return apply_filters('wcpw_step_setting', $value, $wizardId, $stepId, $setting);
    }

    /**
     * Get one of wizard step settings array
     *
     * @param integer $wizardId
     * @param integer|string $stepId
     * @param array $args
     *
     * @return string|float|boolean|array
     */
    public static function getStepArray($wizardId, $stepId, $args = [])
    {
        $defaults = ['public' => false];
        $args = array_merge($defaults, $args);
        $model = self::getModel(['getDynamicValues' => false, 'source' => 'step']);
        $output = [];

        foreach ($model as $settingModelKey => $settingModel) {
            if ($args['public'] && (!isset($settingModel['public']) || !$settingModel['public'])) {
                continue;
            }

            $output[$settingModelKey] = self::getStep($wizardId, $stepId, $settingModelKey);
        }

        return apply_filters('wcpw_step_settings', $output, $wizardId, $args);
    }

    /**
     * Get product setting
     *
     * @param integer $id
     * @param string $setting
     * @param mixed $default
     *
     * @return string|float|bool|array
     */
    public static function getProduct($id, $setting, $default = null)
    {
        $output = self::getPost($id, $setting, 'product', $default);

        // @since 10.5.0 - older versions support
        if ($setting == 'discount' && !empty($output) && isset($output['value'])) {
            $output = [array_merge(['id' => ''], $output)];
        }

        return apply_filters('wcpw_product_setting', $output, $id, $setting);
    }

    /**
     * Get product variation setting
     *
     * @param integer $id
     * @param string $setting
     * @param mixed $default
     *
     * @return string|float|bool|array
     */
    public static function getProductVariation($id, $setting, $default = null)
    {
        $output = self::getPost($id, $setting, 'product_variation', $default);

        // @since 10.5.0 - older versions support
        if ($setting == 'discount' && !empty($output) && isset($output['value'])) {
            $output = [array_merge(['id' => ''], $output)];
        }

        return apply_filters('wcpw_product_variation_setting', $output, $id, $setting);
    }

    /**
     * Get one of term settings
     *
     * @param integer $id
     * @param string $setting
     * @param string $modelSource
     * @param mixed $default
     *
     * @return string|float|boolean|array
     */
    public static function getTerm($id, $setting, $modelSource = 'product_category', $default = null)
    {
        static $cache = [];

        if (isset($cache[$id], $cache[$id][$setting])) {
            return apply_filters('wcpw_term_setting', $cache[$id][$setting], $id, $setting);
        }

        $model = self::getModel(['getDynamicValues' => false, 'source' => $modelSource]);

        if (!isset($model[$setting])) {
            $cache[$id][$setting] = $default;

            return apply_filters('wcpw_term_setting', $default, $id, $setting);
        }

        $value = null;

        if (isset($model[$setting]['key'])) {
            $value = get_term_meta($id, $model[$setting]['key']);
        }

        if (empty($value)) {
            if ($default) {
                $value = $default;
            } elseif (isset($model[$setting]['default'])) {
                $value = $model[$setting]['default'];
            }
        } elseif (isset($value[0])) {
            $value = $value[0];
        }

        $value = self::handleSettingType($value, $model[$setting]['type']);

        if (!isset($cache[$id])) {
            $cache[$id] = [];
        }

        $cache[$id][$setting] = $value;

        return apply_filters('wcpw_term_setting', $value, $id, $setting);
    }

    /**
     * Get product category setting
     *
     * @param integer $id
     * @param string $setting
     * @param mixed $default
     *
     * @return string|float|bool|array
     */
    public static function getProductCategory($id, $setting, $default = null)
    {
        $output = self::getTerm($id, $setting, 'product_category', $default);

        // @since 10.5.0 - older versions support
        if ($setting == 'discount' && !empty($output) && isset($output['value'])) {
            $output = [array_merge(['id' => ''], $output)];
        }

        return apply_filters('wcpw_product_category_setting', $output, $id, $setting);
    }

    /**
     * Get product attribute setting
     *
     * @param integer $id
     * @param string $setting
     * @param mixed $default
     *
     * @return string|float|bool|array
     */
    public static function getProductAttribute($id, $setting, $default = null)
    {
        $output = self::getTerm($id, $setting, 'product_attribute', $default);

        return apply_filters('wcpw_product_attribute_setting', $output, $id, $setting);
    }

    /**
     * Is sidebar should be visible
     *
     * @param integer $wizardId
     *
     * @return bool
     */
    public static function isSidebarShowed($wizardId)
    {
        $stepId = Form::getActiveStepId($wizardId);

        switch (self::getPost($wizardId, 'show_sidebar')) {
            case 'always':
                $output = true;
                break;

            case 'never':
            case '0':
                $output = false;
                break;

            case 'always_until_result_step':
                $output = is_numeric($stepId);
                break;

            case 'not_empty_until_result_step':
                $output = is_numeric($stepId) && !empty(Cart::get($wizardId));
                break;

            default:
            case 'not_empty':
                $output = !empty(Cart::get($wizardId));
        }

        // show a filter in the sidebar, but not for single step layouts
        if (self::getStep($wizardId, $stepId, 'filter_position') == 'before-widget'
            && !empty(Form::getFilterFields($wizardId, $stepId))
            && !in_array(self::getPost($wizardId, 'mode'), ['single-step', 'expanded-sequence', 'sequence'])
        ) {
            $output = true;
        }

        return apply_filters('wcpw_is_sidebar_showed', $output, $wizardId);
    }

    /**
     * Get final redirect URL
     *
     * @param integer $wizardId
     *
     * @return string
     */
    public static function getFinalRedirectUrl($wizardId)
    {
        $output = trim(self::getPost($wizardId, 'final_redirect_url'));

        // if the settings is empty
        if (!$output && function_exists('wc_get_page_id')) {
            $output = get_permalink(wc_get_page_id('cart'));
        }

        // if url is absolute
        if (strpos($output, home_url()) === false) {
            $output = home_url() . '/' . $output;
        }

        return apply_filters('wcpw_final_redirect_url', $output, $wizardId);
    }

    /**
     * Get list of possible filter sources
     *
     * @return array
     */
    public static function getFilterSourcesList()
    {
        $output = [
            '' => '',
            'price' => L10N::r('Price'),
            'category' => L10N::r('Category'),
            'tag' => L10N::r('Tag'),
            'search' => L10N::r('Search')
        ];

        $output = array_merge($output, wc_get_attribute_taxonomy_labels());

        return apply_filters('wcpw_filter_sources_list', $output);
    }

    /**
     * Get list of the filter views
     *
     * @param string $value
     *
     * @return array
     */
    public function getFilterViewSelectOptions($value)
    {
        $output = [];

        switch ($value) {
            case 'price': {
                $output['range'] = L10N::r('Range');
                break;
            }

            case 'category': {
                $output['select'] = L10N::r('Select');
                $output['inline-radio'] = L10N::r('Inline radio');
                $output['radio'] = L10N::r('Radio');
                $output['image-radio'] = L10N::r('Image radio');
                $output['inline-checkbox'] = L10N::r('Inline checkbox');
                $output['checkbox'] = L10N::r('Checkbox');
                $output['image-checkbox'] = L10N::r('Image checkbox');
                break;
            }

            case 'tag': {
                $output['select'] = L10N::r('Select');
                $output['inline-radio'] = L10N::r('Inline radio');
                $output['radio'] = L10N::r('Radio');
                $output['inline-checkbox'] = L10N::r('Inline checkbox');
                $output['checkbox'] = L10N::r('Checkbox');
                break;
            }

            case 'search': {
                $output['text'] = L10N::r('Text');
                break;
            }

            default: {
                $output['range'] = L10N::r('Range');
                $output['select'] = L10N::r('Select');
                $output['inline-radio'] = L10N::r('Inline radio');
                $output['radio'] = L10N::r('Radio');
                $output['image-radio'] = L10N::r('Image radio');
                $output['inline-checkbox'] = L10N::r('Inline checkbox');
                $output['checkbox'] = L10N::r('Checkbox');
                $output['image-checkbox'] = L10N::r('Image checkbox');
            }
        }

        return $output;
    }

    /** Get list of the filter views via Ajax */
    public function getFilterViewSelectOptionsAjax()
    {
        $value = (string) $_GET['value'];

        if (!$value) {
            exit;
        }

        wp_send_json($this->getFilterViewSelectOptions($value));
    }

    /**
     * Get min products selected message
     *
     * @param integer $wizardId
     * @param integer $limit - products limit
     * @param integer $value - products current value
     *
     * @return string
     */
    public static function getMinimumProductsSelectedMessage($wizardId, $limit, $value)
    {
        $output = str_replace(
            [
                '%limit%',
                '%value%'
            ],
            [
                $limit,
                $value
            ],
            self::getPost($wizardId, 'minimum_products_selected_message')
        );

        return apply_filters('wcpw_minimum_products_selected_message', $output, $wizardId, $limit, $value);
    }

    /**
     * Get max products selected message
     *
     * @param integer $wizardId
     * @param integer $limit - products limit
     * @param integer $value - products current value
     *
     * @return string
     */
    public static function getMaximumProductsSelectedMessage($wizardId, $limit, $value)
    {
        $output = str_replace(
            [
                '%limit%',
                '%value%'
            ],
            [
                $limit,
                $value
            ],
            self::getPost($wizardId, 'maximum_products_selected_message')
        );

        return apply_filters('wcpw_maximum_products_selected_message', $output, $wizardId, $limit, $value);
    }

    /**
     * Get min products price message
     *
     * @param integer $wizardId
     * @param integer $limit - products price limit
     * @param integer $value - current products price
     *
     * @return string
     */
    public static function getMinimumProductsPriceMessage($wizardId, $limit, $value)
    {
        $output = str_replace(
            [
                '%limit%',
                '%value%'
            ],
            [
                $limit,
                $value
            ],
            self::getPost($wizardId, 'minimum_products_price_message')
        );

        return apply_filters('wcpw_minimum_products_price_message', $output, $wizardId, $limit, $value);
    }

    /**
     * Get max products price message
     *
     * @param integer $wizardId
     * @param integer $limit - products price limit
     * @param integer $value - current products price
     *
     * @return string
     */
    public static function getMaximumProductsPriceMessage($wizardId, $limit, $value)
    {
        $output = str_replace(
            [
                '%limit%',
                '%value%'
            ],
            [
                $limit,
                $value
            ],
            self::getPost($wizardId, 'maximum_products_price_message')
        );

        return apply_filters('wcpw_maximum_products_price_message', $output, $wizardId, $limit, $value);
    }

    // <editor-fold desc="Deprecated">
    /**
     * Is sidebar should be visible
     *
     * @param integer $wizardId
     *
     * @return bool
     *
     * @deprecated 4.5.1
     */
    public static function showSidebar($wizardId)
    {
        return self::isSidebarShowed($wizardId);
    }

    /**
     * Return settings value from array or default value
     *
     * @param array $settings
     * @param string $key
     * @param string $default
     * @param string $type
     *
     * @return string|float|bool|array
     *
     * @deprecated 4.0.0
     */
    public static function getValue($settings, $key, $default = '', $type = 'string')
    {
        if (!isset($settings[$key])) {
            return $default;
        }

        $value = $settings[$key];

        switch ($type) {
            case 'string':
                $value = (string) $value;
                break;

            case 'int':
            case 'integer':
                $value = (int) $value;
                break;

            case 'float':
                $value = (float) $value;
                break;

            case 'array':
                $value = (array) $value;
                break;

            case 'bool':
            case 'boolean':
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;

            default:
                $value = (string) $value;
        }

        return $value;
    }

    /**
     * Get post settings record from DB
     *
     * @param integer $wizardId
     *
     * @return array
     *
     * @deprecated 4.0.0
     */
    public static function getPostMeta($wizardId)
    {
        static $cache = [];

        if (!isset($cache[$wizardId])) {
            $cache[$wizardId] = (array) get_post_meta($wizardId, 'settings', 1);
        }

        return $cache[$wizardId];
    }
    // </editor-fold>
}
