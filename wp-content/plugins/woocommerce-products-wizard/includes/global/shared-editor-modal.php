<?php
$namespace = 'wcpw';
$settings = apply_filters('wcpw_shared_editor_modal_settings', []);
?>
<div id="<?php echo esc_attr($namespace); ?>-shared-editor-modal"
    class="<?php echo esc_attr($namespace); ?>-modal"
    data-component="<?php echo esc_attr($namespace); ?>-modal">
    <div class="<?php echo esc_attr($namespace); ?>-modal-dialog">
        <a href="#close"
            id="<?php echo esc_attr($namespace); ?>-shared-editor-close"
            title="<?php esc_attr_e('Close'); ?>"
            class="<?php echo esc_attr($namespace); ?>-modal-close"
            data-component="<?php echo esc_attr($namespace); ?>-modal-close">&times;</a>
        <div class="<?php echo esc_attr($namespace); ?>-modal-dialog-body"><?php
            wp_editor('', $namespace . '-shared-editor', $settings);
            ?></div>
        <div class="<?php echo esc_attr($namespace); ?>-modal-dialog-footer">
            <a href="#close"
                id="<?php echo esc_attr($namespace); ?>-shared-editor-save"
                class="button button-primary"
                data-component="<?php echo esc_attr($namespace); ?>-modal-close"><?php
                esc_html_e('Update');
                ?></a>
        </div>
    </div>
</div>
