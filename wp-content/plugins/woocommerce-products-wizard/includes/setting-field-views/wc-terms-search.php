<?php
global $woocommerce;

if (!$woocommerce) {
    return;
}

$args = isset($args) ? $args : [];
$modelItem = isset($modelItem) ? $modelItem : [];
$default = [
    'action' => 'woocommerce_json_search_category', // default woocommerce_json_search_categories has errors sometimes
    'limit' => 30,
    'include' => '',
    'exclude' => 0,
    'allowClear' => true,
    'multiple' => true,
    'minimumInputLength'=> 3,
    'placeholder' => esc_html__('Search for a term&hellip;', 'woocommerce')
];

$defaultQueryArgs = [
    'taxonomy' => 'product_cat',
    'hide_empty' => false,
    'include' => $args['value']
];

$queryArgs = isset($modelItem['queryArgs'])
    ? array_replace($defaultQueryArgs, $modelItem['queryArgs'])
    : $defaultQueryArgs;

$inputAttributes = array_replace($default, $modelItem);
$isMultiply = filter_var($inputAttributes['multiple'], FILTER_VALIDATE_BOOLEAN);

if (version_compare($woocommerce->version, '3.0.0', '>=')) {
    $values = [];

    if (!$args['asTemplate'] && is_array($args['value']) && !empty($args['value'])) {
        $terms = get_terms($queryArgs);

        foreach ($terms as $term) {
            $values[$term->term_id] = rawurldecode(
                $term->name . ' (#' . $term->term_id . ')'
            );
        }
    }
    ?>
    <input type="hidden" value="" name="<?php echo esc_attr($args['name']); ?>">
    <select <?php
        echo $isMultiply ? 'multiple="multiple" ' : '';
        echo ($args['asTemplate'] ? 'data-make-' : '') . 'class="wc-product-search" ';
        echo isset($modelItem['data-component']) ? 'data-component="' . esc_attr($modelItem['data-component']) . '" ' : '';
        echo 'name="' . esc_attr($args['name']) . ($isMultiply ? '[]' : '') . '" ';
        ?>
        data-placeholder="<?php echo esc_attr($inputAttributes['placeholder']); ?>"
        data-multiple="<?php echo esc_attr(var_export($isMultiply, true)); ?>"
        data-action="<?php echo esc_attr($inputAttributes['action']); ?>"
        data-allow_clear="<?php echo esc_attr($inputAttributes['allowClear']); ?>"
        data-limit="<?php echo esc_attr($inputAttributes['limit']); ?>"
        data-include="<?php echo esc_attr($inputAttributes['include']); ?>"
        data-exclude="<?php echo esc_attr($inputAttributes['exclude']); ?>"
        data-minimum_input_length="<?php echo esc_attr($inputAttributes['minimumInputLength']); ?>">
        <?php
        foreach ($values as $key => $item) {
            echo '<option value="' . esc_attr($key) . '" ' . selected(true, true, false) . '>'
                . esc_html($item) . '</option>';
        }
        ?>
    </select>
    <?php
} else {
    ?>
    <input type="text" name="<?php echo esc_attr($args['name']); ?>" <?php
        echo isset($modelItem['data-component']) ? 'data-component="' . esc_attr($modelItem['data-component']) . '" ' : '';
        echo ($args['asTemplate'] ? 'data-make-' : '') . 'class="wc-product-search" ';
        ?>
        data-placeholder="<?php echo esc_attr($inputAttributes['placeholder']); ?>"
        data-multiple="<?php echo esc_attr(var_export($isMultiply, true)); ?>"
        data-action="<?php echo esc_attr($inputAttributes['action']); ?>"
        data-allow_clear="<?php echo esc_attr($inputAttributes['allowClear']); ?>"
        data-limit="<?php echo esc_attr($inputAttributes['limit']); ?>"
        data-include="<?php echo esc_attr($inputAttributes['include']); ?>"
        data-exclude="<?php echo esc_attr($inputAttributes['exclude']); ?>"
        data-minimum_input_length="<?php echo esc_attr($inputAttributes['minimumInputLength']); ?>"
        data-selected="<?php echo esc_attr(wp_json_encode($args['value'])); ?>"
        value="<?php echo esc_attr(implode(',', array_keys($args['value']))); ?>">
    <?php
}
