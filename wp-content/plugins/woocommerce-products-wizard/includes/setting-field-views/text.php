<?php
$args = isset($args) ? $args : [];
$modelItem = isset($modelItem) ? $modelItem : [];
?>
<input type="text" value="<?php echo esc_attr($args['value']); ?>" name="<?php echo esc_attr($args['name']); ?>"<?php
    echo $args['id'] ? ' id="' . esc_attr($args['id']) . '" ' : '';
    echo isset($modelItem['readonly']) && $modelItem['readonly'] ? ' readonly' : '';
    echo isset($modelItem['required']) && $modelItem['required'] ? ' required' : '';
    echo isset($modelItem['pattern']) ? ' pattern="' . esc_attr($modelItem['pattern']) . '"' : '';
    echo isset($modelItem['placeholder']) ? ' placeholder="' . esc_attr($modelItem['placeholder']) . '"' : '';
    echo isset($modelItem['data-component']) ? ' data-component="' . esc_attr($modelItem['data-component']) . '"' : '';
    ?>>
