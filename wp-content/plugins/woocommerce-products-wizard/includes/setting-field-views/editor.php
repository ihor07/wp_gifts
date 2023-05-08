<?php
$defaults = [
    'media_buttons' => 1,
    'drag_drop_upload' => true
];

$args = isset($args) ? array_replace_recursive($defaults, $args) : $defaults;
$modelItem = isset($modelItem) ? $modelItem : [];
$namespace = 'wcpw';
$textDomain = 'woocommerce-products-wizard';

if (isset($modelItem['inModal']) && $modelItem['inModal']) {
    ?>
    <button class="button" data-component="<?php echo esc_attr($namespace); ?>-shared-editor-open"><?php
        esc_html_e('Set', $textDomain);
        ?></button>
    <input type="hidden" value="<?php echo esc_attr($args['value']); ?>" name="<?php echo esc_attr($args['name']); ?>"
        data-component="<?php echo esc_attr($namespace); ?>-shared-editor-target">
    <?php
    return;
}

wp_editor(
    $args['value'],
    $modelItem['key'],
    [
        'wpautop' => 1,
        'media_buttons' => $args['media_buttons'],
        'textarea_name' => $args['name'],
        'textarea_rows' => 10,
        'tabindex' => null,
        'editor_css' => '',
        'editor_class' => '',
        'teeny' => 0,
        'dfw' => 0,
        'tinymce' => 1,
        'quicktags' => 1,
        'drag_drop_upload' => $args['drag_drop_upload']
    ]
);
