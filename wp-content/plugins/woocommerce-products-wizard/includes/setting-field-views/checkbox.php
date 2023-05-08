<?php
$args = isset($args) ? $args : [];
$modelItem = isset($modelItem) ? $modelItem : [];
?>
<input type="hidden" value="0" name="<?php echo esc_attr($args['name']); ?>">
<input type="checkbox" value="1" name="<?php echo esc_attr($args['name']); ?>"<?php
    echo $args['id'] ? ' id="' . esc_attr($args['id']) . '"' : '';
    echo isset($modelItem['required']) && $modelItem['required'] ? ' required' : '';
    echo isset($modelItem['data-component']) ? ' data-component="' . esc_attr($modelItem['data-component']) . '"' : '';
    echo filter_var($args['value'], FILTER_VALIDATE_BOOLEAN) ? ' checked="checked" ' : '';
    ?>>
