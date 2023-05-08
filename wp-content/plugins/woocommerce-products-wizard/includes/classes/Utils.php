<?php
namespace WCProductsWizard;

/**
 * Utils Class
 *
 * @class Utils
 * @version 2.9.0
 */
class Utils
{
    /**
     * getAvailabilityByRules method cache
     * @var array
     */
    protected static $availabilityRulesCache = [];

    /**
     * Get current URL string
     *
     * @return string
     */
    public static function getCurrentURL()
    {
        $url = 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '')
            . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

        $query = [];
        $parts = parse_url($url);

        // parse query args
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        // remove unnecessary keys
        $keysToRemove = [
            'wcpwId',
            'wcpwStep',
            'wcpwCart',
            'wcpwFilter',
            'wcpwPage',
            'wcpwOrderBy'
        ];

        foreach ($keysToRemove as $keyToRemove) {
            if (isset($query[$keyToRemove])) {
                unset($query[$keyToRemove]);
            }
        }

        // build query string
        $parts['query'] = http_build_query($query);

        // stringify URL parts
        return Utils::buildUrl($parts);
    }

    /**
     * Build URL again from the parse_url function
     *
     * @param array $parts
     *
     * @return string
     */
    public static function buildUrl(array $parts)
    {
        return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
            ((isset($parts['user']) || isset($parts['host'])) ? '//' : '') .
            (isset($parts['user']) ? "{$parts['user']}" : '') .
            (isset($parts['pass']) ? ":{$parts['pass']}" : '') .
            (isset($parts['user']) ? '@' : '') .
            (isset($parts['host']) ? "{$parts['host']}" : '') .
            (isset($parts['port']) ? ":{$parts['port']}" : '') .
            (isset($parts['path']) ? "{$parts['path']}" : '') .
            (isset($parts['query']) ? "?{$parts['query']}" : '') .
            (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
    }

    /**
     * Get term children array
     *
     * @param integer $termId
     * @param string $taxonomy
     *
     * @return array
     */
    public static function getSubTerms($termId, $taxonomy)
    {
        $termsIds = get_term_children($termId, $taxonomy);
        $output = [];

        foreach ($termsIds as $termId) {
            $term = get_term_by('id', $termId, $taxonomy);

            $output[$termId] = $term;
        }

        return apply_filters('wcpw_sub_terms', $output, $termId, $taxonomy);
    }

    /**
     * Get min and max products prices of category
     *
     * @param integer $wizardId
     * @param integer|string $stepId
     *
     * @return array
     */
    public static function getPriceLimits($wizardId, $stepId)
    {
        static $cache = [];

        if (isset($cache[$wizardId][$stepId])) {
            return apply_filters('wcpw_price_limits', $cache[$wizardId][$stepId], $wizardId, $stepId);
        }

        global $wpdb;

        $productsIds = Product::getStepProductsIds($wizardId, $stepId, ['filter' => 1]); // avoid of recursion by filter

        if (empty($productsIds)) {
            $output = [
                'min' => 0,
                'max' => 0
            ];

            $cache[$wizardId][$stepId] = $output;

            return apply_filters('wcpw_price_limits', $output, $wizardId, $stepId);
        }

        // get all products prices related to a specific step
        $results = $wpdb->get_col(
            "SELECT pm.meta_value
            FROM {$wpdb->prefix}posts as posts
            INNER JOIN {$wpdb->prefix}postmeta as pm ON posts.ID = pm.post_id
            WHERE posts.ID IN (" . implode(',', $productsIds) . ")
            AND pm.meta_key = '_price'"
        );

        // sorting prices numerically
        sort($results, SORT_NUMERIC);

        // get min and max prices
        $output = [
            'min' => (float) current($results),
            'max' => (float) end($results)
        ];

        $cache[$wizardId][$stepId] = $output;

        return apply_filters('wcpw_price_limits', $output, $wizardId, $stepId);
    }

    /**
     * Get product thumbnail image or placeholder path
     *
     * @param integer $attachmentId
     * @param string $size
     *
     * @return string
     */
    public static function getThumbnailPath($attachmentId = null, $size = 'thumbnail')
    {
        if (!$attachmentId) {
            $placeholder = get_option('woocommerce_placeholder_image', 0);

            if ($placeholder) {
                $attachmentId = $placeholder;
            } else {
                return WC()->plugin_path() . '/assets/images/placeholder.png';
            }
        }

        $file = get_attached_file($attachmentId, true);

        if (empty($size) || $size == 'full') {
            // for the original size get_attached_file is fine
            return realpath($file);
        }

        if (!wp_attachment_is_image($attachmentId)) {
            // id is not referring to a media
            return null;
        }

        $info = image_get_intermediate_size($attachmentId, $size);

        if (!is_array($info) || !isset($info['file'])) {
            return realpath($file);
        }

        return realpath(str_replace(wp_basename($file), $info['file'], $file));
    }

    /**
     * Parse JSONed request to an array
     *
     * @param array $postData
     *
     * @return array
     */
    public static function parseArrayOfJSONs($postData)
    {
        foreach ($postData as &$value) {
            if (is_string($value)) {
                $decode = json_decode(stripslashes($value), true);
                $value = $decode ? $decode : $value;
            }
        }

        return $postData;
    }

    /**
     * Find image tags in the string
     *
     * @param string $htmlString
     *
     * @return array
     */
    public static function getImagesFromHtml($htmlString)
    {
        $images = [];

        // get all images
        preg_match_all('/<img[^>]+>/i', $htmlString, $imageMatches, PREG_SET_ORDER);

        // loop the images and add the raw img html tag to $images
        foreach ($imageMatches as $imageMatch) {
            $image = [];
            $image['html'] = $imageMatch[0];

            // get attributes
            preg_match_all('/\s+?(.+)="([^"]*)"/U', $imageMatch[0], $image_attr_matches, PREG_SET_ORDER);

            foreach ($image_attr_matches as $image_attr) {
                $image['attr'][$image_attr[1]] = $image_attr[2];
            }

            $images[] = $image;
        }

        return $images;
    }

    /**
     * Find and replace image src URLs by base64 version in HTML
     *
     * @param string $string
     *
     * @return string
     */
    public static function replaceImagesToBase64InHtml($string)
    {
        $images = self::getImagesFromHtml($string);

        foreach ($images as $image) {
            if (!isset($image['attr']['src']) || empty($image['attr']['src'])) {
                continue;
            }

            $type = pathinfo($image['attr']['src'], PATHINFO_EXTENSION);
            $data = self::fileGetContents($image['attr']['src']);

            if ($data) {
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                $string = str_replace($image['attr']['src'], $base64, $string);
            }
        }

        return $string;
    }

    /**
     * Send a JSON request
     *
     * @param array $data
     */
    public static function sendJSON($data)
    {
        wp_send_json(apply_filters('wcpw_send_json_data', $data));
    }

    /**
     * Make string of attributes from array
     *
     * @param array $attributes
     *
     * @return string
     */
    public static function attributesArrayToString($attributes)
    {
        return implode(
            ' ',
            array_map(
                function ($key, $value) {
                    if (is_array($value)) {
                        $value = wp_json_encode($value);
                    } elseif (is_integer($key)) {
                        return esc_attr($value);
                    }

                    return esc_attr($key) . '="' . esc_attr($value) . '"';
                },
                array_keys($attributes),
                $attributes
            )
        );
    }

    /**
     * Implode styles array to inline string
     *
     * @param array $array
     *
     * @return string
     */
    public static function stylesArrayToString($array)
    {
        if (!is_array($array)) {
            return '';
        }

        return implode(
            ';',
            array_map(
                function ($value, $key) {
                    return "$key:$value" ;
                },
                array_values($array),
                array_keys($array)
            )
        );
    }

    /**
     * Encode string to URI
     *
     * @param string $str
     *
     * @return string
     */
    public static function encodeURIComponent($str)
    {
        $revert = ['%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')'];

        return strtr(rawurlencode($str), $revert);
    }

    /**
     * Check the availability rules according to the current state
     *
     * @param integer $wizardId
     * @param array $rules
     * @param integer $itemId
     *
     * @return bool
     */
    public static function getAvailabilityByRules($wizardId, $rules = [], $itemId = null)
    {
        if ($itemId && isset(self::$availabilityRulesCache[$wizardId][$itemId])) {
            $output = self::$availabilityRulesCache[$wizardId][$itemId];

            return apply_filters('wcpw_availability_by_rules', $output, $wizardId, $rules, $itemId);
        }

        $output = true;

        if (!$rules || !is_array($rules) || empty($rules)
            || !Settings::getPost($wizardId, 'check_availability_rules')
        ) {
            return apply_filters('wcpw_availability_by_rules', $output, $wizardId, $rules, $itemId);
        }

        $cartProductsIds = Cart::getProductsAndVariationsIds($wizardId);
        $cartCategories = Cart::getCategoriesIds($wizardId);
        $metRules = [];
        $previousMet = null;

        foreach ($rules as $rule) {
            if (!isset($rule['source'], $rule['condition'], $rule['inner_relation'])
                || !($rule['source'] && $rule['condition'] && $rule['inner_relation'])
                || (isset($rule['wizard']) && !empty($rule['wizard']) && $rule['wizard'] != $wizardId)
            ) {
                continue;
            }

            $isMet = true;

            switch ($rule['source']) {
                case 'none':
                    continue 2;

                case 'product': {
                    if (empty($rule['product'])) {
                        continue 2;
                    }

                    $rule['product'] = !is_array($rule['product']) ? [trim($rule['product'])] : $rule['product'];
                    $isMet = $rule['inner_relation'] == 'and'
                        ? count(array_intersect($rule['product'], $cartProductsIds)) == count($rule['product'])
                        : !empty(array_intersect($rule['product'], $cartProductsIds));

                    break;
                }

                case 'category': {
                    if (empty($rule['category'])) {
                        continue 2;
                    }

                    $rule['category'] = !is_array($rule['category']) ? [trim($rule['category'])] : $rule['category'];
                    $isMet = $rule['inner_relation'] == 'and'
                        ? count(array_intersect($rule['category'], $cartCategories)) == count($rule['category'])
                        : !empty(array_intersect($rule['category'], $cartCategories));

                    break;
                }

                case 'attribute': {
                    if (empty($rule['attribute'])) {
                        continue 2;
                    }

                    if (!is_array($rule['attribute']) && isset($rule['attribute_values'])
                        && !empty($rule['attribute_values'])
                    ) {
                        // @since 10.8.1 - older versions support
                        if (!taxonomy_exists("pa_{$rule['attribute']}")) {
                            continue 2;
                        }

                        $values = Cart::getAttributeValues($wizardId, $rule['attribute']);
                        $ids = wp_parse_id_list($rule['attribute_values']);

                        if (empty($ids)) {
                            continue 2;
                        }

                        $isMet = $rule['inner_relation'] == 'and'
                            ? count(array_intersect($ids, $values)) == count($ids)
                            : !empty(array_intersect($ids, $values));
                    } elseif (is_array($rule['attribute'])) {
                        $ids = [];
                        $cartValues = [];

                        foreach ($rule['attribute'] as $attribute) {
                            $attributeParts = explode('#', $attribute);
                            $taxonomy = reset($attributeParts);
                            $id = end($attributeParts);

                            if (!taxonomy_exists($taxonomy) || empty($id)) {
                                continue;
                            }

                            if (!isset($cartValues[$taxonomy])) {
                                $cartValues[$taxonomy] = Cart::getAttributeValues($wizardId, $taxonomy);
                            }

                            $ids[] = $id;
                        }

                        $cartValues = array_merge(...array_values($cartValues));
                        $isMet = $rule['inner_relation'] == 'and'
                            ? count(array_intersect($ids, $cartValues)) == count($ids)
                            : !empty(array_intersect($ids, $cartValues));
                    }

                    break;
                }

                case 'custom_field': {
                    if (empty($rule['custom_field_name']) || empty($rule['custom_field_name'])) {
                        continue 2;
                    }

                    $cartItem = Cart::getStepDataByKey($wizardId, $rule['custom_field_name']);
                    $isMet = $cartItem && (
                        (is_string($cartItem['value']) && $cartItem['value'] == $rule['custom_field_value'])
                        || (is_array($cartItem['value']) && in_array($rule['custom_field_value'], $cartItem['value']))
                    );
                }
            }

            if ($rule['condition'] == 'not_in_cart') {
                $isMet = !$isMet;
            }

            if (isset($rule['outer_relation']) && $rule['outer_relation'] == 'and' && end($rules) != $rule) {
                if (!is_null($previousMet)) {
                    $previousMet = (int) $previousMet && $isMet;
                } else {
                    $previousMet = (int) $isMet;
                }
            } else {
                if (!is_null($previousMet)) {
                    $metRules[] = (int) $previousMet && $isMet;
                    $previousMet = null;
                } else {
                    $metRules[] = (int) $isMet;
                }
            }
        }

        if (!empty($metRules) && !in_array(1, $metRules)) {
            $output = false;
        }

        if ($itemId) {
            self::$availabilityRulesCache[$wizardId][$itemId] = $output;
        }

        return apply_filters('wcpw_availability_by_rules', $output, $wizardId, $rules, $itemId);
    }

    /**
     * Clear availability rules results cache
     *
     * @param integer $wizardId
     * @param integer $itemId
     */
    public static function clearAvailabilityRulesCache($wizardId = null, $itemId = null)
    {
        if ($wizardId && $itemId) {
            self::$availabilityRulesCache[$wizardId][$itemId] = [];
        } elseif ($wizardId) {
            self::$availabilityRulesCache[$wizardId] = [];
        } else {
            self::$availabilityRulesCache = [];
        }
    }

    /**
     * Prepare HTML content for PDF generation using
     *
     * @param string $content
     * @param array $replacements - key => value to replace in content
     *
     * @return string
     *
     * @deprecated 10.1.1
     */
    public static function prepareContentForPDF($content, $replacements = [])
    {
        if (!empty($replacements)) {
            $content = str_replace(array_keys($replacements), $replacements, $content);
        }

        return Utils::replaceImagesToBase64InHtml(apply_filters('the_content', $content));
    }

    /**
     * ksort recursive function
     *
     * @param array $array
     * @param int $flags
     *
     * @return array
     */
    public static function ksortRecursive(&$array, $flags = SORT_REGULAR)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                self::ksortRecursive($value, $flags);
            }
        }

        return ksort($array, $flags);
    }

    /**
     * file_get_content function with curl fallback
     *
     * @param string $url
     *
     * @return string
     */
    public static function fileGetContents($url)
    {
        if (filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
            if (($data = file_get_contents($url)) && $data) {
                return $data;
            }
        }

        if (!function_exists('\curl_init')) {
            return '';
        }

        $ch = \curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);

        curl_close($ch);

        return $data;
    }

    /** Init session if isn't started and not an AJAX request */
    public static function startSession()
    {
        if (!session_id() && !(is_admin() && !wp_doing_ajax()) && apply_filters('wcpw_start_session', true)) {
            @session_start();
        }
    }
}
