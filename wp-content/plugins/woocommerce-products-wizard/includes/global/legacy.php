<?php
// phpcs:disable
// The older versions support
// @since 9.2.0
$actions = [
    'wcProductsWizardBeforeOutput' => 'wcpw_before_output',
    'wcProductsWizardAfterOutput' => 'wcpw_after_output',
    'wcProductsWizardStepSettingsForm' => 'wcpw_step_settings_form',
    'wcProductsWizardGetCart' => 'wcpw_get_cart',
    'wcProductsWizardBeforeAddToCart' => 'wcpw_before_add_to_cart',
    'wcProductsWizardAfterAddToCart' => 'wcpw_after_add_to_cart',
    'wcProductsWizardBeforeRemoveByProductId' => 'wcpw_before_remove_by_product_id',
    'wcProductsWizardAfterRemoveByProductId' => 'wcpw_after_remove_by_product_id',
    'wcProductsWizardBeforeRemoveByVariation' => 'wcpw_before_remove_by_variation',
    'wcProductsWizardAfterRemoveByVariation' => 'wcpw_after_remove_by_variation',
    'wcProductsWizardBeforeRemoveByProductKey' => 'wcpw_before_remove_by_product_key',
    'wcProductsWizardAfterRemoveByProductKey' => 'wcpw_after_remove_by_product_key',
    'wcProductsWizardBeforeRemoveByStepId' => 'wcpw_before_remove_by_step_id',
    'wcProductsWizardAfterRemoveByStepId' => 'wcpw_after_remove_by_step_id',
    'wcProductsWizardBeforeTruncate' => 'wcpw_before_truncate',
    'wcProductsWizardAfterTruncate' => 'wcpw_after_truncate',
    'wcProductsWizardBeforeCalculateTotals' => 'wcpw_before_calculate_totals',
    'wcProductsWizardInit' => 'wcpw_init',
    'wcProductsWizardDomPDFOptions' => 'wcpw_dompdf_options',
    'wcProductsWizardDomPDFInstance' => 'wcpw_dompdf_instance',
    'wcProductsWizardBeforeSubmitForm' => 'wcpw_before_submit_form',
    'wcProductsWizardAfterSubmitForm' => 'wcpw_after_submit_form',
    'wcProductsWizardBeforeAddAllToMainCart' => 'wcpw_before_add_all_to_main_cart',
    'wcProductsWizardAfterAddAllToMainCart' => 'wcpw_after_add_all_to_main_cart',
    'wcProductsWizardBeforeGetForm' => 'wcpw_before_get_form',
    'wcProductsWizardAfterGetForm' => 'wcpw_after_get_form',
    'wcProductsWizardBeforeSkipForm' => 'wcpw_before_skip_form',
    'wcProductsWizardAfterSkipForm' => 'wcpw_after_skip_form',
    'wcProductsWizardBeforeSkipAll' => 'wcpw_before_skip_all',
    'wcProductsWizardAfterSkipAll' => 'wcpw_after_skip_all',
    'wcProductsWizardBeforeResetForm' => 'wcpw_before_reset_form',
    'wcProductsWizardAfterResetForm' => 'wcpw_after_reset_form',
    'wcProductsWizardBeforeAddCartProduct' => 'wcpw_before_add_cart_product',
    'wcProductsWizardBeforeRemoveCartProduct' => 'wcpw_before_remove_cart_product',
    'wcProductsWizardBeforeUpdateCartProduct' => 'wcpw_before_update_cart_product',
    'wcProductsWizardBeforeAddToMainCart' => 'wcpw_before_add_to_main_cart',
    'wcProductsWizardShortCode' => 'wcpw_shortcode'
];

$filters = [
    'wcProductsWizardWidgetGeneratedThumbnailAttributes' => 'wcpw_widget_generated_thumbnail_attributes',
    'wcProductsWizardWidgetItemThumbnail' => 'wcpw_widget_item_thumbnail',
    'wcProductsWizardCartItemThumbnail' => 'wcpw_widget_item_thumbnail',
    'wcProductsWizardResultItemThumbnail' => 'wcpw_result_item_thumbnail',
    'wcProductsWizardPostTypeArgs' => 'wcpw_post_type_args',
    'wcProductsWizardDefaultCartContent' => 'wcpw_default_cart_content',
    'wcProductsWizardCartStepDataImageAttributes' => 'wcpw_cart_step_data_image_attributes',
    'wcProductsWizardCartStepData' => 'wcpw_cart_step_data',
    'wcProductsWizardCart' => 'wcpw_cart',
    'wcProductsWizardCartProductsAndVariationsIds' => 'wcpw_cart_products_and_variations_ids',
    'wcProductsWizardCartCategoriesIds' => 'wcpw_cart_categories_ids',
    'wcProductsWizardCartAttributeValues' => 'wcpw_cart_attribute_values',
    'wcProductsWizardCartByStepId' => 'wcpw_cart_by_step_id',
    'wcProductsWizardCartItemByKey' => 'wcpw_cart_item_by_key',
    'wcProductsWizardCartProductById' => 'wcpw_cart_product_by_id',
    'wcProductsWizardCartStepDataByKey' => 'wcpw_cart_step_data_by_key',
    'wcProductsWizardAddToCartItem' => 'wcpw_add_to_cart_item',
    'wcProductsWizardRemoveMainCartReflectedProducts' => 'wcpw_remove_main_cart_reflected_products',
    'wcProductsWizardCartTotal' => 'wcpw_cart_total',
    'wcProductsWizardCartItemPrice' => 'wcpw_cart_item_price',
    'wcProductsWizardCartItemFinalPrice' => 'wcpw_cart_item_final_price',
    'wcProductsWizardCartKitChildValueParts' => 'wcpw_cart_kit_child_value_parts',
    'wcProductsWizardCartKitChildDisplay' => 'wcpw_cart_kit_child_display',
    'wcProductsWizardKitChildData' => 'wcpw_kit_child_data',
    'wcProductsWizardCartItemGeneratedThumbnailAttributes' => 'wcpw_cart_item_generated_thumbnail_attributes',
    'wcProductsWizardCartItemGeneratedThumbnail' => 'wcpw_cart_item_generated_thumbnail',
    'wcProductsWizardCustomStylesVariables'  => 'wcpw_custom_styles_variables',
    'wcProductsWizardResultPDFInstance'  => 'wcpw_result_pdf_instance',
    'wcProductsWizardResultPDFileName'  => 'wcpw_result_pdf_file_name',
    'wcProductsWizardResultPDFile'  => 'wcpw_result_pdf_file',
    'wcProductsWizardGeneratedThumbnail'  => 'wcpw_generated_thumbnail',
    'wcProductsWizardGeneratedThumbnailCartAreas'  => 'wcpw_generated_thumbnail_cart_areas',
    'wcProductsWizardGeneratedThumbnailData'  => 'wcpw_generated_thumbnail_data',
    'wcProductsWizardFormAjaxActions' => 'wcpw_form_ajax_actions',
    'wcProductsWizardStepQuantitiesRule' => 'wcpw_step_quantities_rule',
    'wcProductsWizardSubmitFormItemData' => 'wcpw_submit_form_item_data',
    'wcProductsWizardAddAllToMainCartItems' => 'wcpw_add_all_to_main_cart_items',
    'wcProductsWizardKitId' => 'wcpw_kit_id',
    'wcProductsWizardKitBaseProductData' => 'wcpw_kit_base_product_data',
    'wcProductsWizardKitTitle' => 'wcpw_kit_title',
    'wcProductsWizardMainCartProductData' => 'wcpw_main_cart_product_data',
    'wcProductsWizardPreventFinalRedirect' => 'wcpw_prevent_final_redirect',
    'wcProductsWizardStepsIds' => 'wcpw_steps_ids',
    'wcProductsWizardSteps' => 'wcpw_steps',
    'wcProductsWizardStep' => 'wcpw_step',
    'wcProductsWizardActiveStepId' => 'wcpw_active_step_id',
    'wcProductsWizardActiveStep' => 'wcpw_active_step',
    'wcProductsWizardPaginationArgs' => 'wcpw_pagination_args',
    'wcProductsWizardPaginationItems' => 'wcpw_pagination_items',
    'wcProductsWizardNavItems' => 'wcpw_nav_items',
    'wcProductsWizardFilterFields' => 'wcpw_filter_fields',
    'wcProductsWizardRedirectToWizardProductData' => 'wcpw_redirect_to_wizard_product_data',
    'wcProductsWizardRedirectToWizardLink' => 'wcpw_redirect_to_wizard_link',
    'wcProductsWizardOrderItemGeneratedThumbnailAttributes' => 'wcpw_order_item_generated_thumbnail_attributes',
    'wcProductsWizardOrderItemGeneratedThumbnail' => 'wcpw_order_item_generated_thumbnail',
    'wcProductsWizardVariationArguments' => 'wcpw_variation_arguments',
    'wcProductsWizardProductsRequestArgs' => 'wcpw_products_request_args',
    'wcProductsWizardMergeCartQuantity' => 'wcpw_merge_cart_quantity',
    'wcProductsWizardStepProductsIds' => 'wcpw_step_products_ids',
    'wcProductsWizardStepProductsQueryArgs' => 'wcpw_step_products_query_args',
    'wcProductsWizardProductAvailability' => 'wcpw_product_availability',
    'wcProductsWizardFilterArgsToQuery' => 'wcpw_filter_args_to_query',
    'wcProductsWizardSettingsModels' => 'wcpw_settings_models',
    'wcProductsWizardGlobalSettingsModel' => 'wcpw_global_settings_model',
    'wcProductsWizardPostSettingsModel' => 'wcpw_post_settings_model',
    'wcProductsWizardStepSettingsModel' => 'wcpw_step_settings_model',
    'wcProductsWizardProductSettingsModel' => 'wcpw_product_settings_model',
    'wcProductsWizardProductVariationSettingsModel' => 'wcpw_product_variation_settings_model',
    'wcProductsWizardProductCategorySettingsModel' => 'wcpw_product_category_settings_model',
    'wcProductsWizardProductAttributeSettingsModel' => 'wcpw_product_attribute_settings_model',
    'wcProductsWizardGlobalSetting' => 'wcpw_global_setting',
    'wcProductsWizardPostSetting' => 'wcpw_post_setting',
    'wcProductsWizardPostSettings' => 'wcpw_post_settings',
    'wcProductsWizardStepsIdsSetting' => 'wcpw_steps_ids_setting',
    'wcProductsWizardStepSettings' => 'wcpw_step_settings',
    'wcProductsWizardStepSetting' => 'wcpw_step_setting',
    'wcProductsWizardProductCategorySetting' => 'wcpw_product_category_setting',
    'wcProductsWizardIsSidebarShowed' => 'wcpw_is_sidebar_showed',
    'wcProductsWizardFinalRedirectUrl' => 'wcpw_final_redirect_url',
    'wcProductsWizardFilterSourcesList' => 'wcpw_filter_sources_list',
    'wcProductsWizardMinimumProductsSelectedMessage' => 'wcpw_minimum_products_selected_message',
    'wcProductsWizardMaximumProductsSelectedMessage' => 'wcpw_maximum_products_selected_message',
    'wcProductsWizardMinimumProductsPriceMessage' => 'wcpw_minimum_products_price_message',
    'wcProductsWizardMaximumProductsPriceMessage' => 'wcpw_maximum_products_price_message',
    'wcProductsWizardFormTemplateName' => 'wcpw_form_template_name',
    'wcProductsWizardFormTemplates' => 'wcpw_form_templates',
    'wcProductsWizardFormItemTemplates' => 'wcpw_form_item_templates',
    'wcProductsWizardVariationsTypeTemplates' => 'wcpw_variations_type_templates',
    'wcProductsWizardFormItemTemplateName' => 'wcpw_form_item_template_name',
    'wcProductsWizardNavListTemplates' => 'wcpw_nav_list_templates',
    'wcProductsWizardTemplateHTMLPath' => 'wcpw_template_html_path',
    'wcProductsWizardSubTerms' => 'wcpw_sub_terms',
    'wcProductsWizardPriceLimits' => 'wcpw_price_limits',
    'wcProductsWizardSendJSONData' => 'wcpw_send_json_data',
    'wcProductsWizardStepInputShortCodeUnsupportedTypes' => 'wcpw_step_input_short_code_unsupported_types'
];

foreach ($actions as $old => $new) {
    add_action($new, function () use ($new, $old) {
        if (!has_action($old)) {
            return;
        }

        if (function_exists('wc_do_deprecated_action')) {
            wc_do_deprecated_action($old, func_get_args(), '9.2.0', $new);
        }
    }, -1000, 10);
}

foreach ($filters as $old => $new) {
    add_filter($new, function () use ($new, $old) {
        $args = func_get_args();

        if (!has_filter($old)) {
            return reset($args);
        }

        if (function_exists('wc_deprecated_hook')) {
            wc_deprecated_hook($old, '9.2.0', $new);
        }

        return apply_filters_ref_array($old, $args);
    }, -1000, 10);
}
// phpcs:enable
