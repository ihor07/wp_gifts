<?php
$args = isset($args) ? $args : [];
$modelItem = isset($modelItem) ? $modelItem : [];
?>
<textarea cols="30" rows="10" name="<?php echo esc_attr($args['name']); ?>"<?php
    echo $args['id'] ? ' id="' . esc_attr($args['id']) . '" ' : '';
    echo isset($modelItem['placeholder']) ? ' placeholder="' . esc_attr($modelItem['placeholder']) . '"' : '';
    echo isset($modelItem['data-component']) ? ' data-component="' . esc_attr($modelItem['data-component']) . '"' : '';
    ?>><?php echo sanitize_textarea_field($args['value']); ?></textarea>
