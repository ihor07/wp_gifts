<?php
namespace WCProductsWizard;

add_shortcode('woocommerce-products-wizard', __NAMESPACE__. '\\appShortCode');

if (!function_exists(__NAMESPACE__ . '\\appShortCode')) {
    function appShortCode($attributes = [])
    {
        // if have no WooCommerce or is the admin part (and not AJAX) or Elementor preview
        if (!Core::$wcIsActive
            || (is_admin() && !defined('DOING_AJAX')
                && (!class_exists('\Elementor\Plugin') || !\Elementor\Plugin::$instance->editor->is_edit_mode()))
        ) {
            return 'woocommerce-products-wizard';
        }

        do_action('wcpw_shortcode', $attributes);

        return Template::html('app', $attributes, ['echo' => false]);
    }
}

add_shortcode('wcpw-result-pdf-page-number', __NAMESPACE__. '\\resultPDFPageNumberShortCode');

if (!function_exists(__NAMESPACE__ . '\\resultPDFPageNumberShortCode')) {
    function resultPDFPageNumberShortCode()
    {
        return '<span class="page-number"></span>';
    }
}

add_shortcode('wcpw-result-pdf-page-total', __NAMESPACE__. '\\resultPDFPageTotalShortCode');

if (!function_exists(__NAMESPACE__ . '\\resultPDFPageTotalShortCode')) {
    function resultPDFPageTotalShortCode()
    {
        $total = Instance()->pdf->currentTotalPages;

        if (!$total) {
            return '';
        }

        return '<span class="page-total">' . $total . '</span>';
    }
}

add_shortcode('wcpw-result-pdf-new-page', __NAMESPACE__. '\\resultPDFNewPage');

if (!function_exists(__NAMESPACE__ . '\\resultPDFNewPage')) {
    function resultPDFNewPage()
    {
        return '<div class="new-page"></div>';
    }
}

add_shortcode('wcpw-step-input', __NAMESPACE__. '\\stepInputShortCode');

if (!function_exists(__NAMESPACE__ . '\\stepInputShortCode')) {
    function stepInputShortCode($attributes = [])
    {
        $id = Instance()->getCurrentId();
        $stepId = Instance()->getCurrentStepId();
        $defaults = [
            'class' => '',
            'form' => "wcpw-form-{$id}",
            'name' => '',
            'type' => 'text',
            'value' => '',
            'data-component' => 'wcpw-step-input',
            'data-step-id' => $stepId
        ];

        $attributes = array_replace($defaults, (array) $attributes);

        $unsupportedTypes = ['button', 'image', 'reset', 'submit'];
        $unsupportedTypes = apply_filters('wcpw_step_input_short_code_unsupported_types', $unsupportedTypes);

        if (in_array($attributes['type'], $unsupportedTypes)) {
            return '';
        }

        $attributes['name'] = str_replace('{}', '[]', $attributes['name']);
        $cartValue = Cart::getItemByKey($id, "{$stepId}-" . str_replace('[]', '', $attributes['name']));
        $attributes['data-name'] = $attributes['name'];

        // is an array input with []
        if (strpos($attributes['name'], '[]') !== false) {
            $attributes['name'] = "stepsData[{$stepId}][" . (str_replace('[]', '', $attributes['name'])) . "][]";
        } else {
            $attributes['name'] = "stepsData[{$stepId}][{$attributes['name']}]";
        }

        // is in cart already
        if ($cartValue && !empty($cartValue['value'])) {
            if (in_array($attributes['type'], ['checkbox', 'radio'])) {
                if ((is_array($cartValue['value']) && in_array($attributes['value'], $cartValue['value']))
                    || (!is_array(is_array($cartValue['value'])) && $attributes['value'] == $cartValue['value'])
                ) {
                    $attributes['checked'] = 'checked';
                }
            } elseif ($attributes['type'] != 'hidden') {
                $attributes['value'] = $cartValue['value'];
            }
        }

        if ($attributes['type'] == 'textarea') {
            $value = $attributes['value'];

            unset($attributes['value']);
            unset($attributes['type']);

            $output = '<textarea ' . Utils::attributesArrayToString($attributes) . '>' . esc_html($value)
                . '</textarea>';
        } elseif ($attributes['type'] == 'select') {
            $value = $attributes['value'];
            $values = explode('|', isset($attributes['values']) ? $attributes['values'] : '');

            unset($attributes['value']);
            unset($attributes['values']);
            unset($attributes['type']);

            $output = '<select ' . Utils::attributesArrayToString($attributes) . '>';

            foreach ($values as $option) {
                $selected = false;

                if ($cartValue) {
                    if ((is_array($cartValue['value']) && in_array($option, $cartValue['value']))
                        || (!is_array($cartValue['value']) && $option == $cartValue['value'])
                    ) {
                        $selected = true;
                    }
                } elseif ($value && $option == $value) {
                    $selected = true;
                }

                $output .= '<option value="' . esc_attr($option) . '" ' . ($selected ? ' selected ' : '') . '>'
                    . esc_html($option) . '</option>';
            }

            $output .= '</select>';
        } else {
            $output = '<input ' . Utils::attributesArrayToString($attributes) . '>';
        }

        return apply_filters('wcpw_step_input_html', $output, $attributes);
    }
}

add_shortcode('wcpw-generated-thumbnail-url', __NAMESPACE__. '\\generatedThumbnailURL');

if (!function_exists(__NAMESPACE__ . '\\generatedThumbnailURL')) {
    function generatedThumbnailURL($attributes = [])
    {
        $defaults = ['id' => Instance()->getCurrentId()];
        $attributes = array_replace($defaults, (array) $attributes);

        if (!Settings::getPost($attributes['id'], 'generate_thumbnail')) {
            return '';
        }

        $thumbnail = Thumbnail::generate($attributes['id']);

        if (empty($thumbnail)) {
            return '';
        }

        return $thumbnail['url'];
    }
}
